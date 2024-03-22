<?php

namespace Adaptdk\GsvAuth0Provider;

use Adaptdk\GsvAuth0Provider\Exceptions\InvalidTokenException;
use Adaptdk\GsvAuth0Provider\Exceptions\UserNotFoundException;
use Adaptdk\GsvAuth0Provider\Models\Auth0User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

use Auth0\SDK\Token;
use Auth0\SDK\Configuration\SdkConfiguration;

class GsvAuth0Provider
{
    protected $configuration;

    public function __construct(?string $domain, ?string $audience)
    {
        $this->configuration = new SdkConfiguration(
            domain: $domain,
            audience: [$audience],
            clientId: 'dummy',     // Don't need a real value as we only validate jwt's
            clientSecret: 'dummy', // Don't need a real value as we only validate jwt's
            cookieSecret: 'dummy', // Don't need a real value as we only validate jwt's
        );
    }

    /**
     * Authenticate the jwt
     *
     * @param string $token
     * @return self
     */
    public function authenticate(string $jwt): self
    {
        if ($this->configuration === null) {
            throw new Exception('Auth0 configuration not set');
        }

        try {
            $token = new Token($this->configuration, $jwt, \Auth0\SDK\Token::TYPE_ACCESS_TOKEN);
            $token->verify();
            $token->validate();
            $this->setUser($token->toArray(), $jwt);
        } catch (\Exception $e) {
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

        if ($user->expires->isAfter(Carbon::now())) {
            $userData = Cache::remember(
                md5($user->sub),
                $user->expires->diffInSeconds(Carbon::now()),
                function () use ($client, $user) {
                    return $client->setToken($user->token)->fetch($user->auth0_id);
                }
            );
        } else {
            $userData = $client->setToken($user->token)->fetch($user->auth0_id);
        }

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
}
