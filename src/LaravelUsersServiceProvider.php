<?php

namespace Backstage\Laravel\Users;

use Backstage\Laravel\Users\Commands\LaravelUsersCommand;
use Backstage\Laravel\Users\Events\Request\WebTrafficDetected;
use Backstage\Laravel\Users\Http\Middleware\DetectUserTraffic;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\File;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use SplFileInfo;

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
            ->hasMigrations($this->getMigrations())
            ->hasCommand(LaravelUsersCommand::class);
    }

    protected function getMigrations(): array
    {
        $migrationPath = __DIR__ . '/../database/migrations/';

        $files = File::allFiles($migrationPath);

        $migrations = collect($files)
            ->map(fn(SplFileInfo $splFile) => str($splFile->getBasename())->before('.')->toString())
            ->toArray();

        return [
            ...$migrations,
        ];
    }

    public function packageBooted()
    {
        $this->getEvents();

        $this->app->booted(function () {
            /**
             * @var Illuminate\Foundation\Http\Kernel $kernel
             */
            $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);

            $middleware = config('users.events.requests.web_traffic.middleware', DetectUserTraffic::class);

            if (config('users.events.requests.web_traffic.enabled', true)) {
                $kernel->appendMiddlewareToGroup('web', $middleware);
            }
        });
    }

    protected function getEvents()
    {
        $this->app['events']->listen(
            WebTrafficDetected::class,
            \Backstage\Laravel\Users\Listeners\Request\RecordUserMovements::class
        );

        $this->app['events']->listen(
            \Illuminate\Auth\Events\Login::class,
            \Backstage\Laravel\Users\Listeners\Auth\HandleUserLogin::class
        );

        $this->app['events']->listen(
            \Illuminate\Auth\Events\Logout::class,
            \Backstage\Laravel\Users\Listeners\Auth\HandleUserLogout::class
        );

        $this->app['events']->listen(
            'eloquent.created: ' . config('users.eloquent.user.model'),
            \Backstage\Laravel\Users\Listeners\Auth\SendInvitationMail::class
        );
    }
}
