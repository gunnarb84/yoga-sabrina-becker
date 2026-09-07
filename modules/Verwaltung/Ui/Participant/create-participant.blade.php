<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Neuen Teilnehmer anlegen</h1>
    </div>

    <div class="au-panel__body">
        @if ($message)
            <div class="au-status au-status--error yoga-mb-2" role="alert">{{ $message }}</div>
        @endif

        @if ($created)
            <div class="au-status au-status--ok yoga-mb-2" role="alert">Der Teilnehmer wurde angelegt.</div>
        @endif

        <form wire:submit="save" class="yoga-form">
            <div class="yoga-form-grid">
                <label class="au-field">
                    <span class="au-field__label">E-Mail</span>
                    <input type="email" wire:model="email" class="au-field__input" required>
                </label>

                <label class="au-field">
                    <span class="au-field__label">Vorname</span>
                    <input type="text" wire:model="firstName" class="au-field__input" required>
                </label>

                <label class="au-field">
                    <span class="au-field__label">Nachname</span>
                    <input type="text" wire:model="lastName" class="au-field__input" required>
                </label>

                <label class="au-field">
                    <span class="au-field__label">Adresse</span>
                    <input type="text" wire:model="addressLine1" class="au-field__input">
                </label>

                <label class="au-field">
                    <span class="au-field__label">Adresszusatz</span>
                    <input type="text" wire:model="addressLine2" class="au-field__input">
                </label>

                <label class="au-field">
                    <span class="au-field__label">PLZ</span>
                    <input type="text" wire:model="postalCode" class="au-field__input">
                </label>

                <label class="au-field">
                    <span class="au-field__label">Ort</span>
                    <input type="text" wire:model="city" class="au-field__input">
                </label>

                <label class="au-field">
                    <span class="au-field__label">Telefon</span>
                    <input type="text" wire:model="phone" class="au-field__input">
                </label>

                <label class="au-field">
                    <span class="au-field__label">Geburtsdatum</span>
                    <input type="date" wire:model="dateOfBirth" class="au-field__input">
                </label>
            </div>

            <label class="au-field yoga-mt-2">
                <span class="au-field__label">Gesundheitsinformationen</span>
                <textarea wire:model="healthNotes" class="au-field__input" rows="4"></textarea>
            </label>

            <div class="yoga-form-actions yoga-mt-3">
                <button type="submit" class="au-btn au-btn--primary">Speichern</button>
                <a href="{{ route('verwaltung.participants') }}" class="au-btn">Zurück</a>
            </div>
        </form>
    </div>
</div>
