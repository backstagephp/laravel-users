<?php

namespace Backstage\Laravel\Users\Enums;

enum NotificationType: string
{
    case Email = 'email';
    case SMS = 'sms';
    case InApp = 'in_app';

    public function label(): string
    {
        return match ($this) {
            self::Email => __('Email'),
            self::SMS => __('SMS'),
            self::InApp => __('In App'),
        };
    }
}
