<?php

namespace Backstage\LaravelUsers\Eloquent\Concerns\User;

use Backstage\LaravelUsers\Eloquent\Models\UserLogin;
use Backstage\LaravelUsers\Eloquent\Models\UserTraffic;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasRelations
{
    /**
     * Get the logins for the user.
     */
    public function logins(): HasMany
    {
        return $this->hasMany(config('users.eloquent.user_login.model', UserLogin::class), 'user_id');
    }

    /**
     * Get the traffic for the user.
     */
    public function traffic(): HasMany
    {
        return $this->hasMany(config('users.eloquent.user_traffic.model', UserTraffic::class), 'user_id');
    }
}
