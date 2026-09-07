<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Teilnehmer anmelden</h1>
    </div>

    <div class="au-panel__body">
        @if ($message)
            <div class="au-status au-status--error yoga-mb-2" role="alert">{{ $message }}</div>
        @endif

        @if ($registered)
            <div class="au-status au-status--ok yoga-mb-2" role="alert">Die Anmeldung wurde durchgeführt.</div>
        @endif

        @if (empty($participants))
            <p class="yoga-empty">Bitte zuerst einen Teilnehmer anlegen.</p>
            <p><a href="{{ route('verwaltung.participant.create') }}" class="au-btn au-btn--primary">Teilnehmer anlegen</a></p>
        @else
            <form wire:submit="register" class="yoga-form">
                <label class="au-field">
                    <span class="au-field__label">Teilnehmer</span>
                    <select wire:model="participantId" class="au-field__input" required>
                        <option value="">Bitte wählen</option>
                        @foreach ($participants as $participant)
                            <option value="{{ $participant->id }}">{{ $participant->vorname }} {{ $participant->nachname }} ({{ $participant->email }})</option>
                        @endforeach
                    </select>
                </label>

                <label class="au-field">
                    <span class="au-field__label">Zahlungsart</span>
                    <select wire:model="paymentMethod" class="au-field__input" required>
                        @foreach ($methods as $method)
                            <option value="{{ $method['value'] }}">{{ $method['label'] }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="yoga-form-actions yoga-mt-3">
                    <button type="submit" class="au-btn au-btn--primary">Anmelden</button>
                    <a href="{{ route('verwaltung.activity.registrations', ['id' => $activityId]) }}" class="au-btn">Zurück</a>
                </div>
            </form>
        @endif
    </div>
</div>
