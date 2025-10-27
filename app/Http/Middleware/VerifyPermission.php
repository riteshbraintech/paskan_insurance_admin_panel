<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // // if login user is super-admin
        // if(admin()->user()->admin_type_id == 1){
        //     return $next($request);
        // }

        // if login user : super-admin, staff, other
        if(is_permission($role)){
            return $next($request);
        }

        // rediretc to unauthorized url
        return abort(401);

    }
}
