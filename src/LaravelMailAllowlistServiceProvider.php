<?php

namespace TobMoeller\LaravelMailAllowlist;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelMailAllowlistServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-mail-allowlist')
            ->hasConfigFile();
    }
}
