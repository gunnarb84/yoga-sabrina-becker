<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Bareinnahmen</h1>
    </div>

    <div class="au-panel__body">
        <div class="yoga-form-grid yoga-mb-2">
            <div class="au-field">
                <label class="au-field__label" for="nummerFilter">Belegnummer</label>
                <input type="search" id="nummerFilter" wire:model.live.debounce.300ms="nummerFilter" class="au-field__input" placeholder="z. B. B-2026">
            </div>

            <div class="au-field">
                <label class="au-field__label" for="empfaengerFilter">Empfänger/in</label>
                <input type="search" id="empfaengerFilter" wire:model.live.debounce.300ms="empfaengerFilter" class="au-field__input">
            </div>
        </div>

        @if (empty($receipts))
            <p class="yoga-empty">Noch keine Bareinnahmen vorhanden.</p>
        @else
            <div style="overflow-x:auto">
                <table class="au-list">
                    <thead>
                        <tr>
                            <th>Nummer</th>
                            <th>Datum</th>
                            <th>Empfänger/in</th>
                            <th>Betrag</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($receipts as $receipt)
                            <tr wire:key="receipt-{{ $receipt->id }}">
                                <td>{{ $receipt->nummer }}</td>
                                <td>{{ \Carbon\Carbon::parse($receipt->ausgestellt_am)->format('d.m.Y') }}</td>
                                <td>{{ $receipt->empfaenger }}</td>
                                <td style="text-align:right">{{ number_format((float) $receipt->betrag, 2, ',', '.') }} {{ $receipt->waehrung }}</td>
                                <td>
                                    <a href="{{ route('verwaltung.bareinnahme.pdf', $receipt->id) }}" target="_blank" class="au-btn au-btn--sm">PDF</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>