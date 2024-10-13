<?php

namespace TobMoeller\LaravelMailAllowlist\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Pipeline;
use TobMoeller\LaravelMailAllowlist\Actions\Logs\LogMessageContract;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

class MessageSendingListener
{
    public function __construct(public LogMessageContract $messageLogger)
    {
        //
    }

    public function handle(MessageSending $messageSendingEvent): ?false
    {
        if (! LaravelMailAllowlist::enabled()) {
            return null;
        }

        $messageContext = app(MessageContext::class, [
            'message' => $messageSendingEvent->message,
            'messageData' => $messageSendingEvent->data,
        ]);

        if (LaravelMailAllowlist::mailMiddlewareEnabled()) {
            Pipeline::send($messageContext)
                ->through(LaravelMailAllowlist::mailMiddleware())
                ->thenReturn();
        }

        if (LaravelMailAllowlist::logEnabled()) {
            $this->messageLogger->log($messageContext);
        }

        // The `Mailer` dispatches the `MessageSending` event until
        // a non null value is returned by a listener. If false is
        // returned, the mail will be stopped entirely. If true is
        // returned, the mail will be sent, but the event will not
        // be dispatched again for other listeners registered
        // after this one.
        return $messageContext->shouldSendMessage() ? null : false;
    }
}
