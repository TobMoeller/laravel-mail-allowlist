<?php

use Illuminate\Support\Facades\Config;
use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\Actions\Logs\GenerateLogMessage;
use TobMoeller\LaravelMailAllowlist\Actions\Logs\GenerateLogMessageContract;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

beforeEach(function () {
    $this->mail = new Email;
    $this->mail->to('foo@bar.de'); // to header
    $this->mail->text('::body::'); // text body

    $this->context = new MessageContext($this->mail);
    $this->context->addLog('::middleware_log::');
    $this->context->addLog('::middleware_log2::');

    $this->logger = new GenerateLogMessage;
});

it('is bound to interface', function () {
    expect(app(GenerateLogMessageContract::class))
        ->toBeInstanceOf(GenerateLogMessage::class);
});

it('generates a log message', function () {
    Config::set('mail-allowlist.log.include.middleware', false);
    Config::set('mail-allowlist.log.include.headers', false);
    Config::set('mail-allowlist.log.include.body', false);

    expect($this->logger->generate($this->context))
        ->toBe('LaravelMailAllowlist.MessageSending:');
});

it('generates a log message with middleware', function () {
    Config::set('mail-allowlist.log.include.middleware', true);
    Config::set('mail-allowlist.log.include.headers', false);
    Config::set('mail-allowlist.log.include.body', false);

    $expectation = <<<'LOG_MESSAGE'
    LaravelMailAllowlist.MessageSending:
    ----------
    MIDDLEWARE
    ----------
    ::middleware_log::
    ::middleware_log2::
    LOG_MESSAGE;

    expect($this->logger->generate($this->context))
        ->toBe($expectation);
});

it('generates a log message with headers', function () {
    Config::set('mail-allowlist.log.include.middleware', false);
    Config::set('mail-allowlist.log.include.headers', true);
    Config::set('mail-allowlist.log.include.body', false);

    $expectation = <<<'LOG_MESSAGE'
    LaravelMailAllowlist.MessageSending:
    ----------
    HEADERS
    ----------
    LOG_MESSAGE;
    $expectation .= PHP_EOL.$this->mail->getHeaders()->toString();

    expect($this->logger->generate($this->context))
        ->toBe($expectation);
});

it('generates a log message with body', function () {
    Config::set('mail-allowlist.log.include.middleware', false);
    Config::set('mail-allowlist.log.include.headers', false);
    Config::set('mail-allowlist.log.include.body', true);

    $expectation = <<<'LOG_MESSAGE'
    LaravelMailAllowlist.MessageSending:
    ----------
    BODY
    ----------
    LOG_MESSAGE;
    $expectation .= PHP_EOL.$this->mail->getBody()->toString();

    expect($this->logger->generate($this->context))
        ->toBe($expectation);
});

it('generates a log message with all options enabled', function () {
    Config::set('mail-allowlist.log.include.middleware', true);
    Config::set('mail-allowlist.log.include.headers', true);
    Config::set('mail-allowlist.log.include.body', true);

    $headers = $this->mail->getHeaders()->toString();
    $body = $this->mail->getBody()->toString();

    $expectation = <<<LOG_MESSAGE
    LaravelMailAllowlist.MessageSending:
    ----------
    MIDDLEWARE
    ----------
    ::middleware_log::
    ::middleware_log2::
    ----------
    HEADERS
    ----------
    {$headers}
    ----------
    BODY
    ----------
    {$body}
    LOG_MESSAGE;

    expect($this->logger->generate($this->context))
        ->toBe($expectation);
});
