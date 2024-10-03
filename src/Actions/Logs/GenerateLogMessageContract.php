<?php

namespace TobMoeller\LaravelMailAllowlist\Actions\Logs;

use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

interface GenerateLogMessageContract
{
    public function generate(MessageContext $messageContext): string;
}
