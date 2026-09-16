/**
 * Story: E2 CMS & Verwaltung / F6 Dashboard /
 *        S1 Nächste Termine anzeigen
 *
 * Geprüfte Kriterien:
 * - Das Dashboard zeigt unter „Nächste Termine" alle Termine vom heutigen Datum
 *   bis zum Ende des laufenden Monats, aufsteigend nach Beginn.
 * - Jede Terminzeile zeigt Titel, Beginn, bestätigte Anmeldungen und freie Plätze.
 * - Ein Klick auf eine Terminzeile öffnet die Anmeldungsliste der Veranstaltung.
 */
import { expect, test } from '@playwright/test';
import { resolve } from 'node:path';
import {
    angelegteAktivitaet,
    angelegterTeilnehmer,
    barAnmeldungAngelegt,
} from './hilfen.js';

const baseURL = process.env.APP_URL ?? 'http://127.0.0.1:8001';

test.use({ storageState: resolve(import.meta.dirname, '../../storage/admin.json') });

test('zeigt die Termine des laufenden Monats mit Belegung und Sprung zur Anmeldungsliste', async ({ page }) => {
    const workerIndex = test.info().workerIndex;

    const aktivitaet = await angelegteAktivitaet(page, workerIndex);
    const aktivitaetId = aktivitaet.anmeldungenPfad.match(/aktivitaeten\/([^/]+)\/anmeldungen/)?.[1] ?? '';
    expect(aktivitaetId).not.toBe('');

    const tag = Math.min(new Date().getDate() + 1, 28);
    const monat = `${new Date().getFullYear()}-${String(new Date().getMonth() + 1).padStart(2, '0')}`;
    const beginn = `${monat}-${String(tag).padStart(2, '0')}T10:00`;
    const ende = `${monat}-${String(tag).padStart(2, '0')}T11:30`;

    await page.goto(`${baseURL}/verwaltung/aktivitaeten/${aktivitaetId}/termine/neu`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();
    await page.getByLabel('Beginn').fill(beginn);
    await page.getByLabel('Ende').fill(ende);
    await page.getByLabel('Ort').fill('Studio Bergen');
    await page.getByRole('button', { name: 'Speichern' }).click();

    const teilnehmer = await angelegterTeilnehmer(page, workerIndex);
    await barAnmeldungAngelegt(page, teilnehmer, aktivitaet.anmeldungenPfad);

    await page.goto(`${baseURL}/verwaltung`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    const zeile = page.getByRole('link', { name: new RegExp(aktivitaet.titel, 'i') });
    await expect(zeile).toBeVisible();
    await expect(zeile).toContainText('bestätigt');
    await expect(zeile).toContainText('freie Plätze');
    await expect(zeile).toContainText('10:00');

    await zeile.click();
    await expect(page).toHaveURL(/\/verwaltung\/aktivitaeten\/[^/]+\/anmeldungen$/);
});