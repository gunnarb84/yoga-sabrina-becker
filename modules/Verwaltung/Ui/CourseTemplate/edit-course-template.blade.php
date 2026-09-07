<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Kursvorlage bearbeiten</h1>
    </div>

    <div class="au-panel__body">
        @if ($message)
            <div class="au-status {{ str_contains($message, 'Fehler') ? 'au-status--error' : 'au-status--ok' }} yoga-mb-2" role="alert">{{ $message }}</div>
        @endif

        @if ($saved)
            <div class="au-status au-status--ok yoga-mb-2" role="alert">Die Vorlage wurde gespeichert.</div>
        @endif

        <form wire:submit="save" class="yoga-form">
            <div class="yoga-form-grid">
                <label class="au-field">
                    <span class="au-field__label">Titel</span>
                    <input type="text" wire:model="title" class="au-field__input" required>
                </label>

                <label class="au-field">
                    <span class="au-field__label">Wochentag</span>
                    <select wire:model="weekday" class="au-field__input">
                        @foreach ($weekdayOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="au-field">
                    <span class="au-field__label">Startzeit</span>
                    <input type="time" wire:model="startTime" class="au-field__input" required>
                </label>

                <label class="au-field">
                    <span class="au-field__label">Dauer (Minuten)</span>
                    <input type="number" wire:model="durationMinutes" class="au-field__input" min="1" required>
                </label>

                <label class="au-field">
                    <span class="au-field__label">Anzahl Termine</span>
                    <input type="number" wire:model="sessionCount" class="au-field__input" min="1" required>
                </label>

                <label class="au-field">
                    <span class="au-field__label">Preis (z. B. 120,00)</span>
                    <input type="text" wire:model="price" class="au-field__input" required>
                </label>

                <label class="au-field">
                    <span class="au-field__label">Max. Teilnehmerzahl</span>
                    <input type="number" wire:model="maxParticipants" class="au-field__input" min="1" required>
                </label>

                <label class="au-field">
                    <span class="au-field__label">Ort</span>
                    <input type="text" wire:model="location" class="au-field__input">
                </label>
            </div>

            <label class="au-field yoga-mt-2">
                <span class="au-field__label">Kurzbeschreibung</span>
                <textarea wire:model="shortDescription" class="au-field__input" rows="3"></textarea>
            </label>

            <label class="au-field yoga-mt-2">
                <span class="au-field__label">Langbeschreibung</span>
                <textarea wire:model="longDescription" class="au-field__input" rows="6"></textarea>
            </label>

            <div class="yoga-form-actions yoga-mt-3">
                <button type="submit" class="au-btn au-btn--primary">Änderungen speichern</button>
                <button type="button" class="au-btn au-btn--danger" wire:click="delete">Löschen</button>
                <a href="{{ route('verwaltung.course-templates') }}" class="au-btn">Zurück zur Übersicht</a>
            </div>
        </form>
    </div>
</div>
