# Prevent stray mails from your laravel application

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tobmoeller/laravel-mail-allowlist.svg?style=flat-square)](https://packagist.org/packages/tobmoeller/laravel-mail-allowlist)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/tobmoeller/laravel-mail-allowlist/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/tobmoeller/laravel-mail-allowlist/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/tobmoeller/laravel-mail-allowlist/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/tobmoeller/laravel-mail-allowlist/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/tobmoeller/laravel-mail-allowlist.svg?style=flat-square)](https://packagist.org/packages/tobmoeller/laravel-mail-allowlist)

This package enables your Laravel application to filter recipients of outgoing emails by domain or specific email addresses through a configurable allowlist. Ideal for staging and testing environments, it ensures that only approved recipients receive emails. Recipients not matching the allowlist are removed from the email, and if no valid "to" recipients remain, the email is stopped altogether, preventing unintended email delivery.

## Installation

You can install the package via composer:

```bash
composer require tobmoeller/laravel-mail-allowlist
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="mail-allowlist-config"
```

Your laravel application will merge your local config file with the package config file. This enables you just to keep the edited config values.
Additionally this package provides the ability to configure most of the required values through your environment variables.

## Usage

You can configure the package through environment variables:

```dotenv
# Enable the package
MAIL_ALLOWLIST_ENABLED=true

# Define a semicolon separated list a allowed domains
MAIL_ALLOWLIST_ALLOWED_DOMAINS="foo.com;bar.com"

# Define a semicolon separated list a allowed emails
MAIL_ALLOWLIST_ALLOWED_EMAILS="mail@foo.com;mail@bar.com"
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Tobias Möller](https://github.com/TobMoeller)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
