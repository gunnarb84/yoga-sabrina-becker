<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Anmeldungen</h1>
        <a href="{{ route('verwaltung.registration.create', ['id' => $activityId]) }}" class="au-btn au-btn--primary">Teilnehmer anmelden</a>
    </div>

    <div class="au-panel__body">
        @if ($message !== '')
            <div class="au-status au-status--warn yoga-mb-2" role="alert">{{ $message }}</div>
        @endif

        @php
            $confirmed = [];
            $waiting = [];
            foreach ($registrations as $registration) {
                if ($registration->status === 'bestaetigt') {
                    $confirmed[] = $registration;
                } elseif ($registration->status === 'warteliste') {
                    $waiting[] = $registration;
                }
            }
        @endphp

        <h2 class="yoga-subtitle">Bestätigte Anmeldungen</h2>
        @if (empty($confirmed))
            <p class="yoga-empty">Noch keine bestätigten Anmeldungen vorhanden.</p>
        @else
            <div style="overflow-x:auto">
                <table class="au-list">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>E-Mail</th>
                            <th>Zahlungsart</th>
                            <th>Zahlungsstatus</th>
                            <th>Angemeldet am</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($confirmed as $registration)
                            <tr>
                                <td>{{ $registration->teilnehmer_name }}</td>
                                <td>{{ $registration->email }}</td>
                                <td>{{ $registration->zahlungsart }}</td>
                                <td>{{ $registration->zahlungsstatus }}</td>
                                <td>{{ \Carbon\Carbon::parse($registration->angemeldet_am)->format('d.m.Y H:i') }}</td>
                                <td>
                                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                                        @if ($registration->zahlungsstatus !== 'bezahlt' && $registration->zahlungsart === 'bar')
                                            <a href="{{ route('verwaltung.payment.create', ['id' => $registration->id]) }}" class="au-btn au-btn--sm">Zahlung erfassen</a>
                                        @elseif ($registration->zahlungsart === 'ueberweisung' && $registration->zahlungsstatus !== 'bezahlt')
                                            <span class="au-status au-status--warn">Rechnung offen</span>
                                        @endif
                                        <button type="button" class="au-btn au-btn--sm au-btn--danger" wire:click="cancel('{{ $registration->id }}')">Stornieren</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <h2 class="yoga-subtitle yoga-mt-3">Warteliste</h2>
        @if (empty($waiting))
            <p class="yoga-empty">Keine Wartelisten-Einträge vorhanden.</p>
        @else
            <div style="overflow-x:auto">
                <table class="au-list">
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Name</th>
                            <th>E-Mail</th>
                            <th>Angemeldet am</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($waiting as $registration)
                            <tr>
                                <td>{{ $registration->rang }}</td>
                                <td>{{ $registration->teilnehmer_name }}</td>
                                <td>{{ $registration->email }}</td>
                                <td>{{ \Carbon\Carbon::parse($registration->angemeldet_am)->format('d.m.Y H:i') }}</td>
                                <td>
                                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                                        <button type="button" class="au-btn au-btn--sm" wire:click="moveUp('{{ $registration->id }}')">Nach oben</button>
                                        <button type="button" class="au-btn au-btn--sm" wire:click="moveDown('{{ $registration->id }}')">Nach unten</button>
                                        <button type="button" class="au-btn au-btn--sm au-btn--primary" wire:click="promote('{{ $registration->id }}')">Nachrücken</button>
                                        <button type="button" class="au-btn au-btn--sm au-btn--danger" wire:click="cancel('{{ $registration->id }}')">Stornieren</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
