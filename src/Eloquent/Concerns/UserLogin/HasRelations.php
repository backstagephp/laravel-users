<?php

namespace Backstage\Laravel\Users\Eloquent\Concerns\UserLogin;

use Backstage\Laravel\Users\Eloquent\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasRelations
{
    public function user(): BelongsTo|User
    {
        return $this->belongsTo(config('users.eloquent.user.model', User::class), 'user_id', 'id');
    }
}
