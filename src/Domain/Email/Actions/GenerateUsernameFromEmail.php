<?php

namespace Backstage\Laravel\Users\Domain\Email\Actions;

use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class GenerateUsernameFromEmail
{
    use AsAction;

    public function handle(string $email, bool $appendSuffix = true): string
    {
        $prefix = Str::slug(strstr($email, '@', true), '_');

        if (! $appendSuffix) {
            return $prefix;
        }

        return $this->ensureUniqueStyle($prefix);
    }

    protected function ensureUniqueStyle(string $prefix): string
    {
        $suffix = Str::random(4);
        return "{$prefix}_{$suffix}";
    }
}
