<?php

namespace Backstage\Laravel\Users\Eloquent\Concerns\User;

use Backstage\Laravel\Users\Eloquent\Models\UserLogin;
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

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(config('users.eloquent.user_notification_preferences.model'), 'user_id');
    }
}
