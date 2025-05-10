<?php

/**
 * Laravel Users Configuration
 */

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

        'user_traffic' => [
            'model' => \Backstage\LaravelUsers\Eloquent\Models\UserTraffic::class,
            'table' => 'user_traffic',
        ],
    ],

    'events' => [
        'requests' => [
            'web_traffic' => [
                'middleware' => \Backstage\LaravelUsers\Http\Middleware\DetectUserTraffic::class,
                'enabled' => true,
            ],
        ],
    ],
];
