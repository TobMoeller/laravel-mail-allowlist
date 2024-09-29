# Prevent stray mails from your Laravel application

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tobmoeller/laravel-mail-allowlist.svg?style=flat-square)](https://packagist.org/packages/tobmoeller/laravel-mail-allowlist)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/tobmoeller/laravel-mail-allowlist/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/tobmoeller/laravel-mail-allowlist/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/tobmoeller/laravel-mail-allowlist/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/tobmoeller/laravel-mail-allowlist/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/tobmoeller/laravel-mail-allowlist.svg?style=flat-square)](https://packagist.org/packages/tobmoeller/laravel-mail-allowlist)

This package enables your Laravel application to filter recipients of outgoing emails by domain or specific email addresses through a configurable allowlist. Ideal for staging environments, it ensures that only approved recipients receive emails. Recipients not matching the allowlist are removed from the email, and if no valid "to" recipients remain, the email is stopped altogether, preventing unintended email delivery.

Additionally, the package now supports a customizable middleware pipeline, allowing you to control the existing functionality and implement additional logic for outgoing emails. You can add your own middleware to modify, inspect, or control email messages.

> **Important Note:**
>
> This package utilizes Laravel's `MessageSending` event to inspect and modify outgoing emails. If your application has custom listeners or modifications affecting this event, please thoroughly test the package to ensure it integrates seamlessly and maintains the correct filtering functionality.

## Installation

You can install the package via composer:

```bash
composer require tobmoeller/laravel-mail-allowlist
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="mail-allowlist-config"
```

Your Laravel application will merge your local config file with the package config file. This enables you just to keep the edited config values.
Additionally this package provides the ability to configure most of the required values through your environment variables.

## Usage

You can configure the package through environment variables:

```dotenv
# Enable the package
MAIL_ALLOWLIST_ENABLED=true

# Define a semicolon separated list of allowed domains
MAIL_ALLOWLIST_ALLOWED_DOMAINS="foo.com;bar.com"

# Define a semicolon separated list of allowed emails
MAIL_ALLOWLIST_ALLOWED_EMAILS="mail@foo.com;mail@bar.com"
```

### Customizing the Middleware Pipeline

The package processes outgoing emails through a middleware pipeline, allowing you to customize or extend the email handling logic. By default, the pipeline includes the following middleware:

```php
'middleware' => [
    ToFilter::class;
    CcFilter::class;
    BccFilter::class;
    EnsureRecipients::class;
],
```

#### Reordering or Removing Middleware

The order of middleware in the pipeline matters. Each middleware can modify the email before passing it to the next middleware.
You can also reorder or remove middleware from the pipeline to suit your requirements. For example, if you want to disable the `BccFilter` and want the pipeline to stop right after no recipients remain in the `ToFilter`, you can adjust the pipeline:

```php
'middleware' => [
    ToFilter::class;
    EnsureRecipients::class; // stops further execution when no recipients remain
    CcFilter::class;
    // BccFilter::class; // disabled
],
```

#### Creating Custom Middleware

You can add your own middleware to the pipeline to modify, inspect, or control outgoing emails according to your application's needs. For example, to prevent a mail from being sent on a custom condition, you might create a middleware like this:

```php
use Closure;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MailMiddlewareContract;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

class CancelMessageMiddleware implements MailMiddlewareContract
{
    public function handle(MessageContext $messageContext, Closure $next): mixed
    {
        if ($customCondition) {
            // Indicate that the message should be canceled
            $messageContext->cancelSendingMessage('Custom reason');
            // Prevent execution of following middleware
            return null;
        }

        return $next($messageContext);
    }
}
```

Then add it to your middleware pipeline. This can be done as a class-string which will be instantiated by Laravel's service container or as a concrete instance.

```php
'middleware' => [
    // Upstream middleware
    \App\Mail\Middleware\CancelMessageMiddleware::class, // As a class-string.
    new \App\Mail\Middleware\CancelMessageMiddleware(), // As an instance
    // Downstream middleware
],
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
