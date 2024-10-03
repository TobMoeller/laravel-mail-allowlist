<?php

namespace TobMoeller\LaravelMailAllowlist\Actions\Logs;

use Illuminate\Support\Facades\Log;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

class LogMessage implements LogMessageContract
{
    public function __construct(public GenerateLogMessageContract $generateLogMessage) {}

    public function log(MessageContext $messageContext): void
    {
        $message = $this->generateLogMessage->generate($messageContext);

        Log::channel(LaravelMailAllowlist::logChannel())
            ->log(LaravelMailAllowlist::logLevel(), $message);
    }
}
