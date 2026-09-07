<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Neuen Termin anlegen</h1>
    </div>

    <div class="au-panel__body">
        @if ($message)
            <div class="au-status au-status--error yoga-mb-2" role="alert">{{ $message }}</div>
        @endif

        @if ($created)
            <div class="au-status au-status--ok yoga-mb-2" role="alert">Der Termin wurde angelegt.</div>
        @endif

        <form wire:submit="save" class="yoga-form">
            <div class="yoga-form-grid">
                <label class="au-field">
                    <span class="au-field__label">Beginn</span>
                    <input type="datetime-local" wire:model="startsAt" class="au-field__input" required>
                </label>

                <label class="au-field">
                    <span class="au-field__label">Ende</span>
                    <input type="datetime-local" wire:model="endsAt" class="au-field__input" required>
                </label>

                <label class="au-field">
                    <span class="au-field__label">Ort</span>
                    <input type="text" wire:model="location" class="au-field__input">
                </label>
            </div>

            <label class="au-field yoga-mt-2">
                <span class="au-field__label">Hinweis</span>
                <textarea wire:model="note" class="au-field__input" rows="4"></textarea>
            </label>

            <div class="yoga-form-actions yoga-mt-3">
                <button type="submit" class="au-btn au-btn--primary">Speichern</button>
                <a href="{{ route('verwaltung.activity.edit', ['id' => $activityId]) }}" class="au-btn">Zurück</a>
            </div>
        </form>
    </div>
</div>
