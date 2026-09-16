<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Neue Aktivität anlegen</h1>
    </div>

    <div class="au-panel__body">
        @if ($message)
            <div class="au-status au-status--warn yoga-mb-2" role="alert">{{ $message }}</div>
        @endif

        @if ($created)
            <div class="au-status au-status--ok yoga-mb-2">Die Aktivität wurde angelegt.</div>
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
                <span class="au-field__label">title">Titel</span>
                <input type="text" id="title" wire:model="title" required class="au-field__input">
            </label>

            <label class="au-field">
                <span class="au-field__label">shortDescription">Kurzbeschreibung</span>
                <textarea id="shortDescription" wire:model="shortDescription" rows="2" class="au-field__textarea"></textarea>
            </label>

            <label class="au-field">
                <span class="au-field__label">longDescription">Langbeschreibung</span>
                <textarea id="longDescription" wire:model="longDescription" rows="4" class="au-field__textarea"></textarea>
            </label>

            <label class="au-field">
                <span class="au-field__label">price">Preis (EUR)</span>
                <input type="number" id="price" step="0.01" wire:model="price" required class="au-field__input">
            </label>

            <label class="au-field">
                <span class="au-field__label">maxParticipants">Maximale Teilnehmerzahl</span>
                <input type="number" id="maxParticipants" wire:model="maxParticipants" required class="au-field__input">
            </label>

            <div class="yoga-form-actions yoga-mt-3">
                <button type="submit" class="au-btn au-btn--primary">Speichern</button>
            </div>
        </form>
    </div>
</div>
