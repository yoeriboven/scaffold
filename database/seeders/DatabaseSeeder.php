<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private User $admin;

    public function run(): void
    {
        $this->admin = $this->seedAdmin();
    }

    private function seedAdmin(): User
    {
        $admin = User::factory()->create([
            'name' => 'Yoeri Boven',
            'email' => 'yoeri@yoeri.me',
        ]);

        $admin->currentTeam()->update(['name' => 'Yoeri\'s Team']);

        return $admin;
    }
}
