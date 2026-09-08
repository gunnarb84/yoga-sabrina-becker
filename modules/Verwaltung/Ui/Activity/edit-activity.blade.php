<div class="yoga-stack">
<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Aktivität bearbeiten</h1>
        <span class="au-status {{ $statusLabel === 'veroeffentlicht' ? 'au-status--ok' : 'au-status--warn' }}">{{ ucfirst($statusLabel) }}</span>
    </div>

    <div class="au-panel__body">
        @if ($message)
            <div class="au-status au-status--warn yoga-mb-2" role="alert">{{ $message }}</div>
        @endif

        @if ($saved)
            <div class="au-status au-status--ok yoga-mb-2" role="status">Die Änderungen wurden gespeichert.</div>
        @endif

        <form wire:submit="save" class="yoga-form">
            <div class="yoga-form-group">
                <label for="type">Typ</label>
                <select id="type" wire:model="type" class="au-field__select">
                    @foreach ($types as $option)
                        <option value="{{ $option->value }}">{{ $option->label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="yoga-form-group">
                <label for="title">Titel *</label>
                <input id="title" type="text" wire:model="title" required class="au-field__input">
            </div>

            <div class="yoga-form-group">
                <label for="shortDescription">Kurzbeschreibung</label>
                <textarea id="shortDescription" wire:model="shortDescription" rows="3" class="au-field__textarea"></textarea>
            </div>

            <div class="yoga-form-group">
                <label for="longDescription">Langbeschreibung</label>
                <textarea id="longDescription" wire:model="longDescription" rows="6" class="au-field__textarea"></textarea>
            </div>

            <div class="yoga-form-group">
                <label for="price">Preis (EUR) *</label>
                <input id="price" type="number" step="0.01" min="0" wire:model="price" required class="au-field__input">
            </div>

            <div class="yoga-form-group">
                <label for="maxParticipants">Maximale Teilnehmerzahl *</label>
                <input id="maxParticipants" type="number" min="1" wire:model="maxParticipants" required class="au-field__input">
            </div>

            <div class="yoga-form-group yoga-mt-2">
                <button type="submit" class="yoga-btn-primary">Speichern</button>
                <a href="{{ route('verwaltung.activities') }}" class="yoga-btn-ghost yoga-ml-2">Zurück zur Liste</a>
            </div>
        </form>
    </div>
</div>

<div class="au-panel">
    <div class="au-panel__header">
        <h2 class="au-panel__title">Termine</h2>
    </div>

    <div class="au-panel__body">
        @if ($sessionMessage !== '')
            <div class="au-status au-status--warn yoga-mb-2" role="alert">{{ $sessionMessage }}</div>
        @endif

        @if (empty($sessions))
            <p class="yoga-empty">Noch keine Termine vorhanden.</p>
        @else
            <ul class="yoga-activity-sessions">
                @foreach ($sessions as $session)
                    <li class="{{ $session->vergangen ? 'yoga-status-not-bookable' : '' }}">
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
                        <div style="display:flex;gap:8px;margin-top:10px">
                            <button type="button" class="au-btn au-btn--sm" wire:click="startEditSession('{{ $session->id }}')">Bearbeiten</button>
                            <button type="button" class="au-btn au-btn--sm" wire:click="deleteSession('{{ $session->id }}')">Entfernen</button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        <h3 style="margin-top:28px">{{ $sessionId === '' ? 'Termin hinzufügen' : 'Termin bearbeiten' }}</h3>
        <form wire:submit.prevent="{{ $sessionId === '' ? 'addSession' : 'saveSession' }}" class="yoga-form">
            <div class="yoga-form-group">
                <label for="sessionStartsAt">Beginn *</label>
                <input id="sessionStartsAt" type="datetime-local" wire:model="sessionStartsAt" required class="au-field__input">
            </div>

            <div class="yoga-form-group">
                <label for="sessionEndsAt">Ende *</label>
                <input id="sessionEndsAt" type="datetime-local" wire:model="sessionEndsAt" required class="au-field__input">
            </div>

            <div class="yoga-form-group">
                <label for="sessionLocation">Ort</label>
                <input id="sessionLocation" type="text" wire:model="sessionLocation" class="au-field__input">
            </div>

            <div class="yoga-form-group">
                <label for="sessionNote">Hinweis</label>
                <input id="sessionNote" type="text" wire:model="sessionNote" class="au-field__input">
            </div>

            <div class="yoga-form-group yoga-mt-2">
                <button type="submit" class="yoga-btn-primary">{{ $sessionId === '' ? 'Hinzufügen' : 'Aktualisieren' }}</button>
                @if ($sessionId !== '')
                    <button type="button" class="yoga-btn-secondary" wire:click="cancelEditSession">Abbrechen</button>
                @endif
            </div>
        </form>
    </div>
</div>
</div>
