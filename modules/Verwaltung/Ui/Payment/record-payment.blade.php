<div class="verwaltung-payment">
    <h1>Zahlung erfassen</h1>

    <form wire:submit="save">
        <p>
            <label for="amount">Betrag (EUR)</label>
            <input type="number" step="0.01" id="amount" wire:model="amount" required>
        </p>

        <p>
            <label for="paidAt">Zahlungsdatum</label>
            <input type="datetime-local" id="paidAt" wire:model="paidAt" required>
        </p>

        <p>
            <label for="recipient">Empfänger/in</label>
            <input type="text" id="recipient" wire:model="recipient" required>
        </p>

        <p>
            <button type="submit">Zahlung speichern</button>
            <a href="{{ route('verwaltung.activity.registrations', ['id' => $activityId]) }}">Abbrechen</a>
        </p>
    </form>
</div>
