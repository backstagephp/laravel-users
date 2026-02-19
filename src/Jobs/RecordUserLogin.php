<?php

namespace Backstage\Laravel\Users\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecordUserLogin implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>|null  $inputs
     */
    public function __construct(
        public int $userId,
        public string $type,
        public ?string $url,
        public ?string $referrer,
        public ?array $inputs,
        public ?string $userAgent,
        public ?string $ipAddress,
    ) {}

    public function handle(): void
    {
        $userModel = config('users.eloquent.user.model');

        if (! $userModel || ! class_exists($userModel)) {
            return;
        }

        $user = $userModel::find($this->userId);

        if (! $user) {
            return;
        }

        $user->logins()->create([
            'user_id' => $this->userId,
            'type' => $this->type,
            'url' => $this->url,
            'referrer' => $this->referrer,
            'inputs' => $this->inputs ? json_encode($this->inputs) : null,
            'user_agent' => $this->userAgent,
            'ip_address' => $this->ipAddress,
            'hostname' => $this->ipAddress ? gethostbyaddr($this->ipAddress) : null,
        ]);
    }
}
