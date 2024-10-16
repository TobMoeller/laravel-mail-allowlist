<?php

namespace TobMoeller\LaravelMailAllowlist\MailSentMiddleware;

use Illuminate\Mail\SentMessage;
use Illuminate\Support\Collection;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;
use TobMoeller\LaravelMailAllowlist\Traits\MailMiddleware\HandlesMessageData;
use TobMoeller\LaravelMailAllowlist\Traits\MailMiddleware\LogsMessages;

class SentMessageContext
{
    use HandlesMessageData;
    use LogsMessages;

    /**
     * Collection for middleware to share temporary data
     * with other middleware further down the pipeline.
     *
     * @var Collection<string, mixed>
     */
    public Collection $sharedData;

    protected SentMessage $sentMessage;

    /**
     * @param  array<string, mixed>  $messageData
     */
    public function __construct(SentMessage $sentMessage, array $messageData = [])
    {
        $this->sentMessage = $sentMessage;
        $this->messageData = $messageData;
        $this->sharedData = new Collection;
    }

    public function getMessage(): Email|RawMessage
    {
        return $this->sentMessage->getOriginalMessage();
    }

    public function getSentMessage(): SentMessage
    {
        return $this->sentMessage;
    }

    public function getDebugInformation(): string
    {
        return $this->getSentMessage()->getDebug();
    }
}
