<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleCheck
{
    /**
     * @param  string  ...$roles  Daftar role yang diizinkan (boleh dipisah koma).
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $allowed = collect($roles)
            ->flatMap(fn (string $role): array => array_map('trim', explode(',', $role)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (! $user->hasAnyRole($allowed)) {
            abort(403, 'Akses Ditolak: Halaman ini khusus '.implode(' / ', $allowed));
        }

        return $next($request);
    }
}
