<?php

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\Actions\Logs\SentLogMessage;
use TobMoeller\LaravelMailAllowlist\Listeners\MessageSentListener;
use TobMoeller\LaravelMailAllowlist\MailSentMiddleware\SentMessageContext;

beforeEach(function () {
    $this->message = new Email;
    $this->sentMessage = generateSentMessage($this->message);
});

it('return null without running middleware if disabled', function () {
    Config::set('mail-allowlist.enabled', false);

    $loggerMock = Mockery::mock(SentLogMessage::class);
    $loggerMock->shouldNotReceive('log');

    $event = new MessageSent($this->sentMessage);
    $listener = new MessageSentListener($loggerMock);

    $mock = Mockery::mock(Pipeline::class);
    $mock->shouldNotReceive('send');
    $this->instance('pipeline', $mock);

    $listener->handle($event);
});

it('runs the middleware pipelines and returns if the message should be sent', function (bool $shouldLog) {
    Config::set('mail-allowlist.enabled', true);
    Config::set('mail-allowlist.sent.log.enabled', $shouldLog);
    Config::set('mail-allowlist.sent.middleware.enabled', true);
    Config::set('mail-allowlist.sent.middleware.pipeline', $middleware = ['::middleware::']);

    $message = $this->message;
    $sentMessage = $this->sentMessage;

    $loggerMock = Mockery::mock(SentLogMessage::class);
    if ($shouldLog) {
        $loggerMock->shouldReceive('log')
            ->once()
            ->with(Mockery::on(fn (SentMessageContext $context) => $context->getMessage() === $message));
    } else {
        $loggerMock->shouldNotReceive('log');
    }

    $messageData = ['test_meta' => '::test_meta::'];
    $event = new MessageSent($this->sentMessage, $messageData);
    $listener = new MessageSentListener($loggerMock);

    $mock = Mockery::mock(Pipeline::class);
    $mock->shouldReceive('send')
        ->with(Mockery::on(function (SentMessageContext $messageContext) use ($message, $sentMessage, $messageData) {
            return $message === $messageContext->getMessage() &&
                $sentMessage === $messageContext->getSentMessage() &&
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

    $listener->handle($event);
})->with([true, false]);

it('does not run the middleware if disabled', function () {
    Config::set('mail-allowlist.enabled', true);
    Config::set('mail-allowlist.sent.middleware.enabled', false);
    Config::set('mail-allowlist.sent.middleware.pipeline', ['::middleware::']);

    $loggerMock = Mockery::mock(SentLogMessage::class);
    $loggerMock->shouldNotReceive('log');

    $event = new MessageSent($this->sentMessage);
    $listener = new MessageSentListener($loggerMock);

    $mock = Mockery::mock(Pipeline::class);
    $mock->shouldNotReceive('send', 'through', 'andReturn');

    $this->instance('pipeline', $mock);

    $listener->handle($event);
});
