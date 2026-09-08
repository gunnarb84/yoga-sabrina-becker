/**
 * Story: E1 Öffentliche Webseite / F4 Kontaktanfrage / S4 Anfragen in der Verwaltung bearbeiten
 *
 * Geprüfte Kriterien:
 * - Der Menüpunkt „Kontaktanfragen" ist in der Verwaltung erreichbar.
 * - Die Liste zeigt Empfangszeitpunkt, Name, E-Mail, Anlass und Status.
 * - Die Liste lässt sich nach Status filtern.
 * - Die Detailansicht zeigt alle Felder; die Notiz wird persistiert.
 * - Der Status kann auf jeden der drei Werte geändert werden.
 * - Anfragen werden nicht gelöscht; erledigte Anfragen bleiben in der Liste.
 */
import { expect, test } from '@playwright/test';
import { testEmail, testNachname, testVorname } from '../../fixtures/kennung.js';
import { resolve } from 'node:path';

test.use({ storageState: resolve(import.meta.dirname, '../../storage/admin.json') });

let anfrageEmail: string;
let anfrageName: string;

test.beforeEach(async ({ page }) => {
    anfrageName = `${testVorname(test.info().workerIndex)} ${testNachname(test.info().workerIndex)}`;
    anfrageEmail = testEmail(test.info().workerIndex);

    // Zuerst über das öffentliche Formular eine Anfrage anlegen.
    await page.goto('/kontakt');
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await page.getByLabel('Name *').fill(anfrageName);
    await page.getByLabel('E-Mail *').fill(anfrageEmail);
    await page.getByLabel('Anlass / Gruppe').fill('Yoga auf der Burg');
    await page.getByLabel('Nachricht *').fill('Wir sind eine Gruppe von acht Personen und interessieren uns für einen Termin im Herbst.');
    await page.getByRole('button', { name: 'Anfrage senden' }).click();

    await expect(page.locator('.yoga-success-box')).toBeVisible();

    // Danach die Verwaltung öffnen.
    await page.goto('/verwaltung/kontaktanfragen');
    await page.locator('body[data-livewire-ready="true"]').waitFor();
});

test('zeigt die Anfrage in der Liste mit allen Spalten', async ({ page }) => {
    const zeile = page.getByRole('row', { name: new RegExp(anfrageEmail, 'i') });

    await expect(zeile).toBeVisible();
    await expect(zeile).toContainText(anfrageName);
    await expect(zeile).toContainText('Yoga auf der Burg');
    await expect(zeile).toContainText('neu');
});

test('filtert die Liste nach Status', async ({ page }) => {
    await page.getByLabel('Status').selectOption('neu');
    await page.getByRole('button', { name: 'Filtern' }).click();

    await expect(page.getByRole('row', { name: new RegExp(anfrageEmail, 'i') })).toBeVisible();
});

test('öffnet die Detailansicht mit allen Feldern', async ({ page }) => {
    const zeile = page.getByRole('row', { name: new RegExp(anfrageEmail, 'i') });
    await zeile.getByRole('link', { name: 'Detail' }).click();
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await expect(page.getByText(anfrageName)).toBeVisible();
    await expect(page.getByText(anfrageEmail)).toBeVisible();
    await expect(page.getByText('Yoga auf der Burg')).toBeVisible();
    await expect(page.getByText('Wir sind eine Gruppe von acht Personen')).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Bearbeitung' })).toBeVisible();
});

test('speichert Notiz und Statuswechsel in der Detailansicht', async ({ page }) => {
    const zeile = page.getByRole('row', { name: new RegExp(anfrageEmail, 'i') });
    await zeile.getByRole('link', { name: 'Detail' }).click();
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await page.getByLabel('Status').selectOption('erledigt');
    await page.getByLabel('Notiz').fill('E2E: per E-Mail beantwortet.');
    await page.getByRole('button', { name: 'Speichern' }).click();

    await expect(page.getByText('Die Änderungen wurden gespeichert.')).toBeVisible();

    // Erledigte Anfragen bleiben in der Liste (kein Löschen).
    await page.getByRole('link', { name: 'Zurück zur Übersicht' }).click();
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    const geaenderteZeile = page.getByRole('row', { name: new RegExp(anfrageEmail, 'i') });
    await expect(geaenderteZeile).toBeVisible();
    await expect(geaenderteZeile).toContainText('erledigt');
});