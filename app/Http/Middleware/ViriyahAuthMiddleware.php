<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use App\Service\ViriyahAuthService;

class ViriyahAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        // Get a valid token (auto refresh)
        $token = app(ViriyahAuthService::class)->getValidToken();

        // Attach Authorization header automatically
        $request->headers->set('Authorization', 'Bearer ' . $token);

        return $next($request);
    }
}
