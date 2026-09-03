<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'sabrina@example.com'],
            [
                'name' => 'Sabrina Becker',
                'password' => bcrypt('yoga2026'),
            ]
        );
    }
}
