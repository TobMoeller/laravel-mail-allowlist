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

    public function handle(MessageSending $messageSendingEvent): bool
    {
        if (! LaravelMailAllowlist::enabled()) {
            return true;
        }

        $messageContext = app(MessageContext::class, ['message' => $messageSendingEvent->message]);

        if (LaravelMailAllowlist::mailMiddlewareEnabled()) {
            Pipeline::send($messageContext)
                ->through(LaravelMailAllowlist::mailMiddleware())
                ->thenReturn();
        }

        if (LaravelMailAllowlist::logEnabled()) {
            $this->messageLogger->log($messageContext);
        }

        return $messageContext->shouldSendMessage();
    }
}
