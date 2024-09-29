<?php

namespace TobMoeller\LaravelMailAllowlist\Actions\Addresses;

use Symfony\Component\Mime\Address;

interface CheckAddressContract
{
    public function check(Address $address): bool;
}

