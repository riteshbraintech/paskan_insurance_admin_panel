<?php

namespace App\Http\Middleware\Api\V1;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AttachMetaInfoMiddleware
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
        $endpoint = $request->path();

        // 1️⃣ Default: user via Sanctum
        $user = auth('sanctum')->user();

        // 2️⃣ Special Case: LOGIN (user not authenticated yet)
        if (!$user && $endpoint === 'api/v1/auth/login') {

            // If login uses id_number
            $user = \App\Models\User::where('id_number', $request->id_number)->first();
            
            // Attach the user temporarily so logActivity() gets user_id
            auth()->setUser($user);
        }

        // 3️⃣ User ID + Name
        $userId = $user->id ?? null;
        $userName = $user->name ?? 'Guest';

        // Description Map
        $endpointDescriptions = [
            'api/v1/auth/login' => "$userName logged in",
            'api/v1/logout'     => "$userName logged out",
            'api/v1/home'       => "$userName opened Home Page",
            'api/v1/profile'    => "$userName viewed Profile Page",
            // add more...
        ];

        $description = $endpointDescriptions[$endpoint] ?? "$userName made a request";

        $ip = $request->ip() === '::1' ? '127.0.0.1' : $request->ip();

        // 4️⃣ Save Log
        if ($userId) {
            logActivity('api_request', $endpoint, $description, $ip);
        }
        
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            $status = $response->getStatusCode();
            $lang = app()->getLocale();

            $success = $status >= 200 && $status < 300;

            // Auto-detect API version from URI (e.g. /api/v1/...)
            $segments = $request->segments();
            $apiVersion = $segments[1] ?? 'v1'; // assumes URI like /api/v1/...

            // Build the base meta info
            $meta = [
                    'meta' => [
                        'success' => $success,
                        'locale' => $lang,
                        'api' => [
                            'version' => $apiVersion,
                            'endpoint' => $request->path(),   // e.g. api/v1/home
                            'method' => $request->method(),   // e.g. GET / POST
                            'status' => $status,
                        ],
                        'timestamp' => now()->toIso8601String(),
                    ]
                ];

            // Merge meta + response data
            // If it's success, merge all directly
            // If it's error, keep error fields also merged
            if (is_array($data)) {
                $merged = array_merge($data, $meta);
            } else {
                $merged = array_merge($meta, ['message' => $data]);
            }

            $response->setData($merged);
        }

        return $response;
    }
}
