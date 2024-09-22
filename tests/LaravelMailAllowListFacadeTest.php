<?php

use Illuminate\Support\Facades\Config;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;

it('checks if the feature is enabled', function (bool $enabled) {
    Config::set('mail-allowlist.enabled', $enabled);

    expect(LaravelMailAllowlist::enabled())
        ->toBe($enabled);
})->with([true, false]);

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
