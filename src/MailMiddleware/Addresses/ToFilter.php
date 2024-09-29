<?php

namespace TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses;

use TobMoeller\LaravelMailAllowlist\Actions\Addresses\IsAllowedRecipient;
use TobMoeller\LaravelMailAllowlist\Enums\Header;

class ToFilter extends AddressFilter
{
    public function __construct(IsAllowedRecipient $addressChecker)
    {
        parent::__construct(
            Header::TO,
            $addressChecker,
        );
    }
}
