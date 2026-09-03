<div class="yoga-home">
    <header class="yoga-header">
        <h1>Yoga Sabrina Becker</h1>
        <p>Entspannung, Bewegung und Balance für Körper und Geist.</p>
    </header>

    <section class="yoga-upcoming">
        <h2>Aktuelle Angebote</h2>

        @if (empty($activities))
            <p>Demnächst gibt es hier neue Kurse, Events und Workshops.</p>
        @else
            <div class="yoga-activity-grid">
                @foreach ($activities as $activity)
                    <article class="yoga-activity-card {{ $activity->ausgebucht ? 'yoga-activity-full' : '' }}">
                        @if ($activity->bild)
                            <img src="{{ $activity->bild }}" alt="{{ $activity->titel }}" class="yoga-activity-image">
                        @endif

                        <h3><a href="{{ route('activity.detail', $activity->slug) }}">{{ $activity->titel }}</a></h3>
                        <p class="yoga-activity-type">{{ $activity->typLabel }}</p>

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
                            <p class="yoga-status-buchbar">Buchbar</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif

        <p><a href="{{ route('activities') }}">Alle Veranstaltungen anzeigen</a></p>
    </section>
</div>
