<?php

use Illuminate\Support\Collection;
use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

it('holds message information', function () {
    $message = new Email;
    $context = new MessageContext($message);

    expect($context->getMessage())
        ->toBe($message)
        ->and($context->shouldSendMessage())
        ->toBeTrue()
        ->and($context->getMessageData())
        ->toBeEmpty();

    $context->addLog('::log1::');
    $context->cancelSendingMessage('::reason::');
    $context->addLog('::log2::');

    expect($context->shouldSendMessage())
        ->toBeFalse()
        ->and($context->getLog())
        ->toMatchArray([
            '::log1::',
            'Message canceled: ::reason::',
            '::log2::',
        ]);
});

it('holds message data and returns the originating class name', function (array $className, mixed $expectation) {
    $message = new Email;
    $messageData = array_merge([
        'test_meta' => '::test_meta::',
    ], $className);
    $context = new MessageContext($message, $messageData);

    expect($context->getMessageData())
        ->toBe($messageData)
        ->and($context->getOriginatingClassName())
        ->toBe($expectation);
})->with([
    [
        'className' => ['__laravel_notification' => '::notification_name::'],
        'expectation' => '::notification_name::',
    ],
    [
        'className' => ['__laravel_mailable' => '::mailable_name::'],
        'expectation' => '::mailable_name::',
    ],
    [
        'className' => ['__laravel_notification' => false],
        'expectation' => null,
    ],
    [
        'className' => [],
        'expectation' => null,
    ],
]);

it('shares data for middleware', function () {
    $message = new Email;
    $context = new MessageContext($message);

    $context->sharedData->put('foo', 'bar');

    expect($context->sharedData)
        ->toBeInstanceOf(Collection::class)
        ->get('foo')->toBe('bar');
});
