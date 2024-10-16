<?php

namespace TobMoeller\LaravelMailAllowlist\MailSentMiddleware;

use Closure;

interface MailSentMiddlewareContract
{
    public function handle(SentMessageContext $messageContext, Closure $next): mixed;
}
