<?php

use Illuminate\Support\Facades\Config;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\AddGlobalTo;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

it('adds global to addresses and continues the pipeline', function () {
    Config::set('mail-allowlist.sending.middleware.global.to', ['foo@bar.com', 'bar@foo.com']);
    $mail = new Email;
    $context = new MessageContext($mail);

    $middlewareReturn = (new AddGlobalTo)->handle($context, fn () => '::next_response::');

    $to = $mail->getTo();
    expect($to)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Address::class)
        ->and($to[0])
        ->getAddress()->toBe('foo@bar.com')
        ->and($to[1])
        ->getAddress()->toBe('bar@foo.com')
        ->and($middlewareReturn)
        ->toBe('::next_response::')
        ->and($context->getLog()[0])
        ->toBe(AddGlobalTo::class.PHP_EOL.'Added Global To Recipients: foo@bar.com;bar@foo.com');
});

it('does not add an address if config is empty and continues the pipeline', function () {
    Config::set('mail-allowlist.sending.middleware.global.to', []);
    $mail = new Email;
    $context = new MessageContext($mail);

    $middlewareReturn = (new AddGlobalTo)->handle($context, fn () => '::next_response::');

    $to = $mail->getTo();
    expect($to)
        ->toBeEmpty()
        ->and($middlewareReturn)
        ->toBe('::next_response::')
        ->and($context->getLog())
        ->toBeEmpty();
});
