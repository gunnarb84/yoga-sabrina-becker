<div class="yoga-activity-overview">
    <h1>Kurse, Events und Workshops</h1>

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
        <p>Aktuell sind keine zukünftigen Veranstaltungen verfügbar.</p>
    @else
        <div class="yoga-activity-grid">
            @foreach ($activities as $activity)
                <article class="yoga-activity-card {{ $activity->ausgebucht ? 'yoga-activity-full' : '' }}">
                    @if ($activity->bild)
                        <img src="{{ $activity->bild }}" alt="{{ $activity->titel }}" class="yoga-activity-image">
                    @endif

                    <h2><a href="{{ route('activity.detail', $activity->slug) }}">{{ $activity->titel }}</a></h2>
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
                        <p class="yoga-status-warteliste">Warteliste ({{ $activity->warteliste_anzahl }})</p>
                    @else
                        <p class="yoga-status-buchbar">Noch {{ $activity->freie_plaetze }} Plätze frei</p>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</div>
