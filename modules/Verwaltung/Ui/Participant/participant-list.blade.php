<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Teilnehmer</h1>
        <a href="{{ route('verwaltung.participant.create') }}" class="au-btn au-btn--primary">Neuen Teilnehmer anlegen</a>
    </div>

    <div class="au-panel__body">
        <label class="au-field au-field--inline">
            <span class="au-field__label">Suchen</span>
            <input type="search" wire:model.live.debounce.250ms="search" placeholder="Teilnehmer suchen..." aria-label="Teilnehmer suchen" class="au-field__input">
        </label>

        @if (empty($participants))
            <p class="yoga-empty">Noch keine Teilnehmer vorhanden.</p>
        @else
            <div style="overflow-x:auto">
                <table class="au-list">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>E-Mail</th>
                            <th>Telefon</th>
                            <th>Ort</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($participants as $participant)
                            <tr>
                                <td>
                                    <a href="{{ route('verwaltung.participant.edit', ['id' => $participant->id]) }}">
                                        {{ $participant->vorname }} {{ $participant->nachname }}
                                    </a>
                                </td>
                                <td>{{ $participant->email !== '' ? $participant->email : '—' }}</td>
                                <td>{{ $participant->telefon }}</td>
                                <td>{{ $participant->stadt }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
