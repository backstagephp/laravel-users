<?php

namespace Backstage\LaravelUsers\Commands;

use Illuminate\Console\Command;

class LaravelUsersCommand extends Command
{
    public $signature = 'laravel-users';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
