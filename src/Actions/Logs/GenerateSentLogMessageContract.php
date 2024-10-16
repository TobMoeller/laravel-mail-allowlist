<?php

namespace TobMoeller\LaravelMailAllowlist\Actions\Logs;

use TobMoeller\LaravelMailAllowlist\MailSentMiddleware\SentMessageContext;

interface GenerateSentLogMessageContract
{
    public function generate(SentMessageContext $messageContext): string;
}
