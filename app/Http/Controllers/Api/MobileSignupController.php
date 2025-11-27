<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;

class MobileSignupController extends Controller
{
    // Method untuk signup mobile tanpa token auth
    public function signup(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama'     => 'required|string|max:255',
            'username' => 'nullable|string|unique:users,username|unique:users_verification,username',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|min:6|same:password',
            'email'    => 'required|email|unique:users,email|unique:users_verification,email',
            'no_hp'    => 'nullable|string|max:20',
            'telepon'  => 'nullable|string|max:20',
        ]);

        $token = Str::random(60);
        
        // Simpan ke users_verification
        $verificationId = DB::table('users_verification')->insertGetId([
            'nama'            => $request->nama,
            'email'           => $request->email,
            'username'        => $request->username ?? $request->email, // Use email as username if not provided
            'password'        => sha1($request->password),
            'telepon'         => $request->telepon ?? $request->no_hp ?? '0', // Simpan telepon dari request
            'gambar'          => null,
            'paket_id'        => 0,
            'token'           => $token,
            'tanggal_expired' => Carbon::now()->addDays(3),
            'created_at'      => now(),
            'updated_at'      => now()
        ]);

        // Kirim email verifikasi
        try {
            $user = new \stdClass();
            $user->nama = $request->nama;
            $user->email = $request->email;
            
            Mail::to($request->email)->send(new VerificationEmail($user, $token));
        } catch (\Exception $e) {
            \Log::error('Email verification failed: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil. Silakan cek email untuk verifikasi.',
            'data' => [
                'verification_id' => (int) $verificationId,
                'email' => $request->email
            ]
        ], 200);
    }

    // Method untuk verifikasi email
    public function verifyEmail(Request $request)
    {
        $token = $request->get('token');
        $verification_data = DB::table('users_verification')->where('token', $token)->first();

        if(!$verification_data) {
            return response()->json([
                'success' => false,
                'message' => 'Token verifikasi tidak valid.'
            ], 400);
        }

        if(Carbon::now()->gt($verification_data->tanggal_expired)) {
            DB::table('users_verification')->where('token', $token)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Waktu pendaftaran sudah berakhir. Silakan daftar kembali.'
            ], 400);
        }

        // Buat user baru
        $newUserId = DB::table('users')->insertGetId([
            'nama'          => $verification_data->nama,
            'email'         => $verification_data->email,
            'username'      => $verification_data->username,
            'password'      => $verification_data->password,
            'akses_level'   => 'User',
            'tanggal'       => now()
        ]);

        // Buat data staff
        $newStaffId = DB::table('staff')->insertGetId([
            'id_user'           => $newUserId,
            'nama_staff'        => $verification_data->nama,
            'email'             => $verification_data->email,
            'telepon'           => $verification_data->telepon ?? '0',
            'status_staff'      => 'Tidak',
            'slug_staff'        => Str::slug($verification_data->nama, '-'),
            'id_kategori_staff' => 1,
            'nickname_staff'    => $verification_data->nama,
            'id_provinsi'       => 1,
            'id_kabupaten'      => 1,
            'id_kecamatan'      => 1,
            'total_kuota_iklan' => 0,
            'sisa_kuota_iklan'  => 0,
            'tanggal'           => now()
        ]);

        // Hapus data verifikasi
        DB::table('users_verification')->where('token', $token)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi berhasil! Akun Anda sudah aktif.',
            'data' => [
                'id_user' => (int) $newUserId,
                'staff_id' => (int) $newStaffId
            ]
        ], 200);
    }

    // Method untuk resend verification
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $verification = DB::table('users_verification')
            ->where('email', $request->email)
            ->first();

        if(!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan dalam data pendaftaran.'
            ], 404);
        }

        // Update token dan expired time
        $newToken = Str::random(60);
        DB::table('users_verification')
            ->where('email', $request->email)
            ->update([
                'token' => $newToken,
                'tanggal_expired' => Carbon::now()->addDays(3),
                'updated_at' => now()
            ]);

        // Kirim email ulang
        try {
            $user = new \stdClass();
            $user->nama = $verification->nama;
            $user->email = $verification->email;
            
            Mail::to($request->email)->send(new VerificationEmail($user, $newToken));
        } catch (\Exception $e) {
            \Log::error('Resend email verification failed: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Email verifikasi telah dikirim ulang.'
        ], 200);
    }

    public function resendVerificationWeb($email)
    {
        $verification = DB::table('users_verification')
            ->where('email', $email)
            ->first();

        if(!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan dalam data pendaftaran.'
            ], 404);
        }

        $newToken = Str::random(60);
        DB::table('users_verification')
            ->where('email', $email)
            ->update([
                'token' => $newToken,
                'tanggal_expired' => Carbon::now()->addDays(3),
                'updated_at' => now()
            ]);

        try {
            $user = new \stdClass();
            $user->nama = $verification->nama;
            $user->email = $verification->email;
            Mail::to($email)->send(new VerificationEmail($user, $newToken));
        } catch (\Exception $e) {
            \Log::error('Resend email verification failed: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Email verifikasi telah dikirim ulang.'
        ], 200);
    }

    public function verifyEmailWeb(Request $request)
    {
        return $this->verifyEmail($request);
    }
}
