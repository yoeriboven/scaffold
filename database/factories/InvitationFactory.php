<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Teams\Models\Invitation;
use App\Domains\Teams\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvitationFactory extends Factory
{
    protected $model = Invitation::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
