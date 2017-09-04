<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CurlService;

class CurlServiceProvider extends ServiceProvider {

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
        $this->app->instance('Curl', new CurlService);
    }

    /**
     * 获取由提供者提供的服务.
     *
     * @return array
     */
    public function provides() {
        return [Curl::class];
    }

}
