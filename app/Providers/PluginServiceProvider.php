<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PluginService;

class PluginServiceProvider extends ServiceProvider
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
        $this->app->instance('Plugin', new PluginService);
    }
}
