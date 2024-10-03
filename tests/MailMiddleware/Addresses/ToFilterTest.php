<?php

use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\Actions\Addresses\IsAllowedRecipient;
use TobMoeller\LaravelMailAllowlist\Enums\Header;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\ToFilter;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

it('creates an address filter for "to" with IsAllowedRecipient address checker', function () {
    $filter = app(ToFilter::class);

    expect($filter)
        ->header->toBe(Header::TO)
        ->addressChecker->toBeInstanceOf(IsAllowedRecipient::class);
});

it('creats a log entry', function () {
    $mail = new Email;
    $context = new MessageContext($mail);

    $filter = app(ToFilter::class);
    $filter->handle($context, fn () => null);

    expect($context->getLog()[0])
        ->toBe(ToFilter::class);
});
