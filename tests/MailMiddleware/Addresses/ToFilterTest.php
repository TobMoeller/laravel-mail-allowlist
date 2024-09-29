<?php

use TobMoeller\LaravelMailAllowlist\Actions\Addresses\IsAllowedRecipient;
use TobMoeller\LaravelMailAllowlist\Enums\Header;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\ToFilter;

it('creates an address filter for "to" with IsAllowedRecipient address checker', function () {
    $filter = app(ToFilter::class);

    expect($filter)
        ->header->toBe(Header::TO)
        ->addressChecker->toBeInstanceOf(IsAllowedRecipient::class);
});
