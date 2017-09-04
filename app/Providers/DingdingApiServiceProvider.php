<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DingdingApi;

class DingdingApiServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton('DingdingApi', function($app) {
            return new DingdingApi;
        });
    }

}
