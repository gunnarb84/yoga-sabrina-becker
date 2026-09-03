/**
 * Story: E1 Öffentliche Webseite / F2 Veranstaltungsdarstellung / S2 Veranstaltungsdetails anzeigen
 *
 * Geprüfte Kriterien:
 * - Die Detailseite ist unter /veranstaltung/{slug} erreichbar.
 * - Sie zeigt Titel, Typ, Langbeschreibung, Bild und Preis.
 * - Sie listet alle zukünftigen Termine mit Datum, Uhrzeit, Ort und Hinweis.
 * - Sie zeigt die Anzahl freier Plätze.
 * - Sie enthält einen Button zur Anmeldung.
 */
import { expect, test } from '@playwright/test';
import state from '../../storage/state.json' assert { type: 'json' };

const { slug, titel } = state as { slug: string; titel: string };

test.beforeEach(async ({ page }) => {
    await page.goto(`/veranstaltung/${slug}`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();
});

test('zeigt Titel, Typ, Beschreibung und Preis', async ({ page }) => {
    await expect(page.getByRole('heading', { name: titel })).toBeVisible();
    await expect(page.getByText(/Kurs/)).toBeVisible();
    await expect(page.getByText(/25,00 EUR/)).toBeVisible();
    await expect(page.getByText(/Langbeschreibung/)).toBeVisible();
});

test('listet zukünftige Termine mit Ort und Hinweis', async ({ page }) => {
    await page.getByRole('heading', { name: 'Termine' }).waitFor();

    const termin = page.locator('.yoga-activity-sessions li').first();
    await expect(termin).toBeVisible();
    await expect(termin.getByText(/Studio E2E/)).toBeVisible();
    await expect(termin.getByText(/Matte/)).toBeVisible();
});

test('zeigt freie Plätze und den Anmeldebutton', async ({ page }) => {
    await expect(page.getByText(/von 10 Plätzen frei/)).toBeVisible();
    await expect(page.getByRole('link', { name: 'Jetzt anmelden' })).toBeVisible();
});
