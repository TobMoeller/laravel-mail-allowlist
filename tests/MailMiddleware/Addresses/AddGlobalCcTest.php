<?php

use Illuminate\Support\Facades\Config;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\AddGlobalCc;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

it('adds global cc addresses and continues the pipeline', function () {
    Config::set('mail-allowlist.sending.middleware.global.cc', ['foo@bar.com', 'bar@foo.com']);
    $mail = new Email;
    $context = new MessageContext($mail);

    $middlewareReturn = (new AddGlobalCc)->handle($context, fn () => '::next_response::');

    $addresses = $mail->getCc();
    expect($addresses)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Address::class)
        ->and($addresses[0])
        ->getAddress()->toBe('foo@bar.com')
        ->and($addresses[1])
        ->getAddress()->toBe('bar@foo.com')
        ->and($middlewareReturn)
        ->toBe('::next_response::')
        ->and($context->getLog()[0])
        ->toBe(AddGlobalCc::class.PHP_EOL.'Added Global Cc Recipients: foo@bar.com;bar@foo.com');
});

it('does not add an address if config is empty and continues the pipeline', function () {
    Config::set('mail-allowlist.sending.middleware.global.cc', []);
    $mail = new Email;
    $context = new MessageContext($mail);

    $middlewareReturn = (new AddGlobalCc)->handle($context, fn () => '::next_response::');

    $addresses = $mail->getCc();
    expect($addresses)
        ->toBeEmpty()
        ->and($middlewareReturn)
        ->toBe('::next_response::')
        ->and($context->getLog())
        ->toBeEmpty();
});
