<?php

namespace TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses;

use Closure;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MailMiddlewareContract;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

class AddGlobalBcc implements MailMiddlewareContract
{
    public function handle(MessageContext $messageContext, Closure $next): mixed
    {
        if (! empty($bcc = LaravelMailAllowlist::globalBccEmailList())) {
            $messageContext->getMessage()->addBcc(...$bcc);
        }

        return $next($messageContext);
    }
}
