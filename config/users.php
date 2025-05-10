<?php

// config for LaravelUsers/LaravelUsers
return [
    'eloquent' => [
        'user' => [
            'model' => \Backstage\LaravelUsers\Eloquent\Models\User::class,
            'table' => 'users',
        ],

        'user_login' => [
            'model' => \Backstage\LaravelUsers\Eloquent\Models\UserLogin::class,
            'table' => 'user_logins',
        ],
    ],
];
