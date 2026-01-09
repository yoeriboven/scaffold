<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasPublicId
{
    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    protected static function bootHasPublicId(): void
    {
        // Tries to create uuid 3 times, write test and improve
        static::creating(function (Model $model) {
            $uuid = Str::uuid();

            if (static::where('public_id', $uuid)->exists()) {
                $uuid = Str::uuid();

                if (static::where('public_id', $uuid)->exists()) {
                    $uuid = Str::uuid();
                }
            }

            $model->public_id = $uuid;
        });
    }
}
