<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\SecurityService;

class SecurityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SecurityActivityService::class, function ($app) {
            return new SecurityService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
