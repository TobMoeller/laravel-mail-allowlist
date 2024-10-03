<?php

namespace TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses;

use Closure;
use Illuminate\Support\Arr;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MailMiddlewareContract;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

class AddGlobalTo implements MailMiddlewareContract
{
    public function handle(MessageContext $messageContext, Closure $next): mixed
    {
        if (! empty($to = LaravelMailAllowlist::globalToEmailList())) {
            $messageContext->getMessage()->addTo(...$to);

            $toList = Arr::join($to, ';');
            $messageContext->addLog(static::class.PHP_EOL.'Added Global To Recipients: '.$toList);
        }

        return $next($messageContext);
    }
}
