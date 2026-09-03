<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Anmeldebestätigung</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #333; line-height: 1.5; }
        .box { background: #f6f6f6; padding: 16px; border-radius: 6px; margin: 16px 0; }
        .footer { margin-top: 24px; font-size: 0.9em; color: #666; }
    </style>
</head>
<body>
    <p>Hallo {{ $participant->vorname }} {{ $participant->nachname }},</p>

    <p>vielen Dank für Ihre Anmeldung zu <strong>{{ $activity->titel }}</strong>.</p>

    <div class="box">
        <p><strong>Status:</strong> {{ $statusLabel }}</p>
        <p><strong>Preis:</strong> {{ number_format((float) $activity->preis, 2, ',', '.') }} €</p>
        <p><strong>Zahlungsart:</strong> {{ $paymentLabel }}</p>
    </div>

    @if ($registration->zahlungsart->value === 'ueberweisung')
        <p>Die Rechnung finden Sie im Anhang dieser E-Mail.</p>
    @endif

    <p class="footer">Bei Rückfragen melden Sie sich gerne bei uns.</p>
</body>
</html>
