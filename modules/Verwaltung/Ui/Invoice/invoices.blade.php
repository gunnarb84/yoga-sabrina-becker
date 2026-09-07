<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Rechnungen</h1>
    </div>

    <div class="au-panel__body">
        @if (empty($invoices))
            <p class="yoga-empty">Noch keine Rechnungen vorhanden.</p>
        @else
            <div style="overflow-x:auto">
                <table class="au-list">
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
                                <td style="text-align:right">{{ number_format((float) $invoice->betrag, 2, ',', '.') }} {{ $invoice->waehrung }}</td>
                                <td>
                                    <span class="au-status {{ $invoice->status === 'bezahlt' ? 'au-status--ok' : 'au-status--warn' }}">
                                        {{ $invoice->status === 'bezahlt' ? 'Bezahlt' : 'Offen' }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                                        <button type="button" class="au-btn au-btn--sm" wire:click="toggleStatus('{{ $invoice->id }}')">
                                            {{ $invoice->status === 'bezahlt' ? 'Auf offen setzen' : 'Als bezahlt markieren' }}
                                        </button>
                                        <a href="{{ route('verwaltung.rechnung.pdf', $invoice->id) }}" target="_blank" class="au-btn au-btn--sm">PDF</a>

                                        <form method="POST" action="{{ route('verwaltung.rechnung.email', $invoice->id) }}" style="display:inline">
                                            @csrf
                                            <button type="submit" class="au-btn au-btn--sm">E-Mail</button>
                                        </form>
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
