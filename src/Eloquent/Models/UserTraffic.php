<?php

namespace Backstage\LaravelUsers\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Backstage\LaravelUsers\Eloquent\Concerns\UserTraffic as Concerns;

class UserTraffic extends Model
{
    use Concerns\HasRelations;

    public function getTable()
    {
        return config('users.eloquent.user_traffic.table', 'user_traffic');
    }

    protected $fillable = [
        'user_id',
        'method',
        'path',
        'full_url',
        'ip',
        'user_agent',
        'referer',
        'route_name',
        'route_action',
        'route_parameters',
    ];

    protected $casts = [
        'route_parameters' => 'array',
    ];
}
