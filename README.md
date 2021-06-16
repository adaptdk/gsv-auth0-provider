# GSV Auth0 Provider

[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/adaptdk/gsv-auth0-provider/run-tests?label=tests)](https://github.com/adaptdk/gsv-auth0-provider/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/adaptdk/gsv-auth0-provider/Check%20&%20fix%20styling?label=code%20style)](https://github.com/adaptdk/gsv-auth0-provider/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)

## Installation

You can install the package via composer. Add the following to composer.json:
```bash
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/adaptdk/gsv-auth0-provider"
        }
    ],
    "require": {
        "adaptdk/gsv-auth0-provider": "*"
    }
}
```

Next run the command:
```bash
composer install
```

## Usage (Lumen)

Make the following changes in `bootstrap/app.php`.

Register the service provider:
```php
$app->register(\Adaptdk\GsvAuth0Provider\GsvAuth0ProviderServiceProvider::class);
```

Enable the authentication middleware:
```php
$app->routeMiddleware([
    'auth' => \Adaptdk\GsvAuth0Provider\Http\Middleware\Auth0Authenticate::class,
]);
```

Apply the middleware to your routes:
```php
$router->group(['middleware' => 'auth'], function ($router) {
    // Your routes here...
});
```

Now it is possible to access the current user in the protected routes with:
```php
auth()->user();
```

You can check for permissions by using `auth()->user()->can()`, for example:
```php
auth()->user()->can('create_post');
```

### Authorization

Add the standard authorization middleware:
```php
$app->routeMiddleware([
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
]);
```

Add the middleware to your routes, for example:
```php
$router->get('/posts', [
    'uses' => 'PostController@index',
    'middleware' => 'can:create_post'
]);
```

Simply change `create_post` to the name of the required permission.

## Usage (Laravel)

_Under construction._

## Config file

This is the contents of the config file:

```php
return [

    // This is the domain for the account in Auth0
    'domain' => env('AUTH0_DOMAIN', 'gsv.eu.auth0.com'),

    // The incoming token must have access to this API (find the API identifier in Auth0)
    'api_identifier' => env('AUTH0_API_IDENTIFIER'),

    // The base URL for the user service
    'user_api_base_url' => env('AUTH0_USER_API'),

    // The URI for the JWKS file (fallback to https://gsv.eu.auth0.com/.well-known/jwks.json)
    'jwks_uri' => null,

];
```

## Testing

```bash
composer test
```
