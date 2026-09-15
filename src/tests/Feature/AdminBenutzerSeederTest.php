<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;

/**
 * Das Seeding legt das Konto der Administratorin ausschließlich aus den
 * Konfigurationsgrößen ADMIN_EMAIL und ADMIN_PASSWORD an — ohne gesetzte
 * Werte verweigert es den Lauf (kein Test-Konto mit bekannten Zugangsdaten).
 */
it('creates the admin user from the configured credentials', function (): void {
    config()->set('app.admin_email', 'admin@example.com');
    config()->set('app.admin_password', 'sicheres-passwort');

    (new DatabaseSeeder())->run();

    $user = User::query()->where('email', 'admin@example.com')->firstOrFail();
    expect(Hash::check('sicheres-passwort', $user->password))->toBeTrue();
});

it('updates the admin user when the credentials change', function (): void {
    config()->set('app.admin_email', 'admin@example.com');
    config()->set('app.admin_password', 'erstes-passwort');
    (new DatabaseSeeder())->run();

    config()->set('app.admin_password', 'zweites-passwort');
    (new DatabaseSeeder())->run();

    $user = User::query()->where('email', 'admin@example.com')->firstOrFail();
    expect(Hash::check('zweites-passwort', $user->password))->toBeTrue();
    expect(User::query()->count())->toBe(1);
});

it('refuses to seed without configured admin credentials', function (): void {
    config()->set('app.admin_email', null);
    config()->set('app.admin_password', null);

    expect(fn (): Throwable => (new DatabaseSeeder())->run())
        ->toThrow(RuntimeException::class);
});