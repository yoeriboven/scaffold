<?php

declare(strict_types=1);

namespace App\Domains\Teams\Models\Concerns;

use App\Domains\Teams\Enum\Permissions;
use App\Domains\Teams\Enum\TeamRole;
use App\Domains\Teams\Models\Team;
use App\Domains\Teams\Models\TeamMembership;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait AttachedToTeams
{
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
        /** @var ?Team */
        return $this
            ->teams()
            ->orderByPivot('updated_at', 'desc')
            ->first();
    }

    public function joinTeam(Team $team, TeamRole $role): void
    {
        $this->teams()->attach($team, [
            'role' => $role,
            'permissions' => Permissions::forTeamRole($role),
        ]);
    }

    public function switchTeam(Team $team): void
    {
        $this->teams()->updateExistingPivot($team, ['updated_at' => now()]);
    }

    public function hasPermissionTo(Permissions $permission, ?int $teamId = null): bool
    {
        if (is_null($teamId)) {
            $teamId = $this->currentTeam()?->id;
        }

        if (is_null($teamId)) {
            return false;
        }

        $team = $this->teams()->where('id', $teamId)->first();

        if ($team === null) {
            return false;
        }

        return $team->membership->permissions->contains($permission);
    }
}
