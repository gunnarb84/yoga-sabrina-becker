<div class="verwaltung-activities">
    <h1>Aktivitaeten</h1>

    <p><a href="{{ route('verwaltung.activity.create') }}">Neue Aktivitaet anlegen</a></p>

    @if ($message)
        <div class="verwaltung-message" role="alert">{{ $message }}</div>
    @endif

    @if (empty($activities))
        <p>Noch keine Aktivitaeten vorhanden.</p>
    @else
        <table class="verwaltung-table">
            <thead>
                <tr>
                    <th>Titel</th>
                    <th>Typ</th>
                    <th>Status</th>
                    <th>Preis</th>
                    <th>Max. Teilnehmer</th>
                    <th>Termine</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($activities as $activity)
                    <tr>
                        <td>{{ $activity->titel }}</td>
                        <td>{{ ucfirst($activity->typ) }}</td>
                        <td>{{ $activity->status }}{{ $activity->veroeffentlicht ? ' / veroeffentlicht' : '' }}</td>
                        <td>{{ number_format((float) $activity->preis, 2, ',', '.') }} EUR</td>
                        <td>{{ $activity->maximale_teilnehmerzahl }}</td>
                        <td>{{ $activity->anzahl_termine }}</td>
                        <td>
                            <a href="{{ route('verwaltung.activity.edit', ['id' => $activity->id]) }}">Bearbeiten</a>
                            <a href="{{ route('verwaltung.session.create', ['id' => $activity->id]) }}">Termin</a>
                            <a href="{{ route('verwaltung.activity.registrations', ['id' => $activity->id]) }}">Anmeldungen</a>
                            @if (! $activity->veroeffentlicht)
                                <button type="button" wire:click="publish('{{ $activity->id }}')">Veroeffentlichen</button>
                            @else
                                <button type="button" wire:click="unpublish('{{ $activity->id }}')">Veroeffentlichung zurueckziehen</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
