<?php

use Illuminate\Support\Facades\Config;
use Pest\Expectation;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\Actions\Addresses\IsAllowedRecipient;
use TobMoeller\LaravelMailAllowlist\Enums\Header;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses\AddressFilter;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

it('filters a mail with address list headers', function (Header $header) {
    Config::set('mail-allowlist.allowed.domains', ['foo.de', 'bar.de']);
    Config::set('mail-allowlist.allowed.emails', ['bar@foo.com', 'foo@bar.com']);

    $allowed = [
        new Address('allowed@foo.de'),
        new Address('allowed@bar.de'),
        new Address('bar@foo.com'),
        new Address('foo@bar.com'),
    ];
    $denied = [new Address('denied@foobar.de')]; // no matching domain or email

    $mail = new Email;
    $mail->getHeaders()->addMailboxListHeader(
        $header->value,
        array_merge(
            $allowed,
            $denied,
        )
    );
    $context = new MessageContext($mail);

    (new AddressFilter($header, app(IsAllowedRecipient::class)))->handle($context, fn () => null);

    $logExpectation = AddressFilter::class;
    $logExpectation .= PHP_EOL.'Allowed Recipients: allowed@foo.de;allowed@bar.de;bar@foo.com;foo@bar.com';
    $logExpectation .= PHP_EOL.'Denied Recipients: denied@foobar.de';

    expect($mail->getHeaders()->getHeaderBody($header->value))
        ->toMatchArray($allowed)
        ->each(fn (Expectation $address) => $address->getAddress() !== 'denied@foobar.de')
        ->and($context->getLog()[0])
        ->toBe($logExpectation);
})->with(Header::addressListHeaders());

it('filters a mail with address headers', function (Header $header) {
    Config::set('mail-allowlist.allowed.domains', ['foo.de']);
    Config::set('mail-allowlist.allowed.emails', ['bar@foo.com']);

    $allowedDomainAddress = new Address('allowed@foo.de');
    $allowedEmailAddress = new Address('bar@foo.com');
    $deniedAddress = new Address('denied@foobar.de'); // no matching domain or email

    $allowedDomainMail = new Email;
    $allowedDomainMail->getHeaders()->addMailboxHeader($header->value, $allowedDomainAddress);
    $allowedDomainContext = new MessageContext($allowedDomainMail);

    $allowedEmailMail = new Email;
    $allowedEmailMail->getHeaders()->addMailboxHeader($header->value, $allowedEmailAddress);
    $allowedEmailContext = new MessageContext($allowedEmailMail);

    $deniedMail = new Email;
    $deniedMail->getHeaders()->addMailboxHeader($header->value, $deniedAddress);
    $deniedContext = new MessageContext($deniedMail);

    (new AddressFilter($header, app(IsAllowedRecipient::class)))->handle($allowedDomainContext, fn () => null);
    (new AddressFilter($header, app(IsAllowedRecipient::class)))->handle($allowedEmailContext, fn () => null);
    (new AddressFilter($header, app(IsAllowedRecipient::class)))->handle($deniedContext, fn () => null);

    $allowedDomainLog = AddressFilter::class.PHP_EOL.'Allowed Recipients: allowed@foo.de';
    $allowedEmailLog = AddressFilter::class.PHP_EOL.'Allowed Recipients: bar@foo.com';
    $deniedLog = AddressFilter::class.PHP_EOL.'Denied Recipients: denied@foobar.de';

    expect($allowedDomainMail->getHeaders()->getHeaderBody($header->value))
        ->toBe($allowedDomainAddress)
        ->and($allowedEmailMail->getHeaders()->getHeaderBody($header->value))
        ->toBe($allowedEmailAddress)
        ->and($deniedMail->getHeaders()->getHeaderBody($header->value))
        ->toBeNull()
        ->and($allowedDomainContext->getLog()[0])
        ->toBe($allowedDomainLog)
        ->and($allowedEmailContext->getLog()[0])
        ->toBe($allowedEmailLog)
        ->and($deniedContext->getLog()[0])
        ->toBe($deniedLog);
})->with(Header::addressHeaders());

it('removes address list headers with no allowed lists', function (Header $header) {
    Config::set('mail-allowlist.allowed.domains', []);
    Config::set('mail-allowlist.allowed.emails', []);

    $mail = new Email;
    $mail->getHeaders()->addMailboxListHeader($header->value, [new Address('foo@bar.com')]);
    $context = new MessageContext($mail);

    (new AddressFilter($header, app(IsAllowedRecipient::class)))->handle($context, fn () => null);

    expect($mail->getHeaders()->getHeaderBody($header->value))
        ->toBeEmpty();
})->with(Header::addressListHeaders());

it('removes address headers with no allowed lists', function (Header $header) {
    Config::set('mail-allowlist.allowed.domains', []);
    Config::set('mail-allowlist.allowed.emails', []);

    $mail = new Email;
    $mail->getHeaders()->addMailboxHeader($header->value, new Address('foo@bar.com'));
    $context = new MessageContext($mail);

    (new AddressFilter($header, app(IsAllowedRecipient::class)))->handle($context, fn () => null);

    expect($mail->getHeaders()->getHeaderBody($header->value))
        ->toBeEmpty();
})->with(Header::addressHeaders());
