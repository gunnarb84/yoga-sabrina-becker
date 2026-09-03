<div class="verwaltung-outbound-messages">
    <h1>Ausgehende Nachrichten</h1>

    <form wire:submit="search" class="verwaltung-filter">
        <p>
            <label for="statusFilter">Status</label>
            <select id="statusFilter" wire:model="statusFilter">
                <option value="">Alle</option>
                <option value="ausstehend">Ausstehend</option>
                <option value="versandt">Versandt</option>
                <option value="fehlgeschlagen">Fehlgeschlagen</option>
            </select>
        </p>

        <p>
            <label for="recipientFilter">Empfänger</label>
            <input type="text" id="recipientFilter" wire:model="recipientFilter" placeholder="E-Mail-Adresse">
        </p>

        <p>
            <button type="submit">Filtern</button>
        </p>
    </form>

    @if (empty($messages))
        <p>Keine Nachrichten vorhanden.</p>
    @else
        <table class="verwaltung-table">
            <thead>
                <tr>
                    <th>Empfänger</th>
                    <th>Betreff</th>
                    <th>Status</th>
                    <th>Versendet am</th>
                    <th>Aktion</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($messages as $message)
                    <tr>
                        <td>{{ $message->empfaenger }}</td>
                        <td>{{ $message->betreff }}</td>
                        <td>{{ $message->status }}</td>
                        <td>{{ $message->versendet_am === null ? '-' : \Carbon\Carbon::parse($message->versendet_am)->format('d.m.Y H:i') }}</td>
                        <td>
                            <a href="{{ route('verwaltung.outbound-message.detail', ['id' => $message->id]) }}">Detail</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
