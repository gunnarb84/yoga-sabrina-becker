<div class="webseite-registration">
    @if ($submitted)
        <h1>Anmeldung erfolgreich</h1>

        <p>Vielen Dank für Ihre Anmeldung zu <strong>{{ $activity->titel }}</strong>.</p>

        @if ($onWaitingList)
            <p>Die Veranstaltung ist aktuell ausgebucht. Sie stehen auf der Warteliste.</p>
        @else
            <p>Ihr Platz ist bestätigt.</p>
        @endif

        <p>Preis: {{ number_format((float) $activity->preis, 2, ',', '.') }} EUR</p>

        <p><a href="{{ route('activities') }}">Zurück zur Übersicht</a></p>
    @else
        <h1>Anmeldung: {{ $activity->titel }}</h1>

        @if ($activity !== null && ! empty($activity->termine))
            <p>Nächster Termin: {{ \Carbon\Carbon::parse($activity->termine[0]->beginn)->format('d.m.Y H:i') }}</p>
        @endif

        @if ($error !== '')
            <p class="error">{{ $error }}</p>
        @endif

        <form wire:submit="submit">
            <p>
                <label for="firstName">Vorname *</label>
                <input type="text" id="firstName" wire:model="firstName" required>
            </p>

            <p>
                <label for="lastName">Nachname *</label>
                <input type="text" id="lastName" wire:model="lastName" required>
            </p>

            <p>
                <label for="email">E-Mail *</label>
                <input type="email" id="email" wire:model="email" required>
            </p>

            <p>
                <label for="addressLine1">Adresse</label>
                <input type="text" id="addressLine1" wire:model="addressLine1">
            </p>

            <p>
                <label for="postalCode">Postleitzahl</label>
                <input type="text" id="postalCode" wire:model="postalCode">
            </p>

            <p>
                <label for="city">Ort</label>
                <input type="text" id="city" wire:model="city">
            </p>

            <p>
                <label for="phone">Telefon</label>
                <input type="tel" id="phone" wire:model="phone">
            </p>

            <p>
                <label for="dateOfBirth">Geburtsdatum</label>
                <input type="date" id="dateOfBirth" wire:model="dateOfBirth">
            </p>

            <p>
                <label for="healthNotes">Gesundheitsinformationen</label>
                <textarea id="healthNotes" wire:model="healthNotes" rows="3"></textarea>
            </p>

            <p>
                <label>
                    <input type="checkbox" wire:model="healthNotesConsent">
                    Ich stimme der Speicherung der Gesundheitsinformationen für den Kursbetrieb zu.
                </label>
            </p>

            @if ((float) $activity->preis !== 0.0)
                <p>
                    <label for="paymentMethod">Zahlungsart *</label>
                    <select id="paymentMethod" wire:model="paymentMethod" required>
                        @foreach ($paymentMethods as $method)
                            <option value="{{ $method['value'] }}" {{ $method['value'] === $paymentMethod ? 'selected' : '' }}>
                                {{ $method['label'] }}
                            </option>
                        @endforeach
                    </select>
                </p>
            @else
                <p>Diese Veranstaltung ist kostenlos.</p>
            @endif

            <p>
                <button type="submit">Anmeldung absenden</button>
            </p>
        </form>
    @endif
</div>
