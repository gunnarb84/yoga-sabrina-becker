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
            <div class="yoga-form-group">
                <label for="type">Typ</label>
                <select id="type" wire:model="type" class="au-field__select">
                    @foreach ($types as $option)
                        <option value="{{ $option->value }}">{{ $option->label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="yoga-form-group">
                <label for="title">Titel</label>
                <input type="text" id="title" wire:model="title" required class="au-field__input">
            </div>

            <div class="yoga-form-group">
                <label for="shortDescription">Kurzbeschreibung</label>
                <textarea id="shortDescription" wire:model="shortDescription" rows="2" class="au-field__textarea"></textarea>
            </div>

            <div class="yoga-form-group">
                <label for="longDescription">Langbeschreibung</label>
                <textarea id="longDescription" wire:model="longDescription" rows="4" class="au-field__textarea"></textarea>
            </div>

            <div class="yoga-form-group">
                <label for="price">Preis (EUR)</label>
                <input type="number" id="price" step="0.01" wire:model="price" required class="au-field__input">
            </div>

            <div class="yoga-form-group">
                <label for="maxParticipants">Maximale Teilnehmerzahl</label>
                <input type="number" id="maxParticipants" wire:model="maxParticipants" required class="au-field__input">
            </div>

            <div class="yoga-form-group yoga-mt-2">
                <button type="submit" class="yoga-btn-primary">Speichern</button>
            </div>
        </form>
    </div>
</div>
