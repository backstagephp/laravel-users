<?php

namespace Backstage\Laravel\Users\Eloquent\Concerns\UserTraffic;

use Backstage\Laravel\Users\Eloquent\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasRelations
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('users.eloquent.users.model', User::class), 'user_id', 'id');
    }
}
