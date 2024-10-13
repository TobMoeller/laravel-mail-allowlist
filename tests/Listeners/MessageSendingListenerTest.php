<?php

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\Actions\Logs\LogMessage;
use TobMoeller\LaravelMailAllowlist\Listeners\MessageSendingListener;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

it('return true without running middleware if disabled', function () {
    Config::set('mail-allowlist.enabled', false);

    $loggerMock = Mockery::mock(LogMessage::class);
    $loggerMock->shouldNotReceive('log');

    $message = new Email;
    $event = new MessageSending($message);
    $listener = new MessageSendingListener($loggerMock);

    $mock = Mockery::mock(Pipeline::class);
    $mock->shouldNotReceive('send');
    $this->instance('pipeline', $mock);

    expect($listener->handle($event))
        ->toBeTrue();
});

it('runs the middleware pipelines and returns if the message should be sent', function (bool $shouldSendMessage, bool $shouldLog) {
    Config::set('mail-allowlist.enabled', true);
    Config::set('mail-allowlist.log.enabled', $shouldLog);
    Config::set('mail-allowlist.middleware_enabled', true);
    Config::set('mail-allowlist.middleware', $middleware = ['::middleware::']);

    $message = new Email;

    $loggerMock = Mockery::mock(LogMessage::class);
    if ($shouldLog) {
        $loggerMock->shouldReceive('log')
            ->once()
            ->with(Mockery::on(fn (MessageContext $context) => $context->getMessage() === $message));
    } else {
        $loggerMock->shouldNotReceive('log');
    }

    $messageData = ['test_meta' => '::test_meta::'];
    $event = new MessageSending($message, $messageData);
    $listener = new MessageSendingListener($loggerMock);

    $mock = Mockery::mock(Pipeline::class);
    $mock->shouldReceive('send')
        ->with(Mockery::on(function (MessageContext $messageContext) use ($message, $messageData, $shouldSendMessage) {
            if (! $shouldSendMessage) {
                $messageContext->cancelSendingMessage('::reason::');
            }

            return $message === $messageContext->getMessage() &&
                $messageData === $messageContext->getMessageData();
        }))
        ->once()
        ->andReturnSelf()
        ->shouldReceive('through')
        ->with($middleware)
        ->once()
        ->andReturnSelf()
        ->shouldReceive('thenReturn')
        ->once()
        ->andReturnSelf();

    $this->instance('pipeline', $mock);

    expect($listener->handle($event))->toBe($shouldSendMessage);
})->with([true, false], [true, false]);

it('does not run the middleware if disabled', function () {
    Config::set('mail-allowlist.enabled', true);
    Config::set('mail-allowlist.middleware_enabled', false);
    Config::set('mail-allowlist.middleware', ['::middleware::']);

    $loggerMock = Mockery::mock(LogMessage::class);
    $loggerMock->shouldNotReceive('log');

    $message = new Email;
    $event = new MessageSending($message);
    $listener = new MessageSendingListener($loggerMock);

    $mock = Mockery::mock(Pipeline::class);
    $mock->shouldNotReceive('send', 'through', 'andReturn');

    $this->instance('pipeline', $mock);

    $listener->handle($event);
});
