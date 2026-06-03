<?php

declare(strict_types=1);

namespace App\Domains\Teams\Models;

use App\Models\Concerns\HasPublicId;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasPublicId;

    /** Team functionality */
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function invite(string $email): Invitation
    {
        return $this->invitations()->firstOrCreate([
            'email' => $email,
        ]);
    }
}
