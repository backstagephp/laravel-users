<?php

namespace Backstage\Laravel\Users\Domain\Password\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class GeneratePassword
{
    use AsAction;

    protected string $lowercaseChars;

    protected string $uppercaseChars;

    protected string $numericChars;

    protected string $specialChars;

    public function __construct()
    {
        $this->lowercaseChars = config('users.actions.password.lowercase_chars', 'abcdefghijklmnopqrstuvwxyz');

        $this->uppercaseChars = config('users.actions.password.uppercase_chars', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');

        $this->numericChars = config('users.actions.password.numeric_chars', '0123456789');

        $this->specialChars = config('users.actions.password.special_chars', '!@#$%^&*()_+-=[]{}|;:,.<>?');
    }

    public function handle(int $length = 12, bool $includeSpecialChars = true, bool $includeNumbers = true, bool $includeUppercase = true): string
    {
        $characters = $this->getCharacterPool($includeSpecialChars, $includeNumbers, $includeUppercase);

        return $this->generatePassword($length, $characters);
    }

    protected function getCharacterPool(bool $includeSpecialChars, bool $includeNumbers, bool $includeUppercase): string
    {
        $pool = $this->lowercaseChars;

        if ($includeUppercase) {
            $pool .= $this->uppercaseChars;
        }

        if ($includeNumbers) {
            $pool .= $this->numericChars;
        }

        if ($includeSpecialChars) {
            $pool .= $this->specialChars;
        }

        return $pool;
    }

    protected function generatePassword(int $length, string $characters): string
    {
        $password = '';

        $charactersLength = strlen($characters);

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $password;
    }
}
