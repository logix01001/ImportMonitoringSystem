<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Session;
class update_user_previlege
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
        if ($request->session()->exists('employee_number')) {
           
                $doc = User::where('employee_number',Session::get('employee_number'))
                ->where('password',  Session::get('password'))
                ->get();

                Session::put('maintenance', $doc[0]->maintenance);
                Session::put('master', $doc[0]->master);
                Session::put('arrival', $doc[0]->arrival);
                Session::put('encoding', $doc[0]->encoding);
                Session::put('e2m', $doc[0]->e2m);
                Session::put('gatepass', $doc[0]->gatepass);
                Session::put('current_status', $doc[0]->current_status);
                Session::put('storage_validity', $doc[0]->storage_validity);
                Session::put('container_movement', $doc[0]->container_movement);
                Session::put('safe_keep', $doc[0]->safe_keep);
                       
                  
               
        }
        return $next($request);
    }
}
