<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $userId = $request->input('user_id');
        if ($userId) {
            $user = \App\Models\User::where('id_user', $userId)->first();
            if ($user && $user->akses_level == 'Admin') {
                return $next($request);
            }
        }

        return response()->json(['message' => 'Akses ditolak. Hanya untuk Admin.'], 403);
    }
}
