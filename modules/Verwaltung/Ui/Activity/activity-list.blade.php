<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Aktivitäten</h1>
        <a href="{{ route('verwaltung.activity.create') }}" class="au-btn au-btn--primary">Neue Aktivität</a>
    </div>

    <div class="au-panel__body">
        @if ($message)
            <div class="au-status au-status--ok yoga-mb-2" role="alert">{{ $message }}</div>
        @endif

        @if (empty($activities))
            <p class="yoga-empty">Noch keine Aktivitäten vorhanden.</p>
        @else
            <div style="overflow-x:auto">
                <table class="au-list">
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
                                <td>{{ $activity->status }}{{ $activity->veroeffentlicht ? ' / veröffentlicht' : '' }}</td>
                                <td style="text-align:right">{{ number_format((float) $activity->preis, 2, ',', '.') }} EUR</td>
                                <td style="text-align:right">{{ $activity->maximale_teilnehmerzahl }}</td>
                                <td style="text-align:right">{{ $activity->anzahl_termine }}</td>
                                <td>
                                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                                        <a href="{{ route('verwaltung.activity.edit', ['id' => $activity->id]) }}" class="au-btn au-btn--sm">Bearbeiten</a>
                                        <a href="{{ route('verwaltung.session.create', ['id' => $activity->id]) }}" class="au-btn au-btn--sm">Termin</a>
                                        <a href="{{ route('verwaltung.activity.registrations', ['id' => $activity->id]) }}" class="au-btn au-btn--sm">Anmeldungen</a>
                                        @if (! $activity->veroeffentlicht)
                                            <button type="button" class="au-btn au-btn--sm" wire:click="publish('{{ $activity->id }}')">Veröffentlichen</button>
                                        @else
                                            <button type="button" class="au-btn au-btn--sm" wire:click="unpublish('{{ $activity->id }}')">Zurückziehen</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
