/**
 * Story: E1 Öffentliche Webseite / F4 Kontaktanfrage / S3 Eingangsbestätigung und Benachrichtigung
 *
 * Geprüfte Kriterien:
 * - Nach erfolgreichem Absenden erscheint der Hinweis auf die Eingangsbestätigung
 *   per E-Mail (durchgängiger Oberflächentest; der Versand beider E-Mails und die
 *   Protokollierung als OutboundMessage sind auf der Application-Ebene geprüft).
 */
import { expect, test } from '@playwright/test';
import { testEmail } from '../../fixtures/kennung.js';

test('zeigt nach dem Absenden den Hinweis auf die Eingangsbestätigung', async ({ page }) => {
    await page.goto('/kontakt');
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await page.getByLabel('Name *').fill('E2E Bestätigung');
    await page.getByLabel('E-Mail *').fill(testEmail(test.info().workerIndex));
    await page.getByLabel('Nachricht *').fill('Wir interessieren uns für einen Termin.');
    await page.getByRole('button', { name: 'Anfrage senden' }).click();

    await expect(page.locator('.yoga-success-box')).toBeVisible();
    await expect(page.getByText(/Eingangsbestätigung per E-Mail/)).toBeVisible();
    await expect(page.getByText(/persönlich bei Ihnen zurück/)).toBeVisible();
});