import type { Page } from '@playwright/test';

/**
 * Logs in through the administration login form.
 *
 * Prefer passing an already-authenticated storageState over calling this helper in every
 * test; it belongs in global setup or in the few tests that explicitly verify the login
 * flow itself.
 */
export async function anmeldenAlsVerwaltung(page: Page, email: string, password: string): Promise<void> {
    const baseURL = process.env.APP_URL ?? 'http://127.0.0.1:8001';

    await page.goto(`${baseURL}/verwaltung/login`);
    await page.getByRole('heading', { name: 'Verwaltung — Anmeldung' }).waitFor();

    await page.getByLabel('E-Mail').fill(email);
    await page.getByLabel('Passwort').fill(password);
    await page.getByRole('button', { name: 'Anmelden' }).click();

    await page.waitForURL(`${baseURL}/verwaltung`);
}
