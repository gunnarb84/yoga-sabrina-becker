<div class="yoga-section yoga-section--creme yoga-section--flush">
<div class="webseite-registration">
    @if ($submitted)
        <div class="yoga-success-box">
            <h1>Anmeldung erfolgreich</h1>

            <p class="yoga-lead">Vielen Dank für Ihre Anmeldung zu <strong>{{ $activity->titel }}</strong>.</p>

            @if ($onWaitingList)
                <p class="yoga-status-warteliste">Die Veranstaltung ist aktuell ausgebucht. Sie stehen auf der Warteliste.</p>
            @else
                <p class="yoga-status-buchbar">Ihr Platz ist bestätigt.</p>
            @endif

            <p class="yoga-activity-price">{{ number_format((float) $activity->preis, 2, ',', '.') }} EUR</p>

            <p class="yoga-mt-2"><a href="{{ route('activities') }}" class="yoga-btn-ghost">Zurück zur Übersicht</a></p>
        </div>
    @else
        <div class="yoga-section-header">
            <h1>Anmeldung: {{ $activity->titel }}</h1>
        </div>

        @if ($activity !== null && ! empty($activity->termine))
            <p class="yoga-lead">
                Nächster Termin: {{ \Carbon\Carbon::parse($activity->termine[0]->beginn)->format('d.m.Y H:i') }}
            </p>
        @endif

        @if ($error !== '')
            <p class="error">{{ $error }}</p>
        @endif

        <form wire:submit="submit" class="yoga-form">
            <div class="yoga-form-group">
                <label for="firstName">Vorname *</label>
                <input type="text" id="firstName" wire:model="firstName" required>
            </div>

            <div class="yoga-form-group">
                <label for="lastName">Nachname *</label>
                <input type="text" id="lastName" wire:model="lastName" required>
            </div>

            <div class="yoga-form-group">
                <label for="email">E-Mail *</label>
                <input type="email" id="email" wire:model="email" required>
            </div>

            <div class="yoga-form-group">
                <label for="addressLine1">Adresse</label>
                <input type="text" id="addressLine1" wire:model="addressLine1">
            </div>

            <div class="yoga-form-group">
                <label for="postalCode">Postleitzahl</label>
                <input type="text" id="postalCode" wire:model="postalCode">
            </div>

            <div class="yoga-form-group">
                <label for="city">Ort</label>
                <input type="text" id="city" wire:model="city">
            </div>

            <div class="yoga-form-group">
                <label for="phone">Telefon</label>
                <input type="tel" id="phone" wire:model="phone">
            </div>

            <div class="yoga-form-group">
                <label for="dateOfBirth">Geburtsdatum</label>
                <input type="date" id="dateOfBirth" wire:model="dateOfBirth">
            </div>

            <div class="yoga-form-group">
                <label for="healthNotes">Gesundheitsinformationen</label>
                <textarea id="healthNotes" wire:model="healthNotes" rows="3"></textarea>
            </div>

            <div class="yoga-form-group">
                <label class="yoga-checkbox-label">
                    <input type="checkbox" wire:model="healthNotesConsent">
                    Ich stimme der Speicherung der Gesundheitsinformationen für den Kursbetrieb zu.
                </label>
            </div>

            @if ((float) $activity->preis !== 0.0)
                <div class="yoga-form-group">
                    <label for="paymentMethod">Zahlungsart *</label>
                    <select id="paymentMethod" wire:model="paymentMethod" required>
                        @foreach ($paymentMethods as $method)
                            <option value="{{ $method['value'] }}" {{ $method['value'] === $paymentMethod ? 'selected' : '' }}>
                                {{ $method['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <p class="yoga-status-buchbar">Diese Veranstaltung ist kostenlos.</p>
            @endif

            <div class="yoga-form-group yoga-mt-2">
                <button type="submit">Anmeldung absenden</button>
            </div>
        </form>
    @endif
</div>
</div>
