<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreventAccessRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $path = $request->segment(1);
        $group_type_id = Auth::guard('admin')->user()->group_admin_id;

        if($path == 'admin' && $group_type_id == 1){
            return $next($request);
        }else if($path == 'restaurant' && $group_type_id == 2){
            return $next($request);
        }else if($path == 'seller' && $group_type_id == 3) {
            return $next($request);
        }else{
            $urlname = '';

            switch ($group_type_id) {
                case 1:
                    $urlname = 'admin';
                    break;
                case 2:
                    $urlname = 'restaurant';
                    break;
                case 3:
                    $urlname = 'seller';
                    break;

                default:
                    # code...
                    break;
            }

            return redirect($urlname.'/dashboard');
        }

    }
}
