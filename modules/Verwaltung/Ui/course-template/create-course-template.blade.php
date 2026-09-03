<div class="verwaltung-create-course-template">
    <h1>Neue Kursvorlage</h1>

    @if ($message !== '')
        <p class="verwaltung-message">{{ $message }}</p>
    @endif

    @if ($created)
        <p class="verwaltung-success">Die Vorlage wurde angelegt.</p>
    @endif

    <form wire:submit="save">
        <label>
            Titel
            <input type="text" wire:model="title" required>
        </label>

        <label>
            Wochentag
            <select wire:model="weekday" required>
                @foreach ($weekdayOptions as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
        </label>

        <label>
            Startzeit
            <input type="time" wire:model="startTime" required>
        </label>

        <label>
            Dauer (Minuten)
            <input type="number" min="1" wire:model="durationMinutes" required>
        </label>

        <label>
            Anzahl Termine
            <input type="number" min="1" wire:model="sessionCount" required>
        </label>

        <label>
            Preis (EUR)
            <input type="number" step="0.01" wire:model="price" required>
        </label>

        <label>
            Maximale Teilnehmerzahl
            <input type="number" min="1" wire:model="maxParticipants" required>
        </label>

        <label>
            Kurzbeschreibung
            <textarea wire:model="shortDescription"></textarea>
        </label>

        <label>
            Langbeschreibung
            <textarea wire:model="longDescription"></textarea>
        </label>

        <label>
            Ort
            <input type="text" wire:model="location">
        </label>

        <button type="submit">Speichern</button>
    </form>
</div>
