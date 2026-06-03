<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Exception;
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
        // Tries to create public id 3 times; write test and improve
        static::creating(function (Model $model) {
            $id = Str::random(8);

            if (static::where('public_id', $id)->exists()) {
                report(new Exception('The public id already exists.'));

                $id = Str::random(8);

                if (static::where('public_id', $id)->exists()) {
                    $id = Str::random(8);
                }
            }

            $model->public_id = $id;
        });
    }
}
