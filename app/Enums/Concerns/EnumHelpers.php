<?php

declare(strict_types=1);

namespace App\Enums\Concerns;

trait EnumHelpers
{
    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }
}
