import { defineConfig, devices } from '@playwright/test';
import dotenv from 'dotenv';
import { resolve } from 'node:path';

const __dirname = import.meta.dirname;
const repoRoot = resolve(__dirname, '..');

dotenv.config({ path: resolve(__dirname, '.env.e2e') });

const baseURL = process.env.APP_URL ?? 'http://127.0.0.1:8001';

export default defineConfig({
    testDir: './e2e',
    outputDir: resolve(__dirname, 'results'),
    fullyParallel: true,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 1 : 0,
    workers: process.env.CI ? 4 : undefined,
    reporter: [['line'], ['html', { outputFolder: resolve(__dirname, 'report') }]],
    globalSetup: resolve(__dirname, 'global.setup.ts'),
    use: {
        baseURL,
        trace: 'on-first-retry',
        video: 'retain-on-failure',
        screenshot: 'only-on-failure',
        actionTimeout: 10_000,
        navigationTimeout: 10_000,
    },
    projects: [
        {
            name: 'chromium',
            use: {
                ...devices['Desktop Chrome'],
                // German locale so the UI uses the expected labels.
                locale: 'de-DE',
            },
        },
    ],
    webServer: {
        command: './start-app.sh',
        cwd: __dirname,
        url: `${baseURL}/up`,
        reuseExistingServer: !process.env.CI,
        timeout: 60_000,
        env: {
            APP_BASE_PATH: 'src',
        },
    },
});
