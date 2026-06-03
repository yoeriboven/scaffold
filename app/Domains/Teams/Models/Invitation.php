<?php

declare(strict_types=1);

namespace App\Domains\Teams\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    use HasPublicId;

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function url(): string
    {
        return route('invitation.accept.show', $this);
    }
}
