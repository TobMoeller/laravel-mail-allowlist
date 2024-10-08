# Prevent stray mails from your Laravel application

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tobmoeller/laravel-mail-allowlist.svg?style=flat-square)](https://packagist.org/packages/tobmoeller/laravel-mail-allowlist)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/tobmoeller/laravel-mail-allowlist/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/tobmoeller/laravel-mail-allowlist/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/tobmoeller/laravel-mail-allowlist/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/tobmoeller/laravel-mail-allowlist/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/tobmoeller/laravel-mail-allowlist.svg?style=flat-square)](https://packagist.org/packages/tobmoeller/laravel-mail-allowlist)

This package provides a customizable middleware pipeline for email messages, allowing you to filter, modify, and inspect emails before they are sent.

**Key Features:**

- **Recipient Allowlist Filtering:**
  - Filter outgoing email recipients based on a configurable allowlist of domains and specific email addresses.
  - Ideal for staging and testing environments to prevent unintended emails from reaching unintended recipients.
  - Automatically removes recipients not matching the allowlist from the "To", "Cc", and "Bcc" fields.
  - If no valid recipients remain after filtering, the email is canceled to prevent unintended delivery.

- **Add Global Recipients:**
  - Set default or global "To", "Cc", and "Bcc" recipients via configuration.
  - Ensure certain recipients always receive emails, such as administrators, audit logs, or monitoring addresses.

- **Customizable Middleware Pipeline:**
  - Utilize a middleware pipeline similar to Laravel's HTTP middleware, but for outgoing emails.
  - Add, remove, or reorder middleware to control the processing of emails.

- **Custom Middleware Support:**
  - Create your own middleware to implement custom logic for outgoing emails.
  - Modify email content, set headers, add attachments, or perform any email transformation needed.
  - Middleware can inspect emails, log information, or integrate with other services.

- **Advanced Logging Options:**
  - Configure logging to use custom channels.
  - Set custom log levels (e.g., 'debug', 'info', 'error', etc.).
  - Enable mail middleware to add individual log messages during email processing.
  - Choose whether to include middleware logs, email message headers or message bodies in the logs.

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

# Define a semicolon separated list of globally added emails
MAIL_ALLOWLIST_GLOBAL_TO="mail@foo.com;mail@bar.com"
MAIL_ALLOWLIST_GLOBAL_CC="mail@foo.com;mail@bar.com"
MAIL_ALLOWLIST_GLOBAL_BCC="mail@foo.com;mail@bar.com"

# Configure logging
MAIL_ALLOWLIST_LOG_ENABLED=true
MAIL_ALLOWLIST_LOG_CHANNEL=stack # optional, defaults to Laravel's logging.default
MAIL_ALLOWLIST_LOG_LEVEL=error # optional, defaults to info
```

### Customizing the Middleware Pipeline

The package processes outgoing emails through a middleware pipeline, allowing you to customize or extend the email handling logic. By default, the pipeline includes the following middleware:

```php
'middleware' => [
    ToFilter::class;
    CcFilter::class;
    BccFilter::class;
    AddGlobalTo::class,
    AddGlobalCc::class,
    AddGlobalBcc::class,
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

### Customizing the Logging Behavior

You can control most of the logging behavior from environment variables or the configuration file. For more advanced use cases, you might want to have full control over how log messages are generated and where they are sent. You can achieve this by binding your own implementations of the log content generation action and/or the logging action itself.

#### Customizing the log message content

Create a new class that implements `GenerateLogMessageContract` to define how log messages are generated:

```php
use TobMoeller\LaravelMailAllowlist\Actions\Logs\GenerateLogMessageContract;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

class CustomLogMessage implements GenerateLogMessageContract
{
    public function generate(MessageContext $messageContext): string
    {
        // Generate your own log message
    }
}
```

#### Customizing the log message content

Create a new class that implements `LogMessageContract` to define how log messages are handled:

```php
use TobMoeller\LaravelMailAllowlist\Actions\Logs\LogMessageContract;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

class CustomMessageLogging implements LogMessageContract
{
    /**
     * Optional: 
     * Inject the message generator into your class to use the default 
     * message generation (is resolved by the service container)
     */
    public function __construct(
        public GenerateLogMessageContract $generateLogMessage
    ) {}

    public function log(MessageContext $messageContext): void
    {
        // Handle logging yourself
    }
}
```

#### Binding Custom Implementations

To instruct Laravel to use your custom classes, you need to bind them in your application's service container. This is typically done in a service provider like `App\Providers\AppServiceProvider`.

```php
use TobMoeller\LaravelMailAllowlist\Actions\Logs\GenerateLogMessageContract;
use TobMoeller\LaravelMailAllowlist\Actions\Logs\LogMessageContract;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind the custom log message generator
        $this->app->bind(GenerateLogMessageContract::class, CustomLogMessage::class);

        // Bind the custom log handler
        $this->app->bind(LogMessageContract::class, CustomMessageLogging::class);
    }
}
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
