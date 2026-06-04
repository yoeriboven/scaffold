<?php

declare(strict_types=1);

namespace App\Domains\Teams\Models\Concerns;

use App\Domains\Teams\Enum\TeamPermission;
use App\Domains\Teams\Enum\TeamRole;
use App\Domains\Teams\Models\Team;
use App\Domains\Teams\Models\TeamMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasTeams
{
    public static function bootHasTeams(): void
    {
        // Every user has their own team and can be invited to others
        static::created(function (User $user) {
            $team = Team::create();

            $user->joinTeam($team, TeamRole::ADMIN);
        });
    }

    public function teams(): BelongsToMany
    {
        return $this
            ->belongsToMany(Team::class)
            ->as('membership')
            ->withPivot(['role', 'permissions'])
            ->withTimestamps()
            ->using(TeamMembership::class);
    }

    /*
     * The most recent updated_at is the current team
     */
    public function currentTeam(): ?Team
    {
        return $this
            ->teams()
            ->orderByPivotDesc('updated_at')
            ->orderByPivotDesc('id')
            ->first();
    }

    public function joinTeam(Team $team, TeamRole $role): void
    {
        $this->teams()->attach($team, [
            'role' => $role,
            'permissions' => TeamPermission::forTeamRole($role),
        ]);
    }

    public function switchTeam(Team $team): void
    {
        $this->teams()->updateExistingPivot($team, ['updated_at' => now()]);
    }

    public function hasTeamPermissionTo(TeamPermission $permission): bool
    {
        if ($this->currentTeam() === null) {
            return false;
        }

        return $this->currentTeam()->membership->permissions->contains($permission);
    }
}
