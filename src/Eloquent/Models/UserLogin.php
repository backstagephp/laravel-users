<?php

namespace Backstage\LaravelUsers\Eloquent\Models;

use Backstage\LaravelUsers\Eloquent\Concerns\UserLogin as Concerns;
use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    use Concerns\HasRelations;

    public function getTable()
    {
        return config('users.eloquent.user_login.table', 'user_logins');
    }

    protected $fillable = [
        'user_id',
        'type',
        'url',
        'referrer',
        'inputs',
        'user_agent',
        'ip_address',
        'hostname',
        'isp',
        'org',
        'city',
        'region',
        'country_code',
    ];
}
