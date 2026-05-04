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

    /*
    |--------------------------------------------------------------------------
    | Email change
    |--------------------------------------------------------------------------
    |
    | This package only ships the building blocks (actions, events,
    | notifications). Consumers must wire their own routes, controllers and
    | a listener for `EmailChangeInitiated` that builds the confirmation URL
    | and dispatches the notification. See the README for a minimal example.
    |
    */
    'email_change' => [
        'enabled' => true,
        'token_lifetime_minutes' => 60 * 24,
        'notify_old_address' => true,
        'cooldown_minutes' => 5,
    ],
];
