<?php

namespace Adaptdk\GsvAuth0Provider\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Adaptdk\GsvAuth0Provider\GsvAuth0Provider
 */
class GsvAuth0Provider extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'gsv-auth0-provider';
    }
}
