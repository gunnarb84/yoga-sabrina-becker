<div class="yoga-home">
<div class="yoga-hero">
    <div class="yoga-hero-grid">
        <div class="yoga-hero-content">
            <p class="yoga-hero-label">Bergen &amp; Umgebung</p>
            <h1>Besondere Orte.<br>Bewusste Auszeiten.</h1>
            <p class="yoga-hero-script">Gemeinsame Momente.</p>

            <svg class="yoga-hero-divider" viewBox="0 0 120 34" aria-hidden="true">
                <path d="M4 22 H44" stroke="currentColor" stroke-width="1" fill="none"/>
                <path d="M76 22 H116" stroke="currentColor" stroke-width="1" fill="none"/>
                <path d="M60 8 C66 14 66 20 60 24 C54 20 54 14 60 8 Z M60 24 C54 16 47 16 45 22 C48 26 55 27 60 24 Z M60 24 C66 16 73 16 75 22 C72 26 65 27 60 24 Z" stroke="currentColor" stroke-width="1" fill="none"/>
            </svg>

            <p>Yoga darf mehr sein als eine regelmäßige Yogastunde. Es darf ein Erlebnis sein, das Menschen verbindet, zur Ruhe bringt und lange in Erinnerung bleibt.</p>

            <div class="yoga-hero-actions">
                <a href="{{ route('activities') }}" class="yoga-btn-primary">Events entdecken</a>
                <a href="{{ route('activities') }}?typeFilter=kurs" class="yoga-btn-secondary">Kurse ansehen</a>
            </div>
        </div>

        <div class="yoga-hero-visual">
            <div class="yoga-hero-portrait">
                <svg viewBox="0 0 40 34" aria-hidden="true">
                    <path d="M20 30 C6 20 4 12 9 8 C13 4.5 18 7 20 11 C22 7 27 4.5 31 8 C36 12 34 20 20 30 Z" stroke="currentColor" stroke-width="1.4" fill="none"/>
                </svg>
                <p>Porträt folgt</p>
            </div>
            <div class="yoga-hero-caption">
                <p class="yoga-script">Yoga mit Herz.</p>
                <p class="yoga-hero-caption-label">Für Körper, Geist &amp; Seele</p>
            </div>
        </div>
    </div>
</div>

<section class="yoga-section yoga-section--creme" id="idee">
    <div class="yoga-section-inner yoga-intro-grid">
        <div>
            <span class="yoga-num">01</span>
            <h2>Meine Idee</h2>
        </div>
        <div>
            <p class="yoga-lead">Mit meinen Yoga-Angeboten schaffe ich bewusste Auszeiten an besonderen Orten – in einer entspannten und persönlichen Atmosphäre.</p>
            <p>Dabei steht nicht die perfekte Haltung im Mittelpunkt, sondern das eigene Wohlbefinden. Egal ob Anfängerin, erfahrene Yogini, Freundesgruppe oder Unternehmen: Jede Stunde wird individuell auf die Gruppe, den Ort und den Anlass angepasst.</p>

            <svg class="yoga-divider" viewBox="0 0 120 34" aria-hidden="true">
                <path d="M4 22 H44" stroke="currentColor" stroke-width="1" fill="none"/>
                <path d="M76 22 H116" stroke="currentColor" stroke-width="1" fill="none"/>
                <path d="M60 8 C66 14 66 20 60 24 C54 20 54 14 60 8 Z M60 24 C54 16 47 16 45 22 C48 26 55 27 60 24 Z M60 24 C66 16 73 16 75 22 C72 26 65 27 60 24 Z" stroke="currentColor" stroke-width="1" fill="none"/>
            </svg>

            <p class="yoga-key-phrase">Ankommen. Durchatmen. Bewegen. Genießen.</p>

            <div class="yoga-highlight-card">
                <div class="yoga-highlight-row">
                    <div class="yoga-highlight-icon">
                        <svg viewBox="0 0 40 34" aria-hidden="true">
                            <path d="M20 30 C6 20 4 12 9 8 C13 4.5 18 7 20 11 C22 7 27 4.5 31 8 C36 12 34 20 20 30 Z" stroke="currentColor" stroke-width="1.4" fill="none"/>
                        </svg>
                    </div>
                    <div>
                        <h4>Yoga mit Herz</h4>
                        <p>Für Körper, Geist &amp; Seele – in jeder Session.</p>
                    </div>
                </div>
                <div class="yoga-highlight-row">
                    <div class="yoga-highlight-icon">
                        <svg viewBox="0 0 40 34" aria-hidden="true">
                            <circle cx="20" cy="17" r="12" stroke="currentColor" stroke-width="1.4" fill="none"/>
                            <path d="M20 11 V17 L24 21" stroke="currentColor" stroke-width="1.4" fill="none"/>
                        </svg>
                    </div>
                    <div>
                        <h4>Bewusste Auszeiten</h4>
                        <p>Eine kleine Pause vom Alltag, um wieder mehr bei sich selbst anzukommen.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="yoga-section yoga-section--warm" id="events">
    <div class="yoga-section-inner">
        <div class="yoga-section-header">
            <span class="yoga-num">02</span>
            <h2>Meine Yoga-Events</h2>
            <p class="yoga-script">Für besondere Momente</p>
            <svg class="yoga-section-divider" viewBox="0 0 120 34" aria-hidden="true">
                <path d="M4 22 H44" stroke="currentColor" stroke-width="1" fill="none"/>
                <path d="M76 22 H116" stroke="currentColor" stroke-width="1" fill="none"/>
                <path d="M60 8 C66 14 66 20 60 24 C54 20 54 14 60 8 Z M60 24 C54 16 47 16 45 22 C48 26 55 27 60 24 Z M60 24 C66 16 73 16 75 22 C72 26 65 27 60 24 Z" stroke="currentColor" stroke-width="1" fill="none"/>
            </svg>
        </div>

        @if (empty($activities))
            <div class="yoga-empty">
                <p>Demnächst gibt es hier neue Kurse, Events und Workshops.</p>
            </div>
        @else
            <div class="yoga-activity-grid">
                @foreach ($activities as $activity)
                    <article class="yoga-activity-card {{ $activity->ausgebucht ? 'yoga-activity-full' : '' }}">
                        @if ($activity->bild)
                            <img src="{{ $activity->bild }}" alt="{{ $activity->titel }}" class="yoga-activity-image">
                        @else
                            <div class="yoga-activity-image-placeholder">{{ $activity->typLabel }}</div>
                        @endif

                        <p class="yoga-activity-type">{{ $activity->typLabel }}</p>
                        <h3><a href="{{ route('activity.detail', $activity->slug) }}">{{ $activity->titel }}</a></h3>

                        @if ($activity->kurzbeschreibung)
                            <p>{{ $activity->kurzbeschreibung }}</p>
                        @endif

                        @if ($activity->naechster_termin)
                            <p class="yoga-activity-next-session">
                                Nächster Termin: {{ \Carbon\Carbon::parse($activity->naechster_termin)->format('d.m.Y H:i') }}
                            </p>
                        @endif

                        <p class="yoga-activity-price">{{ number_format((float) $activity->preis, 2, ',', '.') }} EUR</p>

                        @if ($activity->ausgebucht)
                            <p class="yoga-status-warteliste">Warteliste</p>
                        @else
                            <p class="yoga-status-buchbar">Noch {{ $activity->freie_plaetze }} Plätze frei</p>
                        @endif

                        <div class="yoga-card-actions">
                            @if ($activity->buchbar)
                                <a href="{{ route('activity.register', ['slug' => $activity->slug]) }}" class="yoga-btn-primary yoga-btn-sm">Platz sichern</a>
                            @else
                                <span class="yoga-btn-primary yoga-btn-sm" disabled>Ausgebucht</span>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        <p class="yoga-text-center yoga-mt-3"><a href="{{ route('activities') }}" class="yoga-btn-ghost">Alle Veranstaltungen anzeigen</a></p>
    </div>
</section>

<section class="yoga-section yoga-section--creme" id="kurse">
    <div class="yoga-section-inner">
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
            <div class="yoga-courses-actions">
                <a href="{{ route('activities') }}?typeFilter=kurs" class="yoga-btn-primary">Kurse ansehen</a>
            </div>
        </div>
    </div>
</section>
</div>