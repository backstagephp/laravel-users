<?php

namespace Backstage\Laravel\Users\Eloquent\Models;

use Backstage\Laravel\Users\Eloquent\Concerns\User as Concerns;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailConcern;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordConcern;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as BaseUser;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends BaseUser implements CanResetPasswordContract, MustVerifyEmailContract
{
    use CanResetPasswordConcern;

    // Concerns
    use Concerns\HasAttributes;
    use Concerns\HasConditionals;
    use Concerns\HasRelations;
    use Concerns\HasScopes;
    use HasFactory;
    use HasRoles;
    use MustVerifyEmailConcern;
    use Notifiable;
    use SoftDeletes;

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
    }

    public function guardName(): string
    {
        return config('auth.defaults.guard', 'web');
    }

    protected function getDefaultGuardName(): string
    {
        return $this->guardName();
    }
}
