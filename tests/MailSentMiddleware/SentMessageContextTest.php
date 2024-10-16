<?php

use Illuminate\Support\Collection;
use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\MailSentMiddleware\SentMessageContext;

beforeEach(function () {
    $this->message = new Email;
    $this->sentMessage = generateSentMessage($this->message);
    $this->context = new SentMessageContext($this->sentMessage);
});

it('holds message information', function () {

    expect($this->context->getMessage())
        ->toBe($this->message)
        ->and($this->context->getSentMessage())
        ->toBe($this->sentMessage)
        ->and($this->context->getMessageData())
        ->toBeEmpty();

    $this->context->addLog('::log1::');
    $this->context->addLog('::log2::');

    expect($this->context->getDebugInformation())
        ->toBe('::debug::')
        ->and($this->context->getLog())
        ->toMatchArray([
            '::log1::',
            '::log2::',
        ]);
});

it('holds message data and returns the originating class name', function (array $className, mixed $expectation) {
    $messageData = array_merge([
        'test_meta' => '::test_meta::',
    ], $className);
    $this->context = new SentMessageContext($this->sentMessage, $messageData);

    expect($this->context->getMessageData())
        ->toBe($messageData)
        ->and($this->context->getOriginatingClassName())
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
    $this->context->sharedData->put('foo', 'bar');

    expect($this->context->sharedData)
        ->toBeInstanceOf(Collection::class)
        ->get('foo')->toBe('bar');
});
