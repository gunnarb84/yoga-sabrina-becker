<div class="yoga-stack">
<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Teilnehmer/in bearbeiten</h1>
    </div>

    <div class="au-panel__body">
        @if ($message)
            <div class="au-status au-status--warn yoga-mb-2" role="alert">{{ $message }}</div>
        @endif

        @if ($saved)
            <div class="au-status au-status--ok yoga-mb-2" role="status">Die Änderungen wurden gespeichert.</div>
        @endif

        <form wire:submit="save" class="yoga-form">
            <div class="yoga-form-grid">
                <div class="au-field">
                    <label class="au-field__label" for="email">E-Mail *</label>
                    <input id="email" type="email" wire:model="email" class="au-field__input" required>
                </div>

                <div class="au-field">
                    <label class="au-field__label" for="firstName">Vorname *</label>
                    <input id="firstName" type="text" wire:model="firstName" class="au-field__input" required>
                </div>

                <div class="au-field">
                    <label class="au-field__label" for="lastName">Nachname *</label>
                    <input id="lastName" type="text" wire:model="lastName" class="au-field__input" required>
                </div>

                <div class="au-field">
                    <label class="au-field__label" for="addressLine1">Adresszeile 1</label>
                    <input id="addressLine1" type="text" wire:model="addressLine1" class="au-field__input">
                </div>

                <div class="au-field">
                    <label class="au-field__label" for="addressLine2">Adresszeile 2</label>
                    <input id="addressLine2" type="text" wire:model="addressLine2" class="au-field__input">
                </div>

                <div class="au-field">
                    <label class="au-field__label" for="postalCode">Postleitzahl</label>
                    <input id="postalCode" type="text" wire:model="postalCode" class="au-field__input">
                </div>

                <div class="au-field">
                    <label class="au-field__label" for="city">Stadt</label>
                    <input id="city" type="text" wire:model="city" class="au-field__input">
                </div>

                <div class="au-field">
                    <label class="au-field__label" for="phone">Telefon</label>
                    <input id="phone" type="text" wire:model="phone" class="au-field__input">
                </div>

                <div class="au-field">
                    <label class="au-field__label" for="dateOfBirth">Geburtsdatum</label>
                    <input id="dateOfBirth" type="date" wire:model="dateOfBirth" class="au-field__input">
                </div>
            </div>

            <label class="au-field yoga-mt-2" style="display:flex;align-items:center;gap:var(--au-s-2)">
                <input type="checkbox" wire:model="healthNotesConsent">
                <span class="au-field__label" style="margin:0">Einwilligung zur Speicherung der Gesundheitsinformationen liegt vor</span>
            </label>

            <button type="button" class="au-btn yoga-mt-2" wire:click="$toggle('showHealthNotes')">
                {{ $showHealthNotes ? 'Gesundheitsinformationen ausblenden' : 'Gesundheitsinformationen anzeigen' }}
            </button>

            @if ($showHealthNotes)
                <div class="au-field yoga-mt-2">
                    <label class="au-field__label" for="healthNotes">Gesundheitsinformationen</label>
                    <textarea id="healthNotes" wire:model="healthNotes" class="au-field__input" rows="4"></textarea>
                </div>
            @endif

            <div class="yoga-form-actions yoga-mt-3">
                <button type="submit" class="au-btn au-btn--primary">Speichern</button>
                <a href="{{ route('verwaltung.participants') }}" class="au-btn">Zurück zur Liste</a>
            </div>
        </form>
    </div>
</div>

<div class="au-panel yoga-mt-3">
    <div class="au-panel__header">
        <h2 class="au-panel__title">Anmeldungs-Historie</h2>
    </div>

    <div class="au-panel__body">
        @if (empty($registrations))
            <p class="yoga-empty">Noch keine Anmeldungen vorhanden.</p>
        @else
            <div style="overflow-x:auto">
                <table class="au-list">
                    <thead>
                        <tr>
                            <th>Veranstaltung</th>
                            <th>Status</th>
                            <th>Anmeldedatum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($registrations as $registration)
                            <tr>
                                <td>{{ $registration->aktivitaet_titel }}</td>
                                <td>{{ ucfirst($registration->status) }}</td>
                                <td>{{ $registration->anmeldedatum }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
</div>
