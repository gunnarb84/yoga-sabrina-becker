<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @hasSection('meta-description')
        <meta name="description" content="@yield('meta-description')">
    @endif

    <title>@yield('title', 'Yoga mit Sabrina Becker')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <div class="yoga-site-wrapper">
    <header class="yoga-site-header">
        <div class="yoga-nav-inner">
            <a href="{{ route('home') }}" class="yoga-brand">
                <img src="{{ asset('images/logo.svg') }}" alt="Yoga Sabrina Becker">
                <span class="yoga-brand-text">Yoga Sabrina Becker</span>
            </a>

            <button class="yoga-menu-btn" type="button" aria-label="Menü öffnen" aria-expanded="false" onclick="this.nextElementSibling.classList.toggle('open'); this.setAttribute('aria-expanded', this.getAttribute('aria-expanded') === 'false' ? 'true' : 'false')">
                ☰
            </button>

            <nav class="yoga-nav" aria-label="Hauptnavigation">
                <ul class="yoga-nav-list">
                    <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                        <a href="{{ route('home') }}">Meine Idee</a>
                    </li>
                    <li class="{{ request()->routeIs('activities') || request()->routeIs('activity.detail') ? 'active' : '' }}">
                        <a href="{{ route('activities') }}">Veranstaltungen</a>
                    </li>
                    @if (isset($navigation))
                        @foreach ($navigation as $item)
                            <li class="{{ request()->url() === url($item->url) ? 'active' : '' }}">
                                @if ($item->extern)
                                    <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer">{{ $item->bezeichnung }}</a>
                                @else
                                    <a href="{{ $item->url }}">{{ $item->bezeichnung }}</a>
                                @endif
                            </li>
                        @endforeach
                    @endif
                    <li>
                        <a href="/kontakt" class="yoga-nav-cta">Anfragen</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="yoga-site-footer">
        <div class="yoga-footer-inner">
            <a href="{{ route('home') }}" class="yoga-footer-brand">
                <img src="{{ asset('images/logo.svg') }}" alt="Yoga Sabrina Becker">
            </a>

            <ul class="yoga-footer-links">
                <li><a href="{{ route('home') }}">Meine Idee</a></li>
                <li><a href="{{ route('activities') }}">Veranstaltungen</a></li>
                <li><a href="/kontakt">Kontakt</a></li>
                <li><a href="/impressum">Impressum</a></li>
                <li><a href="/agb">AGB</a></li>
                <li><a href="/datenschutz">Datenschutz</a></li>
            </ul>

            <p>© {{ date('Y') }} Yoga Sabrina Becker</p>
        </div>
    </footer>

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
