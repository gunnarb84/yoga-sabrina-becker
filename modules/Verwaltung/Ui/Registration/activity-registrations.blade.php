<div class="verwaltung-registrations">
    <h1>Anmeldungen</h1>

    <p><a href="{{ route('verwaltung.registration.create', ['id' => $activityId]) }}">Teilnehmer anmelden</a></p>

    @if ($message !== '')
        <p class="verwaltung-message">{{ $message }}</p>
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

    <h2>Bestätigte Anmeldungen</h2>
    @if (empty($confirmed))
        <p>Noch keine bestätigten Anmeldungen vorhanden.</p>
    @else
        <table class="verwaltung-table">
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
                            @if ($registration->zahlungsstatus !== 'bezahlt' && $registration->zahlungsart === 'bar')
                                <a href="{{ route('verwaltung.payment.create', ['id' => $registration->id]) }}">Zahlung erfassen</a>
                            @elseif ($registration->zahlungsart === 'ueberweisung' && $registration->zahlungsstatus !== 'bezahlt')
                                <span>Rechnung offen</span>
                            @endif
                            <button type="button" wire:click="cancel('{{ $registration->id }}')">Stornieren</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Warteliste</h2>
    @if (empty($waiting))
        <p>Keine Wartelisten-Einträge vorhanden.</p>
    @else
        <table class="verwaltung-table">
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
                            <button type="button" wire:click="moveUp('{{ $registration->id }}')">Nach oben</button>
                            <button type="button" wire:click="moveDown('{{ $registration->id }}')">Nach unten</button>
                            <button type="button" wire:click="promote('{{ $registration->id }}')">Nachrücken</button>
                            <button type="button" wire:click="cancel('{{ $registration->id }}')">Stornieren</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
