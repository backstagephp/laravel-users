# Laravel Users

A modern user management package for Laravel, built by [Backstage](https://backstagephp.com).

---

## ✨ Introduction

`backstage/laravel-users` is a full-featured Laravel package that provides a complete user management foundation for Laravel 10 & 11 applications. It leverages Laravel Sanctum and Spatie Permissions to deliver robust authentication, authorization, and user preference handling.

This package includes support for user login tracking, traffic monitoring, preferences, factory seeding, and customizable scaffolding—all designed to scale with your application.

---

## 📦 Features

- ✅ Custom user model with Laravel Auth integration
- 🔐 Role and permission support via [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- 🔑 API token authentication via Laravel Sanctum
- 🔧 Configurable user system with feature toggles
- 🧪 Built-in factory and seeder
- 📊 Tracks login events and traffic history
- 🧩 Modular architecture with domain-driven design (DDD) patterns
- 🛠 Artisan commands for user management (`make`, `list`, `delete`)
- 📨 Email-based username generation
- 🔄 Password generation utility
- 🧠 Customizable notification & sub-navigation preferences

---

## 🧰 Requirements

- PHP ^8.2
- Laravel ^10.0 or ^11.0
- Composer

---

## ⚙️ Installation

Install via Composer:

```bash
composer require backstage/laravel-users
```

Publish configuration, migration, and seeder files:

```bash
php artisan vendor:publish --tag="laravel-users-config"
php artisan vendor:publish --tag="laravel-users-migrations"
```

Run database migrations:

```bash
php artisan migrate
```

---

## 🛠 Configuration

### 1. Set the User Provider

Update `config/auth.php` to use the package's custom user model:

```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => \Backstage\Laravel\Users\Models\User::class,
    ],
],
```

### 2. Enable Sanctum API Tokens

If you haven't already:

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider"
php artisan migrate
```

### 3. Setup Spatie Permissions

Ensure Spatie permissions are published:

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\\Permission\\PermissionServiceProvider"
php artisan migrate
```

Edit `config/permission.php` to adjust your roles and permission settings.

---

## 🧩 Customization

You can modify `config/users.php` to change:

- Default roles
- Middleware behavior
- Feature toggles (registration, email verification, etc.)
- UI scaffolding paths (if applicable)

---

## 🧑‍💻 Artisan Commands

The package includes a suite of CLI tools:

| Command                        | Description                       |
|-------------------------------|-----------------------------------|
| `artisan make:user`          | Create a new user interactively   |
| `artisan users:list`          | List users in a table format      |
| `artisan users:delete`        | Delete a user by ID or email      |

Example:

```bash
artisan make:user --email=user@example.com --name="John Doe" --role=admin
```

---

## 🧱 Architecture

### Key Classes

- **UserManager**: Central config-driven service for runtime overrides
- **Domain/Email/Actions**:
  - `GenerateUsernameFromEmail`
  - `ExtractDomainFromEmail`
  - `ValidateEmail`
- **Domain/Password/Actions**:
  - `GeneratePassword`
- **Eloquent/Concerns/User**:
  - Modular traits like `HasAttributes`, `HasScopes`, `HasRelations`
- **Models**:
  - `User`, `UserLogin`, `UserTraffic`, `UserNotificationPreferences`

### Migrations Included

- Adds sub-navigation and notification preferences to users
- Tracks user logins and traffic history
- Supports nullable password for third-party auth

---

## 🧪 Testing

Run PHPUnit tests via:

```bash
vendor/bin/phpunit
```

Ensure your `.env.testing` or `phpunit.xml` is configured with a test database.

---

## 🔬 Development

### Local Package Development

If you're contributing or using this in a monorepo:

1. Clone the package into `packages/` or your preferred folder
2. Add this to your Laravel app's `composer.json`:

```json
"repositories": [
  {
    "type": "path",
    "url": "packages/laravel-users"
  }
],
```

3. Require the package:

```bash
composer require backstage/laravel-users:*
```

4. Refresh autoload:

```bash
composer dump-autoload
```

---

## 🤝 Contributing

Contributions are welcome! Please follow the PSR-12 coding standard and submit pull requests with clear descriptions.

---

## ✉️ Email change with confirmation

This package ships the building blocks for a confirmed email-change flow:
actions, events and notifications. **Routing, URL generation and the
listener that sends the mail are intentionally left to your application** —
this keeps the package framework-agnostic and lets you decide where the
confirmation link points.

### What's included

- Migration: `pending_email`, `pending_email_token`, `pending_email_token_expires_at`, `pending_email_requested_at`.
- Actions:
    - `Backstage\Laravel\Users\Domain\Email\Actions\InitiateEmailChange`
    - `Backstage\Laravel\Users\Domain\Email\Actions\ConfirmEmailChange`
    - `Backstage\Laravel\Users\Domain\Email\Actions\CancelEmailChange`
- Events: `EmailChangeInitiated`, `EmailChangeConfirmed`, `EmailChangeCancelled` (under `Backstage\Laravel\Users\Events\Email\`).
- Notifications (URL is supplied via constructor): `Backstage\Laravel\Users\Notifications\Email\ConfirmEmailChange`, `Backstage\Laravel\Users\Notifications\Email\EmailChangeRequested`.
- Config block `users.email_change.*` for token lifetime, cooldown and old-address notification.

### Minimal setup in your application

```php
// app/Providers/AppServiceProvider.php
use Backstage\Laravel\Users\Events\Email\EmailChangeInitiated;
use Backstage\Laravel\Users\Notifications\Email\ConfirmEmailChange;
use Backstage\Laravel\Users\Notifications\Email\EmailChangeRequested;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

public function boot(): void
{
    Event::listen(function (EmailChangeInitiated $event) {
        $confirmUrl = URL::temporarySignedRoute(
            'email.confirm',
            now()->addMinutes($event->expiresInMinutes),
            ['user' => $event->user->id, 'token' => $event->rawToken],
        );

        Notification::route('mail', $event->user->pending_email)
            ->notify(new ConfirmEmailChange($event->newEmail, $confirmUrl));

        if (config('users.email_change.notify_old_address')) {
            $cancelUrl = URL::temporarySignedRoute(
                'email.cancel',
                now()->addMinutes($event->expiresInMinutes),
                ['user' => $event->user->id, 'token' => $event->rawToken],
            );

            $event->user->notify(new EmailChangeRequested($event->newEmail, $cancelUrl));
        }
    });
}
```

```php
// routes/web.php
use Backstage\Laravel\Users\Domain\Email\Actions\CancelEmailChange;
use Backstage\Laravel\Users\Domain\Email\Actions\ConfirmEmailChange;
use Backstage\Laravel\Users\Eloquent\Models\User;

Route::middleware('signed')->group(function () {
    Route::get('/email/confirm/{user}/{token}', function (User $user, string $token) {
        ConfirmEmailChange::run($user, $token);

        return redirect('/');
    })->name('email.confirm');

    Route::get('/email/cancel/{user}/{token}', function (User $user, string $token) {
        CancelEmailChange::run($user, $token);

        return redirect('/');
    })->name('email.cancel');
});
```

```php
// trigger from your controller / Filament page / Livewire component
use Backstage\Laravel\Users\Domain\Email\Actions\InitiateEmailChange;

InitiateEmailChange::run($user, $request->input('email'));
```

The `?string $source` parameter on `InitiateEmailChange` is opaque to this
package; pass any identifier you need on the receiving end (panel ID, tenant
slug, channel, …). Listeners can branch on `$event->source` to decide which
URL to build.

---

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

---

## 👤 Author

**Manoj Hortulanus**  
Developer at [Backstage](https://backstagephp.com)  
📧 <manoj@backstagephp.com>

---

## 🏁 Credits

- [Backstage](https://backstagephp.com) for sponsoring development
- [Spatie](https://spatie.be) for the excellent permission package
- [Laravel](https://laravel.com) for the amazing framework
