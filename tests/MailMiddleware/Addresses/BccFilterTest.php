<?php

use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\Actions\Addresses\IsAllowedRecipient;
use TobMoeller\LaravelMailAllowlist\Enums\Header;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\BccFilter;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

it('creates an address filter for "to" with IsAllowedRecipient address checker', function () {
    $filter = app(BccFilter::class);

    expect($filter)
        ->header->toBe(Header::BCC)
        ->addressChecker->toBeInstanceOf(IsAllowedRecipient::class);
});

it('creats a log entry', function () {
    $mail = new Email;
    $context = new MessageContext($mail);

    $filter = app(BccFilter::class);
    $filter->handle($context, fn () => null);

    expect($context->getLog()[0])
        ->toBe(BccFilter::class);
});
