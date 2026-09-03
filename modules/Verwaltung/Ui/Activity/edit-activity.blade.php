<div class="verwaltung-edit-activity">
    <h1>Aktivitaet bearbeiten</h1>

    <p>Status: {{ ucfirst($statusLabel) }}</p>

    @if ($message)
        <div class="verwaltung-message" role="alert">{{ $message }}</div>
    @endif

    @if ($saved)
        <div class="verwaltung-success" role="status">Die Aenderungen wurden gespeichert.</div>
    @endif

    <form wire:submit="save">
        <div>
            <label for="type">Typ</label>
            <select id="type" wire:model="type">
                @foreach ($types as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="title">Titel *</label>
            <input id="title" type="text" wire:model="title" required>
        </div>

        <div>
            <label for="shortDescription">Kurzbeschreibung</label>
            <textarea id="shortDescription" wire:model="shortDescription" rows="3"></textarea>
        </div>

        <div>
            <label for="longDescription">Langbeschreibung</label>
            <textarea id="longDescription" wire:model="longDescription" rows="6"></textarea>
        </div>

        <div>
            <label for="price">Preis (EUR) *</label>
            <input id="price" type="number" step="0.01" min="0" wire:model="price" required>
        </div>

        <div>
            <label for="maxParticipants">Maximale Teilnehmerzahl *</label>
            <input id="maxParticipants" type="number" min="1" wire:model="maxParticipants" required>
        </div>

        <button type="submit">Speichern</button>
        <a href="{{ route('verwaltung.activities') }}">Zurueck zur Liste</a>
    </form>

    <h2>Termine</h2>

    @if ($sessionMessage !== '')
        <p class="verwaltung-message" role="alert">{{ $sessionMessage }}</p>
    @endif

    @if (empty($sessions))
        <p>Noch keine Termine vorhanden.</p>
    @else
        <ul class="verwaltung-session-list">
            @foreach ($sessions as $session)
                <li class="{{ $session->vergangen ? 'vergangen' : '' }}">
                    {{ \Carbon\Carbon::parse($session->beginn)->format('d.m.Y H:i') }}
                    – {{ \Carbon\Carbon::parse($session->ende)->format('d.m.Y H:i') }}
                    @if ($session->ort)
                        | Ort: {{ $session->ort }}
                    @endif
                    @if ($session->hinweis)
                        | Hinweis: {{ $session->hinweis }}
                    @endif
                    @if ($session->vergangen)
                        <span>(vergangen)</span>
                    @endif
                    <button type="button" wire:click="startEditSession('{{ $session->id }}')">Bearbeiten</button>
                    <button type="button" wire:click="deleteSession('{{ $session->id }}')">Entfernen</button>
                </li>
            @endforeach
        </ul>
    @endif

    <h3>{{ $sessionId === '' ? 'Termin hinzufuegen' : 'Termin bearbeiten' }}</h3>
    <form wire:submit.prevent="{{ $sessionId === '' ? 'addSession' : 'saveSession' }}">
        <div>
            <label for="sessionStartsAt">Beginn *</label>
            <input id="sessionStartsAt" type="datetime-local" wire:model="sessionStartsAt" required>
        </div>

        <div>
            <label for="sessionEndsAt">Ende *</label>
            <input id="sessionEndsAt" type="datetime-local" wire:model="sessionEndsAt" required>
        </div>

        <div>
            <label for="sessionLocation">Ort</label>
            <input id="sessionLocation" type="text" wire:model="sessionLocation">
        </div>

        <div>
            <label for="sessionNote">Hinweis</label>
            <input id="sessionNote" type="text" wire:model="sessionNote">
        </div>

        <button type="submit">{{ $sessionId === '' ? 'Hinzufuegen' : 'Aktualisieren' }}</button>
        @if ($sessionId !== '')
            <button type="button" wire:click="cancelEditSession">Abbrechen</button>
        @endif
    </form>
</div>
