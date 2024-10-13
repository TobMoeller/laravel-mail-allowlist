<?php

namespace TobMoeller\LaravelMailAllowlist\MailMiddleware;

use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\Traits\MailMiddleware\LogsMessages;

class MessageContext
{
    use LogsMessages;

    protected Email $message;

    protected bool $shouldSendMessage = true;

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
        $this->addLog('Message canceled: '.$reason);
    }

    public function shouldSendMessage(): bool
    {
        return $this->shouldSendMessage;
    }
}
