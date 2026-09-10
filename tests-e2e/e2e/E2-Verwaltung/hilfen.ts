import type { Page } from '@playwright/test';
import { eindeutigeKennung, testEmail, testNachname, testVorname } from '../../fixtures/kennung.js';

const baseURL = process.env.APP_URL ?? 'http://127.0.0.1:8001';

export interface Aktivitaet {
    titel: string;
    /** Pfad der Anmeldungsliste der Veranstaltung. */
    anmeldungenPfad: string;
}

/**
 * Legt eine unveröffentlichte Aktivität über die Verwaltung an und öffnet deren
 * Anmeldungsliste. Der Preis ist der Barzahlungspreis der F4-Stories.
 */
export async function angelegteAktivitaet(page: Page, workerIndex: number): Promise<Aktivitaet> {
    const id = eindeutigeKennung(workerIndex);
    const titel = `E2E Barzahlung ${id}`;

    await page.goto(`${baseURL}/verwaltung/aktivitaeten/neu`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await page.getByLabel('Typ').selectOption('kurs');
    await page.getByLabel('Titel').fill(titel);
    await page.getByLabel('Kurzbeschreibung').fill(`Kurzbeschreibung ${id}`);
    await page.getByLabel('Langbeschreibung').fill(`Langbeschreibung ${id}`);
    await page.getByLabel('Preis (EUR)').fill('45.00');
    await page.getByLabel('Maximale Teilnehmerzahl').fill('10');
    await page.getByRole('button', { name: 'Speichern' }).click();

    await page.getByText('Die Aktivität wurde angelegt.').waitFor();

    await page.goto(`${baseURL}/verwaltung/aktivitaeten`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    const zeile = page.getByRole('row', { name: new RegExp(titel, 'i') });
    await zeile.getByRole('link', { name: 'Anmeldungen' }).click();
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    return { titel, anmeldungenPfad: page.url() };
}

export interface Teilnehmer {
    vorname: string;
    nachname: string;
    email: string;
}

/** Legt eine Teilnehmerin/einen Teilnehmer über die Verwaltung an. */
export async function angelegterTeilnehmer(page: Page, workerIndex: number): Promise<Teilnehmer> {
    const vorname = testVorname(workerIndex);
    const nachname = testNachname(workerIndex);
    const email = testEmail(workerIndex);

    await page.goto(`${baseURL}/verwaltung/teilnehmer/neu`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await page.getByLabel('E-Mail').fill(email);
    await page.getByLabel('Vorname').fill(vorname);
    await page.getByLabel('Nachname').fill(nachname);
    await page.getByRole('button', { name: 'Speichern' }).click();

    await page.getByText('Der Teilnehmer wurde angelegt.').waitFor();

    return { vorname, nachname, email };
}

/** Meldet die Teilnehmerin/den Teilnehmer über die Anmeldemaske mit Zahlungsart Bar an. */
export async function barAnmeldungAngelegt(page: Page, teilnehmer: Teilnehmer, anmeldungenPfad: string): Promise<void> {
    await anmeldungAngelegt(page, teilnehmer, 'bar', anmeldungenPfad);
}

/** Meldet die Teilnehmerin/den Teilnehmer über die Anmeldemaske mit Überweisung an. */
export async function ueberweisungsAnmeldungAngelegt(page: Page, teilnehmer: Teilnehmer, anmeldungenPfad: string): Promise<void> {
    await anmeldungAngelegt(page, teilnehmer, 'ueberweisung', anmeldungenPfad);
}

async function anmeldungAngelegt(page: Page, teilnehmer: Teilnehmer, zahlungsart: string, anmeldungenPfad: string): Promise<void> {
    await page.goto(anmeldungenPfad);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await page.getByRole('link', { name: 'Teilnehmer anmelden' }).click();
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await page.getByLabel('Teilnehmer').selectOption({
        label: `${teilnehmer.vorname} ${teilnehmer.nachname} (${teilnehmer.email})`,
    });
    await page.getByLabel('Zahlungsart').selectOption(zahlungsart);
    await page.getByRole('button', { name: 'Anmelden' }).click();

    await page.getByText('Die Anmeldung wurde durchgeführt.').waitFor();
}

export interface MasseSeite {
    massePfad: string;
}

/** Öffnet über die Anmeldungsliste die Massenerfassung der Barzahlungen. */
export async function masseSeiteGeoeffnet(page: Page, anmeldungenPfad: string): Promise<MasseSeite> {
    await page.goto(anmeldungenPfad);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await page.getByRole('link', { name: 'Barzahlungen massenweise erfassen' }).click();
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    return { massePfad: page.url() };
}

/**
 * Erfasst die Barzahlung der Teilnehmerin/des Teilnehmers über die Massenerfassung
 * (eine ausgewählte Zeile, ein Zahlungsvorgang) und kehrt auf der Massenmaske zurück.
 */
export async function einzelBarzahlungErfasst(page: Page, anmeldungenPfad: string, teilnehmer: Teilnehmer): Promise<void> {
    await masseSeiteGeoeffnet(page, anmeldungenPfad);

    const zeile = page.getByRole('row', { name: new RegExp(teilnehmer.nachname) });
    await zeile.getByRole('checkbox').check();
    await page.getByRole('button', { name: 'Ausgewählte Zahlungen speichern' }).click();

    await page.getByText('1 Zahlung(en) wurden erfasst.').waitFor();
}