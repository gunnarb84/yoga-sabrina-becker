<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Kursvorlagen</h1>
        <a href="{{ route('verwaltung.course-template.create') }}" class="au-btn au-btn--primary">Neue Vorlage</a>
    </div>

    <div class="au-panel__body">
        @if ($message)
            <div class="au-status {{ str_contains($message, 'Fehler') ? 'au-status--error' : 'au-status--ok' }} yoga-mb-2" role="alert">{{ $message }}</div>
        @endif

        @if (empty($templates))
            <p class="yoga-empty">Noch keine Kursvorlagen vorhanden.</p>
        @else
            <div style="overflow-x:auto">
                <table class="au-list">
                    <thead>
                        <tr>
                            <th>Titel</th>
                            <th>Wochentag</th>
                            <th>Startzeit</th>
                            <th>Dauer</th>
                            <th>Termine</th>
                            <th>Preis</th>
                            <th>Max. Teilnehmer</th>
                            <th>Ort</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($templates as $template)
                            <tr>
                                <td>{{ $template->titel }}</td>
                                <td>{{ $weekdayLabels[$template->wochentag] ?? ucfirst($template->wochentag) }}</td>
                                <td>{{ substr($template->startzeit, 0, 5) }}</td>
                                <td>{{ $template->dauer_minuten }} Min.</td>
                                <td style="text-align:right">{{ $template->anzahl_termine }}</td>
                                <td style="text-align:right">{{ number_format((float) $template->preis, 2, ',', '.') }} EUR</td>
                                <td style="text-align:right">{{ $template->maximale_teilnehmerzahl }}</td>
                                <td>{{ $template->ort ?? '–' }}</td>
                                <td>
                                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                                        <a href="{{ route('verwaltung.course-template.edit', ['id' => $template->id]) }}" class="au-btn au-btn--sm">Bearbeiten</a>
                                        <button type="button" class="au-btn au-btn--sm" wire:click="generate('{{ $template->id }}')">Erzeugen</button>
                                        <button type="button" class="au-btn au-btn--sm au-btn--danger" wire:click="delete('{{ $template->id }}')">Löschen</button>
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
