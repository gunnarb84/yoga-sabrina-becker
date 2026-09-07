<div class="au-panel">
    <div class="au-panel__header">
        <h1 class="au-panel__title">Nachrichtendetail</h1>
    </div>

    <div class="au-panel__body">
        @if ($message === null)
            <p class="yoga-empty">Die Nachricht wurde nicht gefunden.</p>
        @else
            <dl class="yoga-dl">
                <dt>Empfänger</dt>
                <dd>{{ $message->empfaenger }}</dd>

                <dt>Betreff</dt>
                <dd>{{ $message->betreff }}</dd>

                <dt>Status</dt>
                <dd>{{ $message->status }}</dd>

                <dt>Versendet am</dt>
                <dd>{{ $message->versendet_am === null ? '–' : \Carbon\Carbon::parse($message->versendet_am)->format('d.m.Y H:i') }}</dd>

                @if ($message->fehlermeldung !== null)
                    <dt>Fehlermeldung</dt>
                    <dd>{{ $message->fehlermeldung }}</dd>
                @endif
            </dl>

            <h2 class="yoga-subtitle">Inhalt</h2>
            <pre class="yoga-pre">{{ $message->inhalt }}</pre>

            @if ($message->anmeldung_id !== null)
                <p class="yoga-mt-2">
                    <a href="{{ route('verwaltung.activity.registrations', ['id' => $message->aktivitaet_id]) }}" class="au-btn au-btn--sm">Zur Anmeldung</a>
                </p>
            @endif

            @if ($canResend)
                <button type="button" class="au-btn au-btn--primary" wire:click="resend">Erneut senden</button>
            @endif

            @if ($resent)
                <div class="au-status au-status--ok yoga-mt-2" role="status">Die Nachricht wurde erneut zum Versand vorbereitet.</div>
            @endif

            <p class="yoga-mt-3">
                <a href="{{ route('verwaltung.outbound-messages') }}" class="au-btn">Zurück zur Übersicht</a>
            </p>
        @endif
    </div>
</div>
