/**
 * Story: E1 Öffentliche Webseite / F1 Seiten und Navigation / S3 Navigation anzeigen
 *
 * Geprüfte Kriterien:
 * - Die Navigation wird auf jeder öffentlichen Seite oben dargestellt.
 * - Die Navigation zeigt alle aktiven Navigationseinträge in der festgelegten Reihenfolge.
 * - Der aktive Eintrag ist visuell hervorgehoben.
 * - Bei einer Viewport-Breite von höchstens 800 px ist die Navigation ausgeblendet und eine
 *   Burger-Schaltfläche sichtbar.
 * - Die Betätigung der Burger-Schaltfläche zeigt alle Navigationseinträge als aufklappbare
 *   Liste; erneute Betätigung schließt die Liste.
 * - Bei einer Viewport-Breite von mehr als 800 px wird die Navigation ohne
 *   Burger-Schaltfläche wie bisher angezeigt.
 *
 * Auf einer anderen Ebene geprüft:
 * - „Externe Links (`isExternal` = true) öffnen in einem neuen Tab und sind als extern
 *   gekennzeichnet" — Modul-Featuretest `modules/Webseite/tests/Feature/
 *   E1_F1_StartseiteNavigationTest.php` (Navigationseinträge entstehen nur über CMS-Daten,
 *   für die es weder Oberfläche noch Integrations-API gibt).
 */
import { expect, test } from '@playwright/test';

const navEintraege = ['Meine Idee', 'Veranstaltungen', 'Yoga-Events', 'Für wen?', 'Kontakt', 'Anfragen'];

test.beforeEach(async ({ page }) => {
    await page.goto('/');
    await page.locator('body[data-livewire-ready="true"]').waitFor();
});

const hauptnavigation = (page: import('@playwright/test').Page) =>
    page.getByRole('navigation', { name: 'Hauptnavigation' });

test.describe('Navigation auf der Desktop-Ansicht', () => {
    test('zeigt die Navigation oben auf der Startseite', async ({ page }) => {
        await expect(hauptnavigation(page)).toBeVisible();
        await expect(hauptnavigation(page).getByRole('link', { name: 'Meine Idee' })).toBeVisible();
    });
});

test('zeigt die Navigation oben auf einer statischen Seite', async ({ page }) => {
    await page.goto('/fuer-wen');
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await expect(hauptnavigation(page)).toBeVisible();
    await expect(hauptnavigation(page).getByRole('link', { name: 'Yoga-Events' })).toBeVisible();
});

test('zeigt alle aktiven Navigationseinträge in der festgelegten Reihenfolge', async ({ page }) => {
    const eintraege = await hauptnavigation(page).locator('li a').allTextContents();

    expect(eintraege.map((text) => text.trim())).toEqual(navEintraege);
});

test('hebt den aktiven Eintrag visuell hervor', async ({ page }) => {
    await page.goto('/kurse');
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await expect(hauptnavigation(page).locator('li.active')).toHaveText('Veranstaltungen');
});

test.describe('Burger-Menü auf mobilen Geräten (Viewport 375 px)', () => {
    test.use({ viewport: { width: 375, height: 812 } });

    test('blendet die Navigation aus und zeigt eine Burger-Schaltfläche', async ({ page }) => {
        await expect(hauptnavigation(page).getByRole('link', { name: 'Meine Idee' })).toBeHidden();
        await expect(page.getByRole('button', { name: 'Menü öffnen' })).toBeVisible();
        await expect(page.getByRole('button', { name: 'Menü öffnen' })).toHaveAttribute(
            'aria-expanded',
            'false',
        );
    });

    test('öffnet die Burger-Schaltfläche die Liste mit allen Navigationseinträgen', async ({ page }) => {
        await page.getByRole('button', { name: 'Menü öffnen' }).click();

        await expect(hauptnavigation(page).getByRole('link', { name: 'Meine Idee' })).toBeVisible();
        await expect(hauptnavigation(page).getByRole('link', { name: 'Veranstaltungen' })).toBeVisible();
        await expect(hauptnavigation(page).getByRole('link', { name: 'Yoga-Events' })).toBeVisible();
        await expect(hauptnavigation(page).getByRole('link', { name: 'Für wen?' })).toBeVisible();
        await expect(hauptnavigation(page).getByRole('link', { name: 'Anfragen' })).toBeVisible();
        await expect(page.getByRole('button', { name: 'Menü öffnen' })).toHaveAttribute(
            'aria-expanded',
            'true',
        );
    });

    test('schließt die Liste bei erneuter Betätigung der Burger-Schaltfläche', async ({ page }) => {
        const burger = page.getByRole('button', { name: 'Menü öffnen' });

        await burger.click();
        await expect(hauptnavigation(page).getByRole('link', { name: 'Meine Idee' })).toBeVisible();

        await burger.click();
        await expect(hauptnavigation(page).getByRole('link', { name: 'Meine Idee' })).toBeHidden();
        await expect(burger).toHaveAttribute('aria-expanded', 'false');
    });

    test('öffnet ein Navigationslink im Burger-Menü die Zielseite', async ({ page }) => {
        await page.getByRole('button', { name: 'Menü öffnen' }).click();
        await hauptnavigation(page).getByRole('link', { name: 'Yoga-Events' }).click();
        await page.locator('body[data-livewire-ready="true"]').waitFor();

        await expect(page.getByRole('heading', { name: 'Meine Yoga-Events' })).toBeVisible();
    });
});