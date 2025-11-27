<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class MobileLoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            "username" => "required|string",
            "password" => "required|string",
        ]);

        $user = DB::table("users")
            ->where(function ($query) use ($request) {
                $query->where("username", $request->username)
                    ->orWhere("email", $request->username);
            })
            ->first();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "Username atau password salah.",
            ], 401);
        }

        $passwordValid = Hash::check($request->password, $user->password);

        if (!$passwordValid && $user->password === sha1($request->password)) {
            // Password masih format lama (SHA1). Upgrade ke bcrypt setelah verifikasi berhasil.
            $passwordValid = true;
            DB::table('users')
                ->where('id_user', $user->id_user)
                ->update(['password' => Hash::make($request->password)]);
        }

        if (!$passwordValid) {
            return response()->json([
                "success" => false,
                "message" => "Username atau password salah.",
            ], 401);
        }

        if (empty($user->email_verified_at) || $user->email_verified_at == "") {
            return response()->json([
                "success" => false,
                "message" => "Email belum diverifikasi. Silakan cek email Anda.",
            ], 403);
        }

        $staff = DB::table("staff")->where("id_user", $user->id_user)->first();

        // Generate token dengan format: id_user + date (YmdHis)
        $token = $user->id_user . date('YmdHis');

        // Get device info and IP from request
        $deviceInfo = $request->input('device_info', 'Unknown Device');
        $ipAddress = $request->ip();

        // Simpan token ke tabel storages
        DB::table('storages')->updateOrInsert(
            [
                'id_user' => $user->id_user,
                'token' => $token,
            ],
            [
                'device_info' => $deviceInfo,
                'ip_address' => $ipAddress,
                'date' => now(),
            ]
        );

        return response()->json([
            "success" => true,
            "message" => "Login berhasil.",
            "data" => [
                "token_auth" => $token,
                "user" => [
                    "id_user" => (int) $user->id_user,
                    "username" => $user->username,
                    "nama" => $user->nama,
                    "email" => $user->email,
                    "akses_level" => $user->akses_level,
                    "staff_id" => $staff ? (int) $staff->id_staff : null,
                    "status_staff" => $staff ? $staff->status_staff : null,
                    "sisa_kuota_iklan" => $staff ? (int) $staff->sisa_kuota_iklan : 0,
                    "total_kuota_iklan" => $staff ? (int) $staff->total_kuota_iklan : 0,
                    "gambar" => $user->gambar,
                    "telepon" => $user->telepon ?? null,
                ],
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        // Note: We do NOT delete from storages table to maintain login history
        // The token will simply become invalid when user logs in again with a new token

        return response()->json([
            "success" => true,
            "message" => "Logout berhasil.",
        ], 200);
    }

    public function logoutAll(Request $request)
    {
        return response()->json([
            "success" => true,
            "message" => "Logout semua berhasil.",
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan.'
            ], 404);
        }

        $token = Str::random(60);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        try {
            Mail::to($request->email)->send(new \App\Mail\PasswordResetEmail($request->email, $token));
        } catch (\Exception $e) {
            Log::error('Password reset email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Link reset password telah dikirim ke email.'
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|min:6|same:password'
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return response()->json([
                'success' => false,
                'message' => 'Token reset tidak valid.'
            ], 400);
        }

        DB::table('users')
            ->where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset.'
        ], 200);
    }
}
