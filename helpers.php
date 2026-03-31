<?php

use Backstage\Laravel\Users\Domain\Email\Actions\ValidateEmail;
use Backstage\Laravel\Users\Domain\Password\Actions\GeneratePassword;

if (! function_exists('geo')) {
    function geo($attribute = '')
    {
        if (! session('geo')) {
            $geo = json_decode(@file_get_contents('https://pro.ip-api.com/json/'.request()->ip().'?key='.config('services.ip-api.key')));

            session()->put('geo', $geo);
        } else {
            $geo = session('geo');
        }

        return $attribute && isset($geo->{$attribute}) ? $geo->{$attribute} : null;
    }
}

if (! function_exists('generate_password')) {
    function generate_password(...$args)
    {
        return GeneratePassword::run(...$args);
    }
}

if (! function_exists('validate_email')) {
    function validate_email(...$args)
    {
        return ValidateEmail::run(...$args);
    }
}
