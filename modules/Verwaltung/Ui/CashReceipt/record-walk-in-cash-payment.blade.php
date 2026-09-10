<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Bareinnahme erfassen</h1>
    </div>

    <div class="au-panel__body">
        @if ($message)
            <div class="au-status au-status--error yoga-mb-2" role="alert">{{ $message }}</div>
        @endif

        @if ($recorded)
            <div class="au-status au-status--ok yoga-mb-2" role="alert">
                Bareinnahme erfasst. Belegnummer: {{ $receiptNumberRecorded }}
                <a href="{{ route('verwaltung.bareinnahme.pdf', ['id' => $receiptId]) }}">Barquittung als PDF herunterladen</a>
            </div>
        @endif

        <form wire:submit="save" class="yoga-form">
            <div class="yoga-form-grid">
                <label class="au-field">
                    <span class="au-field__label">Veranstaltung</span>
                    <select wire:model="activityId" class="au-field__input" required>
                        <option value="">Bitte wählen</option>
                        @foreach ($activities as $activity)
                            <option value="{{ $activity->id }}">{{ $activity->titel }}</option>
                        @endforeach
                    </select>
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
                    <span class="au-field__label">E-Mail-Adresse (optional)</span>
                    <input type="email" wire:model="email" class="au-field__input">
                </label>

                <label class="au-field">
                    <span class="au-field__label">Betrag (EUR)</span>
                    <input type="number" step="0.01" min="0.01" wire:model="amount" class="au-field__input" required>
                </label>

                <label class="au-field">
                    <span class="au-field__label">Zahlungsdatum</span>
                    <input type="datetime-local" wire:model="paidAt" class="au-field__input" required>
                </label>
            </div>

            <label class="au-field yoga-mt-2">
                <input type="checkbox" wire:model.live="nachpflege">
                <span class="au-field__label">Handschriftlichen Beleg nachpflegen (vorgegebene Belegnummer)</span>
            </label>

            @if ($nachpflege)
                <div class="yoga-form-grid">
                    <label class="au-field">
                        <span class="au-field__label">Belegnummer</span>
                        <input type="text" wire:model="receiptNumber" class="au-field__input" placeholder="2026-00012" required>
                    </label>

                    <label class="au-field">
                        <span class="au-field__label">Ausstellungsdatum</span>
                        <input type="date" wire:model="issuedAt" class="au-field__input" required>
                    </label>
                </div>
            @endif

            <div class="yoga-form-actions yoga-mt-3">
                <button type="submit" class="au-btn au-btn--primary">Bareinnahme speichern</button>
                <a href="{{ route('verwaltung.cash-receipts') }}" class="au-btn">Zur Bareinnahmenliste</a>
            </div>
        </form>
    </div>
</div>