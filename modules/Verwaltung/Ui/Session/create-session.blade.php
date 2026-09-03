<div class="verwaltung-create-session">
    <h1>Neuen Termin anlegen</h1>

    @if ($message)
        <p class="message">{{ $message }}</p>
    @endif

    @if ($created)
        <p class="success">Der Termin wurde angelegt.</p>
    @endif

    <form wire:submit="save">
        <label>
            Beginn
            <input type="datetime-local" wire:model="startsAt" required>
        </label>

        <label>
            Ende
            <input type="datetime-local" wire:model="endsAt" required>
        </label>

        <label>
            Ort
            <input type="text" wire:model="location">
        </label>

        <label>
            Hinweis
            <textarea wire:model="note"></textarea>
        </label>

        <button type="submit">Speichern</button>
    </form>
</div>
