<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Language;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->asTeamAdmin()->create([
            'name' => 'Yoeri Boven',
            'email' => 'yoeri@yoeri.me',
            'language' => Language::DUTCH,
        ]);
    }
}
