<?php

namespace Adaptdk\GsvAuth0Provider;

use Adaptdk\GsvAuth0Provider\Exceptions\InvalidTokenException;
use Adaptdk\GsvAuth0Provider\Exceptions\UserNotFoundException;
use Adaptdk\GsvAuth0Provider\Models\Auth0User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class GsvAuth0Provider
{
    protected $auth0_domain;

    protected $api_identifier;

    protected $jwks_uri;

    protected $cache;

    public function __construct(?string $auth0_domain, ?string $api_identifier, ?string $jwks_uri = null)
    {
        $this->auth0_domain = $auth0_domain;
        $this->api_identifier = $api_identifier;

        $this->jwks_uri = $jwks_uri ?: sprintf('https://%s/.well-known/jwks.json', $this->auth0_domain);

        $this->cache = app()->make('cache.store');

        // Add a neat little custom method that only caches if a condition is met
        $this->cache->macro('rememberWhen', function ($condition, $key, $ttl, $callback) {
            if ($condition) {
                return $this->remember($key, $ttl, $callback);
            } else {
                return $callback();
            }
        });
    }

    /**
     * Authenticate the token
     *
     * @param string $token
     * @return self
     */
    public function authenticate(string $token): self
    {
        if ($this->auth0_domain === null) {
            throw new Exception('Auth0 domain not set');
        }

        if ($this->api_identifier === null) {
            throw new Exception('API identifier not set');
        }

        try {
            $info = $this->decodeJWT($token);

            $this->setUser($info, $token);
        } catch (InvalidTokenException $e) {
            // Re-throw into a 401
            throw new InvalidTokenException($e->getMessage(), 401);
        }

        return $this;
    }

    /**
     * Load user data from the user service
     *
     * @return self
     */
    public function loadUserData(Auth0User $user = null): self
    {
        $user = $user ?: $this->getUser();

        $client = app()->make('gsv-auth0-user-service');

        $userData = $this->cache->rememberWhen(
            $user->expires->isAfter(Carbon::now()), // Only cache if this condition is met
            md5($user->token), // The cache key
            $user->expires->diffInSeconds(Carbon::now()), // Cache expires when the auth expires
            function () use ($client, $user) {
                return $client->setToken($user->token)->fetch($user->auth0_id);
            }
        );

        if ((isset($userData['status']) && $userData['status'] === 'Error') || empty($userData['data'])) {
            throw new UserNotFoundException($userData['message'], 401);
        }

        $user->fill($userData['data']);

        return $this;
    }

    /**
     * Get the current user
     *
     * @return Auth0User
     */
    public function getUser(): Auth0User
    {
        return auth()->user();
    }

    /**
     * Set the current user based on data from the user service
     *
     * @param array $info
     * @param string $token
     * @return self
     */
    protected function setUser(array $info, string $token): self
    {
        $user = new Auth0User([
            'token' => $token,
            'auth0_id' => $info['sub'],
            'expires' => Carbon::parse($info['exp']),
            'abilities' => explode(' ', $info['scope']),
        ]);

        if (config('gsv-auth0-provider.autoload_user')) {
            $this->loadUserData($user);
        }

        auth()->setUser($user);

        collect($user->abilities)
            ->merge(auth()->user()->permission ?? [])
            ->map(function ($permission, $applicationKey) {
                if (is_array($permission)) {
                    $permissions = [];

                    foreach ($permission as $appPermission) {
                        $ability = (string)Str::of($applicationKey)->append('::')->append($appPermission);

                        // in order to keep backwards compatibility
                        // we add both the application specific permission and the one without app prefix(old logic).
                        $permissions[] = $appPermission;
                        $permissions[] = $ability;
                    }

                    return $permissions;
                }

                return $permission;
            })
            ->flatten()
            ->unique()
            ->each(function ($permission) {
                Gate::define($permission, function () {
                    return true;
                });
            });

        return $this;
    }

    /**
     * Verify a JWT from Auth0
     *
     * @see https://github.com/auth0/laravel-auth0/blob/8377bd09644de60d5a8688653589ea299ccd2969/src/Auth0/Login/Auth0Service.php#L206
     * @param string $encUser
     * @param array $verifierOptions
     * @throws InvalidTokenException
     * @return array
     */
    protected function decodeJWT(string $encUser, array $verifierOptions = []): array
    {
        $jwks_fetcher = app()->make('gsv-auth0-jwks-fetcher', [
            'cache' => $this->cache,
        ]);
        $jwks = $jwks_fetcher->getKeys($this->jwks_uri);

        $token_verifier = app()->make('gsv-auth0-token-verifier', [
            'domain' => $this->auth0_domain,
            'apiIdentifier' => $this->api_identifier,
            'jwks' => $jwks,
        ]);

        return $token_verifier->verify($encUser, $verifierOptions);
    }
}
