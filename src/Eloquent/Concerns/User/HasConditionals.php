<?php

namespace Backstage\Laravel\Users\Eloquent\Concerns\User;

trait HasConditionals
{
    public function userIsRegistered(): bool
    {
        return !is_null($this->getAttribute('email_verified_at'))
            && !is_null($this->getAttribute('password'));
    }
}
