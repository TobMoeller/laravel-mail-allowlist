<?php

return [
    'enabled' => env('MAIL_ALLOWLIST_ENABLED', false),

    'allowed' => [
        'domains' => env('MAIL_ALLOWLIST_ALLOWED_DOMAINS'),
        'emails' => env('MAIL_ALLOWLIST_ALLOWED_EMAILS'),
    ],
];
