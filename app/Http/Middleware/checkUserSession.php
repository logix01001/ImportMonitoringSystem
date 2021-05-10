<?php

namespace App\Http\Middleware;

use Closure;

class checkUserSession
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

        
        if (!$request->session()->exists('employee_number')) {
            // user value cannot be found in session
            $request->session()->flash('errorMessage','  Sorry, but you are not yet login.');
           return redirect('/login');
        }

        return $next($request);
    }
}
