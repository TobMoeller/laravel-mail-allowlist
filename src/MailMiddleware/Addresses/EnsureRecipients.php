<?php

namespace TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses;

use Closure;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MailMiddlewareContract;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

class EnsureRecipients implements MailMiddlewareContract
{
    public function handle(MessageContext $messageContext, Closure $next): mixed
    {
        if (! empty($messageContext->getMessage()->getTo())) {
            return $next($messageContext);
        }

        $messageContext->cancelSendingMessage('No recipients left.');
        return null;
    }
}
