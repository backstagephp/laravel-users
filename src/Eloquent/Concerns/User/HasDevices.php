<?php

namespace Backstage\Laravel\Users\Eloquent\Concerns\User;

use Backstage\Laravel\Users\Eloquent\Models\Session;
use Backstage\Laravel\Users\Eloquent\Models\UserDevice;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait HasDevices
{
    public function sessions(): HasMany
    {
        return $this->hasMany(config('users.eloquent.session.model', Session::class), 'user_id', 'id');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(config('users.eloquent.device.model', UserDevice::class), 'user_id', 'id');
    }

    public function deviceHistory(): HasMany
    {
        return $this->devices()->onlyTrashed();
    }

    public function currentSession(): Session
    {
        /**
         * Get session from front-end
         *
         * @var \Illuminate\Session\SessionManager $session
         */
        $session = session();

        return $this->sessions()->where('id', $session->getId())->first();
    }

    public function currentDevice(?Request $request = null, ?int $userId = null, ?string $ip = null, ?string $userAgent = null): UserDevice
    {
        $request ??= request();

        return UserDevice::getSignatureBasedDevice(
            $userId ?? Auth::id(),
            $ip ?? $request->ip(),
            $userAgent ?? $request->userAgent()
        );
    }

    public function getLastSeenDevice(): ?UserDevice
    {
        $sessions = $this->sessions()->get();

        /**
         * @var \Backstage\Laravel\Users\Eloquent\Models\Session $sessionWithMostRecentActivity
         */
        $sessionWithMostRecentActivity = $sessions->sortByDesc('last_activity')->first();

        $device = $sessionWithMostRecentActivity->retrieveDevice();

        if (! $device) {
            return null;
        }

        return $device;
    }
}
