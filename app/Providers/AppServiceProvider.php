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
     */// In app/Providers/AppServiceProvider.php

public function boot(): void
{
    // Add this 'if' statement
    if (config('app.env') === 'production') {
        URL::forceScheme('https');
    }
}
}
