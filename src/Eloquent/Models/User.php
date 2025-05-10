<?php

namespace Backstage\LaravelUsers\Eloquent\Models;

use Backstage\LaravelUsers\Eloquent\Scopes\VerifiedUser;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailConcern;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordConcern;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as BaseUser;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\Contracts\HasApiTokens as HasApiTokensContract;
use Laravel\Sanctum\HasApiTokens as HasApiTokensConcern;
use Spatie\Permission\Traits\HasRoles;
use Backstage\LaravelUsers\Eloquent\Concerns\User as Concerns;

class User extends BaseUser implements CanResetPasswordContract, HasApiTokensContract, MustVerifyEmailContract
{
    use CanResetPasswordConcern;
    use HasApiTokensConcern;
    use HasFactory;
    use HasRoles;
    use MustVerifyEmailConcern;
    use Notifiable;

    // Concerns
    use Concerns\HasAttributes;
    use Concerns\HasConditionals;
    use Concerns\HasRelations;
    use Concerns\HasScopes;

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
    }
}
