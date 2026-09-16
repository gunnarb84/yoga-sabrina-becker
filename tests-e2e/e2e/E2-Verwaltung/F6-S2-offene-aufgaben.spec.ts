/**
 * Story: E2 CMS & Verwaltung / F6 Dashboard /
 *        S2 Offene Aufgaben anzeigen
 *
 * Geprüfte Kriterien:
 * - Das Dashboard zeigt die Anzahl der Rechnungen mit Status `Offen`.
 * - Ein Klick auf den Rechnungs-Zähler öffnet die Rechnungsliste.
 * - Ein Zähler `0` ist nicht sichtbar.
 * - Der Kontaktanfragen-Eintrag zeigt keinen Zähler; sein Klick öffnet die
 *   Kontaktanfragen-Liste.
 */
import { expect, test } from '@playwright/test';
import { resolve } from 'node:path';
import {
    angelegteAktivitaet,
    angelegterTeilnehmer,
    ueberweisungsAnmeldungAngelegt,
} from './hilfen.js';

const baseURL = process.env.APP_URL ?? 'http://127.0.0.1:8001';

test.use({ storageState: resolve(import.meta.dirname, '../../storage/admin.json') });

test('zählt offene Rechnungen, versteckt Nullzähler und verlinkt die Listen', async ({ page }) => {
    const workerIndex = test.info().workerIndex;

    const aktivitaet = await angelegteAktivitaet(page, workerIndex);
    const teilnehmer = await angelegterTeilnehmer(page, workerIndex);
    await ueberweisungsAnmeldungAngelegt(page, teilnehmer, aktivitaet.anmeldungenPfad);

    await page.goto(`${baseURL}/verwaltung`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    const rechnungen = page.getByRole('link', { name: /Offene Rechnungen/ });
    await expect(rechnungen).toBeVisible();
    await expect(rechnungen).toContainText(/\d/);
    await expect(page.locator('body')).not.toContainText('Fehlgeschlagene E-Mails');
    await expect(page.locator('body')).not.toContainText('Wartelisteneinträge');

    const kontakt = page.getByRole('main').getByRole('link', { name: /Kontaktanfragen/ });
    await expect(kontakt).toBeVisible();
    await expect(kontakt).not.toContainText(/\d/);

    await rechnungen.click();
    await expect(page).toHaveURL(/\/verwaltung\/rechnungen$/);
});