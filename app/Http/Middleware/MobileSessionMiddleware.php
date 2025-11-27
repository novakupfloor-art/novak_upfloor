<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Session;
use App\Helpers\SessionHelper;
use App\Helpers\SessionResponseHelper;

class MobileSessionMiddleware
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
        // Ambil session ID dari header atau query parameter
        $sessionId = $request->header('X-Session-ID') 
                    ?? $request->query('id_session') 
                    ?? $request->input('id_session');

        if (!$sessionId) {
            return SessionResponseHelper::unauthorized('Session ID tidak ditemukan');
        }

        // Validasi session
        $session = Session::validateSession($sessionId);
        if (!$session) {
            return SessionResponseHelper::unauthorized('Session tidak valid atau expired');
        }

        // Ambil data user dan staff
        $user = $session->user;
        $staff = $session->staff;

        if (!$user) {
            return SessionResponseHelper::unauthorized('User tidak ditemukan');
        }

        // Tambahkan data session ke request
        $request->merge([
            'session_id' => $session->session_id,
            'user_id' => $user->id_user,
            'staff_id' => $staff ? $staff->id_staff : null,
            'session_data' => [
                'session' => $session,
                'user' => $user,
                'staff' => $staff
            ]
        ]);

        return $next($request);
    }
}
