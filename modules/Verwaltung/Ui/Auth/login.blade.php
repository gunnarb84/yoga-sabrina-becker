<div class="verwaltung-login">
    <h1>Verwaltung — Anmeldung</h1>

    @if ($errors->any())
        <ul class="error">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('verwaltung.login.submit') }}">
        @csrf

        <label>
            E-Mail
            <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        </label>

        <label>
            Passwort
            <input type="password" name="password" required>
        </label>

        <button type="submit">Anmelden</button>
    </form>
</div>
