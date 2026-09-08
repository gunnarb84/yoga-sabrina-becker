/**
 * Story: E1 Öffentliche Webseite / F4 Kontaktanfrage / S1 Anfrageformular anzeigen
 *
 * Geprüfte Kriterien:
 * - Auf der Kontaktseite ist neben der Kontaktkarte ein Anfrageformular sichtbar.
 * - Das Formular enthält die Felder `name`, `email`, `phone`, `topic` und `message`.
 * - Das Formular zeigt an, welche Felder Pflichtfelder sind (`name`, `email`, `message`).
 * - Das Formular enthält neben der Versandaktion einen Link auf die Datenschutzerklärung.
 * - Das Formular enthält keine Eingabemöglichkeit für Gesundheitsinformationen.
 */
import { expect, test } from '@playwright/test';

test.beforeEach(async ({ page }) => {
    await page.goto('/kontakt');
    await page.locator('body[data-livewire-ready="true"]').waitFor();
});

test('zeigt das Anfrageformular neben der Kontaktkarte', async ({ page }) => {
    await expect(page.locator('.yoga-contact-grid')).toBeVisible();
    await expect(page.locator('.yoga-contact-card')).toBeVisible();
    await expect(page.locator('.yoga-form-card')).toBeVisible();
    await expect(page.getByRole('button', { name: 'Anfrage senden' })).toBeVisible();
});

test('zeigt alle Formularfelder', async ({ page }) => {
    await expect(page.getByLabel('Name *')).toBeVisible();
    await expect(page.getByLabel('E-Mail *')).toBeVisible();
    await expect(page.getByLabel('Telefon')).toBeVisible();
    await expect(page.getByLabel('Anlass / Gruppe')).toBeVisible();
    await expect(page.getByLabel('Nachricht *')).toBeVisible();
});

test('markiert die Pflichtfelder', async ({ page }) => {
    await expect(page.getByLabel('Name *')).toHaveAttribute('required', '');
    await expect(page.getByLabel('E-Mail *')).toHaveAttribute('required', '');
    await expect(page.getByLabel('Nachricht *')).toHaveAttribute('required', '');
    await expect(page.getByLabel('Telefon')).not.toHaveAttribute('required', '');
});

test('verlinkt die Datenschutzerklärung neben der Versandaktion', async ({ page }) => {
    const hint = page.locator('.yoga-form-hint');
    await expect(hint).toBeVisible();
    await expect(hint.getByRole('link', { name: 'Datenschutzerklärung' })).toHaveAttribute('href', '/datenschutz');
});

test('bietet keine Eingabe für Gesundheitsinformationen', async ({ page }) => {
    const feldbezeichnungen = await page.locator('.yoga-form-card label').allTextContents();
    const zusammen = feldbezeichnungen.join(' ').toLowerCase();

    expect(zusammen).not.toContain('gesundheit');
});