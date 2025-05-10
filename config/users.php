<?php

// config for LaravelUsers/LaravelUsers
return [
    'eloquent' => [
        'user' => [
            'model' => \Backstage\LaravelUsers\Eloquent\Models\User::class,
            'table' => 'users',
        ],
    ],
];
