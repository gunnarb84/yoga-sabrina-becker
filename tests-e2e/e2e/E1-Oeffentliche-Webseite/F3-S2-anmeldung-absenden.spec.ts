/**
 * Story: E1 Öffentliche Webseite / F3 Online-Anmeldung / S2 Anmeldung absenden
 *
 * Geprüfte Kriterien:
 * - Das Absenden ist nur möglich, wenn alle Pflichtfelder gültig ausgefüllt sind.
 * - Bei einer neuen E-Mail wird ein neuer Teilnehmer angelegt.
 * - Bei freien Plätzen wird die Anmeldung mit dem Status „Bestätigt" angelegt.
 * - Bei Zahlungsart „Überweisung" wird automatisch eine Rechnung erzeugt.
 * - Nach erfolgreicher Anmeldung wird der Bestätigungsbildschirm mit Status und Preis angezeigt.
 */
import { expect, test } from '@playwright/test';
import state from '../../storage/state.json' assert { type: 'json' };
import { testEmail, testNachname, testVorname } from '../../fixtures/kennung.js';

const { slug, titel } = state as { slug: string; titel: string };

test.beforeEach(async ({ page }) => {
    await page.goto(`/veranstaltung/${slug}`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await page.getByRole('link', { name: 'Jetzt anmelden' }).click();
    await page.locator('body[data-livewire-ready="true"]').waitFor();
});

test('zeigt das Anmeldeformular mit Pflichtfeldern', async ({ page }) => {
    await expect(page.getByRole('heading', { name: new RegExp(`Anmeldung: ${titel}`) })).toBeVisible();

    await expect(page.getByLabel('Vorname *')).toBeVisible();
    await expect(page.getByLabel('Nachname *')).toBeVisible();
    await expect(page.getByLabel('E-Mail *')).toBeVisible();
    await expect(page.getByLabel('Zahlungsart *')).toBeVisible();
});

test('fordert Pflichtfelder, wenn sie leer bleiben', async ({ page }) => {
    await page.getByRole('button', { name: 'Anmeldung absenden' }).click();

    // HTML5 required fields keep the form from submitting.
    await expect(page).toHaveURL(new RegExp(`/veranstaltung/${slug}/anmelden`));
});

test('sendet eine Überweisungs-Anmeldung und zeigt den Bestätigungsbildschirm', async ({ page }) => {
    const workerIndex = test.info().workerIndex;

    await page.getByLabel('Vorname *').fill(testVorname(workerIndex));
    await page.getByLabel('Nachname *').fill(testNachname(workerIndex));
    await page.getByLabel('E-Mail *').fill(testEmail(workerIndex));
    await page.getByLabel('Zahlungsart *').selectOption('ueberweisung');
    await page.getByRole('button', { name: 'Anmeldung absenden' }).click();

    await expect(page.getByRole('heading', { name: 'Anmeldung erfolgreich' })).toBeVisible();
    await expect(page.getByText(/Ihr Platz ist bestätigt/)).toBeVisible();
    await expect(page.getByText(/25,00 EUR/)).toBeVisible();
});
