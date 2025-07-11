<?php

namespace Backstage\Laravel\Users\Eloquent\Models;

use Backstage\Laravel\Users\Eloquent\Concerns\UserLogin as Concerns;
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            if ($model->type === 'login') {
                $existingDevice = UserDevice::getSignatureBasedDevice(
                    $model->user_id,
                    $model->ip_address,
                    $model->user_agent,
                );

                if ($existingDevice) {
                    return;
                }

                $signature = UserDevice::generateFingerprint(
                    $model->user_id,
                    $model->ip_address,
                    $model->user_agent
                );

                $device = $model->user->devices()->withTrashed()->where('fingerprint', $signature)->first();

                if (!$device) {
                    $model->user->devices()->create([
                        'name' => UserDevice::getDeviceName(
                            $model->user_agent,
                        ),
                        'ip_address' => $model->ip_address,
                        'user_agent' => $model->user_agent,
                    ]);

                    return;
                }

                $device->restore();
            }

            if ($model->type === 'logout') {
                $existingDevice = UserDevice::getSignatureBasedDevice(
                    $model->user_id,
                    $model->ip_address,
                    $model->user_agent
                );

                $existingDevice?->delete();
            }
        });
    }
}
