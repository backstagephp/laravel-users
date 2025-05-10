<?php

namespace Backstage\LaravelUsers\Eloquent\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class VerifiedUser implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('email_verified_at', '!=', null)
            ->where('password', '!=', null);
    }
}
