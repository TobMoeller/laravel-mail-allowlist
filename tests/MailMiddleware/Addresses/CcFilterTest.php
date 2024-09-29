<?php

use TobMoeller\LaravelMailAllowlist\Actions\Addresses\IsAllowedRecipient;
use TobMoeller\LaravelMailAllowlist\Enums\Header;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\CcFilter;

it('creates an address filter for "to" with IsAllowedRecipient address checker', function () {
    $filter = app(CcFilter::class);

    expect($filter)
        ->header->toBe(Header::CC)
        ->addressChecker->toBeInstanceOf(IsAllowedRecipient::class);
});
