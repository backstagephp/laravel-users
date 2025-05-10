<?php

namespace Backstage\Laravel\Users\Eloquent\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as BaseUser;
use Backstage\Laravel\Users\Events\Auth\UserCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens as HasApiTokensConcern;
use Backstage\Laravel\Users\Eloquent\Scopes\VerifiedUser;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailConcern;
use Backstage\Laravel\Users\Eloquent\Concerns\User as Concerns;
use Laravel\Sanctum\Contracts\HasApiTokens as HasApiTokensContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordConcern;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends BaseUser implements CanResetPasswordContract, HasApiTokensContract, MustVerifyEmailContract
{
    use CanResetPasswordConcern;

    // Concerns
    use Concerns\HasAttributes;
    use Concerns\HasConditionals;
    use Concerns\HasRelations;
    use Concerns\HasScopes;
    use HasApiTokensConcern;
    use HasFactory;
    use HasRoles;
    use MustVerifyEmailConcern;
    use Notifiable;

    public function getTable()
    {
        return config('users.eloquent.user.table', 'users');
    }

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(VerifiedUser::class);

        static::created(function (User $user) {
            event(new UserCreated($user));
        });
    }
}
