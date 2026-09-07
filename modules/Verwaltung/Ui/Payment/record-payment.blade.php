<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Zahlung erfassen</h1>
    </div>

    <div class="au-panel__body">
        <form wire:submit="save" class="yoga-form">
            <div class="yoga-form-grid">
                <div class="au-field">
                    <label class="au-field__label" for="amount">Betrag (EUR)</label>
                    <input type="number" step="0.01" id="amount" wire:model="amount" class="au-field__input" required>
                </div>

                <div class="au-field">
                    <label class="au-field__label" for="paidAt">Zahlungsdatum</label>
                    <input type="datetime-local" id="paidAt" wire:model="paidAt" class="au-field__input" required>
                </div>

                <div class="au-field">
                    <label class="au-field__label" for="recipient">Empfänger/in</label>
                    <input type="text" id="recipient" wire:model="recipient" class="au-field__input" required>
                </div>
            </div>

            <div class="yoga-form-actions yoga-mt-3">
                <button type="submit" class="au-btn au-btn--primary">Zahlung speichern</button>
                <a href="{{ route('verwaltung.activity.registrations', ['id' => $activityId]) }}" class="au-btn">Abbrechen</a>
            </div>
        </form>
    </div>
</div>
