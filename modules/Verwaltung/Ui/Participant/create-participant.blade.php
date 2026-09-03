<div class="verwaltung-create-participant">
    <h1>Neuen Teilnehmer anlegen</h1>

    @if ($message)
        <p class="message">{{ $message }}</p>
    @endif

    @if ($created)
        <p class="success">Der Teilnehmer wurde angelegt.</p>
    @endif

    <form wire:submit="save">
        <label>
            E-Mail
            <input type="email" wire:model="email" required>
        </label>

        <label>
            Vorname
            <input type="text" wire:model="firstName" required>
        </label>

        <label>
            Nachname
            <input type="text" wire:model="lastName" required>
        </label>

        <label>
            Adresse
            <input type="text" wire:model="addressLine1">
        </label>

        <label>
            Adresszusatz
            <input type="text" wire:model="addressLine2">
        </label>

        <label>
            PLZ
            <input type="text" wire:model="postalCode">
        </label>

        <label>
            Ort
            <input type="text" wire:model="city">
        </label>

        <label>
            Telefon
            <input type="text" wire:model="phone">
        </label>

        <label>
            Geburtsdatum
            <input type="date" wire:model="dateOfBirth">
        </label>

        <label>
            Gesundheitsinformationen
            <textarea wire:model="healthNotes"></textarea>
        </label>

        <button type="submit">Speichern</button>
    </form>
</div>
