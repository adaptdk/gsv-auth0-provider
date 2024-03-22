<?php

namespace Adaptdk\GsvAuth0Provider;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class GsvAuth0ProviderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => $this->app->configPath('gsv-auth0-provider.php'),
            ], 'config');
        }
    }

    public function register()
    {
        // Register the config values
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'gsv-auth0-provider');

        // Register the main class
        $this->app->singleton('gsv-auth0-provider', function () {
            return new GsvAuth0Provider(
                config('gsv-auth0-provider.domain'),
                config('gsv-auth0-provider.api_identifier')
            );
        });

        // Register the user service
        $this->app->singleton('gsv-auth0-user-service', function () {
            return new UserService(config('gsv-auth0-provider.user_api_base_url'));
        });

        // Open the gates
        Auth::viaRequest('gsv-auth0-provider', function (Request $request) {
            $token = $request->bearerToken() ?: $request->query('authToken');

            if ($token) {
                return $this->app->make('gsv-auth0-provider')->authenticate($token)->getUser();
            }
        });
    }
}
