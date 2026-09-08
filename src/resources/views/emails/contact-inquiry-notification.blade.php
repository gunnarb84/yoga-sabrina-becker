<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Neue Kontaktanfrage</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #333; line-height: 1.5; }
        .box { background: #f6f6f6; padding: 16px; border-radius: 6px; margin: 16px 0; }
        .box dt { font-weight: bold; margin-top: 8px; }
    </style>
</head>
<body>
    <p>Eine neue Kontaktanfrage ist über die Webseite eingegangen:</p>

    <div class="box">
        <p><strong>Name:</strong> {{ $inquiry->name }}</p>
        <p><strong>E-Mail:</strong> {{ $inquiry->email }}</p>
        <p><strong>Telefon:</strong> {{ $inquiry->telefon ?? '–' }}</p>
        <p><strong>Anlass/Gruppe:</strong> {{ $inquiry->anlass ?? '–' }}</p>
    </div>

    <p><strong>Nachricht:</strong></p>
    <p>{{ $inquiry->nachricht }}</p>

    <p>Die Anfrage ist in der Verwaltung unter „Kontaktanfragen" gelistet.</p>
</body>
</html>