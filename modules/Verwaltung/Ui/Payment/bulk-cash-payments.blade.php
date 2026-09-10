<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Barzahlungen massenweise erfassen</h1>
        <a href="{{ route('verwaltung.activity.registrations', ['id' => $activityId]) }}" class="au-btn">Zurück</a>
    </div>

    <div class="au-panel__body">
        @if ($message !== '')
            <div class="au-status au-status--ok yoga-mb-2" role="status">{{ $message }}</div>
        @endif

        @if ($error !== '')
            <div class="au-status au-status--warn yoga-mb-2" role="alert">{{ $error }}</div>
        @endif

        @if (empty($items))
            <p class="yoga-empty">Alle Bar-Anmeldungen dieser Veranstaltung sind erfasst.</p>
        @else
            <form wire:submit="save" class="yoga-form">
                <div style="overflow-x:auto">
                    <table class="au-list">
                        <thead>
                            <tr>
                                <th>Bezahlt</th>
                                <th>Empfänger/in</th>
                                <th>Betrag</th>
                                <th>Zahlungsdatum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $index => $item)
                                <tr wire:key="bulk-{{ $item['registrationId'] }}">
                                    <td>
                                        <input type="checkbox" id="selected-{{ $index }}" wire:model="items.{{ $index }}.selected" class="au-field__input" style="width:auto">
                                    </td>
                                    <td>{{ $item['empfaenger'] }}</td>
                                    <td style="text-align:right">{{ number_format((float) $item['betrag'], 2, ',', '.') }} {{ $item['waehrung'] }}</td>
                                    <td>
                                        <input type="datetime-local" wire:model="items.{{ $index }}.paidAt" class="au-field__input" aria-label="Zahlungsdatum für {{ $item['empfaenger'] }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="yoga-form-actions yoga-mt-3">
                    <button type="submit" class="au-btn au-btn--primary">Ausgewählte Zahlungen speichern</button>
                    <a href="{{ route('verwaltung.activity.registrations', ['id' => $activityId]) }}" class="au-btn">Abbrechen</a>
                </div>
            </form>
        @endif
    </div>
</div>