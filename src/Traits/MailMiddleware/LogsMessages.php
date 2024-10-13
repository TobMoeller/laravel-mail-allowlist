<?php

namespace TobMoeller\LaravelMailAllowlist\Traits\MailMiddleware;

trait LogsMessages
{
    /** @var array<int, string> */
    protected array $log = [];

    public function addLog(string $logMessage): void
    {
        $this->log[] = $logMessage;
    }

    /**
     * @return array<int, string>
     */
    public function getLog(): array
    {
        return $this->log;
    }
}
