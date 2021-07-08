<?php

return [

    // Whether to load user data automatically (defaults to 'true')
    'autoload_user' => true,

    // This is the domain for the account in Auth0
    'domain' => env('AUTH0_DOMAIN', 'gsv.eu.auth0.com'),

    // The incoming token must have access to this API (find the API identifier in Auth0)
    'api_identifier' => env('AUTH0_API_IDENTIFIER'),

    // The base URL for the user service
    'user_api_base_url' => env('AUTH0_USER_API'),

    // The URI for the JWKS file (fallback to https://gsv.eu.auth0.com/.well-known/jwks.json)
    'jwks_uri' => null,

];
