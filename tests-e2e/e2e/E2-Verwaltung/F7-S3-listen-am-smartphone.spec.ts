/**
 * Story: E2 CMS & Verwaltung / F7 Mobilfähigkeit / S3 Listen am Smartphone
 *
 * Geprüfte Kriterien:
 * - Listen sind bei ≤ 480 px innerhalb eines Scroll-Bereichs horizontal scrollbar.
 * - Beim Scrollen bewegt sich nur der Listenbereich; die Seite wird nicht horizontal gescrollt.
 * - Alle Spalten einer Liste — einschließlich der Zeilen-Aktionen — sind erreichbar.
 * - Die Kopfzeile ist im Scroll-Bereich sichtbar.
 * - Bei > 480 px bleibt die Desktop-Darstellung unverändert.
 */
import { expect, test } from '@playwright/test';
import { resolve } from 'node:path';
import {
    angelegteAktivitaet,
    angelegterTeilnehmer,
    barAnmeldungAngelegt,
    einzelBarzahlungErfasst,
} from './hilfen.js';
import type { Teilnehmer } from './hilfen.js';

const baseURL = process.env.APP_URL ?? 'http://127.0.0.1:8001';

test.use({ storageState: resolve(import.meta.dirname, '../../storage/admin.json') });

const listWrapper = (page: import('@playwright/test').Page) => page.locator('table.au-list').locator('..');

test.describe('Listen auf dem Smartphone', () => {
    test.use({ viewport: { width: 375, height: 812 } });

    let teilnehmer: Teilnehmer;

    test.beforeEach(async ({ page }) => {
        const workerIndex = test.info().workerIndex;

        const aktivitaet = await angelegteAktivitaet(page, workerIndex);
        teilnehmer = await angelegterTeilnehmer(page, workerIndex);
        await barAnmeldungAngelegt(page, teilnehmer, aktivitaet.anmeldungenPfad);
        await einzelBarzahlungErfasst(page, aktivitaet.anmeldungenPfad, teilnehmer);
    });

    test('scrollt die Bareinnahmenliste horizontal im Wrapper', async ({ page }) => {
        await page.goto(`${baseURL}/verwaltung/bareinnahmen`);
        await page.locator('body[data-livewire-ready="true"]').waitFor();

        const wrapper = listWrapper(page);
        const lauffaehig = await wrapper.evaluate((el) => ({
            scrollWidth: el.scrollWidth,
            clientWidth: el.clientWidth,
        }));
        expect(lauffaehig.scrollWidth).toBeGreaterThan(lauffaehig.clientWidth);
    });

    test('scrollt nur den Listenbereich, nicht die Seite', async ({ page }) => {
        await page.goto(`${baseURL}/verwaltung/bareinnahmen`);
        await page.locator('body[data-livewire-ready="true"]').waitFor();

        const wrapper = listWrapper(page);
        await wrapper.evaluate((el) => { el.scrollLeft = el.scrollWidth; });

        const scroll = await page.evaluate(() => ({
            scrollX: window.scrollX,
            scrollWidth: document.documentElement.scrollWidth,
            clientWidth: document.documentElement.clientWidth,
        }));
        expect(scroll.scrollX).toBe(0);
        expect(scroll.scrollWidth).toBeLessThanOrEqual(scroll.clientWidth);
    });

    test('macht alle Spalten einschließlich der Zeilen-Aktionen erreichbar', async ({ page }) => {
        await page.goto(`${baseURL}/verwaltung/bareinnahmen`);
        await page.locator('body[data-livewire-ready="true"]').waitFor();

        const wrapper = listWrapper(page);
        await wrapper.evaluate((el) => { el.scrollLeft = el.scrollWidth; });

        const wrapperBox = await wrapper.boundingBox();
        const aktion = page.getByRole('row', { name: new RegExp(teilnehmer.nachname, 'i') })
            .getByRole('link', { name: 'PDF' });
        const aktionsBox = await aktion.boundingBox();

        expect(wrapperBox).not.toBeNull();
        expect(aktionsBox).not.toBeNull();
        expect(aktionsBox!.x + aktionsBox!.width).toBeLessThanOrEqual(wrapperBox!.x + wrapperBox!.width + 1);
        expect(aktionsBox!.x).toBeGreaterThanOrEqual(wrapperBox!.x);
    });

    test('zeigt die Kopfzeile im Scroll-Bereich', async ({ page }) => {
        await page.goto(`${baseURL}/verwaltung/bareinnahmen`);
        await page.locator('body[data-livewire-ready="true"]').waitFor();

        const kopf = page.locator('table.au-list th').first();
        await expect(kopf).toBeVisible();
        await expect(kopf).toContainText(/datum/i);
    });

    test('scrollt auch die Teilnehmerliste horizontal im Wrapper', async ({ page }) => {
        await page.goto(`${baseURL}/verwaltung/teilnehmer`);
        await page.locator('body[data-livewire-ready="true"]').waitFor();

        const wrapper = listWrapper(page);
        const lauffaehig = await wrapper.evaluate((el) => ({
            scrollWidth: el.scrollWidth,
            clientWidth: el.clientWidth,
        }));
        expect(lauffaehig.scrollWidth).toBeGreaterThan(lauffaehig.clientWidth);
        await expect(page.locator('table.au-list th').first()).toBeVisible();
    });
});

test.describe('Listen auf dem Desktop', () => {
    test.use({ viewport: { width: 1280, height: 800 } });

    test('behält die Desktop-Darstellung der Listen ohne Mindestbreite', async ({ page }) => {
        await angelegterTeilnehmer(page, test.info().workerIndex + 20);

        await page.goto(`${baseURL}/verwaltung/teilnehmer`);
        await page.locator('body[data-livewire-ready="true"]').waitFor();

        const mindestbreite = await page.locator('table.au-list').evaluate(
            (el) => getComputedStyle(el).minWidth,
        );
        expect(mindestbreite).not.toBe('640px');
    });
});