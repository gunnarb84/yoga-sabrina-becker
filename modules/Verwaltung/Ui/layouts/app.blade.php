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

            <button type="button" class="au-menu-btn" aria-label="Menü öffnen" aria-expanded="false" aria-controls="au-topbar-nav" onclick="document.getElementById('au-topbar-nav').classList.toggle('open'); this.setAttribute('aria-expanded', this.getAttribute('aria-expanded') === 'false' ? 'true' : 'false')">
                ☰
            </button>

            <nav class="au-topbar__nav" id="au-topbar-nav" aria-label="Hauptnavigation">
                <a href="{{ route('verwaltung.dashboard') }}" @if (request()->is('verwaltung') || request()->is('verwaltung/')) class="is-active" @endif>Dashboard</a>
                <a href="{{ route('verwaltung.activities') }}" @if (request()->is('verwaltung/aktivitaeten*')) class="is-active" @endif>Aktivitäten</a>
                <a href="{{ route('verwaltung.course-templates') }}" @if (request()->is('verwaltung/kursvorlagen*')) class="is-active" @endif>Kursvorlagen</a>
                <a href="{{ route('verwaltung.registrations') }}" @if (request()->is('verwaltung/anmeldungen*')) class="is-active" @endif>Anmeldungen</a>
                <a href="{{ route('verwaltung.participants') }}" @if (request()->is('verwaltung/teilnehmer*')) class="is-active" @endif>Teilnehmer</a>
                <a href="{{ route('verwaltung.contact-inquiries') }}" @if (request()->is('verwaltung/kontaktanfragen*')) class="is-active" @endif>Kontaktanfragen</a>
                <a href="{{ route('verwaltung.invoices') }}" @if (request()->is('verwaltung/rechnungen*')) class="is-active" @endif>Rechnungen</a>
                <a href="{{ route('verwaltung.cash-receipts') }}" @if (request()->is('verwaltung/bareinnahmen') || request()->is('verwaltung/bareinnahmen/*') && !request()->is('verwaltung/bareinnahmen/erfassen')) class="is-active" @endif>Bareinnahmen</a>
                <a href="{{ route('verwaltung.cash-receipt.record') }}" @if (request()->is('verwaltung/bareinnahmen/erfassen')) class="is-active" @endif>Bareinnahme erfassen</a>
                <a href="{{ route('verwaltung.outbound-messages') }}" @if (request()->is('verwaltung/nachrichten*')) class="is-active" @endif>Nachrichten</a>

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
