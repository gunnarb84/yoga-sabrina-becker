<div class="yoga-section yoga-section--creme yoga-section--flush">
<div class="yoga-activity-overview">
    <div class="yoga-section-header">
        <h1>Kurse, Events und Workshops</h1>
        <p class="yoga-script">Für Körper, Geist &amp; Seele</p>
        <svg class="yoga-section-divider" viewBox="0 0 120 34" aria-hidden="true">
            <path d="M4 22 H44" stroke="currentColor" stroke-width="1" fill="none"/>
            <path d="M76 22 H116" stroke="currentColor" stroke-width="1" fill="none"/>
            <path d="M60 8 C66 14 66 20 60 24 C54 20 54 14 60 8 Z M60 24 C54 16 47 16 45 22 C48 26 55 27 60 24 Z M60 24 C66 16 73 16 75 22 C72 26 65 27 60 24 Z" stroke="currentColor" stroke-width="1" fill="none"/>
        </svg>
    </div>

    <form method="get" class="yoga-filter">
        <label for="type-filter">Filter:</label>
        <select id="type-filter" wire:model.live="typeFilter">
            @foreach ($typeOptions as $option)
                <option value="{{ $option['value'] }}" {{ $typeFilter === $option['value'] ? 'selected' : '' }}>
                    {{ $option['label'] }}
                </option>
            @endforeach
        </select>
    </form>

    @if (empty($activities))
        <div class="yoga-empty">
            <p>Aktuell sind keine zukünftigen Veranstaltungen verfügbar.</p>
        </div>
    @else
        <div class="yoga-activity-grid">
            @foreach ($activities as $activity)
                <article class="yoga-activity-card {{ $activity->ausgebucht ? 'yoga-activity-full' : '' }}">
                    @if ($activity->bild)
                        <img src="{{ $activity->bild }}" alt="{{ $activity->titel }}" class="yoga-activity-image">
                    @else
                        <div class="yoga-activity-image-placeholder">
                            <svg viewBox="0 0 40 34" aria-hidden="true"><path d="M20 30 C6 20 4 12 9 8 C13 4.5 18 7 20 11 C22 7 27 4.5 31 8 C36 12 34 20 20 30 Z" stroke="currentColor" stroke-width="1.4" fill="none"/></svg>
                        </div>
                    @endif

                    <p class="yoga-activity-type">{{ $activity->typLabel }}</p>
                    <h2><a href="{{ route('activity.detail', $activity->slug) }}">{{ $activity->titel }}</a></h2>

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
                        <p class="yoga-status-warteliste">Warteliste ({{ $activity->warteliste_anzahl }})</p>
                    @else
                        <p class="yoga-status-buchbar">Noch {{ $activity->freie_plaetze }} Plätze frei</p>
                    @endif

                    <div class="yoga-card-actions">
                        @if ($activity->buchbar)
                            <a href="{{ route('activity.register', ['slug' => $activity->slug]) }}" class="yoga-btn-primary yoga-btn-sm">Jetzt anmelden</a>
                        @else
                            <span class="yoga-btn-primary yoga-btn-sm" disabled>Ausgebucht</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
</div>