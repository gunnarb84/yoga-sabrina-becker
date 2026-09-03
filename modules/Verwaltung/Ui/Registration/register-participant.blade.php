<div class="verwaltung-register-participant">
    <h1>Teilnehmer anmelden</h1>

    @if ($message)
        <p class="message">{{ $message }}</p>
    @endif

    @if ($registered)
        <p class="success">Die Anmeldung wurde durchgefuehrt.</p>
    @endif

    @if (empty($participants))
        <p>Bitte zuerst einen Teilnehmer anlegen.</p>
    @else
        <form wire:submit="register">
            <label>
                Teilnehmer
                <select wire:model="participantId" required>
                    <option value="">Bitte waehlen</option>
                    @foreach ($participants as $participant)
                        <option value="{{ $participant->id }}">{{ $participant->vorname }} {{ $participant->nachname }} ({{ $participant->email }})</option>
                    @endforeach
                </select>
            </label>

            <label>
                Zahlungsart
                <select wire:model="paymentMethod" required>
                    @foreach ($methods as $method)
                        <option value="{{ $method['value'] }}">{{ $method['label'] }}</option>
                    @endforeach
                </select>
            </label>

            <button type="submit">Anmelden</button>
        </form>
    @endif
</div>
