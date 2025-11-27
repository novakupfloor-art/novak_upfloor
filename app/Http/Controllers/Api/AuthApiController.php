<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\DB;
// Hash facade tidak lagi diperlukan untuk sha1
// use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;

class AuthApiController extends Controller
{
    /**
     * Registrasi pengguna baru dari aplikasi mobile.
     * Endpoint: POST /api/v1/auth/register
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama'              => 'required|string|max:255',
            'username'          => 'required|string|max:255|unique:users,username',
            'email'             => 'required|string|email|max:255|unique:users,email',
            'password'          => 'required|string|min:6',
            'paket_id'          => 'required|integer|exists:paket_iklan,id',
            'bukti_pembayaran'  => 'required|file|mimes:jpeg,jpg,png|max:4096',
        ], [
            'bukti_pembayaran.max' => 'Bukti pembayaran tidak boleh lebih dari 4096 kilobytes (4MB)',
            'bukti_pembayaran.file' => 'Bukti pembayaran harus berupa file',
            'bukti_pembayaran.mimes' => 'Bukti pembayaran harus berupa file dengan format: jpeg, jpg, png',
            'bukti_pembayaran.required' => 'Bukti pembayaran harus diupload'
        ]);

        if ($validator->fails()) {
            // Debug informasi file
            $debugInfo = [];
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $debugInfo = [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'extension' => $file->getClientOriginalExtension(),
                    'is_valid' => $file->isValid(),
                    'error' => $file->getError()
                ];
            }
            
            return response()->json([
                'success' => false, 
                'errors' => $validator->errors(),
                'debug_info' => $debugInfo
            ], 422);
        }

        // Cek juga di tabel verifikasi agar tidak ada duplikasi email/username yang pending
        $isPending = DB::table('users_verification')
                        ->where('email', $request->email)
                        ->orWhere('username', $request->username)
                        ->exists();

        if ($isPending) {
            return response()->json(['success' => false, 'message' => 'Email atau username sudah terdaftar dan menunggu verifikasi.'], 422);
        }

        // Handle upload file bukti pembayaran
        $filePath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $fileName = time() . '_' . Str::slug($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('public/bukti_pembayaran', $fileName);
        }

        $token = Str::random(60);

        DB::table('users_verification')->insert([
            'nama'              => $request->nama,
            'email'	            => $request->email,
            'username'   	    => $request->username,
            // PERUBAHAN: Menggunakan sha1()
            'password'          => sha1($request->password),
            'paket_id'          => $request->paket_id,
            'gambar'            => $filePath,
            'token'             => $token,
            'tanggal_expired'   => Carbon::now()->addDays(3),
            'created_at'        => now(),
            'updated_at'        => now()
        ]);

        try {
            // Kirim email verifikasi menggunakan Mailable class
            $user = new \stdClass();
            $user->nama = $request->nama;
            $user->email = $request->email;
            
            Mail::to($request->email)->send(new \App\Mail\VerificationEmail($user, $token));
        } catch (\Exception $e) {
            // Log::error('Email verification failed to send: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil. Silakan cek email Anda untuk link aktivasi.',
            'data' => [
                'email' => $request->email,
                'nama' => $request->nama,
                'username' => $request->username,
                'paket_id' => $request->paket_id,
                'token' => $token,
                'expired_at' => Carbon::now()->addDays(3)->toISOString()
            ]
        ], 201);
    }

    /**
     * Login pengguna dari aplikasi mobile.
     * Endpoint: POST /api/v1/auth/login
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::where('username', $request->username)
                    ->orWhere('email', $request->username)
                    ->first();
        
        // PERUBAHAN: Cek password hanya menggunakan sha1()
        if (!$user || $user->password !== sha1($request->password) ) {
            return response()->json(['success' => false, 'message' => 'Kredensial tidak valid.'], 401);
        }

        // Ambil data staff yang terhubung
        $staff = DB::table('staff')->where('id_user', $user->id_user)->first();

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'id_user' => $user->id_user,
                'id_staff' => $staff ? $staff->id_staff : null,
                'nama' => $user->nama,
                'username' => $user->username,
                'email' => $user->email,
                'akses_level' => $user->akses_level,
                'sisa_kuota_iklan' => $staff ? $staff->sisa_kuota_iklan : 0,
            ]
        ], 200);
    }

    /**
     * Logout pengguna dari aplikasi mobile.
     * Endpoint: POST /api/v1/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
            'data' => [
                'logged_out_at' => now()->toISOString()
            ]
        ], 200);
    }
}
