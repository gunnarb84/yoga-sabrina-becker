<div class="verwaltung-edit-activity">
    <h1>Aktivitaet bearbeiten</h1>

    <p>Status: {{ ucfirst($statusLabel) }}</p>

    @if ($message)
        <div class="verwaltung-message" role="alert">{{ $message }}</div>
    @endif

    @if ($saved)
        <div class="verwaltung-success" role="status">Die Aenderungen wurden gespeichert.</div>
    @endif

    <form wire:submit="save">
        <div>
            <label for="type">Typ</label>
            <select id="type" wire:model="type">
                @foreach ($types as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="title">Titel *</label>
            <input id="title" type="text" wire:model="title" required>
        </div>

        <div>
            <label for="shortDescription">Kurzbeschreibung</label>
            <textarea id="shortDescription" wire:model="shortDescription" rows="3"></textarea>
        </div>

        <div>
            <label for="longDescription">Langbeschreibung</label>
            <textarea id="longDescription" wire:model="longDescription" rows="6"></textarea>
        </div>

        <div>
            <label for="price">Preis (EUR) *</label>
            <input id="price" type="number" step="0.01" min="0" wire:model="price" required>
        </div>

        <div>
            <label for="maxParticipants">Maximale Teilnehmerzahl *</label>
            <input id="maxParticipants" type="number" min="1" wire:model="maxParticipants" required>
        </div>

        <button type="submit">Speichern</button>
        <a href="{{ route('verwaltung.activities') }}">Zurueck zur Liste</a>
    </form>
</div>
