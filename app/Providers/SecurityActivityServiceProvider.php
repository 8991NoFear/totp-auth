<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use app\Helpers\SecurityActivityService;

class SecurityActivityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SecurityActivityService::class, function ($app) {
            return new SecurityActivityService();
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
