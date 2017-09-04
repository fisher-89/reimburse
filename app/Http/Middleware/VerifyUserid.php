<?php

namespace App\Http\Middleware;

use Closure;

class VerifyUserid {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (!session()->has('current_user')) {
            $url = config('oa.login');
            $user = app('OAService')->getDataFromApi($url);
            session()->put('current_user', $user['message']);
        }

        return $next($request);
    }

}
