/**
 * Story: E1 Öffentliche Webseite / F4 Kontaktanfrage / S2 Anfrage absenden
 *
 * Geprüfte Kriterien:
 * - Das Absenden ist nur möglich, wenn alle Pflichtfelder gültig ausgefüllt sind.
 * - Bei ungültigem E-Mail-Format wird die Anfrage nicht abgeschickt.
 * - Nach erfolgreichem Absenden erscheint eine Bestätigungsmeldung mit dem Hinweis
 *   auf die Eingangsbestätigung per E-Mail.
 */
import { expect, test } from '@playwright/test';
import { testEmail } from '../../fixtures/kennung.js';

test.beforeEach(async ({ page }) => {
    await page.goto('/kontakt');
    await page.locator('body[data-livewire-ready="true"]').waitFor();
});

test('fordert Pflichtfelder, wenn sie leer bleiben', async ({ page }) => {
    await page.getByRole('button', { name: 'Anfrage senden' }).click();

    // HTML5-Pflichtfelder verhindern das Absenden.
    await expect(page).toHaveURL(/\/kontakt$/);
    await expect(page.locator('.yoga-success-box')).not.toBeVisible();
});

test('hindert das Absenden bei ungültiger E-Mail-Adresse', async ({ page }) => {
    await page.getByLabel('Name *').fill('E2E Test');
    await page.getByLabel('E-Mail *').fill('keine-adresse');
    await page.getByLabel('Nachricht *').fill('Wir interessieren uns für einen Termin.');
    await page.getByRole('button', { name: 'Anfrage senden' }).click();

    // Die Browser-Validierung des E-Mail-Feldes verhindert das Absenden.
    await expect(page).toHaveURL(/\/kontakt$/);
    await expect(page.locator('.yoga-success-box')).not.toBeVisible();
});

test('sendet eine gültige Anfrage und zeigt die Erfolgsmeldung', async ({ page }) => {
    const email = testEmail(test.info().workerIndex);

    await page.getByLabel('Name *').fill('E2E Test');
    await page.getByLabel('E-Mail *').fill(email);
    await page.getByLabel('Anlass / Gruppe').fill('Yoga auf der Burg');
    await page.getByLabel('Nachricht *').fill('Wir sind eine Gruppe von acht Personen und interessieren uns für einen Termin im Herbst.');
    await page.getByRole('button', { name: 'Anfrage senden' }).click();

    await expect(page.locator('.yoga-success-box')).toBeVisible();
    await expect(page.getByText('Vielen Dank für Ihre Anfrage')).toBeVisible();
    await expect(page.getByText(/Eingangsbestätigung per E-Mail/)).toBeVisible();
});