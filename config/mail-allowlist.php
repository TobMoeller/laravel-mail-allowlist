<?php

use Psr\Log\LogLevel;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\AddGlobalBcc;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\AddGlobalCc;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\AddGlobalTo;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\BccFilter;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\CcFilter;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\EnsureRecipients;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\ToFilter;

return [
    /*
     * Enables the mail allowlist.
     */
    'enabled' => env('MAIL_ALLOWLIST_ENABLED', false),

    /*
     * Define the mail middleware every message should be passed through.
     * Can be either a class-string or an instance. Class-strings will
     * be instantiated through Laravel's service container.
     *
     * All middleware must implement the MailMiddlewareContract
     */
    'middleware' => [
        ToFilter::class,
        CcFilter::class,
        BccFilter::class,
        AddGlobalTo::class,
        AddGlobalCc::class,
        AddGlobalBcc::class,
        EnsureRecipients::class,
    ],

    /*
     * Define the domains and email addresses that are allowed
     * to receive mails from your application.
     * All other recipients will be filtered out.
     */
    'allowed' => [

        /*
         * Can either be a singular domain string,
         * a semicolon separated list of domains or
         * an array of domain strings.
         *
         * e.g.
         * 'bar.com'
         * 'foo.com;bar.com;...'
         * ['foo.com', 'bar.com']
         */
        'domains' => env('MAIL_ALLOWLIST_ALLOWED_DOMAINS'),

        /*
         * Can either be a singular email address string,
         * a semicolon separated list of email addresses or
         * an array of email address strings (only in config).
         *
         * e.g.
         * 'foo@bar.com'
         * 'foo@bar.com;bar@foo.com;...'
         * ['foo.com', 'bar.com']
         */
        'emails' => env('MAIL_ALLOWLIST_ALLOWED_EMAILS'),
    ],

    /*
     * Define global recipients to be added to every mail sent.
     * Each one can either be a singular email address string,
     * a semicolon separated list of email addresses or
     * an array of email address strings (only in config)
     *
     * e.g.
     * 'foo@bar.com'
     * 'foo@bar.com;bar@foo.com;...'
     * ['foo.com', 'bar.com']
     */
    'global' => [
        'to' => env('MAIL_ALLOWLIST_GLOBAL_TO'),
        'cc' => env('MAIL_ALLOWLIST_GLOBAL_CC'),
        'bcc' => env('MAIL_ALLOWLIST_GLOBAL_BCC'),
    ],

    /*
     * Configure the logging of filtered mails.
     */
    'log' => [
        /*
         * Enables the log.
         */
        'enabled' => env('MAIL_ALLOWLIST_LOG_ENABLED', true),

        /*
         * Define a custom logging channel for your filtered message
         * logs. Leave empty (null) to default to Laravel's default
         * logging channel (config: logging.default). If this is
         * undefined, it will fall back to the 'stack' channel.
         */
        'channel' => env('MAIL_ALLOWLIST_LOG_CHANNEL'),

        /*
         * Define the log level to log your filtered messages in.
         */
        'level' => env('MAIL_ALLOWLIST_LOG_LEVEL', LogLevel::INFO),

        /*
         * Define, what parts of the message should be logged.
         */
        'include' => [
            /*
             * Each middleware can add messages to the log through the
             * message context that is passed through the pipeline.
             */
            'middleware' => true,

            /*
             * Log the final message headers.
             */
            'headers' => true,

            /*
             * Log the final message body.
             */
            'body' => false,
        ],
    ],
];
