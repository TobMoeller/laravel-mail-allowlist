<?php

use Illuminate\Mail\SentMessage;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage as SymfonySentMessage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;
use TobMoeller\LaravelMailAllowlist\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function generateSentMessage(
    Email|RawMessage|null $message = null,
    Address|string|null $sender = null,
    Address|string|null $to = null,
    ?string $text = null,
    ?string $debug = null,
): SentMessage {
    $sender ??= new Address('sender@test.de');
    $to ??= new Address('to@test.de');

    $message ??= new Email;
    if ($message instanceof Email) {
        $message->text($text ?? '::text::')->to($to)->sender($sender);
    }
    $envelope = new Envelope($sender, [$to]);
    $symfonySentMessage = new SymfonySentMessage($message, $envelope);
    $symfonySentMessage->appendDebug($debug ?? '::debug::');

    return new SentMessage($symfonySentMessage);
};
