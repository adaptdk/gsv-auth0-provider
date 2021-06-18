<?php

namespace Adaptdk\GsvAuth0Provider\Tests\Unit;

use Adaptdk\GsvAuth0Provider\GsvAuth0Provider;
use Adaptdk\GsvAuth0Provider\Models\Auth0User;
use Adaptdk\GsvAuth0Provider\Tests\TestCase;
use Adaptdk\GsvAuth0Provider\UserService;
use Auth0\SDK\Helpers\JWKFetcher;
use Auth0\SDK\Helpers\Tokens\TokenVerifier;
use Illuminate\Support\Carbon;
use Mockery;

class GsvAuth0ProviderTest extends TestCase
{
    /** @test */
    public function it_can_instantiate()
    {
        $object = new GsvAuth0Provider('localhost', 'localhost');

        $this->assertInstanceOf(GsvAuth0Provider::class, $object);
    }

    /** @test */
    public function it_can_authenticate()
    {
        $randomToken = '1c1619e44e35560adf878034a8f35773';

        $this->app->bind('gsv-auth0-jwks-fetcher', function () {
            return Mockery::mock(JWKFetcher::class, function ($mock) {
                $mock->shouldReceive('getKeys')->once();
            });
        });

        $this->app->bind('gsv-auth0-token-verifier', function () {
            return Mockery::mock(TokenVerifier::class, function ($mock) {
                $mock->shouldReceive('verify')->once()->andReturn([
                    'exp' => (int) Carbon::now()->addDay()->format('U'),
                    'sub' => 'sms|1234567890',
                    'scope' => 'seePrice book return',
                ]);
            });
        });

        $object = new GsvAuth0Provider('localhost', 'localhost');
        $object->authenticate($randomToken);

        $this->assertTrue(auth()->check());

        $user = $object->getUser();

        $this->assertEquals($randomToken, $user->token);
        $this->assertEquals('sms|1234567890', $user->id);
        $this->assertInstanceOf(Carbon::class, $user->expires);
        $this->assertIsArray($user->abilities);
        $this->assertContains('seePrice', $user->abilities);
        $this->assertContains('book', $user->abilities);
        $this->assertContains('return', $user->abilities);

        // These properties should not be set yet
        $this->assertEmpty($user->id);
        $this->assertEmpty($user->name);
        $this->assertEmpty($user->email);
        $this->assertEmpty($user->company);
    }

    /** @test */
    public function it_can_load_user_data()
    {
        $randomToken = '1c1619e44e35560adf878034a8f35773';

        $this->app->bind('gsv-auth0-jwks-fetcher', function () {
            return Mockery::mock(JWKFetcher::class, function ($mock) {
                $mock->shouldReceive('getKeys')->once();
            });
        });

        $this->app->bind('gsv-auth0-token-verifier', function () {
            return Mockery::mock(TokenVerifier::class, function ($mock) {
                $mock->shouldReceive('verify')->once()->andReturn([
                    'exp' => (int) Carbon::now()->addDay()->format('U'),
                    'sub' => 'sms|1234567890',
                    'scope' => 'seePrice book return',
                ]);
            });
        });

        $this->app->bind('gsv-auth0-user-service', function () {
            return Mockery::mock(UserService::class, function ($mock) {
                $mock->shouldReceive('setToken')->once()->andReturnSelf();
                $mock->shouldReceive('fetch')->once()->andReturn([
                    'data' => [
                        'id' => 1,
                        'name' => 'John Doe',
                        'email' => 'john@doe.com',
                        'company' => [
                            'navision_account_no' => 9,
                        ],
                    ],
                ]);
            });
        });

        $object = new GsvAuth0Provider('localhost', 'localhost');
        $object->authenticate($randomToken)->loadUserData();

        $user = $object->getUser();

        $this->assertEquals(1, $user->id);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@doe.com', $user->email);
        $this->assertIsArray($user->company);
    }

    /** @test */
    public function it_can_return_the_current_user()
    {
        $this->actingAs(new Auth0User([
            'id' => 3,
        ]));

        $object = new GsvAuth0Provider('localhost', 'localhost');
        $user = $object->getUser();

        $this->assertInstanceOf(Auth0User::class, $user);
        $this->assertEquals(3, $user->id);
    }
}
