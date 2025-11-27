<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuthenticateToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('token_auth');
        if (empty($token)) {
            return response()->json(['success' => false, 'message' => 'Token diperlukan'], 401);
        }

        $len = strlen($token);
        if ($len < 15) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid'], 401);
        }

        $tsStr = substr($token, -14);
        $userIdStr = substr($token, 0, $len - 14);

        if (!ctype_digit($tsStr) || !ctype_digit($userIdStr)) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid'], 401);
        }

        try {
            Carbon::createFromFormat('YmdHis', $tsStr);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid'], 401);
        }

        $userId = (int) $userIdStr;

        // Validate token exists in storages table
        $storage = DB::table('storages')
            ->where('token', $token)
            ->where('id_user', $userId)
            ->first();

        if (!$storage) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid atau sudah tidak aktif'], 401);
        }

        $user = DB::table('users')->where('id_user', $userId)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 401);
        }

        if (empty($user->email_verified_at)) {
            return response()->json(['success' => false, 'message' => 'Email belum diverifikasi'], 403);
        }

        $request->attributes->set('auth_user_id', $userId);
        return $next($request);
    }
}