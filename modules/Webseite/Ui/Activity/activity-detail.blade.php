<div class="yoga-section yoga-section--creme yoga-section--flush">
<div class="yoga-activity-detail">
    @if ($activity === null)
        <div class="yoga-empty">
            <p>Die Veranstaltung wurde nicht gefunden.</p>
            <p><a href="{{ route('activities') }}" class="yoga-btn-ghost">Zurück zur Übersicht</a></p>
        </div>
    @else
        <div class="yoga-activity-detail-header yoga-section-header">
            <p class="yoga-activity-type">{{ $activity->typLabel }}</p>
            <h1>{{ $activity->titel }}</h1>
        </div>

        @if ($activity->bild)
            <img src="{{ $activity->bild }}" alt="{{ $activity->titel }}" class="yoga-activity-detail-image">
        @else
            <div class="yoga-activity-detail-image yoga-activity-image-placeholder">
                <svg viewBox="0 0 40 34" aria-hidden="true"><path d="M20 30 C6 20 4 12 9 8 C13 4.5 18 7 20 11 C22 7 27 4.5 31 8 C36 12 34 20 20 30 Z" stroke="currentColor" stroke-width="1.4" fill="none"/></svg>
            </div>
        @endif

        @if ($activity->langbeschreibung)
            <div class="yoga-activity-description">{!! nl2br(e($activity->langbeschreibung)) !!}</div>
        @elseif ($activity->kurzbeschreibung)
            <p class="yoga-lead">{{ $activity->kurzbeschreibung }}</p>
        @endif

        <p class="yoga-activity-price">{{ number_format((float) $activity->preis, 2, ',', '.') }} EUR</p>

        @if ($activity->ausgebucht)
            <p class="yoga-status-warteliste">Warteliste ({{ $activity->warteliste_anzahl }} Einträge) – neue Anmeldungen landen auf der Warteliste.</p>
        @else
            <p class="yoga-status-buchbar">Noch {{ $activity->freie_plaetze }} von {{ $activity->maximale_teilnehmerzahl }} Plätzen frei.</p>
        @endif

        @if (!empty($activity->termine))
            <h2>Termine</h2>
            <ul class="yoga-activity-sessions">
                @foreach ($activity->termine as $session)
                    <li>
                        {{ \Carbon\Carbon::parse($session->beginn)->format('d.m.Y H:i') }}
                        –
                        {{ \Carbon\Carbon::parse($session->ende)->format('H:i') }}
                        @if ($session->ort)
                            | {{ $session->ort }}
                        @endif
                        @if ($session->hinweis)
                            <small>{{ $session->hinweis }}</small>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p class="yoga-empty">Derzeit sind keine zukünftigen Termine vorhanden.</p>
        @endif

        @if ($activity->buchbar)
            <p class="yoga-mt-2"><a href="{{ route('activity.register', ['slug' => $activity->slug]) }}" class="yoga-btn-primary">Jetzt anmelden</a></p>
        @else
            <p class="yoga-status-not-bookable">Diese Veranstaltung ist aktuell nicht buchbar.</p>
        @endif

        <p class="yoga-mt-3"><a href="{{ route('activities') }}" class="yoga-btn-ghost">Zurück zur Übersicht</a></p>
    @endif
</div>
</div>