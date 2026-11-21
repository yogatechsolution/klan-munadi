<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public static function isAuthorized(Request $request): bool
    {
        return (bool) $request->session()->get('is_admin', false);
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!self::isAuthorized($request)) {
            if ($request->isMethod('GET')) {
                $request->session()->put('url.intended', $request->fullUrl());
            }

            return redirect()
                ->route('admin.login')
                ->with('info', 'Silakan masuk sebagai Admin untuk mengelola data keluarga.');
        }

        return $next($request);
    }
}
