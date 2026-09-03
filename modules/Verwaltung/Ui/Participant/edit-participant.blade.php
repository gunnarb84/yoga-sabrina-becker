<div class="verwaltung-edit-participant">
    <h1>Teilnehmer/in bearbeiten</h1>

    @if ($message)
        <div class="verwaltung-message" role="alert">{{ $message }}</div>
    @endif

    @if ($saved)
        <div class="verwaltung-success" role="status">Die Aenderungen wurden gespeichert.</div>
    @endif

    <form wire:submit="save">
        <div>
            <label for="email">E-Mail *</label>
            <input id="email" type="email" wire:model="email" required>
        </div>

        <div>
            <label for="firstName">Vorname *</label>
            <input id="firstName" type="text" wire:model="firstName" required>
        </div>

        <div>
            <label for="lastName">Nachname *</label>
            <input id="lastName" type="text" wire:model="lastName" required>
        </div>

        <div>
            <label for="addressLine1">Adresszeile 1</label>
            <input id="addressLine1" type="text" wire:model="addressLine1">
        </div>

        <div>
            <label for="addressLine2">Adresszeile 2</label>
            <input id="addressLine2" type="text" wire:model="addressLine2">
        </div>

        <div>
            <label for="postalCode">Postleitzahl</label>
            <input id="postalCode" type="text" wire:model="postalCode">
        </div>

        <div>
            <label for="city">Stadt</label>
            <input id="city" type="text" wire:model="city">
        </div>

        <div>
            <label for="phone">Telefon</label>
            <input id="phone" type="text" wire:model="phone">
        </div>

        <div>
            <label for="dateOfBirth">Geburtsdatum</label>
            <input id="dateOfBirth" type="date" wire:model="dateOfBirth">
        </div>

        <div>
            <label for="healthNotes">Gesundheitsinformationen</label>
            <textarea id="healthNotes" wire:model="healthNotes" rows="4"></textarea>
        </div>

        <div>
            <label>
                <input type="checkbox" wire:model="healthNotesConsent">
                Einwilligung zur Speicherung der Gesundheitsinformationen liegt vor
            </label>
        </div>

        <button type="submit">Speichern</button>
        <a href="{{ route('verwaltung.participants') }}">Zurueck zur Liste</a>
    </form>

    <h2>Anmeldungs-Historie</h2>
    @if (empty($registrations))
        <p>Noch keine Anmeldungen vorhanden.</p>
    @else
        <table class="verwaltung-table">
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
    @endif
</div>
