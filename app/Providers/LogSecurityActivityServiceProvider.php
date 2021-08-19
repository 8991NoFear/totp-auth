<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use app\Helpers\SecurityActivityLogger;

class LogSecurityActivityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SecurityActivityLogger::class, function ($app) {
            return new SecurityActivityLogger();
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
