<?php

namespace Backstage\Laravel\Users\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\warning;

class UpgradeCommand extends Command
{
    protected $signature = 'users:upgrade
        {--force : Skip confirmation prompts}
        {--no-migrate : Skip running migrations}';

    protected $description = 'Upgrade backstage/laravel-users: publish config and run pending migrations.';

    public function handle(): int
    {
        info('Upgrading backstage/laravel-users…');

        if (! $this->option('force') && ! confirm(
            label: 'This will publish the package config (if missing) and run pending migrations. Continue?',
            default: true,
        )) {
            warning('Upgrade cancelled.');

            return self::SUCCESS;
        }

        $this->publishConfig();

        if (! $this->option('no-migrate')) {
            $this->runMigrations();
        }

        $this->reportEmailChangeFeature();

        info('backstage/laravel-users is up to date.');

        return self::SUCCESS;
    }

    protected function publishConfig(): void
    {
        if (file_exists(config_path('users.php'))) {
            note('Config already published at config/users.php — skipping.');

            return;
        }

        $this->call('vendor:publish', [
            '--provider' => 'Backstage\Laravel\Users\LaravelUsersServiceProvider',
            '--tag' => 'config',
            '--force' => false,
        ]);
    }

    protected function runMigrations(): void
    {
        $this->call('migrate', [
            '--force' => true,
        ]);
    }

    protected function reportEmailChangeFeature(): void
    {
        $usersTable = config('users.eloquent.user.table', 'users');

        $hasPendingEmail = Schema::hasColumn($usersTable, 'pending_email');

        if (! $hasPendingEmail) {
            warning(__('Email-change columns are missing on the :table table. Run migrations to enable the feature.', ['table' => $usersTable]));

            return;
        }

        info(__('Email-change toolkit is ready.'));

        if (config('users.email_change.enabled', true)) {
            note(__('Reminder: backstage/laravel-users does not register a listener for EmailChangeInitiated by default. If you are not using backstage/users, wire your own listener that builds the confirmation URL and dispatches the notification (see the package README).'));
        }
    }
}
