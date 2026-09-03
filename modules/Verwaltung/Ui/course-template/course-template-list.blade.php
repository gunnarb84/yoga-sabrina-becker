<div class="verwaltung-course-template-list">
    <h1>Kursvorlagen</h1>

    <p><a href="{{ route('verwaltung.course-template.create') }}">Neue Vorlage anlegen</a></p>

    @if ($message !== '')
        <p class="verwaltung-message">{{ $message }}</p>
    @endif

    @if ($generatedActivityId !== null)
        <p>
            <a href="{{ route('verwaltung.activity.edit', ['id' => $generatedActivityId]) }}">Erzeugte Veranstaltung bearbeiten</a>
        </p>
    @endif

    @if (empty($templates))
        <p>Noch keine Kursvorlagen vorhanden.</p>
    @else
        <table class="verwaltung-table">
            <thead>
                <tr>
                    <th>Titel</th>
                    <th>Wochentag</th>
                    <th>Startzeit</th>
                    <th>Dauer (Min.)</th>
                    <th>Termine</th>
                    <th>Preis (EUR)</th>
                    <th>Max. Teilnehmer</th>
                    <th>Ort</th>
                    <th>Aktion</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($templates as $template)
                    <tr>
                        <td>{{ $template->titel }}</td>
                        <td>{{ $weekdayLabels[$template->wochentag] ?? $template->wochentag }}</td>
                        <td>{{ substr($template->startzeit, 0, 5) }}</td>
                        <td>{{ $template->dauer_minuten }}</td>
                        <td>{{ $template->anzahl_termine }}</td>
                        <td>{{ $template->preis }}</td>
                        <td>{{ $template->maximale_teilnehmerzahl }}</td>
                        <td>{{ $template->ort ?? '-' }}</td>
                        <td>
                            <a href="{{ route('verwaltung.course-template.edit', ['id' => $template->id]) }}">Bearbeiten</a>
                            <button type="button" wire:click="generate('{{ $template->id }}')">Termine erzeugen</button>
                            <button type="button" wire:click="delete('{{ $template->id }}')">Löschen</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
