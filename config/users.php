<?php

use Backstage\Laravel\Users\Eloquent\Models\User;
use Backstage\Laravel\Users\Eloquent\Models\UserLogin;
use Backstage\Laravel\Users\Eloquent\Models\UserNotificationPreference;
use Backstage\Laravel\Users\Eloquent\Observers\UserObserver;
use Backstage\Laravel\Users\Notifications\Invitation;

/**
 * Laravel Users Configuration
 */

return [
    'eloquent' => [
        'user' => [
            'model' => User::class,
            'table' => 'users',
            'observer' => UserObserver::class,
        ],

        'user_login' => [
            'model' => UserLogin::class,
            'table' => 'user_logins',
        ],

        'user_notification_preferences' => [
            'model' => UserNotificationPreference::class,
            'table' => 'user_notification_preferences',
        ],
    ],

    'events' => [
        'auth' => [
            'user_created' => [
                // Or set Backstage\Filament\Users\Notifications\UserInvitationNotification
                'invitation_notification' => Invitation::class,
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
