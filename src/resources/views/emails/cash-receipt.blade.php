<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Barquittung</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #333; line-height: 1.5; }
        .box { background: #f6f6f6; padding: 16px; border-radius: 6px; margin: 16px 0; }
        .footer { margin-top: 24px; font-size: 0.9em; color: #666; }
    </style>
</head>
<body>
    <p>Hallo {{ $participant->vorname }} {{ $participant->nachname }},</p>

    <p>vielen Dank für Ihre Barzahlung bei <strong>{{ $activity->titel }}</strong>.</p>

    <div class="box">
        <p><strong>Belegnummer:</strong> {{ $receipt->nummer }}</p>
        <p><strong>Betrag:</strong> {{ number_format((float) $receipt->betrag, 2, ',', '.') }} €</p>
        <p><strong>Empfänger/in:</strong> {{ $receipt->empfaenger }}</p>
    </div>

    <p>Ihre Barquittung finden Sie im Anhang dieser E-Mail.</p>

    <p class="footer">Bei Rückfragen melden Sie sich gerne bei uns.</p>
</body>
</html>