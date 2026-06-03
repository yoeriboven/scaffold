<?php

declare(strict_types=1);

namespace App\Domains\Teams\Enum;

enum TeamRole: string
{
    case ADMIN = 'admin';

    case MEMBER = 'member';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => trans('Admin'),
            self::MEMBER => trans('Member'),
        };
    }
}
