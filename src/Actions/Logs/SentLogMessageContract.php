<?php

namespace TobMoeller\LaravelMailAllowlist\Actions\Logs;

use TobMoeller\LaravelMailAllowlist\MailSentMiddleware\SentMessageContext;

interface SentLogMessageContract
{
    public function log(SentMessageContext $messageContext): void;
}
