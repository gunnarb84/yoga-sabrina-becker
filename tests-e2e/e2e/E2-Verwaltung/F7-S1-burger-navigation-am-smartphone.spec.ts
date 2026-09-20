/**
 * Story: E2 CMS & Verwaltung / F7 Mobilfähigkeit / S1 Burger-Navigation am Smartphone
 *
 * Geprüfte Kriterien:
 * - Bei ≤ 768 px ist die Topbar-Navigation ausgeblendet und die Burger-Schaltfläche sichtbar.
 * - Die Burger-Schaltfläche zeigt alle Navigationseinträge — einschließlich Abmelden —
 *   in der festgelegten Reihenfolge.
 * - Jeder Eintrag ist im geöffneten Menü per Antippen aufrufbar.
 * - Erneutes Betätigen der Burger-Schaltfläche schließt das Menü.
 * - Der aktive Bereich ist im geöffneten Menü hervorgehoben.
 * - Bei > 768 px bleibt die Desktop-Darstellung unverändert (keine Burger-Schaltfläche).
 */
import { expect, test } from '@playwright/test';
import { resolve } from 'node:path';

const baseURL = process.env.APP_URL ?? 'http://127.0.0.1:8001';

test.use({ storageState: resolve(import.meta.dirname, '../../storage/admin.json') });

const nav = (page: import('@playwright/test').Page) => page.locator('#au-topbar-nav');
const burger = (page: import('@playwright/test').Page) => page.getByRole('button', { name: 'Menü öffnen' });

const erwarteteReihenfolge = [
    'Dashboard',
    'Aktivitäten',
    'Kursvorlagen',
    'Anmeldungen',
    'Teilnehmer',
    'Kontaktanfragen',
    'Rechnungen',
    'Bareinnahmen',
    'Bareinnahme erfassen',
    'Nachrichten',
    'Abmelden',
];

test.describe('Burger-Navigation auf dem Smartphone', () => {
    test.use({ viewport: { width: 375, height: 812 } });

    test('blendet die Navigation aus und zeigt die Burger-Schaltfläche', async ({ page }) => {
        await page.goto(`${baseURL}/verwaltung`);
        await page.locator('body[data-livewire-ready="true"]').waitFor();

        await expect(nav(page)).toBeHidden();
        await expect(burger(page)).toBeVisible();
    });

    test('zeigt alle Einträge in der festgelegten Reihenfolge', async ({ page }) => {
        await page.goto(`${baseURL}/verwaltung`);
        await page.locator('body[data-livewire-ready="true"]').waitFor();

        await burger(page).click();

        const eintraege = await nav(page).locator('a, button').allInnerTexts();
        expect(eintraege.map((eintrag) => eintrag.trim())).toEqual(erwarteteReihenfolge);
    });

    test('ruft einen Eintrag per Antippen auf', async ({ page }) => {
        await page.goto(`${baseURL}/verwaltung`);
        await page.locator('body[data-livewire-ready="true"]').waitFor();

        await burger(page).click();
        await nav(page).getByRole('link', { name: 'Aktivitäten' }).click();

        await expect(page).toHaveURL(/\/verwaltung\/aktivitaeten$/);
    });

    test('schließt das Menü bei erneuter Betätigung', async ({ page }) => {
        await page.goto(`${baseURL}/verwaltung`);
        await page.locator('body[data-livewire-ready="true"]').waitFor();

        await burger(page).click();
        await expect(nav(page)).toBeVisible();
        await expect(burger(page)).toHaveAttribute('aria-expanded', 'true');

        await burger(page).click();
        await expect(nav(page)).toBeHidden();
        await expect(burger(page)).toHaveAttribute('aria-expanded', 'false');
    });

    test('hebt den aktiven Bereich im geöffneten Menü hervor', async ({ page }) => {
        await page.goto(`${baseURL}/verwaltung`);
        await page.locator('body[data-livewire-ready="true"]').waitFor();

        await burger(page).click();

        const dashboard = nav(page).getByRole('link', { name: 'Dashboard' });
        await expect(dashboard).toHaveClass(/is-active/);
        await expect(nav(page).getByRole('link', { name: 'Aktivitäten' })).not.toHaveClass(/is-active/);
    });
});

test.describe('Topbar auf dem Desktop', () => {
    test.use({ viewport: { width: 1280, height: 800 } });

    test('zeigt die Navigation ohne Burger-Schaltfläche', async ({ page }) => {
        await page.goto(`${baseURL}/verwaltung`);
        await page.locator('body[data-livewire-ready="true"]').waitFor();

        await expect(nav(page)).toBeVisible();
        await expect(burger(page)).toBeHidden();
    });
});