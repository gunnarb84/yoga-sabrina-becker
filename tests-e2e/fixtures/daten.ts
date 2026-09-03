import type { Page } from '@playwright/test';
import { eindeutigeKennung } from './kennung.js';

export interface AktivitaetsDaten {
    slug: string;
    titel: string;
}

/**
 * Creates a published activity with one future session through the administration UI.
 * The returned slug can be used by public-site tests.
 */
export async function angelegteOeffentlicheAktivitaet(
    page: Page,
    workerIndex: number = 0,
): Promise<AktivitaetsDaten> {
    const baseURL = process.env.APP_URL ?? 'http://127.0.0.1:8001';
    const id = eindeutigeKennung(workerIndex);
    const titel = `E2E Yoga Flow ${id}`;

    await page.goto(`${baseURL}/verwaltung/aktivitaeten/neu`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await page.getByLabel('Typ').selectOption('kurs');
    await page.getByLabel('Titel').fill(titel);
    await page.getByLabel('Kurzbeschreibung').fill(`Kurzbeschreibung ${id}`);
    await page.getByLabel('Langbeschreibung').fill(`Langbeschreibung ${id}`);
    await page.getByLabel('Preis (EUR)').fill('25.00');
    await page.getByLabel('Maximale Teilnehmerzahl').fill('10');
    await page.getByRole('button', { name: 'Speichern' }).click();

    await page.getByText('Die Aktivitaet wurde angelegt.').waitFor();

    // Add a future session.
    await page.goto(`${baseURL}/verwaltung/aktivitaeten`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    const zeile = page.getByRole('row', { name: new RegExp(titel, 'i') });
    await zeile.getByRole('link', { name: 'Termin' }).click();
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    const anfang = terminInZukunft();
    const ende = new Date(anfang.getTime() + 90 * 60_000);

    await page.getByLabel('Beginn').fill(formatDateTimeLocal(anfang));
    await page.getByLabel('Ende').fill(formatDateTimeLocal(ende));
    await page.getByLabel('Ort').fill('Studio E2E');
    await page.getByLabel('Hinweis').fill('Bitte Matte mitbringen.');
    await page.getByRole('button', { name: 'Speichern' }).click();

    await page.getByText('Der Termin wurde angelegt.').waitFor();

    // Publish the activity.
    await page.goto(`${baseURL}/verwaltung/aktivitaeten`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    await zeile.getByRole('button', { name: 'Veroeffentlichen' }).click();

    // Wait for the status to update; published rows no longer show the publish button.
    await zeile.getByRole('button', { name: 'Veroeffentlichen' }).waitFor({ state: 'detached' });

    // Discover the public slug from the public site list.
    await page.goto(`${baseURL}/kurse`);
    await page.locator('body[data-livewire-ready="true"]').waitFor();

    const link = page.getByRole('article').filter({ hasText: titel }).getByRole('link');
    const href = await link.getAttribute('href');

    if (href === null) {
        throw new Error(`Could not discover public slug for activity "${titel}".`);
    }

    const slug = href.split('/').pop();

    if (slug === undefined || slug === '') {
        throw new Error(`Could not parse slug from href "${href}".`);
    }

    return { slug, titel };
}

function terminInZukunft(): Date {
    const jetzt = new Date();
    const termin = new Date(jetzt.getFullYear() + 1, 0, 15, 10, 0, 0, 0);

    if (termin <= jetzt) {
        termin.setFullYear(jetzt.getFullYear() + 2);
    }

    return termin;
}

function formatDateTimeLocal(datum: Date): string {
    const jahr = datum.getFullYear();
    const monat = String(datum.getMonth() + 1).padStart(2, '0');
    const tag = String(datum.getDate()).padStart(2, '0');
    const stunde = String(datum.getHours()).padStart(2, '0');
    const minute = String(datum.getMinutes()).padStart(2, '0');

    return `${jahr}-${monat}-${tag}T${stunde}:${minute}`;
}
