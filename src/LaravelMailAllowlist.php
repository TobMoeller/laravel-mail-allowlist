<?php

namespace TobMoeller\LaravelMailAllowlist;

use Illuminate\Support\Facades\Config;
use InvalidArgumentException;
use Psr\Log\LogLevel;
use ReflectionClass;
use Throwable;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MailMiddlewareContract;

class LaravelMailAllowlist
{
    public function enabled(): bool
    {
        return (bool) Config::get('mail-allowlist.enabled', false);
    }

    public function mailMiddlewareEnabled(): bool
    {
        return (bool) Config::get('mail-allowlist.sending.middleware_enabled', true);
    }

    /**
     * @return array<int, MailMiddlewareContract|class-string<MailMiddlewareContract>>
     */
    public function mailMiddleware(): array
    {
        $middleware = Config::get('mail-allowlist.sending.middleware');

        return is_array($middleware) ? $middleware : [];
    }

    /**
     * @return array<int, string>
     */
    public function allowedDomainList(): array
    {
        $allowedDomains = Config::get('mail-allowlist.sending.allowed.domains');

        return $this->extractArrayFromConfig($allowedDomains);
    }

    /**
     * @return array<int, string>
     */
    public function allowedEmailList(): array
    {
        $allowedEmails = Config::get('mail-allowlist.sending.allowed.emails');

        return $this->extractArrayFromConfig($allowedEmails);
    }

    /**
     * @return array<int, string>
     */
    public function globalToEmailList(): array
    {
        $toEmails = Config::get('mail-allowlist.sending.global.to');

        return $this->extractArrayFromConfig($toEmails);
    }

    /**
     * @return array<int, string>
     */
    public function globalCcEmailList(): array
    {
        $ccEmails = Config::get('mail-allowlist.sending.global.cc');

        return $this->extractArrayFromConfig($ccEmails);
    }

    /**
     * @return array<int, string>
     */
    public function globalBccEmailList(): array
    {
        $bccEmails = Config::get('mail-allowlist.sending.global.bcc');

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

    public function logEnabled(): bool
    {
        return (bool) Config::get('mail-allowlist.sending.log.enabled', false);
    }

    public function logChannel(): string
    {
        $channel = Config::get('mail-allowlist.sending.log.channel');
        $channel = is_string($channel) ? $channel : Config::get('logging.default');

        return is_string($channel) ? $channel : 'stack';
    }

    /**
     * @throws Throwable
     */
    public function logLevel(): string
    {
        $level = Config::get('mail-allowlist.sending.log.level');
        $allowedLevels = array_values((new ReflectionClass(LogLevel::class))->getConstants());

        throw_unless(
            is_string($level) &&
            in_array($level = mb_strtolower($level), $allowedLevels),
            InvalidArgumentException::class,
            'Invalid log level provided'
        );

        return $level;
    }

    public function logMiddleware(): bool
    {
        return (bool) Config::get('mail-allowlist.sending.log.include.middleware');
    }

    public function logHeaders(): bool
    {
        return (bool) Config::get('mail-allowlist.sending.log.include.headers');
    }

    public function logMessageData(): bool
    {
        return (bool) Config::get('mail-allowlist.sending.log.include.message_data');
    }

    public function logBody(): bool
    {
        return (bool) Config::get('mail-allowlist.sending.log.include.body');
    }
}
