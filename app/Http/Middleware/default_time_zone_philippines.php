<?php

namespace App\Http\Middleware;

use Closure;

class default_time_zone_philippines
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        date_default_timezone_set('Asia/Manila');
        return $next($request);
    }
}
