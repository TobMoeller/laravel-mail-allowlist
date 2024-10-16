<?php

use Illuminate\Support\Facades\Config;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;
use TobMoeller\LaravelMailAllowlist\Actions\Logs\GenerateSentLogMessage;
use TobMoeller\LaravelMailAllowlist\Actions\Logs\GenerateSentLogMessageContract;
use TobMoeller\LaravelMailAllowlist\MailSentMiddleware\SentMessageContext;

beforeEach(function () {
    $this->mail = new Email;
    $this->sentMail = generateSentMessage($this->mail);

    $this->messageData = [
        'test_meta' => '::test_meta::',
        '__laravel_notification' => '::notification_name::',
    ];

    $this->context = new SentMessageContext($this->sentMail, $this->messageData);
    $this->context->addLog('::middleware_log::');
    $this->context->addLog('::middleware_log2::');

    $this->logger = new GenerateSentLogMessage;
});

it('is bound to interface', function () {
    expect(app(GenerateSentLogMessageContract::class))
        ->toBeInstanceOf(GenerateSentLogMessage::class);
});

it('generates a log message', function () {
    Config::set('mail-allowlist.sent.log.include.middleware', false);
    Config::set('mail-allowlist.sent.log.include.headers', false);
    Config::set('mail-allowlist.sent.log.include.message_data', false);
    Config::set('mail-allowlist.sent.log.include.body', false);
    Config::set('mail-allowlist.sent.log.include.debug', false);

    $expectation = 'LaravelMailAllowlist.MessageSent:';
    $expectation .= PHP_EOL.'ClassName: ::notification_name::';

    expect($this->logger->generate($this->context))
        ->toBe($expectation);
});

it('generates a log message with middleware', function () {
    Config::set('mail-allowlist.sent.log.include.middleware', true);
    Config::set('mail-allowlist.sent.log.include.headers', false);
    Config::set('mail-allowlist.sent.log.include.message_data', false);
    Config::set('mail-allowlist.sent.log.include.body', false);
    Config::set('mail-allowlist.sent.log.include.debug', false);

    $expectation = <<<'LOG_MESSAGE'
    LaravelMailAllowlist.MessageSent:
    ClassName: ::notification_name::
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
    Config::set('mail-allowlist.sent.log.include.middleware', false);
    Config::set('mail-allowlist.sent.log.include.headers', true);
    Config::set('mail-allowlist.sent.log.include.message_data', false);
    Config::set('mail-allowlist.sent.log.include.body', false);
    Config::set('mail-allowlist.sent.log.include.debug', false);

    $expectation = <<<'LOG_MESSAGE'
    LaravelMailAllowlist.MessageSent:
    ClassName: ::notification_name::
    ----------
    HEADERS
    ----------
    LOG_MESSAGE;
    $expectation .= PHP_EOL.$this->mail->getHeaders()->toString();

    expect($this->logger->generate($this->context))
        ->toBe($expectation);
});

it('generates a log message with body', function () {
    Config::set('mail-allowlist.sent.log.include.middleware', false);
    Config::set('mail-allowlist.sent.log.include.headers', false);
    Config::set('mail-allowlist.sent.log.include.message_data', false);
    Config::set('mail-allowlist.sent.log.include.body', true);
    Config::set('mail-allowlist.sent.log.include.debug', false);

    $expectation = <<<'LOG_MESSAGE'
    LaravelMailAllowlist.MessageSent:
    ClassName: ::notification_name::
    ----------
    BODY
    ----------
    LOG_MESSAGE;
    $expectation .= PHP_EOL.$this->mail->getBody()->toString();

    expect($this->logger->generate($this->context))
        ->toBe($expectation);
});

it('generates a log message with message data', function () {
    Config::set('mail-allowlist.sent.log.include.middleware', false);
    Config::set('mail-allowlist.sent.log.include.headers', false);
    Config::set('mail-allowlist.sent.log.include.message_data', true);
    Config::set('mail-allowlist.sent.log.include.body', false);
    Config::set('mail-allowlist.sent.log.include.debug', false);

    $expectation = <<<'LOG_MESSAGE'
    LaravelMailAllowlist.MessageSent:
    ClassName: ::notification_name::
    ----------
    DATA
    ----------
    LOG_MESSAGE;
    $expectation .= PHP_EOL.json_encode($this->messageData);

    expect($this->logger->generate($this->context))
        ->toBe($expectation);
});

it('generates a log message with all options enabled', function () {
    Config::set('mail-allowlist.sent.log.include.middleware', true);
    Config::set('mail-allowlist.sent.log.include.headers', true);
    Config::set('mail-allowlist.sent.log.include.message_data', true);
    Config::set('mail-allowlist.sent.log.include.body', true);
    Config::set('mail-allowlist.sent.log.include.debug', true);

    $headers = $this->mail->getHeaders()->toString();
    $body = $this->mail->getBody()->toString();
    $data = json_encode($this->messageData);

    $expectation = <<<LOG_MESSAGE
    LaravelMailAllowlist.MessageSent:
    ClassName: ::notification_name::
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
    DATA
    ----------
    {$data}
    ----------
    DEBUG
    ----------
    ::debug::
    ----------
    BODY
    ----------
    {$body}
    LOG_MESSAGE;

    expect($this->logger->generate($this->context))
        ->toBe($expectation);
});

it('generates a log message for raw messages', function () {
    Config::set('mail-allowlist.sent.log.include.middleware', false);
    Config::set('mail-allowlist.sent.log.include.headers', true);
    Config::set('mail-allowlist.sent.log.include.message_data', false);
    Config::set('mail-allowlist.sent.log.include.body', true);
    Config::set('mail-allowlist.sent.log.include.debug', false);

    $rawMessage = new RawMessage($this->mail->toString());
    $context = new SentMessageContext(generateSentMessage($rawMessage));

    $expectation = <<<LOG_MESSAGE
    LaravelMailAllowlist.MessageSent:
    ----------
    RAW
    ----------
    {$rawMessage->toString()}
    LOG_MESSAGE;

    expect($this->logger->generate($context))
        ->toBe($expectation);
});

it('does not generate a log message for raw messages if only headers or body is checked', function (bool $body) {
    Config::set('mail-allowlist.sent.log.include.middleware', false);
    Config::set('mail-allowlist.sent.log.include.headers', ! $body);
    Config::set('mail-allowlist.sent.log.include.message_data', false);
    Config::set('mail-allowlist.sent.log.include.body', $body);
    Config::set('mail-allowlist.sent.log.include.debug', false);

    $rawMessage = new RawMessage($this->mail->toString());
    $context = new SentMessageContext(generateSentMessage($rawMessage));

    $expectation = <<<'LOG_MESSAGE'
    LaravelMailAllowlist.MessageSent:
    RawMessages can only be logged including headers and body
    LOG_MESSAGE;

    expect($this->logger->generate($context))
        ->toBe($expectation);
})->with([true, false]);

it('logs debug information', function () {
    Config::set('mail-allowlist.sent.log.include.middleware', false);
    Config::set('mail-allowlist.sent.log.include.headers', false);
    Config::set('mail-allowlist.sent.log.include.message_data', false);
    Config::set('mail-allowlist.sent.log.include.body', false);
    Config::set('mail-allowlist.sent.log.include.debug', true);

    $expectation = <<<'LOG_MESSAGE'
    LaravelMailAllowlist.MessageSent:
    ClassName: ::notification_name::
    ----------
    DEBUG
    ----------
    ::debug::
    LOG_MESSAGE;

    expect($this->logger->generate($this->context))
        ->toBe($expectation);
});
