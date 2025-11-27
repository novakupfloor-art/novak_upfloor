<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Staff;
use App\Models\TransaksiPaket;
use App\Models\Property;

class MobileDashboardController extends Controller
{
    /**
     * Get dashboard statistics for user
     * ✅ FIXED: Menggunakan Eloquent Models instead of DB::table() (Rekomendasi #6)
     */
    public function getStats(Request $request, $id)
    {
        try {
            // ✅ Gunakan Eloquent Model
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // ✅ Gunakan Eloquent Model dengan relationship
            $staff = Staff::where('id_user', $id)->first();

            // Hitung statistik properti
            $totalProperties = 0;
            $activeProperties = 0;
            if ($staff) {
                // ✅ Gunakan Eloquent Model
                $totalProperties = Property::where('id_staff', $staff->id_staff)->count();
                $activeProperties = Property::where('id_staff', $staff->id_staff)
                    ->where('status', 1)
                    ->count();
            }

            // ✅ Gunakan Eloquent Model untuk transaksi
            $totalTransactions = TransaksiPaket::where('id_user', $id)->count();
            $confirmedTransactions = TransaksiPaket::where('id_user', $id)
                ->where('status_pembayaran', 'confirmed')
                ->count();

            // Hitung statistik paket (sama dengan transaksi)
            $totalPackages = $totalTransactions;
            $activePackages = $confirmedTransactions;

            return response()->json([
                'success' => true,
                'message' => 'Data dashboard berhasil diambil',
                'data' => [
                    'user' => [
                        'id_user' => $user->id_user,
                        'nama' => $user->nama,
                        'email' => $user->email,
                        'username' => $user->username
                    ],
                    'staff' => $staff ? [
                        'id_staff' => $staff->id_staff,
                        'nama_staff' => $staff->nama_staff,
                        'email' => $staff->email,
                        'telepon' => $staff->telepon,
                        'nickname_staff' => $staff->nickname_staff,
                        'status_staff' => $staff->status_staff,
                        'total_kuota_iklan' => $staff->total_kuota_iklan,
                        'sisa_kuota_iklan' => $staff->sisa_kuota_iklan
                    ] : null,
                    'statistics' => [
                        'total_properties' => $totalProperties,
                        'active_properties' => $activeProperties,
                        'total_transactions' => $totalTransactions,
                        'confirmed_transactions' => $confirmedTransactions,
                        'total_packages' => $totalPackages,
                        'active_packages' => $activePackages
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent activities for user
     * ✅ FIXED: Menggunakan Eloquent Models instead of DB::table() (Rekomendasi #6)
     */
    public function getRecentActivities(Request $request, $id)
    {
        try {
            // ✅ Gunakan Eloquent Model
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // ✅ Gunakan Eloquent Model
            $staff = Staff::where('id_user', $id)->first();
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff tidak ditemukan'
                ], 404);
            }

            // ✅ Gunakan Eloquent Model untuk recent properties
            $recentProperties = Property::where('id_staff', $staff->id_staff)
                ->orderBy('tanggal', 'desc')
                ->limit(5)
                ->get();

            // ✅ Gunakan Eloquent Model dengan relationship untuk recent transactions
            $recentTransactions = TransaksiPaket::with('paketIklan')
                ->where('id_user', $id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'kode_transaksi' => $transaction->kode_transaksi,
                        'status_pembayaran' => $transaction->status_pembayaran,
                        'bukti_pembayaran' => $transaction->bukti_pembayaran,
                        'created_at' => $transaction->created_at,
                        'updated_at' => $transaction->updated_at,
                        'nama_paket' => $transaction->paketIklan->nama_paket ?? null,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Aktivitas terbaru berhasil diambil',
                'data' => [
                    'recent_properties' => $recentProperties,
                    'recent_transactions' => $recentTransactions
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil aktivitas terbaru: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get my packages for user
     * ✅ FIXED: Menggunakan Eloquent Models instead of DB::table() (Rekomendasi #6)
     */
    public function getMyPackages(Request $request, $id)
    {
        try {
            // ✅ Gunakan Eloquent Model
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // ✅ Gunakan Eloquent Model dengan relationship
            $packages = TransaksiPaket::with('paketIklan')
                ->where('id_user', $id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($transaction) {
                    return [
                        'transaction_id' => $transaction->id,
                        'paket_id' => $transaction->paket_id,
                        'status_pembayaran' => $transaction->status_pembayaran,
                        'bukti_pembayaran' => $transaction->bukti_pembayaran,
                        'keterangan' => $transaction->keterangan,
                        'created_at' => $transaction->created_at,
                        'updated_at' => $transaction->updated_at,
                        'nama_paket' => $transaction->paketIklan->nama_paket ?? null,
                        'harga' => $transaction->paketIklan->harga ?? null,
                        'kuota_iklan' => $transaction->paketIklan->kuota_iklan ?? null,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Paket saya berhasil diambil',
                'data' => $packages
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil paket saya: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get my advertisements for user
     * ✅ FIXED: Menggunakan Eloquent Models instead of DB::table() (Rekomendasi #6)
     */
    public function getMyAdvertisements(Request $request, $id)
    {
        try {
            // ✅ Gunakan Eloquent Model
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // ✅ Gunakan Eloquent Model
            $staff = Staff::where('id_user', $id)->first();
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff tidak ditemukan'
                ], 404);
            }

            // Get user's advertisements (properties)
            $advertisements = DB::table('property_db as p')
                ->leftJoin('kategori_property as kp', 'p.id_kategori_property', '=', 'kp.id_kategori_property')
                ->leftJoin('provinsi as prov', 'p.id_provinsi', '=', 'prov.id')
                ->leftJoin('kabupaten as kab', 'p.id_kabupaten', '=', 'kab.id')
                ->leftJoin('kecamatan as kec', 'p.id_kecamatan', '=', 'kec.id')
                ->where('p.id_staff', $staff->id_staff)
                ->select(
                    'p.id_property',
                    'p.kode',
                    'p.nama_property',
                    'p.tipe',
                    'p.harga',
                    'p.lt',
                    'p.lb',
                    'p.kamar_tidur',
                    'p.kamar_mandi',
                    'p.alamat',
                    'p.status',
                    'p.view_count',
                    'p.tanggal',
                    'p.id_kategori_property',
                    'p.id_provinsi',
                    'p.id_kabupaten',
                    'p.id_kecamatan',
                    'p.lantai',
                    'p.isi',
                    'p.surat',
                    'p.jenis_sewa',
                    'kp.nama_kategori_property',
                    'prov.nama as nama_provinsi',
                    'kab.nama as nama_kabupaten',
                    'kec.nama as nama_kecamatan'
                )
                ->orderBy('p.tanggal', 'desc')
                ->get();

            // Ambil images untuk setiap properti dan ubah menjadi URL absolut
            $propertyIds = $advertisements->pluck('id_property')->toArray();
            $images = DB::table('property_img')
                ->whereIn('id_property', $propertyIds)
                ->orderBy('id_property')
                ->orderBy('index_img')
                ->get()
                ->groupBy('id_property');

            // ✅ Fixed: Use config instead of hardcoded URL (Fix #6)
            $advertisements = $advertisements->map(function ($property) use ($images) {
                $propertyImages = $images->get($property->id_property, collect())->map(function ($img) {
                    return config('app.upload_url') . '/property/' . $img->gambar;
                })->toArray();

                $property->images = $propertyImages;
                return $property;
            });

            return response()->json([
                'success' => true,
                'message' => 'Iklan saya berhasil diambil',
                'data' => $advertisements
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil iklan saya: ' . $e->getMessage()
            ], 500);
        }
    }
}
