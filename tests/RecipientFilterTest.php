<?php

use Mockery\MockInterface;
use Symfony\Component\Mime\Address;
use TobMoeller\LaravelMailAllowlist\Actions\IsAllowedRecipient;
use TobMoeller\LaravelMailAllowlist\RecipientFilter;

it('adds the recipients to allowed list if check succeeds', function () {
    $mock = Mockery::mock(IsAllowedRecipient::class, function (MockInterface $mock) {
        $mock->shouldReceive('check')
            ->twice()
            ->andReturnTrue();
    });
    $this->instance(IsAllowedRecipient::class, $mock);

    $addresses = [new Address('foo@bar.de'), new Address('bar@foo.de')];

    $filter = app(RecipientFilter::class)->filter($addresses);

    expect($filter)
        ->hasAllowedRecipients()->toBeTrue()
        ->hasDeniedRecipients()->toBeFalse()
        ->allowedRecipients->toMatchArray($addresses);
});

it('adds the recipients to denied list if check fails', function () {
    $mock = Mockery::mock(IsAllowedRecipient::class, function (MockInterface $mock) {
        $mock->shouldReceive('check')
            ->twice()
            ->andReturnFalse();
    });
    $this->instance(IsAllowedRecipient::class, $mock);

    $addresses = [new Address('foo@bar.de'), new Address('bar@foo.de')];

    $filter = app(RecipientFilter::class)->filter($addresses);

    expect($filter)
        ->hasAllowedRecipients()->toBeFalse()
        ->hasDeniedRecipients()->toBeTrue()
        ->deniedRecipients->toMatchArray($addresses);
});
