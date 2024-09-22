<?php

namespace TobMoeller\LaravelMailAllowlist;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use TobMoeller\LaravelMailAllowlist\Actions\IsAllowedRecipient;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\Listeners\FilterMailRecipientsListener;

class LaravelMailAllowlistServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-mail-allowlist')
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        $this->app->bind(RecipientFilter::class);
        $this->app->singleton(IsAllowedRecipient::class, function () {
            return new IsAllowedRecipient(
                LaravelMailAllowlist::allowedDomainList(),
                LaravelMailAllowlist::allowedEmailList(),
            );
        });

        if (LaravelMailAllowlist::enabled()) {
            Event::listen(MessageSending::class, FilterMailRecipientsListener::class);
        }
    }
}
