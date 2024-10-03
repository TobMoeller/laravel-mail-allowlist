<?php

namespace TobMoeller\LaravelMailAllowlist\Actions\Logs;

use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

interface LogMessageContract
{
    public function log(MessageContext $messageContext): void;
}
