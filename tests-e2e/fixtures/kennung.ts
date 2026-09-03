import { randomUUID } from 'node:crypto';

/**
 * Produces a test-unique identifier that can be embedded into names, e-mails and search
 * terms. The identifier contains the Playwright worker index and a short UUID suffix so
 * parallel workers never collide.
 */
export function eindeutigeKennung(workerIndex: number = 0): string {
    const zeit = Date.now().toString(36);
    const suffix = randomUUID().slice(0, 8);

    return `e2e-w${workerIndex}-${zeit}-${suffix}`;
}

/** A test-unique e-mail address. */
export function testEmail(workerIndex: number = 0): string {
    return `${eindeutigeKennung(workerIndex)}@example.com`;
}

/** A test-unique first name. */
export function testVorname(workerIndex: number = 0): string {
    return `E2E-Vorname-${eindeutigeKennung(workerIndex)}`;
}

/** A test-unique last name. */
export function testNachname(workerIndex: number = 0): string {
    return `E2E-Nachname-${eindeutigeKennung(workerIndex)}`;
}
