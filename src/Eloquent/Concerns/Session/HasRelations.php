<?php

namespace Backstage\Laravel\Users\Eloquent\Concerns\Session;

use Backstage\Laravel\Users\Eloquent\Models\UserDevice;
use Illuminate\Database\Eloquent\Model;
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

    public function retrieveDevice(): null|Model|UserDevice
    {
        return UserDevice::getSignatureBasedDevice(
            $this->user_id,
            $this->ip_address,
            $this->user_agent
        );
    }
}
