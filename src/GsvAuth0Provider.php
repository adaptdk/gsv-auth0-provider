<?php

namespace Adaptdk\GsvAuth0Provider;

use Adaptdk\GsvAuth0Provider\Models\Auth0User;
use Auth0\SDK\Exception\InvalidTokenException;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

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
     * @return Authenticatable
     */
    public function authenticate(string $token): Authenticatable
    {
        if ($this->auth0_domain === null) {
            throw new \Exception('Auth0 domain not set');
        }

        if ($this->api_identifier === null) {
            throw new \Exception('API identifier not set');
        }

        try {
            $info = $this->decodeJWT($token);

            $this->setUser($info, $token);
        } catch (InvalidTokenException $e) {
            // Re-throw into a 401
            throw new InvalidTokenException($e->getMessage(), 401);
        }

        return $this->getUser();
    }

    /**
     * Set the cache store
     *
     * @param CacheRepository $cache
     * @return self
     */
    public function setCache(CacheRepository $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Set the current user based on data from the user service
     *
     * @param array $info
     * @param string $token
     * @return self
     */
    public function setUser(array $info, string $token): self
    {
        $expire = Carbon::parse($info['exp']);

        $client = app()->make('gsv-auth0-user-service');

        $user = $this->cache->rememberWhen(
            $expire->isAfter(Carbon::now()),
            md5($token),
            $expire->diffInSeconds(Carbon::now()),
            function () use ($token, $info, $client) {
                return $client->setToken($token)->getUser($info['sub']);
            }
        );

        auth()->setUser(new Auth0User([
            'id' => $user['data']['id'],
            'name' => $user['data']['name'],
            'email' => $user['data']['email'],
            'customer_no' => $user['data']['company']['navision_account_no'],
            'abilities' => explode(' ', $info['scope']),
        ]));

        foreach ($this->getUser()->abilities as $ability) {
            Gate::define($ability, function () {
                return true;
            });
        }

        return $this;
    }

    /**
     * Get the current user
     *
     * @return GenericUser
     */
    public function getUser(): GenericUser
    {
        return auth()->user();
    }

    /**
     * Undocumented function
     *
     * @see https://github.com/auth0/laravel-auth0/blob/8377bd09644de60d5a8688653589ea299ccd2969/src/Auth0/Login/Auth0Service.php#L206
     * @param string $encUser
     * @param array $verifierOptions
     * @throws InvalidTokenException
     * @return array
     */
    public function decodeJWT(string $encUser, array $verifierOptions = []): array
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