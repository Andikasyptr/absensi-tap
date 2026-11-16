<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Hapus pengecekan header x-forwarded-proto agar tidak memaksa HTTPS di local
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}