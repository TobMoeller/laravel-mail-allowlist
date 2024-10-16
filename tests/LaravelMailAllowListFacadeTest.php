<?php

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Psr\Log\LogLevel;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\Listeners\MessageSendingListener;
use TobMoeller\LaravelMailAllowlist\Listeners\MessageSentListener;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\AddGlobalBcc;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\AddGlobalCc;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\AddGlobalTo;
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
    Event::assertListening(MessageSent::class, MessageSentListener::class);
})->with([true, false]);

it('returns default mail middleware', function () {
    expect(LaravelMailAllowlist::mailMiddleware())
        ->toBe([
            ToFilter::class,
            CcFilter::class,
            BccFilter::class,
            AddGlobalTo::class,
            AddGlobalCc::class,
            AddGlobalBcc::class,
            EnsureRecipients::class,
        ]);
});

it('returns mail middleware', function (string $event, mixed $value, mixed $expected) {
    Config::set('mail-allowlist.'.$event.'.middleware.pipeline', $value);

    $middleware = $event === 'sending' ?
        LaravelMailAllowlist::mailMiddleware() :
        LaravelMailAllowlist::sentMailMiddleware();
    expect($middleware)
        ->toBe($expected);
})->with([
    'sending',
    'sent',
], [
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
    Config::set('mail-allowlist.sending.middleware.allowed.domains', $value);

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
    Config::set('mail-allowlist.sending.middleware.allowed.emails', $value);

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

it('returns the global to/cc/bcc email lists', function (mixed $value, mixed $expected) {
    Config::set('mail-allowlist.sending.middleware.global.to', $value);
    Config::set('mail-allowlist.sending.middleware.global.cc', $value);
    Config::set('mail-allowlist.sending.middleware.global.bcc', $value);

    expect(LaravelMailAllowlist::globalToEmailList())
        ->toBe($expected)
        ->and(LaravelMailAllowlist::globalCcEmailList())
        ->toBe($expected)
        ->and(LaravelMailAllowlist::globalBccEmailList())
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

it('returns if logging is enabled', function (bool $enabled) {
    Config::set('mail-allowlist.sending.log.enabled', $enabled);

    expect(LaravelMailAllowlist::logEnabled())
        ->toBe($enabled);
})->with([true, false]);

it('returns if sent logging is enabled', function (?bool $enabled) {
    Config::set('mail-allowlist.sending.log.enabled', true);
    Config::set('mail-allowlist.sent.log.enabled', $enabled);

    expect(LaravelMailAllowlist::sentLogEnabled())
        ->toBe($enabled === null ? true : $enabled);
})->with([true, false, null]);

it('returns the log channel', function (mixed $value, mixed $default, string $expected) {
    Config::set('logging.default', $default);
    Config::set('mail-allowlist.sending.log.channel', $value);

    expect(LaravelMailAllowlist::logChannel())
        ->toBe($expected);
})->with([
    [
        'value' => '::channel::',
        'default' => null,
        'expected' => '::channel::',
    ],
    [
        'value' => null,
        'default' => '::default::',
        'expected' => '::default::',
    ],
    [
        'value' => null,
        'default' => null,
        'expected' => 'stack',
    ],
    [
        'value' => false,
        'default' => '::default::',
        'expected' => '::default::',
    ],
    [
        'value' => false,
        'default' => false,
        'expected' => 'stack',
    ],
]);

it('returns the sent log channel', function (mixed $value, mixed $default, string $expected) {
    Config::set('mail-allowlist.sending.log.channel', $default);
    Config::set('mail-allowlist.sent.log.channel', $value);

    expect(LaravelMailAllowlist::sentLogChannel())
        ->toBe($expected);
})->with([
    [
        'value' => '::channel::',
        'default' => null,
        'expected' => '::channel::',
    ],
    [
        'value' => null,
        'default' => '::default::',
        'expected' => '::default::',
    ],
    [
        'value' => null,
        'default' => null,
        'expected' => 'stack',
    ],
    [
        'value' => false,
        'default' => '::default::',
        'expected' => '::default::',
    ],
    [
        'value' => false,
        'default' => false,
        'expected' => 'stack',
    ],
]);

it('returns the log level', function (string $level) {
    Config::set('mail-allowlist.sending.log.level', $level);

    expect(LaravelMailAllowlist::logLevel())
        ->toBe($level);
})->with(fn () => array_values((new ReflectionClass(LogLevel::class))->getConstants()));

it('returns the sent log level', function (?string $level) {
    Config::set('mail-allowlist.sending.log.level', LogLevel::INFO);
    Config::set('mail-allowlist.sent.log.level', $level);

    expect(LaravelMailAllowlist::sentLogLevel())
        ->toBe($level === null ? LogLevel::INFO : $level);
})->with(fn () => array_merge(array_values((new ReflectionClass(LogLevel::class))->getConstants()), [null]));

it('throws on invalid log levels', function (mixed $level) {
    Config::set('mail-allowlist.sending.log.level', $level);

    expect(fn () => LaravelMailAllowlist::logLevel())
        ->toThrow(InvalidArgumentException::class, 'Invalid log level provided');
})->with([
    '::invalid_level::',
    null,
]);

it('throws on invalid sent log levels', function () {
    Config::set('mail-allowlist.sent.log.level', '::invalid_level::');

    expect(fn () => LaravelMailAllowlist::sentLogLevel())
        ->toThrow(InvalidArgumentException::class, 'Invalid log level provided');
});

it('returns if middleware should be logged', function (bool $enabled) {
    Config::set('mail-allowlist.sending.log.include.middleware', $enabled);

    expect(LaravelMailAllowlist::logMiddleware())
        ->toBe($enabled);
})->with([true, false]);

it('returns if headers should be logged', function (bool $enabled) {
    Config::set('mail-allowlist.sending.log.include.headers', $enabled);

    expect(LaravelMailAllowlist::logHeaders())
        ->toBe($enabled);
})->with([true, false]);

it('returns if body should be logged', function (bool $enabled) {
    Config::set('mail-allowlist.sending.log.include.body', $enabled);

    expect(LaravelMailAllowlist::logBody())
        ->toBe($enabled);
})->with([true, false]);

it('returns if sent middleware should be logged', function (bool $enabled) {
    Config::set('mail-allowlist.sent.log.include.middleware', $enabled);

    expect(LaravelMailAllowlist::sentLogMiddleware())
        ->toBe($enabled);
})->with([true, false]);

it('returns if sent headers should be logged', function (bool $enabled) {
    Config::set('mail-allowlist.sent.log.include.headers', $enabled);

    expect(LaravelMailAllowlist::sentLogHeaders())
        ->toBe($enabled);
})->with([true, false]);

it('returns if sent body should be logged', function (bool $enabled) {
    Config::set('mail-allowlist.sent.log.include.body', $enabled);

    expect(LaravelMailAllowlist::sentLogBody())
        ->toBe($enabled);
})->with([true, false]);
