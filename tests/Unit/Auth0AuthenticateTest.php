<?php

namespace Adaptdk\GsvAuth0Provider\Tests\Unit;

use Adaptdk\GsvAuth0Provider\Http\Middleware\Auth0Authenticate;
use Adaptdk\GsvAuth0Provider\Models\Auth0User;
use Adaptdk\GsvAuth0Provider\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Auth0AuthenticateTest extends TestCase
{
    /** @test */
    public function it_returns_status_200_if_user_is_logged_in()
    {
        $this->actingAs(new Auth0User());

        $response = (new Auth0Authenticate())->handle(new Request(), function ($request) {
            return new Response();
        });

        $this->assertEquals(200, $response->status());
    }

    /** @test */
    public function it_returns_status_401_if_user_is_not_logged_in()
    {
        $response = (new Auth0Authenticate())->handle(new Request(), function ($request) {
            return new Response();
        });

        $this->assertEquals(401, $response->status());
    }
}
