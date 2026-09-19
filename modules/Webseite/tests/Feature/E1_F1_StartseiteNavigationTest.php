<?php

declare(strict_types=1);

/**
 * Story: E1 Öffentliche Webseite / F1 Startseite, Navigation, statische Seiten
 *
 * Geprüfte Kriterien:
 * - Startseite ist erreichbar und zeigt bevorstehende Veranstaltungen.
 * - Navigation zeigt CMS-Einträge aus der Datenbank.
 * - Statische Seiten sind unter ihrem Slug erreichbar.
 * - Unveröffentlichte Seiten werden nicht angezeigt.
 */

use Carbon\Carbon;
use Yoga\Modules\Verwaltung\Tests\TestFactory;
use Yoga\Modules\Webseite\Domain\Navigation\NavigationItem;
use Yoga\Modules\Webseite\Domain\Page\Page;

beforeEach(function (): void {
    $this->future = Carbon::now()->addDays(7);
});

it('renders the homepage with upcoming activities', function (): void {
    $activity = TestFactory::createActivity();
    TestFactory::createSession($activity, $this->future);

    $this->get(route('home'))
        ->assertOk()
        ->assertSeeText('Yoga Sabrina Becker')
        ->assertSeeText($activity->titel);
});

it('renders navigation items from the cms', function (): void {
    new NavigationItem([
        'bezeichnung' => 'Über mich',
        'url' => '/ueber-mich',
        'sortierung' => 10,
        'aktiv' => true,
    ])->save();

    new NavigationItem([
        'bezeichnung' => 'Inaktiver Link',
        'url' => '/versteckt',
        'sortierung' => 20,
        'aktiv' => false,
    ])->save();

    $this->get(route('home'))
        ->assertOk()
        ->assertSeeText('Über mich')
        ->assertDontSeeText('Inaktiver Link');
});

it('opens external navigation items in a new tab and marks them as external', function (): void {
    new NavigationItem([
        'bezeichnung' => 'Externes Angebot',
        'url' => 'https://example.com/angebot',
        'sortierung' => 40,
        'extern' => true,
        'aktiv' => true,
    ])->save();

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<a href="https://example.com/angebot" target="_blank" rel="noopener noreferrer">Externes Angebot<span aria-hidden="true"> ↗</span></a>', false);
});

it('renders a published static page under its slug', function (): void {
    new Page([
        'slug' => 'impressum',
        'titel' => 'Impressum',
        'inhalt' => '<p>Verantwortlich: Sabrina Becker</p>',
        'meta_beschreibung' => 'Impressum der Webseite.',
        'veroeffentlicht' => true,
    ])->save();

    $this->get('/impressum')
        ->assertOk()
        ->assertSeeText('Impressum')
        ->assertSeeText('Verantwortlich: Sabrina Becker');
});

it('returns a 404 for unpublished static pages', function (): void {
    new Page([
        'slug' => 'entwurf',
        'titel' => 'Entwurf',
        'inhalt' => '<p>Noch nicht öffentlich.</p>',
        'veroeffentlicht' => false,
    ])->save();

    $this->get('/entwurf')
        ->assertNotFound();
});

it('renders the activity overview page', function (): void {
    $activity = TestFactory::createActivity();
    TestFactory::createSession($activity, $this->future);

    $this->get(route('activities'))
        ->assertOk()
        ->assertSeeText('Veranstaltungen')
        ->assertSeeText($activity->titel);
});
