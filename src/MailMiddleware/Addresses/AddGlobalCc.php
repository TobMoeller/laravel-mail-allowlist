<?php

namespace TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses;

use Closure;
use Illuminate\Support\Arr;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MailMiddlewareContract;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

class AddGlobalCc implements MailMiddlewareContract
{
    public function handle(MessageContext $messageContext, Closure $next): mixed
    {
        if (! empty($cc = LaravelMailAllowlist::globalCcEmailList())) {
            $messageContext->getMessage()->addCc(...$cc);

            $ccList = Arr::join($cc, ';');
            $messageContext->addLog(static::class.PHP_EOL.'Added Global Cc Recipients: '.$ccList);
        }

        return $next($messageContext);
    }
}
