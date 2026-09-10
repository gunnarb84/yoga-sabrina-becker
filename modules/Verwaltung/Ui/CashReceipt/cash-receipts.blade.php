<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Bareinnahmen</h1>
    </div>

    <div class="au-panel__body">
        <div class="yoga-form-grid yoga-mb-2">
            <div class="au-field">
                <label class="au-field__label" for="nummerFilter">Belegnummer</label>
                <input type="search" id="nummerFilter" wire:model.live.debounce.300ms="nummerFilter" class="au-field__input" placeholder="z. B. 2026-00012">
            </div>

            <div class="au-field">
                <label class="au-field__label" for="empfaengerFilter">Empfänger/in bzw. Zweck</label>
                <input type="search" id="empfaengerFilter" wire:model.live.debounce.300ms="empfaengerFilter" class="au-field__input">
            </div>
        </div>

        <div class="yoga-mb-2">
            <button type="button" wire:click="toggleWithdrawalForm" class="au-btn">Barentnahme erfassen</button>
        </div>

        @if ($showWithdrawalForm)
            <form wire:submit="saveWithdrawal" class="au-panel au-mb-2" style="border:1px solid var(--au-border, #d9cfb2); padding:1rem; margin-bottom:1rem;">
                <h2 class="au-panel__title" style="font-size:1rem;">Barentnahme erfassen</h2>

                @if ($message !== '')
                    <p class="yoga-error">{{ $message }}</p>
                @endif

                <div class="yoga-form-grid">
                    <div class="au-field">
                        <label class="au-field__label" for="withdrawalDate">Datum</label>
                        <input type="date" id="withdrawalDate" wire:model="withdrawalDate" class="au-field__input" required>
                    </div>

                    <div class="au-field">
                        <label class="au-field__label" for="withdrawalAmount">Betrag</label>
                        <input type="number" id="withdrawalAmount" wire:model="withdrawalAmount" class="au-field__input" step="0.01" min="0.01" required>
                    </div>

                    <div class="au-field">
                        <label class="au-field__label" for="withdrawalPurpose">Zweck</label>
                        <input type="text" id="withdrawalPurpose" wire:model="withdrawalPurpose" class="au-field__input" required>
                    </div>

                    <div class="au-field">
                        <label class="au-field__label" for="withdrawalExternalReference">Fremdbelegnummer</label>
                        <input type="text" id="withdrawalExternalReference" wire:model="withdrawalExternalReference" class="au-field__input" placeholder="optional, z. B. Kassenquittung eines Barkaufs">
                    </div>
                </div>

                <div class="yoga-mt-2">
                    <button type="submit" class="au-btn">Barentnahme speichern</button>
                    <button type="button" wire:click="toggleWithdrawalForm" class="au-btn au-btn--secondary">Abbrechen</button>
                </div>
            </form>
        @endif

        @if ($withdrawalRecorded)
            <p class="yoga-success">Barentnahme erfasst.</p>
        @endif

        @if (empty($movements))
            <p class="yoga-empty">Noch keine Kassenbewegungen vorhanden.</p>
        @else
            <div style="overflow-x:auto">
                <table class="au-list">
                    <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Art</th>
                            <th>Beleg-Nr. / Fremdbelegnummer</th>
                            <th>Empfänger/in bzw. Zweck</th>
                            <th style="text-align:right">Betrag</th>
                            <th style="text-align:right">Bestand</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($movements as $movement)
                            <tr wire:key="movement-{{ $movement->id }}">
                                <td>{{ \Carbon\Carbon::parse($movement->datum)->format('d.m.Y') }}</td>
                                <td>{{ ['bareinnahme' => 'Bareinnahme', 'rueckgabe' => 'Rückgabe', 'barentnahme' => 'Barentnahme'][$movement->typ] }}</td>
                                <td>{{ $movement->kennung !== '' ? $movement->kennung : '—' }}</td>
                                <td>{{ $movement->beschreibung }}</td>
                                <td style="text-align:right">
                                    {{ $movement->richtung === 'ausgabe' ? '−' : '' }}{{ number_format((float) $movement->betrag, 2, ',', '.') }} {{ $movement->waehrung }}
                                </td>
                                <td style="text-align:right">{{ number_format((float) $movement->bestand, 2, ',', '.') }} {{ $movement->waehrung }}</td>
                                <td>
                                    @if ($movement->typ === 'bareinnahme')
                                        <a href="{{ route('verwaltung.bareinnahme.pdf', $movement->id) }}" target="_blank" class="au-btn au-btn--sm">PDF</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>