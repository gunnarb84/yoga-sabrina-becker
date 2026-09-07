<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Ausgehende Nachrichten</h1>
    </div>

    <div class="au-panel__body">
        <form wire:submit="search" class="yoga-filter">
            <div class="au-field">
                <label class="au-field__label" for="statusFilter">Status</label>
                <select id="statusFilter" wire:model="statusFilter" class="au-field__input">
                    <option value="">Alle</option>
                    <option value="ausstehend">Ausstehend</option>
                    <option value="versandt">Versandt</option>
                    <option value="fehlgeschlagen">Fehlgeschlagen</option>
                </select>
            </div>

            <div class="au-field">
                <label class="au-field__label" for="recipientFilter">Empfänger</label>
                <input type="text" id="recipientFilter" wire:model="recipientFilter" placeholder="E-Mail-Adresse" class="au-field__input">
            </div>

            <div class="yoga-filter-actions">
                <button type="submit" class="au-btn au-btn--primary">Filtern</button>
            </div>
        </form>

        @if (empty($messages))
            <p class="yoga-empty">Keine Nachrichten vorhanden.</p>
        @else
            <div style="overflow-x:auto">
                <table class="au-list">
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
                                <td>{{ $message->versendet_am === null ? '–' : \Carbon\Carbon::parse($message->versendet_am)->format('d.m.Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('verwaltung.outbound-message.detail', ['id' => $message->id]) }}" class="au-btn au-btn--sm">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
