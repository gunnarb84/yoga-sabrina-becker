<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Anmeldungen</h1>
    </div>

    <div class="au-panel__body">
        <form wire:submit.prevent="applyFilters" class="yoga-filter">
            <div class="au-field">
                <label class="au-field__label" for="filterActivityId">Veranstaltung</label>
                <select id="filterActivityId" wire:model="filterActivityId" class="au-field__input">
                    <option value="">Alle</option>
                    @foreach ($activities as $activity)
                        <option value="{{ $activity->id }}">{{ $activity->titel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="au-field">
                <label class="au-field__label" for="filterStatus">Status</label>
                <select id="filterStatus" wire:model="filterStatus" class="au-field__input">
                    <option value="">Alle</option>
                    @foreach ($statusOptions as $option)
                        <option value="{{ $option->value }}">{{ $option->label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="au-field">
                <label class="au-field__label" for="filterSearch">Suche</label>
                <input id="filterSearch" type="search" wire:model="filterSearch" placeholder="Name, E-Mail, Veranstaltung" class="au-field__input">
            </div>

            <div class="yoga-filter-actions">
                <button type="submit" class="au-btn au-btn--primary">Filtern</button>
                <button type="button" class="au-btn" wire:click="resetFilters">Zurücksetzen</button>
            </div>
        </form>

        @if (empty($registrations))
            <p class="yoga-empty">Keine Anmeldungen vorhanden.</p>
        @else
            <div style="overflow-x:auto">
                <table class="au-list">
                    <thead>
                        <tr>
                            <th>Veranstaltung</th>
                            <th>Teilnehmer/in</th>
                            <th>E-Mail</th>
                            <th>Status</th>
                            <th>Zahlungsart</th>
                            <th>Herkunft</th>
                            <th>Angemeldet am</th>
                            <th>Freie Plätze</th>
                            <th>Warteliste</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($registrations as $registration)
                            <tr class="status-{{ $registration->status }}">
                                <td>{{ $registration->aktivitaet_titel }}</td>
                                <td>
                                    <a href="{{ route('verwaltung.participant.edit', ['id' => $registration->teilnehmer_id]) }}">
                                        {{ $registration->teilnehmer_name }}
                                    </a>
                                </td>
                                <td>{{ $registration->email }}</td>
                                <td>{{ ucfirst($registration->status) }}</td>
                                <td>{{ $typeLabel($registration->zahlungsart) }}</td>
                                <td>{{ $registration->herkunft === 'verwaltung' ? 'Verwaltung' : 'Webseite' }}</td>
                                <td>{{ \Carbon\Carbon::parse($registration->angemeldet_am)->format('d.m.Y H:i') }}</td>
                                <td>{{ $registration->freie_plaetze }}</td>
                                <td>{{ $registration->warteliste_anzahl }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
