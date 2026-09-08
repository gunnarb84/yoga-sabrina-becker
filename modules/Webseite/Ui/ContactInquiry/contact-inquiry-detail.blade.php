<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Kontaktanfrage</h1>
    </div>

    <div class="au-panel__body">
        @if ($inquiry === null)
            <p class="yoga-empty">Die Kontaktanfrage wurde nicht gefunden.</p>
        @else
            <dl class="yoga-dl">
                <dt>Empfangen am</dt>
                <dd>{{ \Carbon\Carbon::parse($inquiry->empfangen_am)->format('d.m.Y H:i') }}</dd>

                <dt>Name</dt>
                <dd>{{ $inquiry->name }}</dd>

                <dt>E-Mail</dt>
                <dd>{{ $inquiry->email }}</dd>

                @if ($inquiry->telefon !== null)
                    <dt>Telefon</dt>
                    <dd>{{ $inquiry->telefon }}</dd>
                @endif

                <dt>Anlass / Gruppe</dt>
                <dd>{{ $inquiry->anlass ?? '–' }}</dd>

                <dt>Status</dt>
                <dd>{{ $inquiry->status }}</dd>
            </dl>

            <h2 class="yoga-subtitle">Nachricht</h2>
            <pre class="yoga-pre">{{ $inquiry->nachricht }}</pre>

            <h2 class="yoga-subtitle">Bearbeitung</h2>
            <form wire:submit="save" class="yoga-filter">
                <div class="au-field">
                    <label class="au-field__label" for="status">Status</label>
                    <select id="status" wire:model="status" class="au-field__input">
                        <option value="neu">Neu</option>
                        <option value="in_bearbeitung">In Bearbeitung</option>
                        <option value="erledigt">Erledigt</option>
                    </select>
                </div>

                <div class="au-field yoga-field--grow">
                    <label class="au-field__label" for="note">Notiz</label>
                    <input type="text" id="note" wire:model="note" class="au-field__input" placeholder="Notiz zur Bearbeitung">
                </div>

                <div class="yoga-filter-actions">
                    <button type="submit" class="au-btn au-btn--primary">Speichern</button>
                </div>
            </form>

            @if ($saved)
                <div class="au-status au-status--ok yoga-mt-2" role="status">Die Änderungen wurden gespeichert.</div>
            @endif

            @if ($error !== '')
                <div class="au-status au-status--error yoga-mt-2" role="alert">{{ $error }}</div>
            @endif

            <p class="yoga-mt-3">
                <a href="{{ route('verwaltung.contact-inquiries') }}" class="au-btn">Zurück zur Übersicht</a>
            </p>
        @endif
    </div>
</div>