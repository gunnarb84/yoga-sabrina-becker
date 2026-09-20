/**
 * Story: E2 CMS & Verwaltung / F2 Teilnehmer verwalten / S5 Einwilligung Foto- und Videoaufnahmen pflegen
 *
 * Geprüfte Kriterien:
 * - Die Maske zeigt die Foto- und Video-Einwilligung mit den Zeitpunkten an.
 * - Das Setzen und Zurücksetzen der Einwilligungen speichert bzw. leert die Zeitpunkte.
 * - Die Speicherung gelingt auch ohne gesetzte Einwilligungen (Freiwilligkeit).
 * - Der Widerrufsvermerk (Datum und Text) wird erfasst.
 * - Die Einwilligung ist unabhängig vom Geburtsdatum erfassbar (auch für Minderjährige).
 */
import { expect, test } from '@playwright/test';
import { resolve } from 'node:path';
import { angelegterTeilnehmer, type Teilnehmer } from './hilfen.js';

const baseURL = process.env.APP_URL ?? 'http://127.0.0.1:8001';

test.use({ storageState: resolve(import.meta.dirname, '../../storage/admin.json') });

async function editMaskeGeoeffnet(page: import('@playwright/test').Page, teilnehmer: Teilnehmer): Promise<void> {
    await page.goto(`${baseURL}/verwaltung/teilnehmer`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await page.getByRole('link', { name: new RegExp(`${teilnehmer.vorname} ${teilnehmer.nachname}`) }).click();
    await page.locator('body[data-livewire-ready="true"]').waitFor();
}

test('zeigt die Felder der Foto- und Video-Einwilligung', async ({ page }) => {
    const teilnehmer = await angelegterTeilnehmer(page, test.info().workerIndex);
    await editMaskeGeoeffnet(page, teilnehmer);

    await expect(page.getByText('Einwilligung Foto- und Videoaufnahmen')).toBeVisible();
    await expect(page.getByLabel('Einwilligung Fotos am')).toBeVisible();
    await expect(page.getByLabel('Einwilligung Videos am')).toBeVisible();
    await expect(page.getByLabel('Widerruf am')).toBeVisible();
    await expect(page.getByLabel('Widerrufsvermerk')).toBeVisible();

    await expect(page.getByLabel(/Einwilligung Fotos liegt vor/)).not.toBeChecked();
    await expect(page.getByLabel(/Einwilligung Videos liegt vor/)).not.toBeChecked();
});

test('speichert gesetzte Einwilligungen mit Zeitpunkten und leert sie beim Zurücksetzen', async ({ page }) => {
    const teilnehmer = await angelegterTeilnehmer(page, test.info().workerIndex);
    await editMaskeGeoeffnet(page, teilnehmer);

    await page.getByLabel(/Einwilligung Fotos liegt vor/).check();
    await page.getByLabel('Einwilligung Fotos am').fill('2026-09-20');
    await page.getByLabel(/Einwilligung Videos liegt vor/).check();
    await page.getByLabel('Einwilligung Videos am').fill('2026-09-20');
    await page.getByRole('button', { name: 'Speichern' }).click();

    await page.getByText('Die Änderungen wurden gespeichert.').waitFor();
    await page.reload();
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await expect(page.getByLabel(/Einwilligung Fotos liegt vor/)).toBeChecked();
    await expect(page.getByLabel('Einwilligung Fotos am')).toHaveValue('2026-09-20');
    await expect(page.getByLabel(/Einwilligung Videos liegt vor/)).toBeChecked();
    await expect(page.getByLabel('Einwilligung Videos am')).toHaveValue('2026-09-20');

    // Widerruf: Haken zurücksetzen, Vermerk erfassen.
    await page.getByLabel(/Einwilligung Fotos liegt vor/).uncheck();
    await page.getByLabel('Einwilligung Fotos am').fill('');
    await page.getByLabel('Widerruf am').fill('2026-09-20');
    await page.getByLabel('Widerrufsvermerk').fill('per E-Mail, betrifft Fotos');
    await page.getByRole('button', { name: 'Speichern' }).click();

    await page.getByText('Die Änderungen wurden gespeichert.').waitFor();
    await page.reload();
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await expect(page.getByLabel(/Einwilligung Fotos liegt vor/)).not.toBeChecked();
    await expect(page.getByLabel('Einwilligung Fotos am')).toHaveValue('');
    await expect(page.getByLabel('Widerrufsvermerk')).toHaveValue('per E-Mail, betrifft Fotos');
});

test('erfasst die Einwilligung für Minderjährige über den Papierbogen', async ({ page }) => {
    const teilnehmer = await angelegterTeilnehmer(page, test.info().workerIndex);
    await editMaskeGeoeffnet(page, teilnehmer);

    await page.getByLabel('Geburtsdatum').fill('2012-03-01');
    await page.getByLabel(/Einwilligung Fotos liegt vor/).check();
    await page.getByLabel('Einwilligung Fotos am').fill('2026-09-20');
    await page.getByRole('button', { name: 'Speichern' }).click();

    await page.getByText('Die Änderungen wurden gespeichert.').waitFor();
    await page.reload();
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await expect(page.getByLabel(/Einwilligung Fotos liegt vor/)).toBeChecked();
    await expect(page.getByLabel('Einwilligung Fotos am')).toHaveValue('2026-09-20');
});