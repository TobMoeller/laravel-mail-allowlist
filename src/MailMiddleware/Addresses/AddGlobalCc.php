<?php

namespace TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses;

use Closure;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MailMiddlewareContract;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

class AddGlobalCc implements MailMiddlewareContract
{
    public function handle(MessageContext $messageContext, Closure $next): mixed
    {
        if (! empty($cc = LaravelMailAllowlist::globalCcEmailList())) {
            $messageContext->getMessage()->addCc(...$cc);
        }

        return $next($messageContext);
    }
}
