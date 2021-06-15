<?php

namespace Adaptdk\GsvAuth0Provider\Tests;

use Adaptdk\GsvAuth0Provider\GsvAuth0ProviderServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Adaptdk\\GsvAuth0Provider\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            GsvAuth0ProviderServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        include_once __DIR__.'/../database/migrations/create_gsv-auth0-provider_table.php.stub';
        (new \CreatePackageTable())->up();
        */
    }
}
