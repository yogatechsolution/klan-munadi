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
        // Paksa HTTPS di production / di balik reverse proxy (seperti Render.com)
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
