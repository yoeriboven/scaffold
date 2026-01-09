<?php

declare(strict_types=1);

namespace App\Support\Flash;

enum FlashLevel: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
}
