<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::lower($credentials['identifier']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'identifier' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $remember = $request->boolean('remember');

        $authenticated = Auth::attempt([
            'identifier' => $credentials['identifier'],
            'password' => $credentials['password'],
        ], $remember) || Auth::attempt([
            'email' => $credentials['identifier'],
            'password' => $credentials['password'],
        ], $remember);

        if (! $authenticated) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'identifier' => 'Kredensial tidak valid. Silakan periksa kembali identifier dan kata sandi Anda.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        $dashboard = route($request->user()->roleEnum()->dashboardRoute());
        $intended = $request->session()->pull('url.intended');

        // URL tujuan hanya dipakai bila bukan halaman dashboard — setiap role
        // selalu kembali ke dashboard miliknya sendiri setelah login.
        if ($intended !== null && ! $this->isDashboardUrl($intended)) {
            return redirect()->to($intended);
        }

        return redirect()->to($dashboard);
    }

    private function isDashboardUrl(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '/';

        $dashboardPaths = collect(['dashboard', 'admin.dashboard', 'petugas.dashboard', 'siswa.dashboard'])
            ->map(fn (string $route): string => parse_url(route($route), PHP_URL_PATH) ?: '/')
            ->all();

        return in_array($path, $dashboardPaths, true);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
