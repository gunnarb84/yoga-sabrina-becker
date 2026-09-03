import { chromium, type FullConfig } from '@playwright/test';
import { execSync } from 'node:child_process';
import { mkdirSync, writeFileSync } from 'node:fs';
import { resolve } from 'node:path';
import { anmeldenAlsVerwaltung } from './fixtures/anmeldung.js';
import { angelegteOeffentlicheAktivitaet } from './fixtures/daten.js';

const repoRoot = resolve(import.meta.dirname, '..');
const storageDir = resolve(import.meta.dirname, 'storage');
const stateFile = resolve(storageDir, 'state.json');
const adminStateFile = resolve(storageDir, 'admin.json');

export default async function globalSetup(config: FullConfig): Promise<void> {
    const env = config.projects[0]?.use?.locale ?? 'de-DE';
    const workerIndex = 0;

    // Ensure the dedicated e2e database exists.
    execSync(
        'mysql -uroot -e "CREATE DATABASE IF NOT EXISTS yoga_e2e CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"',
        { cwd: repoRoot, stdio: 'inherit' },
    );

    // Run migrations and the default seeder (creates the admin account).
    execSync('php src/artisan migrate:fresh --seed --force --no-interaction', {
        cwd: repoRoot,
        env: {
            ...process.env,
            APP_BASE_PATH: 'src',
        },
        stdio: 'inherit',
    });

    const browser = await chromium.launch();
    const context = await browser.newContext({ locale: env });
    const page = await context.newPage();

    const adminEmail = process.env.E2E_ADMIN_EMAIL ?? 'sabrina@example.com';
    const adminPassword = process.env.E2E_ADMIN_PASSWORD ?? 'yoga2026';

    await anmeldenAlsVerwaltung(page, adminEmail, adminPassword);

    await context.storageState({ path: adminStateFile });

    const aktivitaet = await angelegteOeffentlicheAktivitaet(page, workerIndex);

    mkdirSync(storageDir, { recursive: true });
    writeFileSync(stateFile, JSON.stringify(aktivitaet, null, 2));

    await context.close();
    await browser.close();
}
