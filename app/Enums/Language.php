<?php

declare(strict_types=1);

namespace App\Enums;

enum Language: string
{
    case DUTCH = 'nl_NL';
    case ENGLISH = 'en_US';

    public function short(): string
    {
        return match ($this) {
            self::DUTCH => 'nl',
            self::ENGLISH => 'en',
        };
    }

    public static function default(): self
    {
        return self::ENGLISH;
    }
}
