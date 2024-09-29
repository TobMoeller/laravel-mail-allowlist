<?php

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\Listeners\MessageSendingListener;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\BccFilter;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\CcFilter;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\EnsureRecipients;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\ToFilter;

it('checks if the feature is enabled', function (bool $enabled) {
    Event::fake();
    Config::set('mail-allowlist.enabled', $enabled);

    expect(LaravelMailAllowlist::enabled())
        ->toBe($enabled);

    Event::assertListening(MessageSending::class, MessageSendingListener::class);
})->with([true, false]);

it('returns default mail middleware', function () {
    expect(LaravelMailAllowlist::mailMiddleware())
        ->toBe([
            ToFilter::class,
            CcFilter::class,
            BccFilter::class,
            EnsureRecipients::class,
        ]);
});
it('returns mail middleware', function (mixed $value, mixed $expected) {
    Config::set('mail-allowlist.middleware', $value);

    expect(LaravelMailAllowlist::mailMiddleware())
        ->toBe($expected);
})->with([
    [
        'value' => ['::class-string::', $class = new class {}],
        'expected' => ['::class-string::', $class],
    ],
    [
        'value' => [],
        'expected' => [],
    ],
    [
        'value' => null,
        'expected' => [],
    ],
    [
        'value' => false,
        'expected' => [],
    ],
]);

it('returns the allowed domain list', function (mixed $value, mixed $expected) {
    Config::set('mail-allowlist.allowed.domains', $value);

    expect(LaravelMailAllowlist::allowedDomainList())
        ->toBe($expected);
})->with([
    [
        'value' => ['foo.de', 'bar.de'],
        'expected' => ['foo.de', 'bar.de'],
    ],
    [
        'value' => 'foo.de;bar.de',
        'expected' => ['foo.de', 'bar.de'],
    ],
    [
        'value' => 'bar.de',
        'expected' => ['bar.de'],
    ],
    [
        'value' => null,
        'expected' => [],
    ],
]);

it('returns the allowed email list', function (mixed $value, mixed $expected) {
    Config::set('mail-allowlist.allowed.emails', $value);

    expect(LaravelMailAllowlist::allowedEmailList())
        ->toBe($expected);
})->with([
    [
        'value' => ['bar@foo.de', 'foo@bar.de'],
        'expected' => ['bar@foo.de', 'foo@bar.de'],
    ],
    [
        'value' => 'bar@foo.de;foo@bar.de',
        'expected' => ['bar@foo.de', 'foo@bar.de'],
    ],
    [
        'value' => 'foo@bar.de',
        'expected' => ['foo@bar.de'],
    ],
    [
        'value' => null,
        'expected' => [],
    ],
]);
