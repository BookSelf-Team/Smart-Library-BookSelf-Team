<?php

use App\Http\Middleware\AuthorizeMiddleware;
use App\Http\Middleware\RoleCheck;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleCheck::class,
            'can' => AuthorizeMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->reportable(function (Throwable $e) {
            Log::error('Exception occurred', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        });

        $exceptions->renderable(function (QueryException $e, Request $request) {
            $connectionErrors = [
                'SQLSTATE[08006]',
                'SQLSTATE[HY000]',
                'Connection refused',
                'No connection',
                'Lost connection',
            ];
            $isConnectionError = Str::contains($e->getMessage(), $connectionErrors);

            $user = $request->user();

            if (! $isConnectionError && $user?->email !== null) {
                try {
                    $user = User::query()->where('email', $user->email)->first() ?? $user;
                } catch (Throwable) {
                    // Biarkan $user dari sesi bila query ke database juga gagal.
                }
            }

            Log::error('Database query exception', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'url' => $request->fullUrl(),
                'user_id' => $user?->id,
                'user_name' => $user?->name,
                'user_email' => $user?->email,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan database. Silakan coba lagi nanti.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            if ($isConnectionError) {
                return response()->view('errors.503', [], 503);
            }

            return response()->view('errors.500', [], 500);
        });

        $exceptions->renderable(function (HttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'status' => $e->getStatusCode(),
                ], $e->getStatusCode());
            }

            $view = match ($e->getStatusCode()) {
                403 => 'errors.403',
                404 => 'errors.404',
                500 => 'errors.500',
                503 => 'errors.503',
                default => 'errors.500',
            };

            if (view()->exists($view)) {
                return response()->view($view, [], $e->getStatusCode());
            }

            return response()->view('errors.500', [], $e->getStatusCode());
        });
    })->create();
