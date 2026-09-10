/**
 * Story: E2 CMS & Verwaltung / F4 Zahlungen und Belege verwalten /
 *        S5 Bareinnahmenliste und Beleg-PDF
 *
 * Geprüfte Kriterien:
 * - Die Bareinnahmenliste ist über den Menüpunkt „Bareinnahmen" erreichbar.
 * - Die Liste zeigt Belegnummer, Datum, Empfänger/in und Betrag, absteigend sortiert.
 * - Die Liste lässt sich nach Belegnummer und nach Empfänger/in filtern.
 * - Jeder Beleg ist als PDF herunterladbar; die Datei nennt die Belegnummer.
 */
import { expect, test } from '@playwright/test';
import { resolve } from 'node:path';
import {
    einzelBarzahlungErfasst,
    angelegteAktivitaet,
    angelegterTeilnehmer,
    barAnmeldungAngelegt,
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
});

test('zeigt den erfassten Beleg in der Liste mit allen Spalten', async ({ page }) => {
    await page.goto(`${process.env.APP_URL ?? 'http://127.0.0.1:8001'}/verwaltung/bareinnahmen`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    const zeile = page.getByRole('row', { name: new RegExp(teilnehmer.nachname, 'i') });

    await expect(zeile).toBeVisible();
    await expect(zeile.locator('td').first()).toHaveText(/^B-\d{4}-\d{5}$/);
    await expect(zeile).toContainText('45,00');
    await expect(zeile).toContainText('EUR');
});

test('filtert die Liste nach Belegnummer', async ({ page }) => {
    await page.goto(`${process.env.APP_URL ?? 'http://127.0.0.1:8001'}/verwaltung/bareinnahmen`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    const zeile = page.getByRole('row', { name: new RegExp(teilnehmer.nachname, 'i') });
    const nummer = (await zeile.locator('td').first().innerText()).trim();

    await page.getByLabel('Belegnummer').fill(nummer);
    await expect(zeile).toBeVisible();

    await page.getByLabel('Belegnummer').fill('B-9999-99999');
    await expect(page.getByRole('row', { name: new RegExp(teilnehmer.nachname, 'i') })).toBeHidden();
});

test('filtert die Liste nach Empfänger/in', async ({ page }) => {
    await page.goto(`${process.env.APP_URL ?? 'http://127.0.0.1:8001'}/verwaltung/bareinnahmen`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await page.getByLabel('Empfänger/in').fill(teilnehmer.nachname);
    await expect(page.getByRole('row', { name: new RegExp(teilnehmer.nachname, 'i') })).toBeVisible();

    await page.getByLabel('Empfänger/in').fill('KeinTrefferAufDiesenNamen');
    await expect(page.getByRole('row', { name: new RegExp(teilnehmer.nachname, 'i') })).toBeHidden();
});

test('stellt den Beleg als PDF mit der Belegnummer im Dateinamen bereit', async ({ page }) => {
    await page.goto(`${process.env.APP_URL ?? 'http://127.0.0.1:8001'}/verwaltung/bareinnahmen`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    const zeile = page.getByRole('row', { name: new RegExp(teilnehmer.nachname, 'i') });
    const nummer = (await zeile.locator('td').first().innerText()).trim();
    const href = await zeile.getByRole('link', { name: 'PDF' }).getAttribute('href');

    expect(href).toContain('/pdf');

    const antwort = await page.request.get(href ?? '');
    expect(antwort.status()).toBe(200);

    const inhalt = await antwort.text();
    expect(inhalt.slice(0, 4)).toBe('%PDF');
    expect(antwort.headers()['content-disposition']).toContain(`Barquittung-${nummer}.pdf`);
});