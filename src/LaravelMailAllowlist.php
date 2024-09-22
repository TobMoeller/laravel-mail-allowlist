<?php

namespace TobMoeller\LaravelMailAllowlist;

use Illuminate\Support\Facades\Config;

class LaravelMailAllowlist
{
    public function enabled(): bool
    {
        return (bool) Config::get('mail-allowlist.enabled', false);
    }

    /**
     * @return array<int, string>
     */
    public function allowedDomainList(): array
    {
        $allowedDomains = Config::get('mail-allowlist.allowed.domains');

        if (is_string($allowedDomains)) {
            return explode(';', $allowedDomains);
        }

        if (is_array($allowedDomains)) {
            return $allowedDomains;
        }

        return [];
    }

    /**
     * @return array<int, string>
     */
    public function allowedEmailList(): array
    {
        $allowedEmails = Config::get('mail-allowlist.allowed.emails');

        if (is_string($allowedEmails)) {
            return explode(';', $allowedEmails);
        }

        if (is_array($allowedEmails)) {
            return $allowedEmails;
        }

        return [];
    }
}
