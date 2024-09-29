<?php

use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\MessageContext;

it('holds message information', function () {
    $message = new Email();
    $context = new MessageContext($message);

    expect($context->getMessage())
        ->toBe($message)
        ->and($context->shouldSendMessage())
        ->toBeTrue();

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
