/**
 * Story: E2 CMS & Verwaltung / F7 Mobilfähigkeit / S4 PDF-Ausgaben am Smartphone
 *
 * Geprüfte Kriterien (bei einer Viewport-Breite von höchstens 480 px):
 * - Die PDF-Ausgabe einer Rechnung ist aus der Rechnungsliste erreichbar und wird als PDF ausgeliefert.
 * - Die PDF-Ausgabe eines Bareinnahmenbelegs ist aus der Bareinnahmenliste erreichbar und wird als PDF ausgeliefert.
 * - Der Bareinnahmen-Monatsdruck ist aus der Bareinnahmenliste erreichbar und wird als PDF ausgeliefert.
 */
import { expect, test } from '@playwright/test';
import { resolve } from 'node:path';
import {
    angelegteAktivitaet,
    angelegterTeilnehmer,
    barAnmeldungAngelegt,
    einzelBarzahlungErfasst,
    ueberweisungsAnmeldungAngelegt,
} from './hilfen.js';
import type { Aktivitaet, Teilnehmer } from './hilfen.js';

const baseURL = process.env.APP_URL ?? 'http://127.0.0.1:8001';

test.use({ storageState: resolve(import.meta.dirname, '../../storage/admin.json') });
test.use({ viewport: { width: 375, height: 812 } });

let aktivitaet: Aktivitaet;
let teilnehmer: Teilnehmer;
let ueberweisungsTeilnehmer: Teilnehmer;

test.beforeEach(async ({ page }) => {
    const workerIndex = test.info().workerIndex;

    aktivitaet = await angelegteAktivitaet(page, workerIndex);
    teilnehmer = await angelegterTeilnehmer(page, workerIndex);
    await barAnmeldungAngelegt(page, teilnehmer, aktivitaet.anmeldungenPfad);
    await einzelBarzahlungErfasst(page, aktivitaet.anmeldungenPfad, teilnehmer);

    ueberweisungsTeilnehmer = await angelegterTeilnehmer(page, workerIndex + 10);
    await ueberweisungsAnmeldungAngelegt(page, ueberweisungsTeilnehmer, aktivitaet.anmeldungenPfad);
});

test('stellt die Rechnung aus der Rechnungsliste als PDF bereit', async ({ page }) => {
    await page.goto(`${baseURL}/verwaltung/rechnungen`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    const zeile = page.getByRole('row', { name: new RegExp(ueberweisungsTeilnehmer.nachname, 'i') });
    await expect(zeile).toBeVisible();

    const nummer = (await zeile.locator('td').first().innerText()).trim();
    const href = await zeile.getByRole('link', { name: 'PDF' }).getAttribute('href');
    expect(href).toContain('/pdf');

    const antwort = await page.request.get(href ?? '');
    expect(antwort.status()).toBe(200);

    const inhalt = await antwort.text();
    expect(inhalt.slice(0, 4)).toBe('%PDF');
    expect(antwort.headers()['content-disposition']).toContain(`Rechnung-${nummer}.pdf`);
});

test('stellt den Bareinnahmenbeleg aus der Bareinnahmenliste als PDF bereit', async ({ page }) => {
    await page.goto(`${baseURL}/verwaltung/bareinnahmen`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    const zeile = page.getByRole('row', { name: new RegExp(teilnehmer.nachname, 'i') });
    await expect(zeile).toBeVisible();

    const nummer = (await zeile.locator('td').nth(2).innerText()).trim();
    const href = await zeile.getByRole('link', { name: 'PDF' }).getAttribute('href');
    expect(href).toContain('/pdf');

    const antwort = await page.request.get(href ?? '');
    expect(antwort.status()).toBe(200);

    const inhalt = await antwort.text();
    expect(inhalt.slice(0, 4)).toBe('%PDF');
    expect(antwort.headers()['content-disposition']).toContain(`Barquittung-${nummer}.pdf`);
});

test('stellt den Bareinnahmen-Monatsdruck aus der Bareinnahmenliste als PDF bereit', async ({ page }) => {
    await page.goto(`${baseURL}/verwaltung/bareinnahmen`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    const monat = new Date().toISOString().slice(0, 7);
    await page.getByLabel('Monat/Jahr').fill(monat);

    const monatsdruck = await page.getByRole('link', { name: 'Monat drucken' }).getAttribute('href');
    expect(monatsdruck).toContain(`/monat/${monat}/pdf`);

    const antwort = await page.request.get(monatsdruck ?? '');
    expect(antwort.status()).toBe(200);

    const inhalt = await antwort.text();
    expect(inhalt.slice(0, 4)).toBe('%PDF');
});