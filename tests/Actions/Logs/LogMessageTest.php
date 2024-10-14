<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Psr\Log\LogLevel;
use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\Actions\Logs\GenerateLogMessageContract;
use TobMoeller\LaravelMailAllowlist\Actions\Logs\LogMessage;
use TobMoeller\LaravelMailAllowlist\Actions\Logs\LogMessageContract;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

it('is bound to interface', function () {
    expect(app(LogMessageContract::class))
        ->toBeInstanceOf(LogMessage::class);
});

it('logs the message context', function () {
    Config::set('mail-allowlist.sending.log.channel', '::channel::');
    Config::set('mail-allowlist.sending.log.level', LogLevel::INFO);

    $mail = new Email;
    $context = new MessageContext($mail);

    $messageGeneratorMock = Mockery::mock(GenerateLogMessageContract::class);
    $messageGeneratorMock->shouldReceive('generate')
        ->once()
        ->with(Mockery::on(fn (MessageContext $contextArgument) => $contextArgument === $context))
        ->andReturn('::log_message::');

    Log::shouldReceive('channel')
        ->once()
        ->with('::channel::')
        ->andReturnSelf()
        ->shouldReceive('log')
        ->once()
        ->with(LogLevel::INFO, '::log_message::');

    $logger = new LogMessage($messageGeneratorMock);
    $logger->log($context);
});
