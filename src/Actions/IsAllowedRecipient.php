<?php

namespace TobMoeller\LaravelMailAllowlist\Actions;

use Symfony\Component\Mime\Address;

class IsAllowedRecipient
{
    /**
     * @param  array<int, string>  $allowedDomains
     * @param  array<int, string>  $allowedEmails
     */
    public function __construct(
        public array $allowedDomains,
        public array $allowedEmails,
    ) {
        //
    }

    public function check(Address $recipient): bool
    {
        $email = $recipient->getAddress();

        if (in_array(explode('@', $email)[1] ?? null, $this->allowedDomains)) {
            return true;
        }

        if (in_array($email, $this->allowedEmails)) {
            return true;
        }

        return false;
    }
}
