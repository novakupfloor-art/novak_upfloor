<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MobileStaffController extends Controller
{
    /**
     * Get staff profile by ID
     */
    public function getProfile(Request $request, $id)
    {
        try {
            $staff = DB::table('staff')
                ->join('users', 'users.id_user', '=', 'staff.id_user')
                ->where('staff.id_staff', $id)
                ->select(
                    'staff.*',
                    'users.nama as user_nama',
                    'users.email as user_email',
                    'users.username as user_username'
                )
                ->first();

            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff tidak ditemukan'
                ], 404);
            }

            // Transform data untuk mengembalikan URL lengkap gambar
            $staffData = (array) $staff;
            if ($staffData['gambar']) {
                $staffData['gambar'] = asset('assets/upload/staff/' . $staffData['gambar']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profil staff berhasil diambil',
                'data' => $staffData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil profil staff: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update staff profile
     * Menggunakan POST karena Laravel tidak support PUT multipart secara native
     */
    public function updateProfile(Request $request, $id)
    {
        try {
            // Cek apakah staff ada
            $staff = DB::table('staff')->where('id_staff', $id)->first();
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff tidak ditemukan'
                ], 404);
            }

            // Ambil data langsung dari request
            $namaStaff = trim($request->input('nama_staff', ''));
            $email = trim($request->input('email', ''));
            $telepon = trim($request->input('telepon', ''));
            $nicknameStaff = trim($request->input('nickname_staff', ''));

            // Validasi data tidak boleh kosong
            if (empty($namaStaff)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama staff tidak boleh kosong'
                ], 400);
            }

            if (empty($email)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak boleh kosong'
                ], 400);
            }

            if (empty($telepon)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Telepon tidak boleh kosong'
                ], 400);
            }

            if (empty($nicknameStaff)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nickname tidak boleh kosong'
                ], 400);
            }

            // Upload gambar jika ada
            $gambarFilename = null;
            if ($request->hasFile('gambar')) {
                $gambarFile = $request->file('gambar');
                
                // Hapus gambar lama jika ada
                if ($staff->gambar && file_exists(public_path('assets/upload/staff/' . $staff->gambar))) {
                    @unlink(public_path('assets/upload/staff/' . $staff->gambar));
                }
                
                // Generate nama file yang aman
                $gambarFilename = 'staff_' . $id . '_' . time() . '.' . $gambarFile->getClientOriginalExtension();
                $gambarFile->move(public_path('assets/upload/staff/'), $gambarFilename);
            }

            // Siapkan data untuk update tabel staff
            $updateStaffData = [
                'nama_staff' => $namaStaff,
                'email' => $email,
                'telepon' => $telepon,
                'nickname_staff' => $nicknameStaff,
                'tanggal' => now()
            ];

            // Tambahkan gambar jika ada upload baru
            if ($gambarFilename) {
                $updateStaffData['gambar'] = $gambarFilename;
            }

            // Update tabel staff
            DB::table('staff')
                ->where('id_staff', $id)
                ->update($updateStaffData);

            // Sinkronisasi ke tabel users
            DB::table('users')
                ->where('id_user', $staff->id_user)
                ->update([
                    'nama' => $namaStaff,
                    'email' => $email,
                    'tanggal' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get staff statistics
     */
    public function getStats(Request $request, $id)
    {
        try {
            $staff = DB::table('staff')->where('id_staff', $id)->first();
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff tidak ditemukan'
                ], 404);
            }

            // Hitung statistik properti
            $totalProperties = DB::table('property_db')
                ->where('id_staff', $id)
                ->count();

            $activeProperties = DB::table('property_db')
                ->where('id_staff', $id)
                ->where('status', 1)
                ->count();

            // Hitung statistik transaksi
            $totalTransactions = DB::table('transaksi_paket')
                ->where('id_staff', $id)
                ->count();

            $confirmedTransactions = DB::table('transaksi_paket')
                ->where('id_staff', $id)
                ->where('status_pembayaran', 'confirmed')
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Statistik staff berhasil diambil',
                'data' => [
                    'staff' => [
                        'id_staff' => $staff->id_staff,
                        'nama_staff' => $staff->nama_staff,
                        'status_staff' => $staff->status_staff,
                        'total_kuota_iklan' => $staff->total_kuota_iklan,
                        'sisa_kuota_iklan' => $staff->sisa_kuota_iklan
                    ],
                    'statistics' => [
                        'total_properties' => $totalProperties,
                        'active_properties' => $activeProperties,
                        'total_transactions' => $totalTransactions,
                        'confirmed_transactions' => $confirmedTransactions
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik staff: ' . $e->getMessage()
            ], 500);
        }
    }
}
