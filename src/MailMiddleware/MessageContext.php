<?php

namespace TobMoeller\LaravelMailAllowlist\MailMiddleware;

use Symfony\Component\Mime\Email;

class MessageContext
{
    protected Email $message;
    protected bool $shouldSendMessage = true;
    /** @var array<int, string> */
    protected array $log = [];

    public function __construct(Email $message)
    {
        $this->message = $message;
    }

    public function getMessage(): Email
    {
        return $this->message;
    }

    public function cancelSendingMessage(string $reason): void
    {
        $this->shouldSendMessage = false;
        $this->addLog('Message canceled: ' . $reason);
    }

    public function shouldSendMessage(): bool
    {
        return $this->shouldSendMessage;
    }

    public function addLog(string $logMessage): void
    {
        $this->log[] = $logMessage;
    }

    /**
     * @return array <int, string>
     */
    public function getLog(): array
    {
        return $this->log;
    }
}
