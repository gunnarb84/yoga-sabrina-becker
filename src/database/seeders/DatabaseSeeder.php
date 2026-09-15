<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $email = config('app.admin_email');
        $password = config('app.admin_password');

        if (! is_string($email) || $email === '' || ! is_string($password) || $password === '') {
            throw new RuntimeException(
                'ADMIN_EMAIL und ADMIN_PASSWORD müssen in der .env der Installation gesetzt sein — '
                .'das Seeding legt das Konto der Administratorin mit diesen Zugangsdaten an.',
            );
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Sabrina Becker',
                'password' => $password,
            ]
        );

        $this->call(LegalPagesSeeder::class);
        $this->call(ContentPagesSeeder::class);
    }
}
