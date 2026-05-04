<?php

namespace Backstage\Laravel\Users;

use Backstage\Laravel\Users\Console\Commands;
use Backstage\Laravel\Users\Eloquent\Models\User;
use Backstage\Laravel\Users\Eloquent\Observers\UserObserver;
use Backstage\Laravel\Users\Events\Auth\UserCreated;
use Backstage\Laravel\Users\Facades\UserManager;
use Backstage\Laravel\Users\Listeners\Auth\HandleUserLogin;
use Backstage\Laravel\Users\Listeners\Auth\HandleUserLogout;
use Backstage\Laravel\Users\Listeners\Auth\SendInvitationMail;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\File;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use SplFileInfo;

class LaravelUsersServiceProvider extends PackageServiceProvider
{
    public static string $name = 'backstage:laravel/users';

    public static string $viewNamespace = 'backstage/laravel/users';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile('users')
            ->hasMigrations($this->getMigrations())
            ->hasCommands([
                Commands\MakeUserCommand::class,
                Commands\ListUsersCommand::class,
                Commands\DeleteUser::class,
                Commands\UpgradeCommand::class,
            ]);
    }

    protected function getMigrations(): array
    {
        $migrationPath = __DIR__ . '/../database/migrations/';

        $files = File::allFiles($migrationPath);

        $migrations = collect($files)
            ->map(fn (SplFileInfo $splFile) => str($splFile->getBasename())->before('.')->toString())
            ->toArray();

        return [
            ...$migrations,
        ];
    }

    public function packageRegistered()
    {
        $this->app->singleton('user-manager', function ($app) {
            return new UserManager($app);
        });

        $this->app->alias('user-manager', UserManager::class);
    }

    public function packageBooted()
    {
        $this->getEvents();

        $this->app->booted(function () {
            /**
             * @var Illuminate\Foundation\Http\Kernel $kernel
             */
            $kernel = $this->app->make(Kernel::class);
        });

        if (config('users.eloquent.user.observer', UserObserver::class)) {
            config('auth.providers.users.model', User::class)::observe(config('users.eloquent.user.observer', UserObserver::class));
        }
    }

    protected function getEvents()
    {
        $this->app['events']->listen(
            Login::class,
            HandleUserLogin::class
        );

        $this->app['events']->listen(
            Logout::class,
            HandleUserLogout::class
        );

        if (config('users.events.auth.user_created.enabled', true)) {
            $this->app['events']->listen(
                UserCreated::class,
                SendInvitationMail::class
            );
        }
    }
}
