<?php

namespace LaravelUsers\LaravelUsers;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use LaravelUsers\LaravelUsers\Commands\LaravelUsersCommand;

class LaravelUsersServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-users')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_users_table')
            ->hasCommand(LaravelUsersCommand::class);
    }
}
