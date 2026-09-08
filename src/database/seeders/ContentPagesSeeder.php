<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Yoga\Modules\Webseite\Domain\Navigation\NavigationItem;
use Yoga\Modules\Webseite\Domain\Page\Page;

/**
 * Legt die Inhaltsseiten aus dem Homepage-Entwurf (vorlagen/homepage-entwurf.html)
 * als veröffentlichte CMS-Seiten an und pflegt die zugehörigen Navigationseinträge.
 * Der Abschnitt „Meine Idee" ist Teil der Startseite und hat daher keine eigene Seite.
 */
class ContentPagesSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedNavigation();
        $this->removeMeineIdeePage();
        $this->createYogaEvents();
        $this->createFuerWen();
        $this->createKontakt();
    }

    private function seedNavigation(): void
    {
        NavigationItem::where('url', '/meine-idee')->delete();

        $eintraege = [
            ['bezeichnung' => 'Yoga-Events', 'url' => '/yoga-events', 'sortierung' => 10],
            ['bezeichnung' => 'Für wen?', 'url' => '/fuer-wen', 'sortierung' => 20],
            ['bezeichnung' => 'Kontakt', 'url' => '/kontakt', 'sortierung' => 30],
        ];

        foreach ($eintraege as $eintrag) {
            NavigationItem::updateOrCreate(
                ['url' => $eintrag['url']],
                [
                    'bezeichnung' => $eintrag['bezeichnung'],
                    'sortierung' => $eintrag['sortierung'],
                    'extern' => false,
                    'aktiv' => true,
                ]
            );
        }
    }

    /**
     * „Meine Idee" ist nun Teil der Startseite; die eigenständige Seite entfällt.
     */
    private function removeMeineIdeePage(): void
    {
        Page::where('slug', 'meine-idee')->delete();
    }

    private function createYogaEvents(): void
    {
        Page::updateOrCreate(
            ['slug' => 'yoga-events'],
            [
                'titel' => 'Meine Yoga-Events',
                'inhalt' => <<<'HTML'
<p>Für besondere Momente: private Yoga-Events für Gruppen, Freund:innen und Unternehmen – individuell auf Gruppe, Ort und Anlass abgestimmt.</p>

<div class="yoga-card-grid">
    <article class="yoga-card">
        <div class="yoga-card-image-placeholder"><svg viewBox="0 0 40 34" aria-hidden="true"><path d="M20 30 C6 20 4 12 9 8 C13 4.5 18 7 20 11 C22 7 27 4.5 31 8 C36 12 34 20 20 30 Z" stroke="currentColor" stroke-width="1.4" fill="none"/></svg></div>
        <div class="yoga-card-body">
            <p class="yoga-card-label">Historisches Ambiente</p>
            <h3>Yoga auf der Burg</h3>
            <p>Eine besondere Yoga-Auszeit in historischem Ambiente. Bewegung, Entspannung und eine außergewöhnliche Atmosphäre für Körper und Seele.</p>
            <div class="yoga-card-actions"><a href="/kontakt" class="yoga-btn-primary yoga-btn-sm">Jetzt anfragen</a></div>
        </div>
    </article>
    <article class="yoga-card">
        <div class="yoga-card-image-placeholder"><svg viewBox="0 0 40 34" aria-hidden="true"><path d="M20 30 C6 20 4 12 9 8 C13 4.5 18 7 20 11 C22 7 27 4.5 31 8 C36 12 34 20 20 30 Z" stroke="currentColor" stroke-width="1.4" fill="none"/></svg></div>
        <div class="yoga-card-body">
            <p class="yoga-card-label">Zwischen Weinbergen</p>
            <h3>Yoga im Weingut &amp; Weinverkostung</h3>
            <p>Yoga zwischen Weinbergen oder im Weingut – anschließend darf gemeinsam genossen werden. Eine entspannte Yogastunde mit Weinverkostung für Freundinnen, Paare oder Gruppen.</p>
            <div class="yoga-card-actions"><a href="/kontakt" class="yoga-btn-primary yoga-btn-sm">Jetzt anfragen</a></div>
        </div>
    </article>
    <article class="yoga-card">
        <div class="yoga-card-image-placeholder"><svg viewBox="0 0 40 34" aria-hidden="true"><path d="M20 30 C6 20 4 12 9 8 C13 4.5 18 7 20 11 C22 7 27 4.5 31 8 C36 12 34 20 20 30 Z" stroke="currentColor" stroke-width="1.4" fill="none"/></svg></div>
        <div class="yoga-card-body">
            <p class="yoga-card-label">Für die Braut &amp; Freund:innen</p>
            <h3>Junggesellinnenabschied</h3>
            <p>Eine bewusste Auszeit für die Braut und ihre Lieblingsmenschen. Gemeinsam bewegen, lachen, entspannen und einen besonderen Moment miteinander verbringen.</p>
            <div class="yoga-card-actions"><a href="/kontakt" class="yoga-btn-primary yoga-btn-sm">Jetzt anfragen</a></div>
        </div>
    </article>
    <article class="yoga-card">
        <div class="yoga-card-image-placeholder"><svg viewBox="0 0 40 34" aria-hidden="true"><path d="M20 30 C6 20 4 12 9 8 C13 4.5 18 7 20 11 C22 7 27 4.5 31 8 C36 12 34 20 20 30 Z" stroke="currentColor" stroke-width="1.4" fill="none"/></svg></div>
        <div class="yoga-card-body">
            <p class="yoga-card-label">Sanfte Bewegung &amp; Genuss</p>
            <h3>Yoga im Teehaus</h3>
            <p>Sanfte Bewegung, Ruhe und Genuss. Eine entspannte Yoga-Einheit trifft auf wohltuende Teemomente – ideal, um dem Alltag für eine Weile zu entfliehen.</p>
            <div class="yoga-card-actions"><a href="/kontakt" class="yoga-btn-primary yoga-btn-sm">Jetzt anfragen</a></div>
        </div>
    </article>
    <article class="yoga-card">
        <div class="yoga-card-image-placeholder"><svg viewBox="0 0 40 34" aria-hidden="true"><path d="M20 30 C6 20 4 12 9 8 C13 4.5 18 7 20 11 C22 7 27 4.5 31 8 C36 12 34 20 20 30 Z" stroke="currentColor" stroke-width="1.4" fill="none"/></svg></div>
        <div class="yoga-card-body">
            <p class="yoga-card-label">Für Unternehmen</p>
            <h3>Rückenyoga für Firmen</h3>
            <p>Gezielte Übungen für Rücken, Schultern und Nacken helfen dabei, Verspannungen vorzubeugen und einen Ausgleich zum Arbeitsalltag zu schaffen.</p>
            <div class="yoga-card-actions"><a href="/kontakt" class="yoga-btn-primary yoga-btn-sm">Jetzt anfragen</a></div>
        </div>
    </article>
</div>

<div class="yoga-cta-panel">
    <p class="yoga-script">Gemeinsam bewegen. Durchatmen. Genießen.</p>
    <p>Private Gruppen, Freund:innen, Unternehmen und besondere Veranstaltungen sind herzlich willkommen.</p>
    <a href="/kontakt" class="yoga-btn-primary">Jetzt anfragen</a>
</div>

<div class="yoga-courses-band">
    <div class="yoga-courses-icon">
        <svg viewBox="0 0 48 48" aria-hidden="true">
            <rect x="10" y="14" width="28" height="24" rx="3" stroke="currentColor" stroke-width="1.4" fill="none"/>
            <path d="M16 20 V14 M24 20 V10 M32 20 V14" stroke="currentColor" stroke-width="1.4" fill="none"/>
            <circle cx="24" cy="26" r="5" stroke="currentColor" stroke-width="1.4" fill="none"/>
            <path d="M16 34 H32" stroke="currentColor" stroke-width="1.4" fill="none"/>
        </svg>
    </div>
    <div class="yoga-courses-content">
        <p class="yoga-card-label">Wöchentlich</p>
        <h3>Regelmäßige Kurse in Bergen</h3>
        <p>Neben meinen Yoga-Events finden regelmäßig Yogakurse in Bergen statt. Eine kleine Auszeit vom Alltag, um den Körper zu bewegen, neue Energie zu sammeln und wieder mehr bei sich selbst anzukommen.</p>
    </div>
    <div class="yoga-courses-actions"><a href="/kurse?typeFilter=kurs" class="yoga-btn-primary">Kurse ansehen</a></div>
</div>
HTML,
                'meta_beschreibung' => 'Private Yoga-Events: Yoga auf der Burg, im Weingut, Junggesellinnenabschied, Yoga im Teehaus und Rückenyoga für Firmen.',
                'veroeffentlicht' => true,
            ]
        );
    }

    private function createFuerWen(): void
    {
        Page::updateOrCreate(
            ['slug' => 'fuer-wen'],
            [
                'titel' => 'Für wen sind meine Angebote?',
                'inhalt' => <<<'HTML'
<div class="yoga-audience-grid">
    <div>
        <p>Meine Yoga-Events eignen sich für Anfängerinnen und Anfänger ebenso wie für Yoga-Erfahrene. Die Stunden werden an die jeweilige Gruppe, den Ort und den Anlass angepasst.</p>
        <blockquote class="yoga-content-quote">Private Gruppen, Freund:innen, Unternehmen und besondere Veranstaltungen sind herzlich willkommen.</blockquote>
    </div>
    <div class="yoga-audience-card">
        <h3>Das passt zu dir, wenn …</h3>
        <ul class="yoga-checklist">
            <li>du Yoga in besonderen Locations erleben möchtest</li>
            <li>du eine private Gruppe oder einen JGA planst</li>
            <li>du mit deinem Unternehmen etwas für die Gesundheit deiner Mitarbeitenden tun willst</li>
            <li>du regelmäßig in Bergen an deinem Wohlbefinden arbeiten möchtest</li>
            <li>dir Ankommen, Entspannen und Genießen wichtiger sind als Perfektion</li>
        </ul>
        <div class="yoga-tags">
            <span class="yoga-tag yoga-tag--gold">Anfänger:innen</span>
            <span class="yoga-tag">Outdoor</span>
            <span class="yoga-tag">90 Min</span>
            <span class="yoga-tag">Firmenkurse</span>
        </div>
    </div>
</div>
HTML,
                'meta_beschreibung' => 'Meine Yoga-Angebote passen für Anfänger:innen und Yoga-Erfahrene, private Gruppen, Unternehmen und besondere Anlässe.',
                'veroeffentlicht' => true,
            ]
        );
    }

    private function createKontakt(): void
    {
        // Nur die Kontaktkarte: das Anfrageformular liefert die Livewire-Komponente
        // webseite.kontaktanfrage selbst (Feature Kontaktanfrage, Spec E1/F4).
        Page::updateOrCreate(
            ['slug' => 'kontakt'],
            [
                'titel' => 'Kontakt',
                'inhalt' => <<<'HTML'
<div class="yoga-contact-card">
    <h3>Gerne melden</h3>
    <span class="yoga-script">Ich freue mich, euch kennenzulernen!</span>
    <ul class="yoga-contact-rows">
        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg><span>Telefon – Nummer folgt in Kürze</span></li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg><a href="mailto:info@yoga-sabrinabecker.de">info@yoga-sabrinabecker.de</a></li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg><span>@yoga_sabrina_becker</span></li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg><a href="https://yoga-sabrinabecker.de" rel="noopener noreferrer">yoga-sabrinabecker.de</a></li>
    </ul>
</div>
<blockquote><strong>Hinweis:</strong> Ich melde mich innerhalb von zwei Tagen persönlich zurück.</blockquote>
HTML,
                'meta_beschreibung' => 'Kontakt zu Yoga Sabrina Becker: Anfragen für private Yoga-Events, Firmenkurse und Kurse in Bergen.',
                'veroeffentlicht' => true,
            ]
        );
    }
}
