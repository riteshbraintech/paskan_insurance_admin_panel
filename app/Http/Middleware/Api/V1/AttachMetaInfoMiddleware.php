<?php

namespace App\Http\Middleware\Api\v1;

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
