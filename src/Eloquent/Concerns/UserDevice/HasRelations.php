<?php

namespace Backstage\Laravel\Users\Eloquent\Concerns\UserDevice;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasRelations
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            config('users.eloquent.user.model', \Backstage\Laravel\Users\Eloquent\Models\User::class),
            'user_id',
            'id'
        );
    }
}
