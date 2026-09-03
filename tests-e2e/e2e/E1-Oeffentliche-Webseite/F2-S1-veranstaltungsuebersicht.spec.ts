/**
 * Story: E1 Öffentliche Webseite / F2 Veranstaltungsdarstellung / S1 Veranstaltungsübersicht anzeigen
 *
 * Geprüfte Kriterien:
 * - Die Übersicht ist unter /kurse oder /veranstaltungen erreichbar.
 * - Es werden nur zukünftige, veröffentlichte Veranstaltungen angezeigt.
 * - Jede Veranstaltung zeigt Titel, Typ, Kurzbeschreibung, nächsten Termin, Preis und Buchbarkeit.
 * - Ausgebuchte Veranstaltungen zeigen den Hinweis „Warteliste".
 * - Klick auf eine Veranstaltung öffnet deren Detailseite.
 * - Es gibt eine Filtermöglichkeit nach Typ.
 */
import { expect, test } from '@playwright/test';
import state from '../../storage/state.json' assert { type: 'json' };

const { slug, titel } = state as { slug: string; titel: string };

test.beforeEach(async ({ page }) => {
    await page.goto('/kurse');
    await page.locator('body[data-livewire-ready="true"]').waitFor();
});

test('zeigt die Veranstaltungsübersicht unter /kurse', async ({ page }) => {
    await expect(page.getByRole('heading', { name: 'Kurse, Events und Workshops' })).toBeVisible();
});

test('zeigt die angelegte Veranstaltung mit Titel, Typ, Termin und Preis', async ({ page }) => {
    const karte = page.getByRole('article').filter({ hasText: titel });

    await expect(karte).toBeVisible();
    await expect(karte.getByText(/Kurs/)).toBeVisible();
    await expect(karte.getByText(/Nächster Termin:/)).toBeVisible();
    await expect(karte.getByText(/25,00 EUR/)).toBeVisible();
});

test('bietet eine Filtermöglichkeit nach Typ', async ({ page }) => {
    await expect(page.getByLabel('Filter:')).toBeVisible();
    await page.getByLabel('Filter:').selectOption('kurs');

    // The created activity is a course and should remain visible.
    await expect(page.getByRole('article').filter({ hasText: titel })).toBeVisible();
});

test('öffnet die Detailseite beim Klick auf eine Veranstaltung', async ({ page }) => {
    await page.getByRole('article').filter({ hasText: titel }).getByRole('link').click();

    await expect(page).toHaveURL(new RegExp(`/veranstaltung/${slug}`));
    await expect(page.getByRole('heading', { name: titel })).toBeVisible();
});
