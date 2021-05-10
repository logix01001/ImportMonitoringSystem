<?php

namespace App\Http\Middleware;

use Closure;

class RoleEncoding
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
        
        if($request->session()->get('encoding') == 0){
            $request->session()->flash('errorMessage','You don\'t have access to this page.');
            return redirect('/index');
        }
        
        return $next($request);
    }
}
