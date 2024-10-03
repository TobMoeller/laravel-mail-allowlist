<?php

namespace TobMoeller\LaravelMailAllowlist;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use TobMoeller\LaravelMailAllowlist\Actions\Addresses\IsAllowedRecipient;
use TobMoeller\LaravelMailAllowlist\Actions\Logs\GenerateLogMessage;
use TobMoeller\LaravelMailAllowlist\Actions\Logs\GenerateLogMessageContract;
use TobMoeller\LaravelMailAllowlist\Actions\Logs\LogMessage;
use TobMoeller\LaravelMailAllowlist\Actions\Logs\LogMessageContract;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\Listeners\MessageSendingListener;

class LaravelMailAllowlistServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-mail-allowlist')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->bind(LogMessageContract::class, LogMessage::class);
        $this->app->bind(GenerateLogMessageContract::class, GenerateLogMessage::class);
        $this->app->singleton(IsAllowedRecipient::class, function () {
            return new IsAllowedRecipient(
                LaravelMailAllowlist::allowedDomainList(),
                LaravelMailAllowlist::allowedEmailList(),
            );
        });
    }

    public function packageBooted(): void
    {
        if (LaravelMailAllowlist::enabled()) {
            Event::listen(MessageSending::class, MessageSendingListener::class);
        }
    }
}
