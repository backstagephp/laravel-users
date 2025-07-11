<?php

namespace Backstage\Laravel\Users\Eloquent\Models;

use Backstage\Laravel\Users\Eloquent\Concerns\UserDevice as Concerns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;

class UserDevice extends Model
{
    use Concerns\HasRelations;
    use SoftDeletes;

    protected $table = 'user_devices';

    protected $appends = [
        'device_name',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'ip_address',
        'user_agent',
        'fingerprint',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $device) {
            $device->fingerprint = static::generateFingerprint(
                $device->user_id,
                $device->ip_address,
                $device->user_agent
            );
        });
    }

    public static function generateFingerprint($user_id, $ip, $userAgent): string
    {
        return md5($user_id.$ip.$userAgent);
    }

    public static function getSignatureBasedDevice($user_id, $ip, $userAgent): ?self
    {
        $fingerprint = static::generateFingerprint($user_id, $ip, $userAgent);

        $query = self::query();

        $query = $query->where('fingerprint', $fingerprint);

        return $query->first();
    }

    public static function getDeviceName(string $userAgent, $headers = []): string
    {
        $agent = new Agent;

        $agent->setUserAgent($userAgent);

        if (! empty($headers)) {
            $agent->setHttpHeaders($headers);
        }

        return $agent->device();
    }

    public function getDeviceNameAttribute(): string
    {
        return static::getDeviceName($this->user_agent);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('users.eloquent.user.model', \App\Models\User::class));
    }
}
