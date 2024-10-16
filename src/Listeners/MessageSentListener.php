<?php

namespace TobMoeller\LaravelMailAllowlist\Listeners;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Pipeline;
use TobMoeller\LaravelMailAllowlist\Actions\Logs\SentLogMessageContract;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\MailSentMiddleware\SentMessageContext;

class MessageSentListener
{
    public function __construct(public SentLogMessageContract $messageLogger)
    {
        //
    }

    public function handle(MessageSent $messageSent): void
    {
        if (! LaravelMailAllowlist::enabled()) {
            return;
        }

        $messageContext = app(SentMessageContext::class, [
            'sentMessage' => $messageSent->sent,
            'messageData' => $messageSent->data,
        ]);

        if (LaravelMailAllowlist::sentMailMiddlewareEnabled()) {
            Pipeline::send($messageContext)
                ->through(LaravelMailAllowlist::sentMailMiddleware())
                ->thenReturn();
        }

        if (LaravelMailAllowlist::sentLogEnabled()) {
            $this->messageLogger->log($messageContext);
        }
    }
}
