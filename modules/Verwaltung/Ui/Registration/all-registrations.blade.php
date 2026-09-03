<div class="verwaltung-all-registrations">
    <h1>Anmeldungen</h1>

    <form wire:submit.prevent="applyFilters" class="verwaltung-filters">
        <div>
            <label for="filterActivityId">Veranstaltung</label>
            <select id="filterActivityId" wire:model="filterActivityId">
                <option value="">Alle</option>
                @foreach ($activities as $activity)
                    <option value="{{ $activity->id }}">{{ $activity->titel }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="filterStatus">Status</label>
            <select id="filterStatus" wire:model="filterStatus">
                <option value="">Alle</option>
                @foreach ($statusOptions as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="filterSearch">Suche</label>
            <input id="filterSearch" type="search" wire:model="filterSearch" placeholder="Name, E-Mail, Veranstaltung">
        </div>

        <button type="submit">Filtern</button>
        <button type="button" wire:click="resetFilters">Zurücksetzen</button>
    </form>

    @if (empty($registrations))
        <p>Keine Anmeldungen vorhanden.</p>
    @else
        <table class="verwaltung-table">
            <thead>
                <tr>
                    <th>Veranstaltung</th>
                    <th>Teilnehmer/in</th>
                    <th>E-Mail</th>
                    <th>Status</th>
                    <th>Zahlungsart</th>
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
                        <td>{{ \Carbon\Carbon::parse($registration->angemeldet_am)->format('d.m.Y H:i') }}</td>
                        <td>{{ $registration->freie_plaetze }}</td>
                        <td>{{ $registration->warteliste_anzahl }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
