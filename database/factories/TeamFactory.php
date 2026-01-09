<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Teams\Models\Team;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'created_at' => CarbonImmutable::now(),
            'updated_at' => CarbonImmutable::now(),
        ];
    }
}
