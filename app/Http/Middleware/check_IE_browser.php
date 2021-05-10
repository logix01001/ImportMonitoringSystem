<?php

namespace App\Http\Middleware;
use Agent;
use Closure;

class check_IE_browser
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

        if(Agent::browser() == 'Firefox'){

            return view('pages.ie_browser_used');

        }

        
        
        return $next($request);
    }
}
