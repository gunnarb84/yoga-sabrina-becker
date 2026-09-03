<div class="verwaltung-outbound-message-detail">
    <h1>Nachrichtendetail</h1>

    @if ($message === null)
        <p>Die Nachricht wurde nicht gefunden.</p>
    @else
        <dl>
            <dt>Empfänger</dt>
            <dd>{{ $message->empfaenger }}</dd>

            <dt>Betreff</dt>
            <dd>{{ $message->betreff }}</dd>

            <dt>Status</dt>
            <dd>{{ $message->status }}</dd>

            <dt>Versendet am</dt>
            <dd>{{ $message->versendet_am === null ? '-' : \Carbon\Carbon::parse($message->versendet_am)->format('d.m.Y H:i') }}</dd>

            @if ($message->fehlermeldung !== null)
                <dt>Fehlermeldung</dt>
                <dd>{{ $message->fehlermeldung }}</dd>
            @endif
        </dl>

        <h2>Inhalt</h2>
        <pre>{{ $message->inhalt }}</pre>

        @if ($message->anmeldung_id !== null)
            <p><a href="{{ route('verwaltung.activity.registrations', ['id' => $message->aktivitaet_id]) }}">Zur Anmeldung</a></p>
        @endif

        @if ($canResend)
            <button type="button" wire:click="resend">Erneut senden</button>
        @endif

        @if ($resent)
            <p>Die Nachricht wurde erneut zum Versand vorbereitet.</p>
        @endif

        <p><a href="{{ route('verwaltung.outbound-messages') }}">Zurück zur Übersicht</a></p>
    @endif
</div>
