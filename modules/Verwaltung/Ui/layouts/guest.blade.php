<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Verwaltung — Anmeldung') — Yoga Sabrina Becker</title>

    <link rel="stylesheet" href="{{ asset('css/yoga-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>
<body class="au-body--login">
    <div class="au-login">
        @yield('content')
    </div>

    <div class="au-login__foot">
        <span class="au-login__state" data-state="ok">Anmeldedienst Lokal</span>
        <span class="au-login__state" data-state="{{ $datenbankZustand }}">Datenbank {{ $datenbankWort }}</span>
        <span>Version {{ $version }}</span>
    </div>
</body>
</html>