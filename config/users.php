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

        'user_notification_preferences' => [
            'model' => \Backstage\Laravel\Users\Eloquent\Models\UserNotificationPreference::class,
            'table' => 'user_notification_preferences',
        ],
    ],

    'events' => [
        'auth' => [
            'user_created' => [
                // Or set Backstage\Filament\Users\Notifications\UserInvitationNotification
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
