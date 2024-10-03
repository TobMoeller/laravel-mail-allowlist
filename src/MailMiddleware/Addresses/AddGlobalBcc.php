<?php

namespace TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses;

use Closure;
use Illuminate\Support\Arr;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MailMiddlewareContract;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

class AddGlobalBcc implements MailMiddlewareContract
{
    public function handle(MessageContext $messageContext, Closure $next): mixed
    {
        if (! empty($bcc = LaravelMailAllowlist::globalBccEmailList())) {
            $messageContext->getMessage()->addBcc(...$bcc);

            $bccList = Arr::join($bcc, ';');
            $messageContext->addLog(static::class.PHP_EOL.'Added Global Bcc Recipients: '.$bccList);
        }

        return $next($messageContext);
    }
}
