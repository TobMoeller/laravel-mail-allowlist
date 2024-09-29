<?php

namespace TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses;

use TobMoeller\LaravelMailAllowlist\Actions\Addresses\IsAllowedRecipient;
use TobMoeller\LaravelMailAllowlist\Enums\Header;

class BccFilter extends AddressFilter
{
    public function __construct(IsAllowedRecipient $addressChecker)
    {
        parent::__construct(
            Header::BCC,
            $addressChecker,
        );
    }
}
