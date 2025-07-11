<?php

namespace Backstage\Laravel\Users\Eloquent\Models;

use Backstage\Laravel\Users\Eloquent\Concerns\Session as Concerns;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string|null $user_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string $payload
 * @property int $last_activity
 */
class Session extends Model
{
    use Concerns\HasRelations;

    protected $table = 'sessions';

    protected $guarded = [];
}
