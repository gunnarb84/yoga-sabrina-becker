<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Rechnung {{ $invoice->nummer }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #333; line-height: 1.5; }
    </style>
</head>
<body>
    <p>Sehr geehrte Teilnehmerin, sehr geehrter Teilnehmer,</p>

    <p>im Anhang finden Sie die gewünschte Rechnung <strong>{{ $invoice->nummer }}</strong>.</p>

    <p>Bei Rückfragen melden Sie sich gerne bei uns.</p>
</body>
</html>
