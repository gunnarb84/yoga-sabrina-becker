<?php

declare(strict_types=1);

namespace Yoga\Modules\Verwaltung\Ui\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class LoginController
{
    public function showLoginForm(): View
    {
        return view('verwaltung::auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! is_array($credentials)) {
            return back()->withErrors([
                'email' => 'Die Anmeldedaten sind nicht korrekt.',
            ]);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->route('verwaltung.dashboard');
        }

        return back()->withErrors([
            'email' => 'Die Anmeldedaten sind nicht korrekt.',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('verwaltung.login');
    }
}
