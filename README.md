# This package providers a middleware and some helper functions for connecting with Auth0 and the GSV Auth API.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/adaptdk/gsv-auth0-provider.svg?style=flat-square)](https://packagist.org/packages/adaptdk/gsv-auth0-provider)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/adaptdk/gsv-auth0-provider/run-tests?label=tests)](https://github.com/adaptdk/gsv-auth0-provider/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/adaptdk/gsv-auth0-provider/Check%20&%20fix%20styling?label=code%20style)](https://github.com/adaptdk/gsv-auth0-provider/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/adaptdk/gsv-auth0-provider.svg?style=flat-square)](https://packagist.org/packages/adaptdk/gsv-auth0-provider)

---
This repo can be used as to scaffold a Laravel package. Follow these steps to get started:

1. Press the "Use template" button at the top of this repo to create a new repo with the contents of this gsv-auth0-provider
2. Run "./configure-gsv-auth0-provider.sh" to run a script that will replace all placeholders throughout all the files
3. Remove this block of text.
4. Have fun creating your package.
5. If you need help creating a package, consider picking up our <a href="https://laravelpackage.training">Laravel Package Training</a> video course.
---

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/gsv-auth0-provider.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/gsv-auth0-provider)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require adaptdk/gsv-auth0-provider
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Adaptdk\GsvAuth0Provider\GsvAuth0ProviderServiceProvider" --tag="gsv-auth0-provider-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Adaptdk\GsvAuth0Provider\GsvAuth0ProviderServiceProvider" --tag="gsv-auth0-provider-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$gsv-auth0-provider = new Adaptdk\GsvAuth0Provider();
echo $gsv-auth0-provider->echoPhrase('Hello, Spatie!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jess Stopa](https://github.com/jezzdk)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
