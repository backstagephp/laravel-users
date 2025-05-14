<?php

namespace Backstage\Laravel\Users\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Backstage\Laravel\Users\Enums\NotificationType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'navigation_type',
    ];

    protected $casts = [
        'navigation_type' => NotificationType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('users.eloquent.user.model', User::class), 'user_id', 'id');
    }
}
