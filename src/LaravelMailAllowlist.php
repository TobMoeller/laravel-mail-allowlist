<?php

namespace TobMoeller\LaravelMailAllowlist;

use Illuminate\Support\Facades\Config;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MailMiddlewareContract;

class LaravelMailAllowlist
{
    public function enabled(): bool
    {
        return (bool) Config::get('mail-allowlist.enabled', false);
    }

    /**
     * @return array<int, MailMiddlewareContract|class-string<MailMiddlewareContract>>
     */
    public function mailMiddleware(): array
    {
        $middleware = Config::get('mail-allowlist.middleware');

        return is_array($middleware) ? $middleware : [];
    }

    /**
     * @return array<int, string>
     */
    public function allowedDomainList(): array
    {
        $allowedDomains = Config::get('mail-allowlist.allowed.domains');

        return $this->extractArrayFromConfig($allowedDomains);
    }

    /**
     * @return array<int, string>
     */
    public function allowedEmailList(): array
    {
        $allowedEmails = Config::get('mail-allowlist.allowed.emails');

        return $this->extractArrayFromConfig($allowedEmails);
    }

    /**
     * @return array<int, string>
     */
    public function globalToEmailList(): array
    {
        $toEmails = Config::get('mail-allowlist.global.to');

        return $this->extractArrayFromConfig($toEmails);
    }

    /**
     * @return array<int, string>
     */
    public function globalCcEmailList(): array
    {
        $ccEmails = Config::get('mail-allowlist.global.cc');

        return $this->extractArrayFromConfig($ccEmails);
    }

    /**
     * @return array<int, string>
     */
    public function globalBccEmailList(): array
    {
        $bccEmails = Config::get('mail-allowlist.global.bcc');

        return $this->extractArrayFromConfig($bccEmails);
    }

    /**
     * Extracts the array from a config value that can be
     * either a semicolon separated string or an array
     *
     * @return array<int, string>
     */
    protected function extractArrayFromConfig(mixed $configValue): array
    {
        if (is_string($configValue)) {
            return explode(';', $configValue);
        }

        if (is_array($configValue)) {
            return $configValue;
        }

        return [];
    }
}
