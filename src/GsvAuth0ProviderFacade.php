<?php

namespace Adaptdk\GsvAuth0Provider;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Adaptdk\GsvAuth0Provider\GsvAuth0Provider
 */
class GsvAuth0ProviderFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'gsv-auth0-provider';
    }
}
