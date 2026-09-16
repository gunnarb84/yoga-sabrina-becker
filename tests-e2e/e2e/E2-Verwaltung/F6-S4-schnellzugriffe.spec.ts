/**
 * Story: E2 CMS & Verwaltung / F6 Dashboard /
 *        S4 Schnellzugriffe
 *
 * Geprüfte Kriterien:
 * - Das Dashboard zeigt den Schnellzugriff „Bareinnahme erfassen"; sein Sprung
 *   öffnet die Maske zur Erfassung einer Bareinnahme.
 * - Das Dashboard zeigt den Schnellzugriff „Aktivität anlegen"; sein Sprung
 *   öffnet die Maske zum Anlegen einer Aktivität.
 * - Das Dashboard zeigt den Schnellzugriff „Anmeldungen"; sein Sprung öffnet
 *   die Anmeldungsliste.
 */
import { expect, test } from '@playwright/test';
import { resolve } from 'node:path';

const baseURL = process.env.APP_URL ?? 'http://127.0.0.1:8001';

test.use({ storageState: resolve(import.meta.dirname, '../../storage/admin.json') });

test('öffnet die häufigsten Masken direkt vom Dashboard', async ({ page }) => {
    await page.goto(`${baseURL}/verwaltung`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    const hauptbereich = page.getByRole('main');

    await hauptbereich.getByRole('link', { name: 'Bareinnahme erfassen' }).click();
    await expect(page).toHaveURL(/\/verwaltung\/bareinnahmen\/erfassen$/);

    await page.goto(`${baseURL}/verwaltung`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();
    await hauptbereich.getByRole('link', { name: 'Aktivität anlegen' }).click();
    await expect(page).toHaveURL(/\/verwaltung\/aktivitaeten\/neu$/);

    await page.goto(`${baseURL}/verwaltung`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();
    await hauptbereich.getByRole('link', { name: 'Anmeldungen', exact: true }).click();
    await expect(page).toHaveURL(/\/verwaltung\/anmeldungen$/);
});