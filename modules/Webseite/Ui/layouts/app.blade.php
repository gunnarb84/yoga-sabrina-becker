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

    @livewireStyles
</head>
<body>
    <header class="yoga-site-header">
        <a href="{{ route('home') }}" class="yoga-logo">Yoga Sabrina Becker</a>

        <nav class="yoga-nav" aria-label="Hauptnavigation">
            <ul class="yoga-nav-list">
                <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                    <a href="{{ route('home') }}">Startseite</a>
                </li>
                <li class="{{ request()->routeIs('activities') || request()->routeIs('activity.detail') ? 'active' : '' }}">
                    <a href="{{ route('activities') }}">Veranstaltungen</a>
                </li>
                @if (isset($navigation))
                    @foreach ($navigation as $item)
                        <li class="{{ request()->url() === url($item->url) ? 'active' : '' }}">
                            @if ($item->extern)
                                <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer">{{ $item->bezeichnung }} (extern)</a>
                            @else
                                <a href="{{ $item->url }}">{{ $item->bezeichnung }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            </ul>
        </nav>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="yoga-site-footer">
        <ul class="yoga-footer-links">
            <li><a href="{{ route('home') }}">Startseite</a></li>
            <li><a href="{{ route('activities') }}">Veranstaltungen</a></li>
            <li><a href="/impressum">Impressum</a></li>
            <li><a href="/agb">AGB</a></li>
            <li><a href="/datenschutz">Datenschutz</a></li>
        </ul>

        <p>© {{ date('Y') }} Yoga Sabrina Becker</p>
    </footer>

    @livewireScripts
</body>
</html>
