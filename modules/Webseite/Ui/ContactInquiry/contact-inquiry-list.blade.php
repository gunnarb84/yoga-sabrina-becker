<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Kontaktanfragen</h1>
    </div>

    <div class="au-panel__body">
        <form wire:submit="search" class="yoga-filter">
            <div class="au-field">
                <label class="au-field__label" for="statusFilter">Status</label>
                <select id="statusFilter" wire:model="statusFilter" class="au-field__input">
                    <option value="">Alle</option>
                    <option value="neu">Neu</option>
                    <option value="in_bearbeitung">In Bearbeitung</option>
                    <option value="erledigt">Erledigt</option>
                </select>
            </div>

            <div class="yoga-filter-actions">
                <button type="submit" class="au-btn au-btn--primary">Filtern</button>
            </div>
        </form>

        @if (empty($inquiries))
            <p class="yoga-empty">Keine Kontaktanfragen vorhanden.</p>
        @else
            <div style="overflow-x:auto">
                <table class="au-list">
                    <thead>
                        <tr>
                            <th>Empfangen am</th>
                            <th>Name</th>
                            <th>E-Mail</th>
                            <th>Anlass</th>
                            <th>Status</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inquiries as $inquiry)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($inquiry->empfangen_am)->format('d.m.Y H:i') }}</td>
                                <td>{{ $inquiry->name }}</td>
                                <td>{{ $inquiry->email }}</td>
                                <td>{{ $inquiry->anlass ?? '–' }}</td>
                                <td>{{ $inquiry->status }}</td>
                                <td>
                                    <a href="{{ route('verwaltung.contact-inquiry.detail', ['id' => $inquiry->id]) }}" class="au-btn au-btn--sm">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>