<?php

declare(strict_types=1);

namespace App\Enums;

enum Locale: string
{
    case DUTCH = 'nl_NL';
    case AMERICAN_ENGLISH = 'en_US';

    public static function default(): self
    {
        return self::AMERICAN_ENGLISH;
    }
}
