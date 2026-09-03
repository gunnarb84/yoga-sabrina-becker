<div class="verwaltung-invoices">
    <h1>Rechnungen</h1>

    @if (empty($invoices))
        <p>Noch keine Rechnungen vorhanden.</p>
    @else
        <table class="verwaltung-table">
            <thead>
                <tr>
                    <th>Nummer</th>
                    <th>Datum</th>
                    <th>Empfänger/in</th>
                    <th>Betrag</th>
                    <th>Status</th>
                    <th>Aktion</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->nummer }}</td>
                        <td>{{ \Carbon\Carbon::parse($invoice->ausgestellt_am)->format('d.m.Y') }}</td>
                        <td>{{ $invoice->empfaenger }}</td>
                        <td>{{ number_format((float) $invoice->betrag, 2, ',', '.') }} {{ $invoice->waehrung }}</td>
                        <td>{{ $invoice->status === 'bezahlt' ? 'Bezahlt' : 'Offen' }}</td>
                        <td>
                            <button type="button" wire:click="toggleStatus('{{ $invoice->id }}')">
                                {{ $invoice->status === 'bezahlt' ? 'Auf offen setzen' : 'Als bezahlt markieren' }}
                            </button>
                            <a href="{{ route('verwaltung.rechnung.pdf', $invoice->id) }}" target="_blank">PDF</a>
                            <form method="POST" action="{{ route('verwaltung.rechnung.email', $invoice->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit">E-Mail</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
