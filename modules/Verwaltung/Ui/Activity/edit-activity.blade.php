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
            <label class="au-field">
                <span class="au-field__label">type">Typ</span>
                <select id="type" wire:model="type" class="au-field__select">
                    @foreach ($types as $option)
                        <option value="{{ $option->value }}">{{ $option->label }}</option>
                    @endforeach
                </select>
            </label>

            <label class="au-field">
                <span class="au-field__label">title">Titel *</span>
                <input id="title" type="text" wire:model="title" required class="au-field__input">
            </label>

            <label class="au-field">
                <span class="au-field__label">shortDescription">Kurzbeschreibung</span>
                <textarea id="shortDescription" wire:model="shortDescription" rows="3" class="au-field__textarea"></textarea>
            </label>

            <label class="au-field">
                <span class="au-field__label">longDescription">Langbeschreibung</span>
                <textarea id="longDescription" wire:model="longDescription" rows="6" class="au-field__textarea"></textarea>
            </label>

            <label class="au-field">
                <span class="au-field__label">price">Preis (EUR) *</span>
                <input id="price" type="number" step="0.01" min="0" wire:model="price" required class="au-field__input">
            </label>

            <label class="au-field">
                <span class="au-field__label">maxParticipants">Maximale Teilnehmerzahl *</span>
                <input id="maxParticipants" type="number" min="1" wire:model="maxParticipants" required class="au-field__input">
            </label>

            <div class="yoga-form-actions yoga-mt-3">
                <button type="submit" class="au-btn au-btn--primary">Speichern</button>
                <a href="{{ route('verwaltung.activities') }}" class="au-btn yoga-ml-2">Zurück zur Liste</a>
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
            <label class="au-field">
                <span class="au-field__label">sessionStartsAt">Beginn *</span>
                <input id="sessionStartsAt" type="datetime-local" wire:model="sessionStartsAt" required class="au-field__input">
            </label>

            <label class="au-field">
                <span class="au-field__label">sessionEndsAt">Ende *</span>
                <input id="sessionEndsAt" type="datetime-local" wire:model="sessionEndsAt" required class="au-field__input">
            </label>

            <label class="au-field">
                <span class="au-field__label">sessionLocation">Ort</span>
                <input id="sessionLocation" type="text" wire:model="sessionLocation" class="au-field__input">
            </label>

            <label class="au-field">
                <span class="au-field__label">sessionNote">Hinweis</span>
                <input id="sessionNote" type="text" wire:model="sessionNote" class="au-field__input">
            </label>

            <div class="yoga-form-actions yoga-mt-3">
                <button type="submit" class="au-btn au-btn--primary">{{ $sessionId === '' ? 'Hinzufügen' : 'Aktualisieren' }}</button>
                @if ($sessionId !== '')
                    <button type="button" class="au-btn" wire:click="cancelEditSession">Abbrechen</button>
                @endif
            </div>
        </form>
    </div>
</div>
</div>
