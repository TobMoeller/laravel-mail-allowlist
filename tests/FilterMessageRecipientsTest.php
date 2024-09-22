<?php

use Illuminate\Support\Facades\Config;
use Pest\Expectation;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\Actions\FilterMessageRecipients;

it('filters a mail', function () {
    Config::set('mail-allowlist.allowed.domains', ['foo.de', 'bar.de']);
    Config::set('mail-allowlist.allowed.emails', ['bar@foo.com', 'foo@bar.com']);

    $allowed = [
        new Address('allowed@foo.de'),
        new Address('allowed@bar.de'),
        new Address('bar@foo.com'),
        new Address('foo@bar.com'),
    ];
    $denied = [new Address('denied@foobar.de')]; // no matching domain or email

    $mail = new Email;
    $mail->to(...array_merge(
        $allowed,
        $denied,
    ));
    $mail->cc(...array_merge(
        $allowed,
        $denied,
    ));
    $mail->bcc(...array_merge(
        $allowed,
        $denied,
    ));

    app(FilterMessageRecipients::class)->filter($mail);

    expect($mail->getTo())
        ->toMatchArray($allowed)
        ->each(fn (Expectation $address) => $address->getAddress() !== 'denied@foobar.de')
        ->and($mail->getCc())
        ->toMatchArray($allowed)
        ->each(fn (Expectation $address) => $address->getAddress() !== 'denied@foobar.de')
        ->and($mail->getBcc())
        ->toMatchArray($allowed)
        ->each(fn (Expectation $address) => $address->getAddress() !== 'denied@foobar.de');
});

it('leaves empty recipients for unset filters', function () {
    $address = new Address('allowed@foo.de');

    $mail = new Email;
    $mail->to($address);
    $mail->cc($address);
    $mail->bcc($address);

    app(FilterMessageRecipients::class)->filter($mail);

    expect($mail->getTo())
        ->toBeEmpty()
        ->and($mail->getCc())
        ->toBeEmpty()
        ->and($mail->getBcc())
        ->toBeEmpty()
        ->and($mail->getHeaders())
        ->has('to')->toBeFalse()
        ->has('cc')->toBeFalse()
        ->has('bcc')->toBeFalse();
});
