@extends('verwaltung::layouts.guest')

@section('content')
<div class="au-login__card">
    <div>
        <p class="au-login__firm">Yoga Sabrina Becker</p>
        <h1 class="au-login__title">Verwaltung — Anmeldung</h1>
    </div>

    @if ($errors->any())
        <div class="au-login__error" role="alert">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('verwaltung.login.submit') }}">
        @csrf

        <div class="au-field">
            <label class="au-field__label" for="email">E-Mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus class="au-field__input">
        </div>

        <div class="au-field">
            <label class="au-field__label" for="password">Passwort</label>
            <input type="password" id="password" name="password" required class="au-field__input">
        </div>

        <button type="submit" class="au-btn au-btn--primary">Anmelden</button>
    </form>
</div>
@endsection