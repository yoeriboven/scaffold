<?php

declare(strict_types=1);

namespace App\Support\Flash;

use Inertia\Inertia;

class FlashNotifier
{
    public function success(string $message): void
    {
        $this->flash($message, FlashLevel::SUCCESS);
    }

    public function error(string $message): void
    {
        $this->flash($message, FlashLevel::ERROR);
    }

    protected function flash(string $message, FlashLevel $level): void
    {
        Inertia::flash('toast', [
            'message' => $message,
            'level' => $level->value,
        ]);
    }
}
