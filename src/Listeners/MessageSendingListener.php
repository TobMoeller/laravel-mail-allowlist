<?php

namespace TobMoeller\LaravelMailAllowlist\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Pipeline;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

class MessageSendingListener
{
    public function handle(MessageSending $messageSendingEvent): bool
    {
        if (! LaravelMailAllowlist::enabled()) {
            return true;
        }

        $messageContext = app(MessageContext::class, ['message' => $messageSendingEvent->message]);

        Pipeline::send($messageContext)
            ->through(LaravelMailAllowlist::mailMiddleware())
            ->thenReturn();

        return $messageContext->shouldSendMessage();
    }
}
