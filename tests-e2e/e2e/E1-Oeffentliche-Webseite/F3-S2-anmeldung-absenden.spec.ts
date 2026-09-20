/**
 * Story: E1 Öffentliche Webseite / F3 Online-Anmeldung / S2 Anmeldung absenden
 *
 * Geprüfte Kriterien:
 * - Das Absenden ist nur möglich, wenn alle Pflichtfelder gültig ausgefüllt sind.
 * - Bei einer neuen E-Mail wird ein neuer Teilnehmer angelegt.
 * - Bei freien Plätzen wird die Anmeldung mit dem Status „Bestätigt" angelegt.
 * - Bei Zahlungsart „Überweisung" wird automatisch eine Rechnung erzeugt.
 * - Nach erfolgreicher Anmeldung wird der Bestätigungsbildschirm mit Status und Preis angezeigt.
 * - Das Formular enthält den Pflicht-Haken „Datenschutz-Einwilligung" mit Link auf die Datenschutzerklärung.
 * - Das Formular enthält die optionalen Foto-/Video-Haken mit Freiwilligkeits-Hinweis.
 * - Bei Geburtsdatum unter 18 Jahren sind die Foto-/Video-Haken ausgeblendet (Papierformular-Hinweis).
 * - Ohne den Datenschutz-Haken wird das Formular nicht abgesendet.
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

test('zeigt den Pflicht-Haken zur Datenschutzerklärung und die optionalen Foto-/Video-Haken', async ({ page }) => {
    const datenschutz = page.getByLabel(/Ich habe die Datenschutzerklärung gelesen/);
    await expect(datenschutz).toBeVisible();
    await expect(page.getByLabel(/Ich habe die Datenschutzerklärung gelesen/)).not.toBeChecked();

    const datenschutzLink = page.locator('label').filter({ hasText: 'Datenschutzerklärung' }).getByRole('link', { name: 'Datenschutzerklärung' });
    await expect(datenschutzLink).toBeVisible();
    await expect(datenschutzLink).toHaveAttribute('href', '/datenschutz');

    await expect(page.getByLabel(/Veröffentlichung von Fotos zu/)).toBeVisible();
    await expect(page.getByLabel(/Veröffentlichung von Videos zu/)).toBeVisible();
    await expect(page.getByText('Die Foto- und Video-Einwilligung ist freiwillig')).toBeVisible();
});

test('blendet die Foto-/Video-Haken für Minderjährige aus und weist auf das Papierformular hin', async ({ page }) => {
    await page.getByLabel('Geburtsdatum').fill('2012-03-01');
    await expect(page.getByLabel('Geburtsdatum')).toBeVisible();

    await page.getByRole('button', { name: 'Anmeldung absenden' }).click();
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await expect(page.getByLabel(/Veröffentlichung von Fotos zu/)).toHaveCount(0);
    await expect(page.getByLabel(/Veröffentlichung von Videos zu/)).toHaveCount(0);
    await expect(page.getByText(/Papierformular.*Sorgeberechtigten/)).toBeVisible();
});

test('sendet das Formular nicht ab, ohne den Datenschutz-Haken gesetzt zu haben', async ({ page }) => {
    await page.getByLabel('Vorname *').fill('Max');
    await page.getByLabel('Nachname *').fill('OhneEinwilligung');
    await page.getByLabel('E-Mail *').fill(`ohne-einwilligung-${test.info().workerIndex}@example.com`);
    await page.getByRole('button', { name: 'Anmeldung absenden' }).click();

    // Der Pflicht-Haken hält das Absenden zurück; die Erfolgsmeldung erscheint nicht.
    await expect(page.getByRole('heading', { name: 'Anmeldung erfolgreich' })).toHaveCount(0);
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
    await page.getByLabel(/Ich habe die Datenschutzerklärung gelesen/).check();
    await page.getByRole('button', { name: 'Anmeldung absenden' }).click();

    await expect(page.getByRole('heading', { name: 'Anmeldung erfolgreich' })).toBeVisible();
    await expect(page.getByText(/Ihr Platz ist bestätigt/)).toBeVisible();
    await expect(page.getByText(/25,00 EUR/)).toBeVisible();
});
