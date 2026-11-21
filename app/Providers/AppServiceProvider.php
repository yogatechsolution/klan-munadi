<?php

namespace App\Providers;

use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Dukungan otomatis Neon PostgreSQL SNI untuk libpq versi lama (Windows / Serverless)
        $pgUrl = config('database.connections.pgsql.url');
        if (is_string($pgUrl) && $pgUrl !== '' && !getenv('PGOPTIONS')) {
            $pgHost = parse_url($pgUrl, PHP_URL_HOST);
            if (is_string($pgHost) && str_ends_with($pgHost, '.neon.tech')) {
                $endpoint = preg_replace('/-pooler$/', '', explode('.', $pgHost)[0] ?? '');
                if ($endpoint !== '') {
                    @putenv('PGOPTIONS=endpoint=' . $endpoint);
                }
            }
        }

        // Paksa HTTPS di production / di balik reverse proxy (seperti Vercel / Render)
        if ($this->app->environment('production') || str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Bagikan status Mode Admin secara aman ke seluruh Blade view
        View::composer('*', function ($view) {
            $request = request();
            $isAdmin = ($request && $request->hasSession())
                ? EnsureAdmin::isAuthorized($request)
                : false;

            $view->with('isAdmin', $isAdmin);
        });
    }
}
