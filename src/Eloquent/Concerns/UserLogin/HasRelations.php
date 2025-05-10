<?php

namespace Backstage\LaravelUsers\Eloquent\Concerns\UserLogin;

use Backstage\LaravelUsers\Eloquent\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasRelations
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('users.eloquent.user.model', User::class), 'user_id', 'id');
    }
}
