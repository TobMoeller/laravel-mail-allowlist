<?php

namespace TobMoeller\LaravelMailAllowlist\Listeners;

use Illuminate\Mail\Events\MessageSending;
use TobMoeller\LaravelMailAllowlist\Actions\FilterMessageRecipients;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;

class MessageSendingListener
{
    public function handle(MessageSending $messageSendingEvent): bool
    {
        if (! LaravelMailAllowlist::enabled()) {
            return true;
        }

        $message = $messageSendingEvent->message;

        app(FilterMessageRecipients::class)->filter($message);

        if (empty($message->getTo())) {
            return false;
        }

        return true;
    }
}
