<?php

namespace Adaptdk\GsvAuth0Provider\Tests\Unit;

use Adaptdk\GsvAuth0Provider\GsvAuth0Provider;
use Adaptdk\GsvAuth0Provider\Models\Auth0User;
use Adaptdk\GsvAuth0Provider\Tests\TestCase;
use Adaptdk\GsvAuth0Provider\UserService;
use Auth0\SDK\Helpers\JWKFetcher;
use Auth0\SDK\Helpers\Tokens\TokenVerifier;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Mockery;

class UserServiceTest extends TestCase
{
    /** @test */
    public function it_can_instantiate()
    {
        $service = new UserService();

        $this->assertInstanceOf(UserService::class, $service);
    }

    /** @test */
    public function it_can_get_and_set_a_baseurl()
    {
        $service = new UserService();
        $service->setBaseUrl('domain.com');

        $this->assertEquals('domain.com', $service->getBaseUrl());
    }

    /** @test */
    public function it_can_get_and_set_a_token()
    {
        $service = new UserService();
        $service->setToken('abcdefg');

        $this->assertEquals('abcdefg', $service->getToken());
    }

    /** @test */
    public function it_can_instantiate_with_a_baseurl()
    {
        $service = new UserService('localhost');

        $this->assertEquals('localhost', $service->getBaseUrl());
    }

    /** @test */
    public function it_requires_a_token()
    {
        $this->expectExceptionMessageMatches('/^Token not set/');

        $service = new UserService();
        $service->fetch();
    }

    /** @test */
    public function it_requires_a_baseurl()
    {
        $this->expectExceptionMessageMatches('/^Base URL not set/');

        $service = new UserService();
        $service->setToken('abcdefg')->fetch();
    }

    /** @test */
    public function it_can_fetch_user_data()
    {
        Http::fake(function ($request) {
            return Http::response([
                'data' => [
                    'id' => 1,
                    'name' => 'John Doe',
                    'email' => 'john@doe.com',
                ]
            ], 200);
        });

        $service = new UserService();
        $userData = $service->setToken('abcdefg')->setBaseUrl('localhost')->fetch();

        $this->assertIsArray($userData['data']);
        $this->assertEquals(1, $userData['data']['id']);
        $this->assertEquals('John Doe', $userData['data']['name']);
        $this->assertEquals('john@doe.com', $userData['data']['email']);
    }
}