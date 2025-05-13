<?php

namespace Backstage\Laravel\Users\Eloquent\Concerns\User;

trait HasScopes
{
    // WIP

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }
}
