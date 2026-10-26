<?php

namespace App\Providers;

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Recheck route role restrictions on every Livewire update request.
        Livewire::addPersistentMiddleware([RoleMiddleware::class]);

        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');

            // Force HTTPS detection for signed URLs
            request()->server->set('HTTPS', 'on');
        }
    }
}
