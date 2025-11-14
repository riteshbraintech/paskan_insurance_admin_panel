<?php

namespace App\Http\Middleware\Api\V1;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class DetectLanguage
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
         // Priority order: Query → Header → Body → Default
        $lang = $request->get('lang') 
              ?? $request->header('Accept-Language') 
              ?? $request->input('lang') 
              ?? config('app.locale');

        // Normalize (e.g., en-US → en)
        $lang = substr(strtolower($lang), 0, 2);

        App::setLocale($lang);
        $request->merge(['lang' => $lang]);

        return $next($request);
    }
}
