<?php

namespace Backstage\Laravel\Users\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Backstage\Laravel\Users\UserManager
 */
class UserManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'user-manager';
    }
}
