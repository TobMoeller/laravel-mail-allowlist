<?php

use Illuminate\Support\Facades\Config;
use Symfony\Component\Mime\Address;
use TobMoeller\LaravelMailAllowlist\Actions\Addresses\IsAllowedRecipient;

it('gets initialized with allowed domain and email lists', function () {
    Config::set('mail-allowlist.sending.allowed.domains', $allowedDomains = ['foo.de', 'bar.de']);
    Config::set('mail-allowlist.sending.allowed.emails', $allowedEmails = ['bar@foo.de', 'foo@bar.de']);

    $action = app(IsAllowedRecipient::class);

    expect($action)
        ->allowedDomains->toMatchArray($allowedDomains)
        ->allowedEmails->toMatchArray($allowedEmails);
});

it('checks if the recipient has an allowed domain', function () {
    Config::set('mail-allowlist.sending.allowed.domains', ['bar.de']);

    $allowedAddress = new Address('foo@bar.de');
    $deniedAddress = new Address('bar@foo.de');

    $action = app(IsAllowedRecipient::class);

    expect($action->check($allowedAddress))->toBeTrue()
        ->and($action->check($deniedAddress))->toBeFalse();
});

it('checks if the recipient has an allowed email', function () {
    Config::set('mail-allowlist.sending.allowed.emails', ['foo@bar.de']);

    $allowedAddress = new Address('foo@bar.de');
    $deniedAddress = new Address('bar@foo.de');

    $action = app(IsAllowedRecipient::class);

    expect($action->check($allowedAddress))->toBeTrue()
        ->and($action->check($deniedAddress))->toBeFalse();
});

it('denies if no allowed domains or emails are set', function () {
    $deniedAddress = new Address('bar@foo.de');

    $action = app(IsAllowedRecipient::class);

    expect($action->check($deniedAddress))->toBeFalse();
});
