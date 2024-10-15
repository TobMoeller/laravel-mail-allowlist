<?php

namespace TobMoeller\LaravelMailAllowlist\MailSentMiddleware;

use Closure;

interface MailMiddlewareContract
{
    public function handle(SentMessageContext $messageContext, Closure $next): mixed;
}
