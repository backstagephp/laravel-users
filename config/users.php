<?php

/**
 * Laravel Users Configuration
 */

return [
    'eloquent' => [
        'user' => [
            'model' => \Backstage\Laravel\Users\Eloquent\Models\User::class,
            'table' => 'users',
            'observer' => \Backstage\Laravel\Users\Eloquent\Observers\UserObserver::class,
        ],

        'user_login' => [
            'model' => \Backstage\Laravel\Users\Eloquent\Models\UserLogin::class,
            'table' => 'user_logins',
        ],

        'user_traffic' => [
            'model' => \Backstage\Laravel\Users\Eloquent\Models\UserTraffic::class,
            'table' => 'user_traffic',
        ],
    ],

    'events' => [
        'requests' => [
            'web_traffic' => [
                'middleware' => \Backstage\Laravel\Users\Http\Middleware\DetectUserTraffic::class,
                'enabled' => true,
            ],
        ],

        'auth' => [
            'user_created' => [
                'invitation_notification' => \Backstage\Laravel\Users\Notifications\Invitation::class,
                'notification_delivery_channels' => [
                    'mail',
                ],
                'enabled' => true,
            ],
        ],
    ],

    'actions' => [
        'password' => [
            'lowercase_chars' => 'abcdefghijklmnopqrstuvwxyz',
            'uppercase_chars' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'numeric_chars' => '0123456789',
            'special_chars' => '!@#$%^&*()_+-=[]{}|;:,.<>?',
        ],
    ],
];
