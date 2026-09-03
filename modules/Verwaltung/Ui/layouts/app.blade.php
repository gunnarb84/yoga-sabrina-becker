<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Verwaltung') — Yoga Sabrina Becker</title>

    @livewireStyles
</head>
<body>
    <header>
        <nav>
            <a href="{{ route('verwaltung.dashboard') }}">Dashboard</a>
            <a href="{{ route('verwaltung.activities') }}">Aktivitaeten</a>
            <a href="{{ route('verwaltung.participants') }}">Teilnehmer</a>
            <a href="{{ route('verwaltung.invoices') }}">Rechnungen</a>
            <a href="{{ route('verwaltung.outbound-messages') }}">Nachrichten</a>

            @auth
                <form method="POST" action="{{ route('verwaltung.logout') }}" style="display:inline">
                    @csrf
                    <button type="submit">Abmelden</button>
                </form>
            @endauth
        </nav>
    </header>

    <main>
        {{ $slot }}
    </main>

    @livewireScripts
    <script>
        (function () {
            function markReady() {
                document.body.setAttribute('data-livewire-ready', 'true');
            }
            if (window.Livewire) {
                markReady();
            }
            document.addEventListener('livewire:initialized', markReady);
        })();
    </script>
</body>
</html>
