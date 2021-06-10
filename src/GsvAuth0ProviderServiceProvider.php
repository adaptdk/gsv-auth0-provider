<?php

namespace Adaptdk\GsvAuth0Provider;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Adaptdk\GsvAuth0Provider\Commands\GsvAuth0ProviderCommand;

class GsvAuth0ProviderServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('gsv-auth0-provider')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_gsv-auth0-provider_table')
            ->hasCommand(GsvAuth0ProviderCommand::class);
    }
}
