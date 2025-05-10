<?php

namespace Backstage\LaravelUsers\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Backstage\LaravelUsers\Eloquent\Concerns\UserLogin as Concerns;

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
