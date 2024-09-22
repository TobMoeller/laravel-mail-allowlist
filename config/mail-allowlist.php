<?php

return [
    /**
     * Enables the mail allowlist
     */
    'enabled' => env('MAIL_ALLOWLIST_ENABLED', false),

    /**
     * Define the domains and email addresses that are allowed
     * to receive mails from your application.
     * All other recipients will be filtered out
     */
    'allowed' => [

        /**
         * Can either be a singular domain string,
         * a semicolon separated list of domains or
         * an array of domain strings
         */
        'domains' => env('MAIL_ALLOWLIST_ALLOWED_DOMAINS'),

        /**
         * Can either be a singular email address string,
         * a semicolon separated list of email addresses or
         * an array of email address strings
         */
        'emails' => env('MAIL_ALLOWLIST_ALLOWED_EMAILS'),
    ],
];
