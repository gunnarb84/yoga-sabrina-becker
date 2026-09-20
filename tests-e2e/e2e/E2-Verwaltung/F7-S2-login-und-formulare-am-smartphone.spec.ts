/**
 * Story: E2 CMS & Verwaltung / F7 Mobilfähigkeit / S2 Login und Formulare am Smartphone
 *
 * Geprüfte Kriterien:
 * - Die Login-Karte ist bei ≤ 480 px vollständig sichtbar, ohne horizontales Scrollen.
 * - Formulare der Verwaltung sind bei ≤ 480 px einspaltig.
 * - Alle Eingabefelder sind bei ≤ 480 px vollständig sichtbar, ohne horizontales Scrollen.
 * - Schaltflächen sind bei ≤ 480 px sichtbar und auslösbar.
 * - Bei > 480 px bleibt die Desktop-Darstellung von Login-Karte und Formularen unverändert.
 */
import { expect, test } from '@playwright/test';
import { resolve } from 'node:path';

const baseURL = process.env.APP_URL ?? 'http://127.0.0.1:8001';

test.use({ storageState: resolve(import.meta.dirname, '../../storage/admin.json') });

const formularFelder = (page: import('@playwright/test').Page) =>
    page.locator('.yoga-form .au-field input, .yoga-form .au-field select, .yoga-form .au-field textarea');

/** Liefert die linken Kanten aller Felder — bei einspaltigem Layout sind sie identisch. */
const feldLinkeKanten = (page: import('@playwright/test').Page) =>
    formularFelder(page).evaluateAll((felder) =>
        felder.map((feld) => feld.getBoundingClientRect().left),
    );

test.describe('Login und Formulare auf dem Smartphone', () => {
    test.use({ viewport: { width: 375, height: 812 } });

    test('zeigt die Login-Karte vollständig ohne horizontales Scrollen', async ({ page }) => {
        await page.context().clearCookies();

        await page.goto(`${baseURL}/verwaltung/login`);
        await page.locator('.au-login__card').waitFor();

        const karte = page.locator('.au-login__card');
        const box = await karte.boundingBox();
        expect(box).not.toBeNull();
        expect(box!.x).toBeGreaterThanOrEqual(0);
        expect(box!.x + box!.width).toBeLessThanOrEqual(375);

        const scroll = await page.evaluate(() => ({
            scrollWidth: document.documentElement.scrollWidth,
            clientWidth: document.documentElement.clientWidth,
        }));
        expect(scroll.scrollWidth).toBeLessThanOrEqual(scroll.clientWidth);

        await expect(page.getByLabel('E-Mail')).toBeVisible();
        await expect(page.getByLabel('Passwort')).toBeVisible();
        await expect(page.getByRole('button', { name: 'Anmelden' })).toBeEnabled();
    });

    test('stellt das Teilnehmer-Formular einspaltig und vollständig sichtbar dar', async ({ page }) => {
        await page.goto(`${baseURL}/verwaltung/teilnehmer/neu`);
        await page.locator('body[data-livewire-ready="true"]').waitFor();

        const kanten = await feldLinkeKanten(page);
        expect(kanten.length).toBeGreaterThan(1);
        expect(new Set(kanten.map((kante) => Math.round(kante))).size).toBe(1);

        for (const feld of await formularFelder(page).all()) {
            const box = await feld.boundingBox();
            expect(box, 'Eingabefeld vollständig im Viewport').not.toBeNull();
            expect(box!.x).toBeGreaterThanOrEqual(0);
            expect(box!.x + box!.width).toBeLessThanOrEqual(375);
        }

        await expect(page.getByRole('button', { name: 'Speichern' })).toBeVisible();
        await expect(page.getByRole('button', { name: 'Speichern' })).toBeEnabled();
    });

    test('stellt das Aktivitäts-Formular einspaltig und vollständig sichtbar dar', async ({ page }) => {
        await page.goto(`${baseURL}/verwaltung/aktivitaeten/neu`);
        await page.locator('body[data-livewire-ready="true"]').waitFor();

        const kanten = await feldLinkeKanten(page);
        expect(kanten.length).toBeGreaterThan(1);
        expect(new Set(kanten.map((kante) => Math.round(kante))).size).toBe(1);

        const scroll = await page.evaluate(() => ({
            scrollWidth: document.documentElement.scrollWidth,
            clientWidth: document.documentElement.clientWidth,
        }));
        expect(scroll.scrollWidth).toBeLessThanOrEqual(scroll.clientWidth);

        await expect(page.getByLabel('Titel')).toBeVisible();
        await expect(page.getByLabel('Preis (EUR)')).toBeVisible();
        await expect(page.getByRole('button', { name: 'Speichern' })).toBeVisible();
        await expect(page.getByRole('button', { name: 'Speichern' })).toBeEnabled();
    });
});

test.describe('Login und Formulare auf dem Desktop', () => {
    test.use({ viewport: { width: 1280, height: 800 } });

    test('behält die Desktop-Darstellung von Login-Karte und Formularen', async ({ page }) => {
        await page.goto(`${baseURL}/verwaltung/teilnehmer/neu`);
        await page.locator('body[data-livewire-ready="true"]').waitFor();

        const kanten = await feldLinkeKanten(page);
        expect(new Set(kanten.map((kante) => Math.round(kante))).size).toBe(2);

        await page.context().clearCookies();

        await page.goto(`${baseURL}/verwaltung/login`);
        await page.locator('.au-login__card').waitFor();

        const karte = page.locator('.au-login__card');
        await expect(karte).toHaveCSS('width', '404px');
    });
});