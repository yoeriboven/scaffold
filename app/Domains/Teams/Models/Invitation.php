<?php

declare(strict_types=1);

namespace App\Domains\Teams\Models;

use App\Http\Controllers\Teams\AcceptInvitationController;
use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    use HasFactory;
    use HasPublicId;

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function url(): string
    {
        return action([AcceptInvitationController::class, 'show'], $this);
    }
}
