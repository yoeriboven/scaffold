<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\EnumHelpers;

enum Queue: string
{
    use EnumHelpers;

    case Default = 'default';

    case Monitoring = 'monitoring';
}

