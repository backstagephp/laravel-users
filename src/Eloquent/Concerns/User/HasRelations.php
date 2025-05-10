<?php

namespace Backstage\LaravelUsers\Eloquent\Concerns\User;

use Backstage\LaravelUsers\Eloquent\Models\UserLogin;

trait HasRelations
{
    public function logins()
    {
        return $this->hasMany(config('users.eloquent.user_login.model', UserLogin::class), 'user_id');
    }
}
