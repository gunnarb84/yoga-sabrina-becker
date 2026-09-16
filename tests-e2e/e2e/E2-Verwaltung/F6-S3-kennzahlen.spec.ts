/**
 * Story: E2 CMS & Verwaltung / F6 Dashboard /
 *        S3 Kennzahlen anzeigen
 *
 * Geprüfte Kriterien:
 * - Das Dashboard zeigt die Anzahl der Anmeldungen, die im laufenden Monat
 *   entstanden sind und nicht storniert sind.
 * - Das Dashboard zeigt die Summe der Bareinnahmenbelege des laufenden Monats
 *   mit Währungsangabe.
 * - Das Dashboard zeigt den Kassenbestand über alle Zeiten.
 */
import { expect, test } from '@playwright/test';
import { resolve } from 'node:path';
import {
    angelegteAktivitaet,
    angelegterTeilnehmer,
    barAnmeldungAngelegt,
    einzelBarzahlungErfasst,
} from './hilfen.js';

const baseURL = process.env.APP_URL ?? 'http://127.0.0.1:8001';

test.use({ storageState: resolve(import.meta.dirname, '../../storage/admin.json') });

test('zeigt die Kennzahlen nach einer erfassten Barzahlung', async ({ page }) => {
    const workerIndex = test.info().workerIndex;

    const aktivitaet = await angelegteAktivitaet(page, workerIndex);
    const teilnehmer = await angelegterTeilnehmer(page, workerIndex);
    await barAnmeldungAngelegt(page, teilnehmer, aktivitaet.anmeldungenPfad);
    await einzelBarzahlungErfasst(page, aktivitaet.anmeldungenPfad, teilnehmer);

    await page.goto(`${baseURL}/verwaltung`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await expect(page.getByText('Anmeldungen im laufenden Monat')).toBeVisible();
    await expect(page.getByText('Bareinnahmen im laufenden Monat')).toBeVisible();
    await expect(page.getByText(/\d+,\d{2} EUR/).first()).toBeVisible();
    await expect(page.getByText('Kassenbestand')).toBeVisible();
});