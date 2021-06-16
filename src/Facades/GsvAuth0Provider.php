<?php

namespace Adaptdk\GsvAuth0Provider\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Adaptdk\GsvAuth0Provider\GsvAuth0Provider authenticate(string $token)
 * @method static \Adaptdk\GsvAuth0Provider\GsvAuth0Provider loadUserData()
 * @method static \Adaptdk\GsvAuth0Provider\GsvAuth0Provider getUser()
 * @see \Adaptdk\GsvAuth0Provider\GsvAuth0Provider
 */
class GsvAuth0Provider extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'gsv-auth0-provider';
    }
}
