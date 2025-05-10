<?php

namespace Backstage\LaravelUsers;

use Backstage\LaravelUsers\Commands\LaravelUsersCommand;
use Backstage\LaravelUsers\Domain\Events\Actions\RegisterEventListeners;
use Backstage\LaravelUsers\Events\Request\WebTrafficDetected;
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
            ->map(fn(SplFileInfo $splFile) => $splFile->getBasename())
            ->toArray();

        return [
            ...$migrations
        ];
    }

    public function packageBooted()
    {
        $router = $this->app->make(Router::class);

        if (config('users.events.requests.web_traffic.enabled', true)) {
            $router->pushMiddlewareToGroup('web', config('users.events.requests.web_traffic.middleware', WebTrafficDetected::class));
        }
    }
}
