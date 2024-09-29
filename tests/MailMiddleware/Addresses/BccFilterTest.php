<?php

use TobMoeller\LaravelMailAllowlist\Actions\Addresses\IsAllowedRecipient;
use TobMoeller\LaravelMailAllowlist\Enums\Header;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\BccFilter;

it('creates an address filter for "to" with IsAllowedRecipient address checker', function () {
    $filter = app(BccFilter::class);

    expect($filter)
        ->header->toBe(Header::BCC)
        ->addressChecker->toBeInstanceOf(IsAllowedRecipient::class);
});
