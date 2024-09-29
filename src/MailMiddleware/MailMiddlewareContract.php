<?php

namespace TobMoeller\LaravelMailAllowlist\MailMiddleware;

use Closure;
use TobMoeller\LaravelMailAllowlist\MessageContext;

interface MailMiddlewareContract
{
    public function handle(MessageContext $messageContext, Closure $next): mixed;
}

