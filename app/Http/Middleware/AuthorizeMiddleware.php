<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  string  ...$abilities  The gate abilities to check.
     */
    public function handle(Request $request, Closure $next, string ...$abilities): Response
    {
        foreach ($abilities as $ability) {
            if (! Gate::allows($ability)) {
                abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk aksi ini.');
            }
        }

        return $next($request);
    }
}
