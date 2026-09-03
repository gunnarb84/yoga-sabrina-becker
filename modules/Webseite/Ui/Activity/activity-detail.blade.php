<div class="yoga-activity-detail">
    @if ($activity === null)
        <p>Die Veranstaltung wurde nicht gefunden.</p>
    @else
        <h1>{{ $activity->titel }}</h1>
        <p class="yoga-activity-type">{{ $activity->typLabel }}</p>

        @if ($activity->bild)
            <img src="{{ $activity->bild }}" alt="{{ $activity->titel }}" class="yoga-activity-image">
        @endif

        @if ($activity->longbeschreibung)
            <div class="yoga-activity-description">{!! nl2br(e($activity->longbeschreibung)) !!}</div>
        @elseif ($activity->kurzbeschreibung)
            <p>{{ $activity->kurzbeschreibung }}</p>
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
                            <br><small>{{ $session->hinweis }}</small>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p>Derzeit sind keine zukünftigen Termine vorhanden.</p>
        @endif

        @if ($activity->buchbar)
            <p><a href="{{ route('activity.register', ['slug' => $activity->slug]) }}" class="yoga-btn-primary">Jetzt anmelden</a></p>
        @else
            <p class="yoga-status-not-bookable">Diese Veranstaltung ist aktuell nicht buchbar.</p>
        @endif

        <a href="{{ route('activities') }}">Zurück zur Übersicht</a>
    @endif
</div>
