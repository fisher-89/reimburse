<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class OaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('OAService', \App\Services\OAService::class);
    }
}
