<?php

namespace TobMoeller\LaravelMailAllowlist\Actions\Logs;

use Illuminate\Support\Facades\Log;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\MailSentMiddleware\SentMessageContext;

class SentLogMessage implements SentLogMessageContract
{
    public function __construct(public GenerateSentLogMessageContract $generateLogMessage) {}

    public function log(SentMessageContext $messageContext): void
    {
        $message = $this->generateLogMessage->generate($messageContext);

        Log::channel(LaravelMailAllowlist::sentLogChannel())
            ->log(LaravelMailAllowlist::sentLogLevel(), $message);
    }
}
