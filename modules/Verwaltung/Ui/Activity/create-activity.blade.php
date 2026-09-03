<div class="verwaltung-create-activity">
    <h1>Neue Aktivitaet anlegen</h1>

    @if ($message)
        <p class="message">{{ $message }}</p>
    @endif

    @if ($created)
        <p class="success">Die Aktivitaet wurde angelegt.</p>
    @endif

    <form wire:submit="save">
        <label>
            Typ
            <select wire:model="type">
                @foreach ($types as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
        </label>

        <label>
            Titel
            <input type="text" wire:model="title" required>
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
            Preis (EUR)
            <input type="number" step="0.01" wire:model="price" required>
        </label>

        <label>
            Maximale Teilnehmerzahl
            <input type="number" wire:model="maxParticipants" required>
        </label>

        <button type="submit">Speichern</button>
    </form>
</div>
