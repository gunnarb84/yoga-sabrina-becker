<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Verwaltung') — Yoga Sabrina Becker</title>

    <link rel="stylesheet" href="{{ asset('css/yoga-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    @livewireStyles
</head>
<body>
    <div class="au-app">
        <header class="au-topbar">
            <a href="{{ route('verwaltung.dashboard') }}" class="au-topbar__brand">
                <img src="{{ asset('images/logo.svg') }}" alt="">
                <span>Verwaltung</span>
            </a>

            <nav class="au-topbar__nav" aria-label="Hauptnavigation">
                <a href="{{ route('verwaltung.dashboard') }}">Dashboard</a>
                <a href="{{ route('verwaltung.activities') }}">Aktivitäten</a>
                <a href="{{ route('verwaltung.course-templates') }}">Kursvorlagen</a>
                <a href="{{ route('verwaltung.registrations') }}">Anmeldungen</a>
                <a href="{{ route('verwaltung.participants') }}">Teilnehmer</a>
                <a href="{{ route('verwaltung.contact-inquiries') }}">Kontaktanfragen</a>
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

        <main class="au-work">
            {{ $slot }}
        </main>
    </div>

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
