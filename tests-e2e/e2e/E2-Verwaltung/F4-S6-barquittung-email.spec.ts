/**
 * Story: E2 CMS & Verwaltung / F4 Zahlungen und Belege verwalten /
 *        S6 Barquittung per E-Mail versenden
 *
 * Geprüfte Kriterien:
 * - Bei der Erfassung einer Barzahlung wird eine E-Mail mit der Barquittung
 *   an die E-Mail-Adresse der Teilnehmerin/des Teilnehmers versendet.
 * - Der Versand wird als ausgehende Nachricht mit dem Betreff „Barquittung …"
 *   und dem Status „versandt" protokolliert.
 * - Die Detailansicht der Nachricht zeigt Empfänger, Betreff, Status und Inhalt.
 */
import { expect, test } from '@playwright/test';
import { resolve } from 'node:path';
import {
    angelegteAktivitaet,
    angelegterTeilnehmer,
    barAnmeldungAngelegt,
    einzelBarzahlungErfasst,
} from './hilfen.js';
import type { Aktivitaet, Teilnehmer } from './hilfen.js';

test.use({ storageState: resolve(import.meta.dirname, '../../storage/admin.json') });

let teilnehmer: Teilnehmer;

test.beforeEach(async ({ page }) => {
    const workerIndex = test.info().workerIndex;

    const aktivitaet: Aktivitaet = await angelegteAktivitaet(page, workerIndex);
    teilnehmer = await angelegterTeilnehmer(page, workerIndex);
    await barAnmeldungAngelegt(page, teilnehmer, aktivitaet.anmeldungenPfad);
    await einzelBarzahlungErfasst(page, aktivitaet.anmeldungenPfad, teilnehmer);

    await page.goto(`${process.env.APP_URL ?? 'http://127.0.0.1:8001'}/verwaltung/nachrichten`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await page.getByLabel('Empfänger').fill(teilnehmer.email);
    await page.getByRole('button', { name: 'Filtern' }).click();
    await page.locator('body[data-livewire-ready="true"]').waitFor();
});

test('protokolliert die Barquittung als versandte Nachricht an die Teilnehmerin', async ({ page }) => {
    const zeile = page.getByRole('row', { name: new RegExp(teilnehmer.email, 'i') });

    await expect(zeile).toBeVisible();
    await expect(zeile).toContainText(/Barquittung B-\d{4}-\d{5}/);
    await expect(zeile).toContainText('versandt');
});

test('zeigt Empfänger, Betreff, Status und Inhalt im Nachrichtendetail', async ({ page }) => {
    const zeile = page.getByRole('row', { name: new RegExp(teilnehmer.email, 'i') });
    await zeile.getByRole('link', { name: 'Detail' }).click();
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await expect(page.getByText(teilnehmer.email)).toBeVisible();
    await expect(page.getByText(/Barquittung B-\d{4}-\d{5}/).first()).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Inhalt' })).toBeVisible();
    await expect(page.locator('.yoga-pre')).toContainText('Barquittung');
});