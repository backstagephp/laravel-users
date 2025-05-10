# Laravel Users

A Laravel package for managing users, built by [Backstage](https://backstagephp.com).

## Description

`backstage/laravel-users` is a package designed to simplify user management in Laravel applications. It integrates seamlessly with Laravel Sanctum and Spatie Permission to provide robust authentication and authorization functionality out of the box.

---

## Features

- User model scaffolding
- Role and permission support using Spatie
- API token authentication via Laravel Sanctum
- Configurable user provider
- Factory and seeder support for testing

---

## Requirements

- PHP ^8.2
- Laravel 10 or 11
- Composer

---

## Installation

Install the package using Composer:

```bash
composer require backstage/laravel-users
```

Publish the configuration and migration files:

```bash
php artisan vendor:publish --tag="laravel-users-config"
php artisan vendor:publish --tag="laravel-users-migrations"
```

Run the migrations:

```bash
php artisan migrate
```

---

## Configuration

### 1. Update `config/auth.php`

To use the custom user model provided by the package, modify your `auth.providers.users.model` to point to the package’s model.

```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => \Backstage\LaravelUsers\Models\User::class,
    ],
],
```

### 2. Sanctum Configuration (Optional)

If you're using Laravel Sanctum for API authentication, make sure Sanctum's middleware is applied correctly in `app/Http/Kernel.php`:

```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

Also, publish Sanctum’s config if needed:

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 3. Configure roles and permissions

This package depends on [spatie/laravel-permission](https://spatie.be/docs/laravel-permission), so ensure its config is published:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

You can modify `config/permission.php` and seed roles/permissions as needed.

---

## Customization

After publishing the config file:

```bash
php artisan vendor:publish --tag="laravel-users-config"
```

You can customize behavior by editing `config/users.php`, which may include:

- Role defaults
- Middleware toggles
- Feature toggles (registration, email verification, etc.)
- UI scaffolding paths (if applicable)

---

## Usage

After setup, your application will use `Backstage\LaravelUsers\Models\User` as the default user model.

The model is fully compatible with:

- Laravel’s built-in authentication system
- Laravel Sanctum for API tokens
- Spatie's permission and role traits

You may use Laravel’s Auth facade or guards as usual:

```php
use Illuminate\Support\Facades\Auth;

$user = Auth::user();
```

---

## Testing

To run the package tests:

```bash
vendor/bin/phpunit
```

Ensure you have a test database configured in your `.env.testing` or `phpunit.xml` file.

---

## Development

### Autoloading

This package uses PSR-4 autoloading:

```json
"autoload": {
  "psr-4": {
    "Backstage\\LaravelUsers\\": "src/",
    "Backstage\\LaravelUsers\\Database\\Factories\\": "database/factories/"
  }
}
```

After modifying package source files, refresh the autoload files:

```bash
composer dump-autoload
```

---

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

---

## Author

**Manoj Hortulanus**  
Developer at [Backstage](https://backstagephp.com)  
<manoj@backstagephp.com>
