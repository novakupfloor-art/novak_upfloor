<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MobilePackageController extends Controller
{
    // Method untuk mendapatkan paket yang tersedia
    public function getAvailablePackages()
    {
        $packages = DB::table('paket_iklan')
            ->where('is_active', 1)
            ->orderBy('harga', 'asc')
            ->get();

        // Transform data untuk konsistensi dengan mobile app
        $transformedPackages = $packages->map(function($package) {
            return [
                'id' => (int) $package->id,
                'nama_paket' => $package->nama_paket,
                'harga' => (int) $package->harga,
                'kuota_iklan' => (int) $package->kuota_iklan,
                'deskripsi' => $package->deskripsi,
                'is_active' => (bool) $package->is_active
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar paket berhasil diambil',
            'data' => $transformedPackages
        ], 200);
    }

    // Method untuk mendapatkan paket yang dibeli user
    // ✅ Fixed: Changed to accept $id parameter from route instead of Request body (Fix #3)
    public function getMyPackages($id)
    {
        $userId = $id;
        
        // Get staff quota info
        $staff = DB::table('staff')
            ->where('id_user', $userId)
            ->select('total_kuota_iklan', 'sisa_kuota_iklan', 'status_staff')
            ->first();
        
        $packages = DB::table('transaksi_paket as tp')
            ->join('paket_iklan as p', 'tp.paket_id', '=', 'p.id')
            ->where('tp.id_user', $userId)
            ->select('tp.*', 'p.nama_paket', 'p.harga', 'p.kuota_iklan')
            ->orderBy('tp.created_at', 'desc')
            ->get();

        // Transform data untuk konsistensi dengan mobile app
        $transformedPackages = $packages->map(function ($package) use ($staff) {
            return [
                'id' => (int) $package->id,
                'id_user' => (int) $package->id_user,
                'id_staff' => (int) $package->id_staff,
                'paket_id' => (int) $package->paket_id,
                'kode_transaksi' => $package->kode_transaksi,
                'status_pembayaran' => $package->status_pembayaran,
                'bukti_pembayaran' => $package->bukti_pembayaran,
                'keterangan' => $package->keterangan,
                'created_at' => $package->created_at,
                'updated_at' => $package->updated_at,
                'nama_paket' => $package->nama_paket,
                'harga' => (int) $package->harga,
                'kuota_iklan' => (int) $package->kuota_iklan,
                'sisa_kuota_iklan' => $staff ? (int) $staff->sisa_kuota_iklan : 0,
                'total_kuota_iklan' => $staff ? (int) $staff->total_kuota_iklan : 0,
                'status_staff' => $staff ? $staff->status_staff : 'Tidak'
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar paket user berhasil diambil',
            'data' => $transformedPackages
        ], 200);
    }

    // Method untuk membeli paket
    public function buyPackage(Request $request)
    {
        $request->validate([
            'id_user' => 'required|integer',
            'paket_id' => 'required|exists:paket_iklan,id',
            'bukti_pembayaran' => 'nullable|file|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $userId = $request->id_user;
        
        // Get staff ID from user
        $staff = DB::table('staff')->where('id_user', $userId)->first();
        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff tidak ditemukan untuk user ini.'
            ], 404);
        }
        $staffId = $staff->id_staff;

        // Upload bukti pembayaran (optional)
        $filename = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = Str::slug($userId . '-' . time()) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/upload/bukti/'), $filename);
        }

        // Buat transaksi
        // ✅ Fixed: Added required 'keterangan' field (Fix #4)
        $transactionId = DB::table('transaksi_paket')->insertGetId([
            'id_user' => $userId,
            'id_staff' => $staffId,
            'paket_id' => $request->paket_id,
            'kode_transaksi' => 'WPM-' . strtoupper(Str::random(8)),
            'status_pembayaran' => 'pending',
            'bukti_pembayaran' => $filename,
            'keterangan' => $request->keterangan ?? 'Pembelian paket iklan',  // ✅ Added
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembelian paket berhasil. Menunggu konfirmasi admin.',
            'data' => [
                'transaction_id' => (int) $transactionId
            ]
        ], 200);
    }

    // Method untuk reupload bukti pembayaran
    public function reuploadPaymentProof(Request $request)
    {
        $request->validate([
            'id_user' => 'required|integer',
            'transaction_id' => 'required|exists:transaksi_paket,id',
            'bukti_pembayaran' => 'required|file|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $userId = $request->id_user;
        
        // Cek apakah transaksi milik user
        $transaction = DB::table('transaksi_paket')
            ->where('id', $request->transaction_id)
            ->where('id_user', $userId)
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.'
            ], 404);
        }

        // Upload bukti pembayaran baru
        $file = $request->file('bukti_pembayaran');
        $filename = Str::slug($userId . '-' . time()) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('assets/upload/bukti/'), $filename);

        // Update transaksi
        DB::table('transaksi_paket')
            ->where('id', $request->transaction_id)
            ->update([
                'bukti_pembayaran' => $filename,
                'status_pembayaran' => 'pending',
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil diupload ulang.'
        ], 200);
    }

    // Method untuk mendapatkan transaksi user
    public function getTransactions($id_user)
    {
        $userId = (int) $id_user;

        if ($userId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'User ID tidak valid.'
            ], 400);
        }

        $transactions = DB::table('transaksi_paket as tp')
            ->join('paket_iklan as p', 'tp.paket_id', '=', 'p.id')
            ->where('tp.id_user', $userId)
            ->select('tp.*', 'p.nama_paket', 'p.harga', 'p.kuota_iklan')
            ->orderBy('tp.created_at', 'desc')
            ->get();

        // Transform data untuk konsistensi dengan mobile app
        $transformedTransactions = $transactions->map(function($transaction) {
            return [
                'id' => (int) $transaction->id,
                'id_user' => (int) $transaction->id_user,
                'id_staff' => (int) $transaction->id_staff,
                'paket_id' => (int) $transaction->paket_id,
                'kode_transaksi' => $transaction->kode_transaksi,
                'status_pembayaran' => $transaction->status_pembayaran,
                'bukti_pembayaran' => $transaction->bukti_pembayaran,
                'keterangan' => $transaction->keterangan,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
                'nama_paket' => $transaction->nama_paket,
                'harga' => (int) $transaction->harga,
                'kuota_iklan' => (int) $transaction->kuota_iklan
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar transaksi berhasil diambil',
            'data' => $transformedTransactions
        ], 200);
    }

    // Method untuk update transaction
    public function updateTransaction(Request $request, $id)
    {
        try {
            $request->validate([
                'id_user' => 'required|integer',
                'paket_id' => 'required|integer',
                'kode_transaksi' => 'required|string|max:255',
                'keterangan' => 'nullable|string',
                'bukti_pembayaran' => 'nullable|file|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $userId = $request->id_user;
            
            // Check if transaction exists and belongs to user
            $transaction = DB::table('transaksi_paket')
                ->where('id', $id)
                ->where('id_user', $userId)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan atau tidak memiliki akses'
                ], 404);
            }

            // Prepare update data
            $updateData = [
                'paket_id' => $request->paket_id,
                'kode_transaksi' => $request->kode_transaksi,
                'keterangan' => $request->keterangan,
                'updated_at' => now()
            ];

            // Upload bukti pembayaran jika ada
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $filename = Str::slug($userId . '-' . time()) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/upload/bukti/'), $filename);
                $updateData['bukti_pembayaran'] = $filename;
            }

            // Update transaction
            DB::table('transaksi_paket')
                ->where('id', $id)
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diperbarui'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // PUBLIC ROUTES (No Authentication Required)
    // ========================================

    /**
     * Menampilkan daftar paket iklan untuk public
     */
    public function index()
    {
        try {
            $packages = DB::table('paket_iklan')
                ->where('is_active', 1)
                ->orderBy('harga', 'asc')
                ->get();

            // Transform data untuk konsistensi dengan mobile app
            $transformedPackages = $packages->map(function($package) {
                return [
                    'id' => (int) $package->id,
                    'nama_paket' => $package->nama_paket,
                    'harga' => (int) $package->harga,
                    'kuota_iklan' => (int) $package->kuota_iklan,
                    'deskripsi' => $package->deskripsi,
                    'is_active' => (bool) $package->is_active
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Daftar paket iklan berhasil diambil',
                'data' => $transformedPackages
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar paket iklan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
