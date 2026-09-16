<?php

declare(strict_types=1);

/**
 * Story: E2 CMS & Verwaltung / F6 Dashboard / S4 Schnellzugriffe
 *
 * Geprüfte Kriterien:
 * - Das Dashboard zeigt den Schnellzugriff „Bareinnahme erfassen" und öffnet
 *   die Maske zur Erfassung einer Bareinnahme.
 * - Das Dashboard zeigt den Schnellzugriff „Aktivität anlegen" und öffnet die
 *   Maske zum Anlegen einer Aktivität.
 * - Das Dashboard zeigt den Schnellzugriff „Anmeldungen" und öffnet die
 *   Anmeldungsliste.
 */

use Carbon\Carbon;
use Livewire\Livewire;
use Yoga\Modules\Verwaltung\Ui\Dashboard;

beforeEach(function (): void {
    Carbon::setTestNow('2026-09-02 12:00:00');
});

afterEach(function (): void {
    Carbon::setTestNow();
});

it('shows the quick access links to the frequently used views', function (): void {
    Livewire::test(Dashboard::class)
        ->assertSee('Schnellzugriffe')
        ->assertSee('Bareinnahme erfassen')
        ->assertSee(route('verwaltung.cash-receipt.record'))
        ->assertSee('Aktivität anlegen')
        ->assertSee(route('verwaltung.activity.create'))
        ->assertSee('Anmeldungen')
        ->assertSee(route('verwaltung.registrations'));
});
