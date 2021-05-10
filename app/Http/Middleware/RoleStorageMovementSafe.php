<?php

namespace App\Http\Middleware;

use Closure;

class RoleStorageMovementSafe
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

        if($request->session()->get('storage_validity') == 0 && $request->session()->get('container_movement') == 0 && $request->session()->get('safe_keep') == 0){
            
            $request->session()->flash('errorMessage','  Sorry, but you are not authorized to access this page');
            
            return redirect('/index');
        }
        return $next($request);
    }
}
