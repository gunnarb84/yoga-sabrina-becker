<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Barquittung {{ $receipt->nummer }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #222; margin: 40px; }
        .header { border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 22px; }
        .meta { width: 100%; margin-bottom: 30px; }
        .meta td { vertical-align: top; padding-bottom: 6px; }
        .label { font-weight: bold; width: 140px; }
        .amount { font-size: 14px; font-weight: bold; margin-top: 30px; }
        .footer { margin-top: 50px; font-size: 10px; color: #666; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Barquittung</h1>
        <p>Yoga Sabrina Becker</p>
    </div>

    <table class="meta">
        <tr>
            <td class="label">Belegnummer:</td>
            <td>{{ $receipt->nummer }}</td>
        </tr>
        <tr>
            <td class="label">Ausgestellt am:</td>
            <td>{{ $receipt->ausgestellt_am?->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td class="label">Empfänger:</td>
            <td>{{ $receipt->empfaenger }}</td>
        </tr>
        <tr>
            <td class="label">Veranstaltung:</td>
            <td>{{ $activity->titel }}</td>
        </tr>
        @if ($participant->email)
            <tr>
                <td class="label">E-Mail:</td>
                <td>{{ $participant->email }}</td>
            </tr>
        @endif
    </table>

    <p class="amount">Erhaltener Betrag: {{ number_format((float) $receipt->betrag, 2, ',', '.') }} €</p>

    <div class="footer">
        Vielen Dank für Ihre Barzahlung. Diese Quittung wurde automatisch erstellt.
    </div>
</body>
</html>