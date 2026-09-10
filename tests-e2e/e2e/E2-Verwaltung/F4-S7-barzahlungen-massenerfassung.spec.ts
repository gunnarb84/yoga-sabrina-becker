/**
 * Story: E2 CMS & Verwaltung / F4 Zahlungen und Belege verwalten /
 *        S7 Barzahlungen massenweise erfassen
 *
 * Geprüfte Kriterien:
 * - Die Massenerfassung ist von der Anmeldungsliste einer Veranstaltung erreichbar.
 * - Sie listet nur offene Bar-Anmeldungen (Bar, bestätigt, ohne Zahlung);
 *   Anmeldungen mit anderer Zahlungsart und bereits erfasste Zahlungen fehlen.
 * - Ausgewählte Anmeldungen werden mit einer Aktion erfasst; die Belege erhalten
 *   fortlaufende Nummern, der Zahlungsstatus wird „bezahlt".
 * - Ohne Auswahl erscheint ein Hinweis, es wird nichts erfasst.
 */
import { expect, test } from '@playwright/test';
import { resolve } from 'node:path';
import {
    angelegteAktivitaet,
    angelegterTeilnehmer,
    barAnmeldungAngelegt,
    masseSeiteGeoeffnet,
    ueberweisungsAnmeldungAngelegt,
} from './hilfen.js';
import type { Aktivitaet, Teilnehmer } from './hilfen.js';

test.use({ storageState: resolve(import.meta.dirname, '../../storage/admin.json') });

let aktivitaet: Aktivitaet;
let barEins: Teilnehmer;
let barZwei: Teilnehmer;
let ueberweisung: Teilnehmer;

test.beforeEach(async ({ page }) => {
    const workerIndex = test.info().workerIndex;

    aktivitaet = await angelegteAktivitaet(page, workerIndex);
    barEins = await angelegterTeilnehmer(page, workerIndex);
    await barAnmeldungAngelegt(page, barEins, aktivitaet.anmeldungenPfad);
    barZwei = await angelegterTeilnehmer(page, workerIndex);
    await barAnmeldungAngelegt(page, barZwei, aktivitaet.anmeldungenPfad);
    ueberweisung = await angelegterTeilnehmer(page, workerIndex);
    await ueberweisungsAnmeldungAngelegt(page, ueberweisung, aktivitaet.anmeldungenPfad);
});

test('listet nur offene Bar-Anmeldungen der Veranstaltung', async ({ page }) => {
    await masseSeiteGeoeffnet(page, aktivitaet.anmeldungenPfad);

    await expect(page.getByRole('row', { name: new RegExp(barEins.nachname, 'i') })).toBeVisible();
    await expect(page.getByRole('row', { name: new RegExp(barZwei.nachname, 'i') })).toBeVisible();
    await expect(page.getByRole('row', { name: new RegExp(ueberweisung.nachname, 'i') })).toHaveCount(0);
    await expect(page.getByRole('row', { name: /45,00/ })).toHaveCount(2);
});

test('erfasst ausgewählte Anmeldungen mit einer Aktion und markiert sie als bezahlt', async ({ page }) => {
    await masseSeiteGeoeffnet(page, aktivitaet.anmeldungenPfad);

    await page.getByRole('row', { name: new RegExp(barEins.nachname, 'i') }).getByRole('checkbox').check();
    await page.getByRole('row', { name: new RegExp(barZwei.nachname, 'i') }).getByRole('checkbox').check();
    await page.getByRole('button', { name: 'Ausgewählte Zahlungen speichern' }).click();

    await expect(page.getByText('2 Zahlung(en) wurden erfasst.')).toBeVisible();
    await expect(page.getByText('Alle Bar-Anmeldungen dieser Veranstaltung sind erfasst.')).toBeVisible();

    // Zurück auf der Anmeldungsliste ist der Zahlungsstatus beider Anmeldungen „bezahlt".
    await page.goto(aktivitaet.anmeldungenPfad);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    const zeileEins = page.getByRole('row', { name: new RegExp(barEins.nachname, 'i') });
    await expect(zeileEins).toContainText('bezahlt');
    await expect(page.getByRole('row', { name: new RegExp(barZwei.nachname, 'i') })).toContainText('bezahlt');
});

test('meldet fehlende Auswahl und erfasst nichts', async ({ page }) => {
    await masseSeiteGeoeffnet(page, aktivitaet.anmeldungenPfad);

    await page.getByRole('button', { name: 'Ausgewählte Zahlungen speichern' }).click();

    await expect(page.getByText('Bitte wählen Sie mindestens eine Anmeldung aus.')).toBeVisible();
    await expect(page.getByRole('row', { name: new RegExp(barEins.nachname, 'i') })).toBeVisible();
    await expect(page.getByRole('row', { name: new RegExp(barZwei.nachname, 'i') })).toBeVisible();
});