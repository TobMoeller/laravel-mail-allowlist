<?php

namespace TobMoeller\LaravelMailAllowlist\Commands;

use Illuminate\Console\Command;

class LaravelMailAllowlistCommand extends Command
{
    public $signature = 'laravel-mail-allowlist';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
