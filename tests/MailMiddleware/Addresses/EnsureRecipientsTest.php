<?php

use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\EnsureRecipients;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

it('cancels the mail if no to recipients are found', function () {
    $mail = new Email;
    $context = new MessageContext($mail);

    expect((new EnsureRecipients)->handle($context, fn () => '::next_response::'))
        ->toBeNull()
        ->and($context->shouldSendMessage())
        ->toBeFalse();
});

it('does not cancel the mail if to recipients are found', function () {
    $mail = new Email;
    $mail->addTo(new Address('foo@bar.com'));
    $context = new MessageContext($mail);

    expect((new EnsureRecipients)->handle($context, fn () => '::next_response::'))
        ->toBe('::next_response::')
        ->and($context->shouldSendMessage())
        ->toBeTrue();
});
