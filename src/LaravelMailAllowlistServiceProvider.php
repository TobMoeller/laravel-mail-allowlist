<?php

namespace TobMoeller\LaravelMailAllowlist;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use TobMoeller\LaravelMailAllowlist\Commands\LaravelMailAllowlistCommand;

class LaravelMailAllowlistServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-mail-allowlist')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_mail_allowlist_table')
            ->hasCommand(LaravelMailAllowlistCommand::class);
    }
}
