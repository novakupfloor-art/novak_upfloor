<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Controller untuk Admin Dashboard (Mobile App)
 * Menangani fitur khusus admin seperti konfirmasi transaksi, kelola artikel, kelola paket iklan, dan statistik
 */
class AdminDashboardController extends Controller
{
    /**
     * Get admin dashboard statistics
     * Menampilkan statistik untuk admin dashboard
     */
    public function getAdminStats()
    {
        try {
            // Hitung statistik users
            $totalAdmins = DB::table('users')
                ->whereIn(DB::raw('LOWER(akses_level)'), ['admin', 'superadmin'])
                ->count();
            $totalNonAdminUsers = DB::table('users')
                ->whereNotIn(DB::raw('LOWER(akses_level)'), ['admin', 'superadmin'])
                ->count();

            // Hitung statistik iklan/property
            $totalAds = DB::table('property_db')->count();
            $activeAds = DB::table('property_db')->where('status', 1)->count();

            // Hitung total pengunjung dari semua iklan
            $totalVisitors = DB::table('property_db')->sum('view_count');

            // Hitung statistik transaksi paket
            $pendingTransactions = DB::table('transaksi_paket')
                ->where('status_pembayaran', 'pending')
                ->count();
            $confirmedTransactions = DB::table('transaksi_paket')
                ->where('status_pembayaran', 'confirmed')
                ->count();
            $rejectedTransactions = DB::table('transaksi_paket')
                ->where('status_pembayaran', 'rejected')
                ->count();
            $totalTransactions = DB::table('transaksi_paket')->count();

            // Hitung total revenue dari transaksi confirmed
            $totalRevenue = DB::table('transaksi_paket')
                ->join('paket_iklan', 'transaksi_paket.paket_id', '=', 'paket_iklan.id')
                ->where('transaksi_paket.status_pembayaran', 'confirmed')
                ->sum('paket_iklan.harga');

            // Hitung statistik artikel
            $totalArticles = DB::table('berita')->count();
            $publishedArticles = DB::table('berita')->where('status_berita', 'Publish')->count();
            $draftArticles = DB::table('berita')->where('status_berita', 'Draft')->count();

            // Hitung statistik staff
            $totalStaff = DB::table('staff')->count();
            $activeStaff = DB::table('staff')->where('status_staff', 'Ya')->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'users' => [
                        'total_admin' => $totalAdmins,
                        'total_non_admin' => $totalNonAdminUsers,
                    ],
                    'advertisements' => [
                        'total' => $totalAds,
                        'active' => $activeAds,
                    ],
                    'visitors' => [
                        'total' => $totalVisitors,
                    ],
                    'transactions' => [
                        'pending' => $pendingTransactions,
                        'confirmed' => $confirmedTransactions,
                        'rejected' => $rejectedTransactions,
                        'total' => $totalTransactions,
                    ],
                    'revenue' => [
                        'total' => $totalRevenue,
                    ],
                    'articles' => [
                        'total' => $totalArticles,
                        'published' => $publishedArticles,
                        'draft' => $draftArticles,
                    ],
                    'staff' => [
                        'total' => $totalStaff,
                        'active' => $activeStaff,
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all transactions for admin management
     * Menampilkan semua transaksi dari semua users untuk dikelola admin
     */
    public function getAllTransactions(Request $request)
    {
        try {
            $status = $request->input('status', null); // Filter by status if provided
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 20);

            $query = DB::table('transaksi_paket')
                ->join('users', 'transaksi_paket.id_user', '=', 'users.id_user')
                ->join('paket_iklan', 'transaksi_paket.paket_id', '=', 'paket_iklan.id')
                ->leftJoin('staff', 'transaksi_paket.id_staff', '=', 'staff.id_staff')
                ->select(
                    'transaksi_paket.*',
                    'users.nama as nama_user',
                    'users.email as email_user',
                    'staff.nama_staff',
                    'staff.telepon as telepon_staff',
                    'paket_iklan.nama_paket',
                    'paket_iklan.harga',
                    'paket_iklan.kuota_iklan'
                );

            if ($status) {
                $query->where('transaksi_paket.status_pembayaran', $status);
            }

            $total = $query->count();
            $transactions = $query
                ->orderBy('transaksi_paket.created_at', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $transactions,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update transaction status (confirm/reject/edit)
     * Mengupdate status transaksi atau mengedit data transaksi
     */
    public function updateTransaction(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status_pembayaran' => 'nullable|in:pending,confirmed,rejected,unverified',
                'keterangan' => 'nullable|string',
                'paket_id' => 'nullable|exists:paket_iklan,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $transaction = DB::table('transaksi_paket')->where('id', $id)->first();
            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan',
                ], 404);
            }

            $updateData = [];
            if ($request->has('status_pembayaran')) {
                $updateData['status_pembayaran'] = $request->status_pembayaran;

                // If confirming transaction, update staff quotas
                if ($request->status_pembayaran === 'confirmed') {
                    // Get package details
                    $paket = DB::table('paket_iklan')->where('id', $transaction->paket_id)->first();
                    
                    if (!$paket) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Paket iklan tidak ditemukan',
                        ], 404);
                    }

                    // Get staff by id_staff from transaction
                    $staff = DB::table('staff')->where('id_staff', $transaction->id_staff)->first();

                    if ($staff) {
                        // Calculate new quotas
                        $newTotalKuota = $staff->total_kuota_iklan + $paket->kuota_iklan;
                        $newSisaKuota = $staff->sisa_kuota_iklan + $paket->kuota_iklan;

                        // Update staff status and quota
                        DB::table('staff')->where('id_staff', $staff->id_staff)->update([
                            'status_staff' => 'Ya',
                            'total_kuota_iklan' => $newTotalKuota,
                            'sisa_kuota_iklan' => $newSisaKuota,
                        ]);
                    }

                    $updateData['tanggal_konfirmasi'] = now();
                    $updateData['dikonfirmasi_oleh'] = 1; // Admin user ID, should be from auth
                } elseif ($request->status_pembayaran === 'rejected') {
                    $updateData['tanggal_konfirmasi'] = now();
                    $updateData['dikonfirmasi_oleh'] = 1; // Admin user ID, should be from auth
                }
            }

            if ($request->has('keterangan')) {
                $updateData['keterangan'] = $request->keterangan;
            }

            if ($request->has('paket_id')) {
                $updateData['paket_id'] = $request->paket_id;
            }

            $updateData['updated_at'] = now();

            DB::table('transaksi_paket')->where('id', $id)->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diupdate',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all articles for admin management
     * Menampilkan semua artikel untuk dikelola admin
     */
    public function getAllArticles(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);
            $status = $request->input('status', null); // Filter by status

            $query = DB::table('berita')
                ->join('users', 'berita.id_user', '=', 'users.id_user')
                ->select(
                    'berita.*',
                    'users.nama as author_name'
                );

            if ($status) {
                $query->where('berita.status_berita', $status);
            }

            $total = $query->count();
            $articles = $query
                ->orderBy('berita.tanggal_publish', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $articles,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil artikel: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create new article
     * Membuat artikel baru
     */
    public function createArticle(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'judul_berita' => 'required|string|max:255',
                'isi' => 'required|string',
                'id_kategori' => 'required|integer',
                'status_berita' => 'required|in:Draft,Publish',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $slug = \Illuminate\Support\Str::slug($request->judul_berita);
            
            // Handle image upload
            $gambarName = null;
            if ($request->hasFile('gambar')) {
                $image = $request->file('gambar');
                $gambarName = time() . '_' . $slug . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('assets/upload/image'), $gambarName);
            }

            $articleId = DB::table('berita')->insertGetId([
                'id_user' => 1, // Admin user ID, should be from auth
                'id_kategori' => $request->id_kategori,
                'bahasa' => 'ID',
                'slug_berita' => $slug,
                'judul_berita' => $request->judul_berita,
                'isi' => $request->isi,
                'status_berita' => $request->status_berita,
                'jenis_berita' => 'Berita',
                'keywords' => $request->keywords ?? '',
                'gambar' => $gambarName,
                'hits' => 0,
                'tanggal_post' => now(),
                'tanggal_publish' => now(),
                'tanggal' => now(),
                'link_berita' => '',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Artikel berhasil dibuat',
                'data' => ['id' => $articleId],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat artikel: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update article
     * Mengupdate artikel
     */
    public function updateArticle(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'judul_berita' => 'required|string|max:255',
                'isi' => 'required|string',
                'id_kategori' => 'required|integer',
                'status_berita' => 'required|in:Draft,Publish',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $article = DB::table('berita')->where('id_berita', $id)->first();
            if (!$article) {
                return response()->json([
                    'success' => false,
                    'message' => 'Artikel tidak ditemukan',
                ], 404);
            }

            $slug = \Illuminate\Support\Str::slug($request->judul_berita);
            
            // Handle image upload
            $gambarName = $article->gambar;
            if ($request->hasFile('gambar')) {
                // Delete old image
                if ($article->gambar && file_exists(public_path('assets/upload/image/' . $article->gambar))) {
                    unlink(public_path('assets/upload/image/' . $article->gambar));
                }
                
                $image = $request->file('gambar');
                $gambarName = time() . '_' . $slug . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('assets/upload/image'), $gambarName);
            }

            DB::table('berita')->where('id_berita', $id)->update([
                'id_kategori' => $request->id_kategori,
                'slug_berita' => $slug,
                'judul_berita' => $request->judul_berita,
                'isi' => $request->isi,
                'status_berita' => $request->status_berita,
                'keywords' => $request->keywords ?? $article->keywords,
                'gambar' => $gambarName,
                'tanggal' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Artikel berhasil diupdate',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update artikel: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete article
     * Menghapus artikel
     */
    public function deleteArticle($id)
    {
        try {
            $article = DB::table('berita')->where('id_berita', $id)->first();
            if (!$article) {
                return response()->json([
                    'success' => false,
                    'message' => 'Artikel tidak ditemukan',
                ], 404);
            }

            // Delete image file
            if ($article->gambar && file_exists(public_path('assets/upload/image/' . $article->gambar))) {
                unlink(public_path('assets/upload/image/' . $article->gambar));
            }

            DB::table('berita')->where('id_berita', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Artikel berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus artikel: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all advertisement packages
     * Menampilkan semua paket iklan
     */
    public function getAllPackages()
    {
        try {
            $packages = DB::table('paket_iklan')
                ->orderBy('harga', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $packages,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil paket iklan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create new package
     * Membuat paket iklan baru
     */
    public function createPackage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama_paket' => 'required|string|max:100|unique:paket_iklan',
                'harga' => 'required|numeric|min:0',
                'kuota_iklan' => 'required|integer|min:0',
                'deskripsi' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $packageId = DB::table('paket_iklan')->insertGetId([
                'nama_paket' => $request->nama_paket,
                'harga' => $request->harga,
                'kuota_iklan' => $request->kuota_iklan,
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->is_active ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Paket iklan berhasil dibuat',
                'data' => ['id' => $packageId],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat paket iklan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update package
     * Mengupdate paket iklan
     */
    public function updatePackage(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama_paket' => 'required|string|max:100|unique:paket_iklan,nama_paket,' . $id,
                'harga' => 'required|numeric|min:0',
                'kuota_iklan' => 'required|integer|min:0',
                'deskripsi' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $package = DB::table('paket_iklan')->where('id', $id)->first();
            if (!$package) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paket iklan tidak ditemukan',
                ], 404);
            }

            DB::table('paket_iklan')->where('id', $id)->update([
                'nama_paket' => $request->nama_paket,
                'harga' => $request->harga,
                'kuota_iklan' => $request->kuota_iklan,
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->is_active ?? $package->is_active,
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Paket iklan berhasil diupdate',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update paket iklan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete package
     * Menghapus paket iklan
     */
    public function deletePackage($id)
    {
        try {
            $package = DB::table('paket_iklan')->where('id', $id)->first();
            if (!$package) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paket iklan tidak ditemukan',
                ], 404);
            }

            DB::table('paket_iklan')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Paket iklan berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus paket iklan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get article categories
     * Mendapatkan kategori artikel
     */
    public function getArticleCategories()
    {
        try {
            $categories = DB::table('kategori')
                ->where('bahasa', 'ID')
                ->orderBy('urutan', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kategori: ' . $e->getMessage(),
            ], 500);
        }
    }
}
