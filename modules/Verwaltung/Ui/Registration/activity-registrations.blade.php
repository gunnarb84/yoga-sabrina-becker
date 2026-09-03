<div class="verwaltung-registrations">
    <h1>Anmeldungen</h1>

    <p><a href="{{ route('verwaltung.registration.create', ['id' => $activityId]) }}">Teilnehmer anmelden</a></p>

    @if ($message !== '')
        <p class="verwaltung-message">{{ $message }}</p>
    @endif

    @if (empty($registrations))
        <p>Noch keine Anmeldungen vorhanden.</p>
    @else
        <table class="verwaltung-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>E-Mail</th>
                    <th>Status</th>
                    <th>Zahlungsart</th>
                    <th>Zahlungsstatus</th>
                    <th>Angemeldet am</th>
                    <th>Rang</th>
                    <th>Aktion</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($registrations as $registration)
                    <tr>
                        <td>{{ $registration->teilnehmer_name }}</td>
                        <td>{{ $registration->email }}</td>
                        <td>{{ $registration->status }}</td>
                        <td>{{ $registration->zahlungsart }}</td>
                        <td>{{ $registration->zahlungsstatus }}</td>
                        <td>{{ \Carbon\Carbon::parse($registration->angemeldet_am)->format('d.m.Y H:i') }}</td>
                        <td>{{ $registration->rang ?? '-' }}</td>
                        <td>
                            @if ($registration->zahlungsstatus !== 'bezahlt' && $registration->zahlungsart === 'bar')
                                <a href="{{ route('verwaltung.payment.create', ['id' => $registration->id]) }}">Zahlung erfassen</a>
                            @elseif ($registration->zahlungsart === 'ueberweisung' && $registration->zahlungsstatus !== 'bezahlt')
                                <span>Rechnung offen</span>
                            @endif
                            @if ($registration->status !== 'storniert')
                                <button type="button" wire:click="cancel('{{ $registration->id }}')">Stornieren</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
