<?php

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Config;
use Mockery\MockInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\Actions\FilterMessageRecipients;
use TobMoeller\LaravelMailAllowlist\Listeners\MessageSendingListener;

it('return false if disabled', function () {
    Config::set('mail-allowlist.enabled', false);

    $message = new Email();
    $event = new MessageSending($message);
    $listener = new MessageSendingListener();

    expect($listener->handle($event))
        ->toBeTrue();
});

it('filters recipients and returns false if "to" is empty', function () {
    Config::set('mail-allowlist.enabled', true);

    $message = new Email();
    $event = new MessageSending($message);
    $listener = new MessageSendingListener();

    $this->instance(
        FilterMessageRecipients::class,
        Mockery::mock(FilterMessageRecipients::class, function (MockInterface $mock) use ($message) {
            $mock->shouldReceive('filter')
                ->with($message)
                ->once();
        })
    );

    expect($listener->handle($event))->ToBeFalse();
});

it('filters recipients and returns true if "to" is filled', function () {
    Config::set('mail-allowlist.enabled', true);

    $message = new Email();
    $message->to(new Address('foo@bar.de'));
    $event = new MessageSending($message);
    $listener = new MessageSendingListener();

    $this->instance(
        FilterMessageRecipients::class,
        Mockery::mock(FilterMessageRecipients::class, function (MockInterface $mock) use ($message) {
            $mock->shouldReceive('filter')
                ->with($message)
                ->once();
        })
    );

    expect($listener->handle($event))->toBeTrue();
});
