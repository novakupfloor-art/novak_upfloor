<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\SessionHelper;
use App\Helpers\SessionResponseHelper;
use Illuminate\Http\JsonResponse;

class SessionMiddleware
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
        // Skip session validation untuk public routes
        if ($this->isPublicRoute($request)) {
            return $next($request);
        }

        // Validate session
        $sessionData = SessionHelper::validateSession($request);
        
        if (!$sessionData) {
            // Log failed session validation
            \Illuminate\Support\Facades\Log::warning('Session validation failed in middleware', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'headers' => [
                    'X-Session-ID' => $request->header('X-Session-ID'),
                    'X-User-ID' => $request->header('X-User-ID'),
                ]
            ]);

            return SessionResponseHelper::unauthorized(
                'Session tidak valid atau telah expired. Silakan login kembali.',
                [
                    'session_id' => $request->header('X-Session-ID'),
                    'user_id' => $request->header('X-User-ID'),
                    'reason' => 'Session validation failed in middleware'
                ]
            );
        }

        // Add session data to request for use in controllers
        $request->merge([
            '_session_data' => $sessionData,
            '_session_info' => [
                'session_id' => $sessionData['session']->session_id,
                'user_id' => $sessionData['user_id'],
                'staf_id' => $sessionData['staf_id'],
                'expires_at' => $sessionData['session']->expires_at?->toISOString(),
                'is_valid' => true,
            ]
        ]);

        // Process request
        $response = $next($request);

        // Auto-inject session info to JSON responses
        if ($response instanceof JsonResponse) {
            $responseData = $response->getData(true);
            
            // Only inject if not already present
            if (!isset($responseData['session'])) {
                $sessionInfo = $request->get('_session_info');
                if ($sessionInfo) {
                    $responseData['session'] = $sessionInfo;
                    $response->setData($responseData);
                }
            }
        }

        return $response;
    }

    /**
     * Check if route is public (doesn't require session)
     */
    private function isPublicRoute(Request $request): bool
    {
        $publicRoutes = [
            'api/v1/mobile/auth/login',
            'api/v1/mobile/auth/register',
            'api/v1/mobile/auth/verify-email',
            'api/v1/mobile/auth/forgot-password',
            'api/v1/mobile/auth/reset-password',
            'api/v1/mobile/auth/packages',
            'api/v1/mobile/properties',
            'api/v1/mobile/properties/search',
            'api/v1/mobile/properties/categories',
        ];

        $path = $request->path();
        
        foreach ($publicRoutes as $route) {
            if (str_starts_with($path, $route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get session info for logging
     */
    private function getSessionInfo(Request $request): array
    {
        return [
            'session_id' => $request->header('X-Session-ID'),
            'user_id' => $request->header('X-User-ID'),
            'staf_id' => $request->header('X-Staf-ID'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];
    }
}
