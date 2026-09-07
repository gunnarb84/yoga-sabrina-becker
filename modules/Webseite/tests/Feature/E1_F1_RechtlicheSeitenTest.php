<?php

declare(strict_types=1);

/**
 * Story: E1 Öffentliche Webseite / F1 Startseite, Navigation, statische Seiten
 *
 * Geprüfte Kriterien:
 * - Impressum, AGB und Datenschutzerklärung liegen als veröffentlichte statische
 *   Seiten vor und sind unter ihrem Slug erreichbar (Inhalt aus vorlagen/).
 */

use Database\Seeders\ContentPagesSeeder;
use Database\Seeders\LegalPagesSeeder;

it('publishes the content pages from the homepage draft under their slugs', function (): void {
    $this->seed(ContentPagesSeeder::class);

    $this->get('/yoga-events')
        ->assertOk()
        ->assertSeeText('Meine Yoga-Events')
        ->assertSeeText('Yoga auf der Burg')
        ->assertSeeText('Rückenyoga für Firmen');

    $this->get('/fuer-wen')
        ->assertOk()
        ->assertSeeText('Für wen sind meine Angebote?')
        ->assertSeeText('Das passt zu dir, wenn');

    $this->get('/kontakt')
        ->assertOk()
        ->assertSeeText('Kontakt')
        ->assertSeeText('info@yoga-sabrinabecker.de');
});

it('presents the meine-idee section on the homepage instead of its own page', function (): void {
    $this->seed(ContentPagesSeeder::class);

    $this->get(route('home'))
        ->assertOk()
        ->assertSeeText('Meine Idee')
        ->assertSeeText('Ankommen. Durchatmen. Bewegen. Genießen.');

    $this->get('/meine-idee')->assertNotFound();
});

it('orders the menu: meine idee, veranstaltungen, yoga-events, fuer wen, kontakt', function (): void {
    $this->seed(ContentPagesSeeder::class);

    $html = $this->get(route('home'))->assertOk()->getContent();

    // Erstes Vorkommen = Hauptnavigation (vor dem Footer).
    $positionOf = fn (string $text): int => (int) mb_strpos($html, $text);

    expect($positionOf('>Meine Idee</a>'))
        ->toBeLessThan($positionOf('>Veranstaltungen</a>'))
        ->toBeLessThan($positionOf('>Yoga-Events</a>'))
        ->toBeLessThan($positionOf('>Für wen?</a>'))
        ->toBeLessThan($positionOf('>Kontakt</a>'));
});

it('publishes the legal pages from the templates under their slugs', function (): void {
    $this->seed(LegalPagesSeeder::class);

    $this->get('/impressum')
        ->assertOk()
        ->assertSeeText('Impressum')
        ->assertSeeText('Yoga Sabrina Becker')
        ->assertSeeText('In der Flöz 2')
        ->assertSeeText('Kleinunternehmerin');

    $this->get('/agb')
        ->assertOk()
        ->assertSeeText('Allgemeine Geschäftsbedingungen (AGB)')
        ->assertSeeText('Geltungsbereich')
        ->assertSeeText('Widerrufsrecht')
        ->assertSeeText('Warteliste');

    $this->get('/datenschutz')
        ->assertOk()
        ->assertSeeText('Datenschutzerklärung')
        ->assertSeeText('Verantwortliche')
        ->assertSeeText('Art. 15 DSGVO');
});
