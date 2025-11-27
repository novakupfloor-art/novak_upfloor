<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\WaisakaAiService;

class MobilePropertyController extends Controller
{
    /**
     * Helper function untuk mengecek apakah user sudah memiliki transaksi paket yang dikonfirmasi
     */
    private function checkConfirmedTransaction($userId)
    {
        return DB::table('transaksi_paket')
            ->where('id_user', $userId)
            ->where('status_pembayaran', 'confirmed')
            ->exists();
    }

    // ========================================
    // PUBLIC ROUTES (No Authentication Required)
    // ========================================

    /**
     * Menampilkan daftar properti untuk public (home screen)
     * Disesuaikan dengan data yang sama seperti Home Controller Web
     */
    public function index(Request $request)
    {
        try {
            $page = $request->query('page', 1);
            $perPage = 10;
            $offset = ($page - 1) * $perPage;

            // Gunakan query yang sama seperti Home Controller Web
            $properties = DB::table('property_db')
                ->join('kategori_property', 'kategori_property.id_kategori_property', '=', 'property_db.id_kategori_property', 'LEFT')
                ->join('staff', 'staff.id_staff', '=', 'property_db.id_staff', 'LEFT')
                ->join('provinsi', 'provinsi.id', '=', 'property_db.id_provinsi', 'LEFT')
                ->join('kabupaten', 'kabupaten.id', '=', 'property_db.id_kabupaten', 'LEFT')
                ->join('kecamatan', 'kecamatan.id', '=', 'property_db.id_kecamatan', 'LEFT')
                ->select(
                    'property_db.*',
                    'kategori_property.slug_kategori_property',
                    'kategori_property.nama_kategori_property',
                    'staff.nama_staff',
                    'provinsi.nama as nama_provinsi',
                    'kabupaten.nama as nama_kabupaten',
                    'kecamatan.nama as nama_kecamatan',
                    'property_db.tanggal as created_at'
                )
                ->selectRaw("(CASE WHEN property_db.status = 0 THEN CONCAT('belum ter',property_db.tipe) ELSE CONCAT('sudah ter',property_db.tipe) END) AS nama_status")
                ->selectRaw("property_db.tanggal as updated_at")
                ->where('property_db.status', 1)
                ->orderBy('property_db.tanggal', 'DESC')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            // Group images for each property
            $propertyIds = $properties->pluck('id_property')->toArray();
            $images = DB::table('property_img')
                ->whereIn('id_property', $propertyIds)
                ->orderBy('id_property')
                ->orderBy('index_img')
                ->get()
                ->groupBy('id_property');

            // Transform data inline
            $properties = $properties->map(function ($property) use ($images) {
                // Process images
                $propertyImages = [];
                if ($images) {
                    $propertyImages = $images->get($property->id_property, collect())->map(function ($img) {
                        return [
                            'property_images' => asset('assets/upload/property/' . $img->gambar),
                            'index_img' => (int) $img->index_img
                        ];
                    })->toArray();
                }

                // Parse peta_map dari JSON string ke object atau extract dari iframe
                $petaMapData = null;
                if (!empty($property->peta_map ?? '')) {
                    // Coba decode sebagai JSON dulu (format mobile)
                    $decoded = json_decode($property->peta_map, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $petaMapData = [
                            'latitude' => $decoded['latitude'] ?? null,
                            'longitude' => $decoded['longitude'] ?? null,
                            'maps_query' => $decoded['maps_query'] ?? ''
                        ];

                    } else {
                        // Jika bukan JSON, mungkin format iframe (format web)
                        // Format: <iframe src="https://www.google.com/maps?q=-7.752930,110.384970&z=16..." ...
                        if (preg_match('/q=([-0-9.]+),([-0-9.]+)/', $property->peta_map, $matches)) {
                            $petaMapData = [
                                'latitude' => $matches[1],
                                'longitude' => $matches[2],
                                'maps_query' => ''
                            ];
                        } elseif (preg_match('/q=([^&"]+)/', $property->peta_map, $matches)) {
                            // Fallback jika q adalah query string (bukan koordinat)
                            $petaMapData = [
                                'latitude' => null,
                                'longitude' => null,
                                'maps_query' => urldecode($matches[1])
                            ];
                        }
                    }
                }

                $transformedProperty = [
                    'id_property' => (int) $property->id_property,
                    'kode' => $property->kode ?? '',
                    'nama_property' => $property->nama_property ?? '',
                    'tipe' => $property->tipe ?? '',
                    'harga' => (int) ($property->harga ?? 0),
                    'lt' => $property->lt ? (int) $property->lt : null,
                    'lb' => $property->lb ? (int) $property->lb : null,
                    'kamar_tidur' => (int) ($property->kamar_tidur ?? 0),
                    'kamar_mandi' => (int) ($property->kamar_mandi ?? 0),
                    'alamat' => $property->alamat ?? '',
                    'view_count' => (int) ($property->view_count ?? 0),
                    'created_at' => $property->created_at ?? $property->tanggal ?? null,
                    'updated_at' => $property->updated_at ?? $property->tanggal ?? null,
                    'id_kategori_property' => isset($property->id_kategori_property) ? (int) $property->id_kategori_property : null,
                    'id_provinsi' => isset($property->id_provinsi) ? (int) $property->id_provinsi : null,
                    'id_kabupaten' => isset($property->id_kabupaten) ? (int) $property->id_kabupaten : null,
                    'id_kecamatan' => isset($property->id_kecamatan) ? (int) $property->id_kecamatan : null,
                    'nama_kategori_property' => $property->nama_kategori_property ?? '',
                    'nama_provinsi' => $property->nama_provinsi ?? '',
                    'nama_kabupaten' => $property->nama_kabupaten ?? '',
                    'nama_kecamatan' => $property->nama_kecamatan ?? '',
                    'images' => $propertyImages,
                    // Field tambahan dari database property_db
                    'slug_property' => $property->slug_property ?? '',
                    'surat' => $property->surat ?? 'SHM',
                    'lantai' => $property->lantai ? (int) $property->lantai : 1,
                    'jenis_sewa' => $property->jenis_sewa ?? '',
                    'status' => (int) ($property->status ?? 0),
                    'isi' => $property->isi ?? '',
                    'keywords' => $property->keywords ?? '',
                    'nama_staff' => $property->nama_staff ?? null,
                    'telepon_staff' => $property->telepon_staff ?? null,
                    // AI Insights dari Waisaka AI
                    'harga_rata' => $property->harga_rata ?? null,
                    'fasilitas_terdekat' => $property->fasilitas_terdekat ?? null,
                    'peta_map' => $petaMapData
                ];

                if (!empty($transformedProperty['images'])) {
                    $transformedProperty['main_image_url'] = $transformedProperty['images'][0]['property_images'];
                } else {
                    $transformedProperty['main_image_url'] = null;
                }
                return $transformedProperty;
            });

            $total = DB::table('property_db')
                ->where('status', 1)
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Daftar properti berhasil diambil',
                'data' => $properties,
                'current_page' => (int) $page,
                'last_page' => (int) ceil($total / $perPage),
                'total' => (int) $total,
                'per_page' => (int) $perPage,
                'next_page_url' => $page < ceil($total / $perPage) ? $page + 1 : null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar properti',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail properti untuk public
     */
    public function show(Request $request, $id)
    {
        try {
            if (!$id || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID properti tidak valid.'
                ], 400);
            }

            $queryBuilder = function () use ($id) {
                return DB::table('property_db')
                    ->join('kategori_property', 'kategori_property.id_kategori_property', '=', 'property_db.id_kategori_property', 'LEFT')
                    ->join('provinsi', 'provinsi.id', '=', 'property_db.id_provinsi', 'LEFT')
                    ->join('kabupaten', 'kabupaten.id', '=', 'property_db.id_kabupaten', 'LEFT')
                    ->join('kecamatan', 'kecamatan.id', '=', 'property_db.id_kecamatan', 'LEFT')
                    ->join('staff', 'staff.id_staff', '=', 'property_db.id_staff', 'LEFT')
                    ->select(
                        'property_db.*',
                        'kategori_property.nama_kategori_property',
                        'provinsi.nama as nama_provinsi',
                        'kabupaten.nama as nama_kabupaten',
                        'kecamatan.nama as nama_kecamatan',
                        'staff.nama_staff',
                        'staff.telepon as telepon_staff',
                        'staff.gambar as gambar_staff',
                        'property_db.tanggal as created_at',
                        'property_db.tanggal as updated_at'
                    )
                    ->where('property_db.id_property', $id)
                    ->where('property_db.status', 1);
            };

            $property = $queryBuilder()->first();

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Properti tidak ditemukan atau belum aktif'
                ], 404);
            }

            $needsAiInsights = empty(trim($property->harga_rata ?? '')) ||
                empty(trim($property->fasilitas_terdekat ?? '')) ||
                empty(trim($property->peta_map ?? ''));

            if ($needsAiInsights) {
                try {
                    $this->generateAiInsights($property->id_property);
                    $property = $queryBuilder()->first();
                } catch (\Exception $e) {
                    Log::warning("AI insights generation failed for public property {$property->id_property}: " . $e->getMessage());
                }
            }

            try {
                DB::table('property_db')
                    ->where('id_property', $id)
                    ->increment('view_count');
            } catch (\Exception $e) {
                \Log::warning('Failed to increment view count for property ' . $id . ': ' . $e->getMessage());
            }

            $images = collect();
            try {
                $images = DB::table('property_img')
                    ->where('id_property', $property->id_property)
                    ->orderBy('index_img')
                    ->get()
                    ->groupBy('id_property');
            } catch (\Exception $e) {
                \Log::warning('Failed to fetch images for property ' . $id . ': ' . $e->getMessage());
            }

            // Transform data inline
            // Process images
            $propertyImages = [];
            if ($images) {
                $propertyImages = $images->get($property->id_property, collect())->map(function ($img) {
                    return [
                        'property_images' => asset('assets/upload/property/' . $img->gambar),
                        'index_img' => (int) $img->index_img
                    ];
                })->toArray();
            }

            // Parse peta_map dari JSON string ke object atau extract dari iframe
            $petaMapData = null;
            if (!empty($property->peta_map ?? '')) {
                // Coba decode sebagai JSON dulu (format mobile)
                $decoded = json_decode($property->peta_map, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $petaMapData = [
                        'latitude' => $decoded['latitude'] ?? null,
                        'longitude' => $decoded['longitude'] ?? null,
                        'maps_query' => $decoded['maps_query'] ?? ''
                    ];
                } else {
                    // Jika bukan JSON, mungkin format iframe (format web)
                    // Format: <iframe src="https://www.google.com/maps?q=-7.752930,110.384970&z=16..." ...
                    if (preg_match('/q=([-0-9.]+),([-0-9.]+)/', $property->peta_map, $matches)) {
                        $petaMapData = [
                            'latitude' => $matches[1],
                            'longitude' => $matches[2],
                            'maps_query' => ''
                        ];
                    } elseif (preg_match('/q=([^&"]+)/', $property->peta_map, $matches)) {
                        // Fallback jika q adalah query string (bukan koordinat)
                        $petaMapData = [
                            'latitude' => null,
                            'longitude' => null,
                            'maps_query' => urldecode($matches[1])
                        ];
                    }
                }
            }

            $propertyData = [
                'id_property' => (int) $property->id_property,
                'kode' => $property->kode ?? '',
                'nama_property' => $property->nama_property ?? '',
                'tipe' => $property->tipe ?? '',
                'harga' => (int) ($property->harga ?? 0),
                'lt' => $property->lt ? (int) $property->lt : null,
                'lb' => $property->lb ? (int) $property->lb : null,
                'kamar_tidur' => (int) ($property->kamar_tidur ?? 0),
                'kamar_mandi' => (int) ($property->kamar_mandi ?? 0),
                'alamat' => $property->alamat ?? '',
                'view_count' => (int) ($property->view_count ?? 0),
                'created_at' => $property->created_at ?? $property->tanggal ?? null,
                'updated_at' => $property->updated_at ?? $property->tanggal ?? null,
                'id_kategori_property' => isset($property->id_kategori_property) ? (int) $property->id_kategori_property : null,
                'id_provinsi' => isset($property->id_provinsi) ? (int) $property->id_provinsi : null,
                'id_kabupaten' => isset($property->id_kabupaten) ? (int) $property->id_kabupaten : null,
                'id_kecamatan' => isset($property->id_kecamatan) ? (int) $property->id_kecamatan : null,
                'nama_kategori_property' => $property->nama_kategori_property ?? '',
                'nama_provinsi' => $property->nama_provinsi ?? '',
                'nama_kabupaten' => $property->nama_kabupaten ?? '',
                'nama_kecamatan' => $property->nama_kecamatan ?? '',
                'images' => $propertyImages,
                // Field tambahan dari database property_db
                'slug_property' => $property->slug_property ?? '',
                'surat' => $property->surat ?? 'SHM',
                'lantai' => $property->lantai ? (int) $property->lantai : 1,
                'jenis_sewa' => $property->jenis_sewa ?? '',
                'status' => (int) ($property->status ?? 0),
                'isi' => $property->isi ?? '',
                'keywords' => $property->keywords ?? '',
                'nama_staff' => $property->nama_staff ?? null,
                'telepon_staff' => $property->telepon_staff ?? null,
                // AI Insights dari Waisaka AI
                'harga_rata' => $property->harga_rata ?? null,
                'fasilitas_terdekat' => $property->fasilitas_terdekat ?? null,
                'fasilitas_dekorasi' => $property->fasilitas_dekorasi ?? null,
                'peta_map' => $petaMapData
            ];

            $propertyData['isi'] = $property->isi ?? '';
            $propertyData['keywords'] = $property->keywords ?? '';
            $propertyData['nama_staff'] = $property->nama_staff ?? '';
            $propertyData['telepon_staff'] = $property->telepon_staff ?? '';
            $propertyData['gambar_staff'] = $property->gambar_staff;
            $propertyData['surat'] = $property->surat ?? '';
            $propertyData['jenis_sewa'] = $property->jenis_sewa ?? '';
            $propertyData['lantai'] = (int) ($property->lantai ?? 1);
            $propertyData['view_count'] = (int) ($property->view_count + 1);

            return response()->json([
                'success' => true,
                'message' => 'Detail properti berhasil diambil',
                'data' => $propertyData
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error in show method: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail properti',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mencari properti untuk public
     */
    public function search(Request $request)
    {
        try {
            $page = $request->query('page', 1);
            $perPage = 10;
            $offset = ($page - 1) * $perPage;

            $query = DB::table('property_db')
                ->where('status', 1);

            if ($request->has('tipe') && $request->tipe) {
                $query->where('tipe', $request->tipe);
            }
            if ($request->has('id_kategori_property') && $request->id_kategori_property) {
                $query->where('id_kategori_property', $request->id_kategori_property);
            }
            if ($request->has('id_provinsi') && $request->id_provinsi) {
                $query->where('id_provinsi', $request->id_provinsi);
            }
            if ($request->has('id_kabupaten') && $request->id_kabupaten) {
                $query->where('id_kabupaten', $request->id_kabupaten);
            }
            if ($request->has('id_kecamatan') && $request->id_kecamatan) {
                $query->where('id_kecamatan', $request->id_kecamatan);
            }
            if ($request->has('min_price') && $request->min_price) {
                $query->where('harga', '>=', $request->min_price);
            }
            // Support both min_price and min_harga
            if ($request->has('min_harga') && $request->min_harga) {
                $query->where('harga', '>=', $request->min_harga);
            }
            if ($request->has('max_price') && $request->max_price) {
                $query->where('harga', '<=', $request->max_price);
            }
            // Support both max_price and max_harga
            if ($request->has('max_harga') && $request->max_harga) {
                $query->where('harga', '<=', $request->max_harga);
            }
            // Filter luas bangunan (lb)
            if ($request->has('min_lb') && $request->min_lb) {
                $query->where('lb', '>=', $request->min_lb);
            }
            if ($request->has('max_lb') && $request->max_lb) {
                $query->where('lb', '<=', $request->max_lb);
            }
            // Filter luas tanah (lt)
            if ($request->has('min_lt') && $request->min_lt) {
                $query->where('lt', '>=', $request->min_lt);
            }
            if ($request->has('max_lt') && $request->max_lt) {
                $query->where('lt', '<=', $request->max_lt);
            }
            if ($request->has('kamar_tidur') && $request->kamar_tidur) {
                $query->where('kamar_tidur', '>=', $request->kamar_tidur);
            }
            if ($request->has('kamar_mandi') && $request->kamar_mandi) {
                $query->where('kamar_mandi', '>=', $request->kamar_mandi);
            }
            if ($request->has('keywords') && $request->keywords) {
                $keywords = $request->keywords;
                $query->where(function ($q) use ($keywords) {
                    $q->where('nama_property', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('kode', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('isi', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('alamat', 'LIKE', '%' . $keywords . '%');
                });
            }

            $total = $query->count();

            $properties = $query->orderBy('id_property', 'DESC')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            $propertyIds = $properties->pluck('id_property')->toArray();
            $images = DB::table('property_img')
                ->whereIn('id_property', $propertyIds)
                ->orderBy('id_property')
                ->orderBy('index_img')
                ->get()
                ->groupBy('id_property');

            $properties = $properties->map(function ($property) use ($images) {
                // Transform data inline
                // Process images
                $propertyImages = [];
                if ($images) {
                    $propertyImages = $images->get($property->id_property, collect())->map(function ($img) {
                        return [
                            'property_images' => asset('assets/upload/property/' . $img->gambar),
                            'index_img' => (int) $img->index_img
                        ];
                    })->toArray();
                }

                // Parse peta_map dari JSON string ke object atau extract dari iframe
                $petaMapData = null;
                if (!empty($property->peta_map ?? '')) {
                    // Coba decode sebagai JSON dulu (format mobile)
                    $decoded = json_decode($property->peta_map, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $petaMapData = [
                            'latitude' => $decoded['latitude'] ?? null,
                            'longitude' => $decoded['longitude'] ?? null,
                            'maps_query' => $decoded['maps_query'] ?? ''
                        ];
                    } else {
                        // Jika bukan JSON, mungkin format iframe (format web)
                        // Format: <iframe src="https://www.google.com/maps?q=-7.752930,110.384970&z=16..." ...
                        if (preg_match('/q=([-0-9.]+),([-0-9.]+)/', $property->peta_map, $matches)) {
                            $petaMapData = [
                                'latitude' => $matches[1],
                                'longitude' => $matches[2],
                                'maps_query' => ''
                            ];
                        } elseif (preg_match('/q=([^&"]+)/', $property->peta_map, $matches)) {
                            // Fallback jika q adalah query string (bukan koordinat)
                            $petaMapData = [
                                'latitude' => null,
                                'longitude' => null,
                                'maps_query' => urldecode($matches[1])
                            ];
                        }
                    }
                }

                return [
                    'id_property' => (int) $property->id_property,
                    'kode' => $property->kode ?? '',
                    'nama_property' => $property->nama_property ?? '',
                    'tipe' => $property->tipe ?? '',
                    'harga' => (int) ($property->harga ?? 0),
                    'lt' => $property->lt ? (int) $property->lt : null,
                    'lb' => $property->lb ? (int) $property->lb : null,
                    'kamar_tidur' => (int) ($property->kamar_tidur ?? 0),
                    'kamar_mandi' => (int) ($property->kamar_mandi ?? 0),
                    'alamat' => $property->alamat ?? '',
                    'view_count' => (int) ($property->view_count ?? 0),
                    'created_at' => $property->created_at ?? $property->tanggal ?? null,
                    'updated_at' => $property->updated_at ?? $property->tanggal ?? null,
                    'id_kategori_property' => isset($property->id_kategori_property) ? (int) $property->id_kategori_property : null,
                    'id_provinsi' => isset($property->id_provinsi) ? (int) $property->id_provinsi : null,
                    'id_kabupaten' => isset($property->id_kabupaten) ? (int) $property->id_kabupaten : null,
                    'id_kecamatan' => isset($property->id_kecamatan) ? (int) $property->id_kecamatan : null,
                    'nama_kategori_property' => $property->nama_kategori_property ?? '',
                    'nama_provinsi' => $property->nama_provinsi ?? '',
                    'nama_kabupaten' => $property->nama_kabupaten ?? '',
                    'nama_kecamatan' => $property->nama_kecamatan ?? '',
                    'images' => $propertyImages,
                    // Field tambahan dari database property_db
                    'slug_property' => $property->slug_property ?? '',
                    'surat' => $property->surat ?? 'SHM',
                    'lantai' => $property->lantai ? (int) $property->lantai : 1,
                    'jenis_sewa' => $property->jenis_sewa ?? '',
                    'status' => (int) ($property->status ?? 0),
                    'isi' => $property->isi ?? '',
                    'keywords' => $property->keywords ?? '',
                    'nama_staff' => $property->nama_staff ?? null,
                    'telepon_staff' => $property->telepon_staff ?? null,
                    // AI Insights dari Waisaka AI
                    'harga_rata' => $property->harga_rata ?? null,
                    'fasilitas_terdekat' => $property->fasilitas_terdekat ?? null,
                    'peta_map' => $petaMapData
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Pencarian properti berhasil',
                'data' => $properties,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
                'total' => $total,
                'per_page' => $perPage,
                'next_page_url' => $page < ceil($total / $perPage) ? $page + 1 : null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari properti',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan kategori properti untuk public
     */
    public function getCategories()
    {
        try {
            $categories = DB::table('kategori_property')
                ->orderBy('urutan', 'ASC')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar kategori berhasil diambil',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan seluruh data iklan properti untuk halaman All Properties di Flutter
     * Endpoint ini digunakan untuk menampilkan semua properti yang aktif
     */
    public function getPropertiesAll(Request $request)
    {
        try {
            $page = $request->query('page', 1);
            $perPage = $request->query('per_page', 10);
            $offset = ($page - 1) * $perPage;

            // Query untuk mengambil semua properti yang aktif (status = 1)
            $properties = DB::table('property_db')
                ->join('kategori_property', 'kategori_property.id_kategori_property', '=', 'property_db.id_kategori_property', 'LEFT')
                ->join('staff', 'staff.id_staff', '=', 'property_db.id_staff', 'LEFT')
                ->join('provinsi', 'provinsi.id', '=', 'property_db.id_provinsi', 'LEFT')
                ->join('kabupaten', 'kabupaten.id', '=', 'property_db.id_kabupaten', 'LEFT')
                ->join('kecamatan', 'kecamatan.id', '=', 'property_db.id_kecamatan', 'LEFT')
                ->select(
                    'property_db.*',
                    'kategori_property.slug_kategori_property',
                    'kategori_property.nama_kategori_property',
                    'staff.nama_staff',
                    'staff.telepon as telepon_staff',
                    'provinsi.nama as nama_provinsi',
                    'kabupaten.nama as nama_kabupaten',
                    'kecamatan.nama as nama_kecamatan',
                    'property_db.tanggal as created_at'
                )
                ->selectRaw("(CASE WHEN property_db.status = 0 THEN CONCAT('belum ter',property_db.tipe) ELSE CONCAT('sudah ter',property_db.tipe) END) AS nama_status")
                ->selectRaw("property_db.tanggal as updated_at")
                ->where('property_db.status', 1)
                ->orderBy('property_db.tanggal', 'DESC')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            // Group images for each property
            $propertyIds = $properties->pluck('id_property')->toArray();
            $images = DB::table('property_img')
                ->whereIn('id_property', $propertyIds)
                ->orderBy('id_property')
                ->orderBy('index_img')
                ->get()
                ->groupBy('id_property');

            // Transform data inline
            $properties = $properties->map(function ($property) use ($images) {
                // Process images
                $propertyImages = [];
                if ($images) {
                    $propertyImages = $images->get($property->id_property, collect())->map(function ($img) {
                        return [
                            'property_images' => asset('assets/upload/property/' . $img->gambar),
                            'index_img' => (int) $img->index_img
                        ];
                    })->toArray();
                }

                // Parse peta_map dari JSON string ke object atau extract dari iframe
                $petaMapData = null;
                if (!empty($property->peta_map ?? '')) {
                    // Coba decode sebagai JSON dulu (format mobile)
                    $decoded = json_decode($property->peta_map, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $petaMapData = [
                            'latitude' => $decoded['latitude'] ?? null,
                            'longitude' => $decoded['longitude'] ?? null,
                            'maps_query' => $decoded['maps_query'] ?? ''
                        ];
                    } else {
                        // Jika bukan JSON, mungkin format iframe (format web)
                        // Format: <iframe src="https://www.google.com/maps?q=-7.752930,110.384970&z=16..." ...
                        if (preg_match('/q=([-0-9.]+),([-0-9.]+)/', $property->peta_map, $matches)) {
                            $petaMapData = [
                                'latitude' => $matches[1],
                                'longitude' => $matches[2],
                                'maps_query' => ''
                            ];
                        } elseif (preg_match('/q=([^&"]+)/', $property->peta_map, $matches)) {
                            // Fallback jika q adalah query string (bukan koordinat)
                            $petaMapData = [
                                'latitude' => null,
                                'longitude' => null,
                                'maps_query' => urldecode($matches[1])
                            ];
                        }
                    }
                }

                $transformedProperty = [
                    'id_property' => (int) $property->id_property,
                    'kode' => $property->kode ?? '',
                    'nama_property' => $property->nama_property ?? '',
                    'tipe' => $property->tipe ?? '',
                    'harga' => (int) ($property->harga ?? 0),
                    'lt' => $property->lt ? (int) $property->lt : null,
                    'lb' => $property->lb ? (int) $property->lb : null,
                    'kamar_tidur' => (int) ($property->kamar_tidur ?? 0),
                    'kamar_mandi' => (int) ($property->kamar_mandi ?? 0),
                    'alamat' => $property->alamat ?? '',
                    'view_count' => (int) ($property->view_count ?? 0),
                    'created_at' => $property->created_at ?? $property->tanggal ?? null,
                    'updated_at' => $property->updated_at ?? $property->tanggal ?? null,
                    'id_kategori_property' => isset($property->id_kategori_property) ? (int) $property->id_kategori_property : null,
                    'id_provinsi' => isset($property->id_provinsi) ? (int) $property->id_provinsi : null,
                    'id_kabupaten' => isset($property->id_kabupaten) ? (int) $property->id_kabupaten : null,
                    'id_kecamatan' => isset($property->id_kecamatan) ? (int) $property->id_kecamatan : null,
                    'nama_kategori_property' => $property->nama_kategori_property ?? '',
                    'nama_provinsi' => $property->nama_provinsi ?? '',
                    'nama_kabupaten' => $property->nama_kabupaten ?? '',
                    'nama_kecamatan' => $property->nama_kecamatan ?? '',
                    'images' => $propertyImages,
                    // Field tambahan dari database property_db
                    'slug_property' => $property->slug_property ?? '',
                    'surat' => $property->surat ?? 'SHM',
                    'lantai' => $property->lantai ? (int) $property->lantai : 1,
                    'jenis_sewa' => $property->jenis_sewa ?? '',
                    'status' => (int) ($property->status ?? 0),
                    'isi' => $property->isi ?? '',
                    'keywords' => $property->keywords ?? '',
                    'nama_staff' => $property->nama_staff ?? null,
                    'telepon_staff' => $property->telepon_staff ?? null,
                    // AI Insights dari Waisaka AI
                    'harga_rata' => $property->harga_rata ?? null,
                    'fasilitas_terdekat' => $property->fasilitas_terdekat ?? null,
                    'peta_map' => $petaMapData
                ];

                // Tambahkan main_image_url untuk preview
                if (!empty($transformedProperty['images'])) {
                    $transformedProperty['main_image_url'] = $transformedProperty['images'][0]['property_images'];
                } else {
                    $transformedProperty['main_image_url'] = null;
                }
                return $transformedProperty;
            });

            // Hitung total properti aktif
            $total = DB::table('property_db')
                ->where('status', 1)
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Daftar seluruh properti berhasil diambil',
                'data' => $properties,
                'current_page' => (int) $page,
                'last_page' => (int) ceil($total / $perPage),
                'total' => (int) $total,
                'per_page' => (int) $perPage,
                'next_page_url' => $page < ceil($total / $perPage) ? $page + 1 : null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar properti',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper function untuk mendapatkan query properti dengan join yang optimal
     */
    private function getPropertyQuery($staffId = null)
    {
        $query = DB::table('property_db')
            ->join('kategori_property', 'kategori_property.id_kategori_property', '=', 'property_db.id_kategori_property', 'LEFT')
            ->join('provinsi', 'provinsi.id', '=', 'property_db.id_provinsi', 'LEFT')
            ->join('kabupaten', 'kabupaten.id', '=', 'property_db.id_kabupaten', 'LEFT')
            ->join('kecamatan', 'kecamatan.id', '=', 'property_db.id_kecamatan', 'LEFT')
            ->select(
                'property_db.*',
                'property_db.id_kategori_property',
                'property_db.id_provinsi',
                'property_db.id_kabupaten',
                'property_db.id_kecamatan',
                'kategori_property.nama_kategori_property',
                'provinsi.nama as nama_provinsi',
                'kabupaten.nama as nama_kabupaten',
                'kecamatan.nama as nama_kecamatan'
            );

        if ($staffId) {
            $query->where('property_db.id_staff', $staffId);
        }

        return $query;
    }

    /**
     * Menampilkan daftar properti milik user dengan pagination
     */
    public function getPropertiesPaginated(Request $request, $id)
    {
        try {
            // Ambil data staff user
            $staff = DB::table('staff')->where('id_user', $id)->first();
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil staff tidak ditemukan'
                ], 404);
            }

            // Ambil parameter pagination
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);
            $tipe = $request->input('tipe');

            // Query properti dengan pagination menggunakan helper function
            $query = $this->getPropertyQuery($staff->id_staff);

            // Filter berdasarkan tipe jika ada
            if ($tipe) {
                $query->where('property_db.tipe', $tipe);
            }

            // Hitung total data
            $total = $query->count();

            // Ambil data dengan pagination
            $properties = $query->orderBy('property_db.id_property', 'DESC')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            // Group images for each property
            $propertyIds = $properties->pluck('id_property')->toArray();
            $images = DB::table('property_img')
                ->whereIn('id_property', $propertyIds)
                ->orderBy('id_property')
                ->orderBy('index_img')
                ->get()
                ->groupBy('id_property');

            // Transform data inline
            $properties = $properties->map(function ($property) use ($images) {
                // Process images
                $propertyImages = [];
                if ($images) {
                    $propertyImages = $images->get($property->id_property, collect())->map(function ($img) {
                        return [
                            'property_images' => asset('assets/upload/property/' . $img->gambar),
                            'index_img' => (int) $img->index_img
                        ];
                    })->toArray();
                }

                // Parse peta_map dari JSON string ke object atau extract dari iframe
                $petaMapData = null;
                if (!empty($property->peta_map ?? '')) {
                    // Coba decode sebagai JSON dulu (format mobile)
                    $decoded = json_decode($property->peta_map, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $petaMapData = [
                            'latitude' => $decoded['latitude'] ?? null,
                            'longitude' => $decoded['longitude'] ?? null,
                            'maps_query' => $decoded['maps_query'] ?? ''
                        ];
                    } else {
                        // Jika bukan JSON, mungkin format iframe (format web)
                        // Format: <iframe src="https://www.google.com/maps?q=-7.752930,110.384970&z=16..." ...
                        if (preg_match('/q=([-0-9.]+),([-0-9.]+)/', $property->peta_map, $matches)) {
                            $petaMapData = [
                                'latitude' => $matches[1],
                                'longitude' => $matches[2],
                                'maps_query' => ''
                            ];
                        } elseif (preg_match('/q=([^&"]+)/', $property->peta_map, $matches)) {
                            // Fallback jika q adalah query string (bukan koordinat)
                            $petaMapData = [
                                'latitude' => null,
                                'longitude' => null,
                                'maps_query' => urldecode($matches[1])
                            ];
                        }
                    }
                }

                return [
                    'id_property' => (int) $property->id_property,
                    'kode' => $property->kode ?? '',
                    'nama_property' => $property->nama_property ?? '',
                    'tipe' => $property->tipe ?? '',
                    'harga' => (int) ($property->harga ?? 0),
                    'lt' => $property->lt ? (int) $property->lt : null,
                    'lb' => $property->lb ? (int) $property->lb : null,
                    'kamar_tidur' => (int) ($property->kamar_tidur ?? 0),
                    'kamar_mandi' => (int) ($property->kamar_mandi ?? 0),
                    'alamat' => $property->alamat ?? '',
                    'view_count' => (int) ($property->view_count ?? 0),
                    'created_at' => $property->created_at ?? $property->tanggal ?? null,
                    'updated_at' => $property->updated_at ?? $property->tanggal ?? null,
                    'id_kategori_property' => isset($property->id_kategori_property) ? (int) $property->id_kategori_property : null,
                    'id_provinsi' => isset($property->id_provinsi) ? (int) $property->id_provinsi : null,
                    'id_kabupaten' => isset($property->id_kabupaten) ? (int) $property->id_kabupaten : null,
                    'id_kecamatan' => isset($property->id_kecamatan) ? (int) $property->id_kecamatan : null,
                    'nama_kategori_property' => $property->nama_kategori_property ?? '',
                    'nama_provinsi' => $property->nama_provinsi ?? '',
                    'nama_kabupaten' => $property->nama_kabupaten ?? '',
                    'nama_kecamatan' => $property->nama_kecamatan ?? '',
                    'images' => $propertyImages,
                    // Field tambahan dari database property_db
                    'slug_property' => $property->slug_property ?? '',
                    'surat' => $property->surat ?? 'SHM',
                    'lantai' => $property->lantai ? (int) $property->lantai : 1,
                    'jenis_sewa' => $property->jenis_sewa ?? '',
                    'status' => (int) ($property->status ?? 0),
                    'isi' => $property->isi ?? '',
                    'keywords' => $property->keywords ?? '',
                    'nama_staff' => $property->nama_staff ?? null,
                    'telepon_staff' => $property->telepon_staff ?? null,
                    // AI Insights dari Waisaka AI
                    'harga_rata' => $property->harga_rata ?? null,
                    'fasilitas_terdekat' => $property->fasilitas_terdekat ?? null,
                    'peta_map' => $petaMapData
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Daftar properti berhasil diambil',
                'data' => $properties,
                'pagination' => [
                    'current_page' => (int) $page,
                    'per_page' => (int) $perPage,
                    'total' => (int) $total,
                    'last_page' => (int) ceil($total / $perPage),
                    'from' => (int) (($page - 1) * $perPage + 1),
                    'to' => (int) min($page * $perPage, $total)
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar properti',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alias untuk kompatibilitas route lama:
     * /mobile/control-panel/properties/properties/{id}
     */
    public function getProperties(Request $request, $id)
    {
        return $this->getPropertiesPaginated($request, $id);
    }

    /**
     * Menampilkan detail properti
     */
    public function getPropertyDetail(Request $request, int $id)
    {
        try {
            // Validate property ID
            if (!$id || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID properti tidak valid.'
                ], 400);
            }

            // Ambil id_user dari request body atau query parameter (optional untuk marketing view)
            $userId = $request->input('id_user')
                ?? $request->input('id_user')
                ?? $request->query('id_user')
                ?? $request->query('id_user');

            // Ambil data staff user jika tersedia
            $staff = null;
            if ($userId) {
                $staff = DB::table('staff')->where('id_user', $userId)->first();
            }

            // Ambil detail properti menggunakan helper function
            $query = $this->getPropertyQuery($staff->id_staff ?? null)
                ->where('property_db.id_property', $id);

            $property = $query->first();

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Properti tidak ditemukan'
                ], 404);
            }

            // AUTO-GENERATE AI INSIGHTS jika belum ada (background process)
            // Cek apakah ada field AI yang kosong
            $needsAiInsights = empty(trim($property->harga_rata ?? '')) ||
                empty(trim($property->fasilitas_terdekat ?? '')) ||
                empty(trim($property->peta_map ?? ''));

            if ($needsAiInsights) {
                // Generate AI insights di background (tidak menunggu hasil)
                // Agar response tetap cepat untuk user
                try {
                    $this->generateAiInsights($property->id_property);

                    // Refresh data property setelah AI insights di-generate
                    $property = $query->first();
                } catch (\Exception $e) {
                    // Log error tapi jangan ganggu response
                    Log::warning("AI insights generation failed for property {$property->id_property}: " . $e->getMessage());
                }
            }

            // Ambil gambar properti
            $images = DB::table('property_img')
                ->where('id_property', $property->id_property)
                ->orderBy('index_img')
                ->get()
                ->groupBy('id_property');

            // Transform data inline
            // Process images
            $propertyImages = [];
            if ($images) {
                $propertyImages = $images->get($property->id_property, collect())->map(function ($img) {
                    return [
                        'property_images' => asset('assets/upload/property/' . $img->gambar),
                        'index_img' => (int) $img->index_img
                    ];
                })->toArray();
            }

            // Parse peta_map dari JSON string ke object atau extract dari iframe
            $petaMapData = null;
            if (!empty($property->peta_map ?? '')) {
                // Coba decode sebagai JSON dulu (format mobile)
                $decoded = json_decode($property->peta_map, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $petaMapData = [
                        'latitude' => $decoded['latitude'] ?? null,
                        'longitude' => $decoded['longitude'] ?? null,
                        'maps_query' => $decoded['maps_query'] ?? ''
                    ];
                } else {
                    // Jika bukan JSON, mungkin format iframe (format web)
                    // Format: <iframe src="https://www.google.com/maps?q=-7.752930,110.384970&z=16..." ...
                    if (preg_match('/q=([-0-9.]+),([-0-9.]+)/', $property->peta_map, $matches)) {
                        $petaMapData = [
                            'latitude' => $matches[1],
                            'longitude' => $matches[2],
                            'maps_query' => ''
                        ];
                    } elseif (preg_match('/q=([^&"]+)/', $property->peta_map, $matches)) {
                        // Fallback jika q adalah query string (bukan koordinat)
                        $petaMapData = [
                            'latitude' => null,
                            'longitude' => null,
                            'maps_query' => urldecode($matches[1])
                        ];
                    }
                }
            }

            $propertyData = [
                'id_property' => (int) $property->id_property,
                'kode' => $property->kode ?? '',
                'nama_property' => $property->nama_property ?? '',
                'tipe' => $property->tipe ?? '',
                'harga' => (int) ($property->harga ?? 0),
                'lt' => $property->lt ? (int) $property->lt : null,
                'lb' => $property->lb ? (int) $property->lb : null,
                'kamar_tidur' => (int) ($property->kamar_tidur ?? 0),
                'kamar_mandi' => (int) ($property->kamar_mandi ?? 0),
                'alamat' => $property->alamat ?? '',
                'view_count' => (int) ($property->view_count ?? 0),
                'created_at' => $property->created_at ?? $property->tanggal ?? null,
                'updated_at' => $property->updated_at ?? $property->tanggal ?? null,
                'id_kategori_property' => isset($property->id_kategori_property) ? (int) $property->id_kategori_property : null,
                'id_provinsi' => isset($property->id_provinsi) ? (int) $property->id_provinsi : null,
                'id_kabupaten' => isset($property->id_kabupaten) ? (int) $property->id_kabupaten : null,
                'id_kecamatan' => isset($property->id_kecamatan) ? (int) $property->id_kecamatan : null,
                'nama_kategori_property' => $property->nama_kategori_property ?? '',
                'nama_provinsi' => $property->nama_provinsi ?? '',
                'nama_kabupaten' => $property->nama_kabupaten ?? '',
                'nama_kecamatan' => $property->nama_kecamatan ?? '',
                'images' => $propertyImages,
                // Field tambahan dari database property_db
                'slug_property' => $property->slug_property ?? '',
                'surat' => $property->surat ?? 'SHM',
                'lantai' => $property->lantai ? (int) $property->lantai : 1,
                'jenis_sewa' => $property->jenis_sewa ?? '',
                'status' => (int) ($property->status ?? 0),
                'isi' => $property->isi ?? '',
                'keywords' => $property->keywords ?? '',
                'nama_staff' => $property->nama_staff ?? null,
                'telepon_staff' => $property->telepon_staff ?? null,
                // AI Insights dari Waisaka AI
                'harga_rata' => $property->harga_rata ?? null,
                'fasilitas_terdekat' => $property->fasilitas_terdekat ?? null,
                'fasilitas_dekorasi' => $property->fasilitas_dekorasi ?? null,
                'peta_map' => $petaMapData
            ];

            // Tambahkan field tambahan untuk detail
            $propertyData['nama_staff'] = $property->nama_staff ?? null;
            $propertyData['telepon_staff'] = $property->telepon_staff ?? null;

            return response()->json([
                'success' => true,
                'message' => 'Detail properti berhasil diambil',
                'property' => $propertyData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail properti',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menambah properti baru
     */
    public function createProperty(Request $request)
    {
        try {
            // Ambil id_user dari request body atau query parameter
            $userId = $request->input('id_user') ?? $request->query('id_user');

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID diperlukan.'
                ], 400);
            }

            // Ambil data staff user
            $staff = DB::table('staff')->where('id_user', $userId)->first();
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil staff tidak ditemukan'
                ], 404);
            }

            // Cek apakah user sudah memiliki transaksi paket yang dikonfirmasi
            if (!$this->checkConfirmedTransaction($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum dapat menggunakan fitur ini. Silakan tunggu konfirmasi pembelian paket iklan Anda.'
                ], 403);
            }

            // Cek apakah staff aktif
            if ($staff->status_staff !== 'Ya') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda belum aktif. Silakan tunggu konfirmasi admin.'
                ], 403);
            }

            // Cek kuota iklan
            if ($staff->sisa_kuota_iklan <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kuota iklan Anda telah habis. Silakan beli paket iklan baru.'
                ], 403);
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'nama_property' => 'required|string|max:255',
                'tipe' => 'required|in:jual,sewa',
                'harga' => 'required|numeric|min:0',
                'lt' => 'required|numeric|min:0',
                'lb' => 'required|numeric|min:0',
                'kamar_tidur' => 'nullable|integer|min:0',
                'kamar_mandi' => 'nullable|integer|min:0',
                'lantai' => 'nullable|integer|min:0',
                'id_kategori_property' => 'required|exists:kategori_property,id_kategori_property',
                'id_provinsi' => 'required|exists:provinsi,id',
                'id_kabupaten' => 'required|exists:kabupaten,id',
                'id_kecamatan' => 'required|exists:kecamatan,id',
                'alamat' => 'required|string|max:500',
                'isi' => 'nullable|string',
                'keywords' => 'nullable|string|max:255',
                'surat' => 'nullable|string|max:50',
                'jenis_sewa' => 'nullable|string|max:50',
                'gambar' => 'nullable|array|max:10',
                'gambar.*' => 'image|mimes:jpeg,png,jpg|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Generate kode dan slug
            $kode = 'WPS' . str_pad(DB::table('property_db')->count() + 1, 3, '0', STR_PAD_LEFT);
            $slug_property = Str::slug($request->nama_property);

            // Simpan properti
            $propertyId = DB::table('property_db')->insertGetId([
                'id_kategori_property' => $request->id_kategori_property,
                'kode' => $kode,
                'slug_property' => $slug_property,
                'nama_property' => $request->nama_property,
                'tipe' => $request->tipe,
                'jenis_sewa' => $request->jenis_sewa ?? 'tahun',
                'harga' => $request->harga,
                'status' => 0,
                'surat' => $request->surat ?? 'SHM',
                'lt' => $request->lt,
                'lb' => $request->lb,
                'isi' => $request->isi ?? '',
                'kamar_tidur' => $request->kamar_tidur ?? 0,
                'kamar_mandi' => $request->kamar_mandi ?? 0,
                'lantai' => $request->lantai ?? 1,
                'id_staff' => $staff->id_staff,
                'alamat' => $request->alamat,
                'id_provinsi' => $request->id_provinsi,
                'id_kabupaten' => $request->id_kabupaten,
                'id_kecamatan' => $request->id_kecamatan,
                'keywords' => $request->keywords ?? ''
            ]);

            // Upload gambar (optional)
            $images = $request->file('gambar');
            if ($images && is_array($images)) {
                foreach ($images as $key => $image) {
                    if (!empty($image)) {
                        $filename = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME), '-') . '-' . time() . $key . '.' . $image->getClientOriginalExtension();
                        $destinationPath = public_path('assets/upload/property/');
                        $image->move($destinationPath, $filename);

                        DB::table('property_img')->insert([
                            'id_property' => $propertyId,
                            'gambar' => $filename,
                            'index_img' => $key
                        ]);
                    }
                }
            }

            // Kurangi kuota iklan
            DB::table('staff')
                ->where('id_staff', $staff->id_staff)
                ->update([
                    'sisa_kuota_iklan' => DB::raw('sisa_kuota_iklan - 1')
                ]);

            // AUTO-GENERATE AI INSIGHTS (Harga Rata, Fasilitas Terdekat, Peta Map)
            $this->generateAiInsights($propertyId);

            return response()->json([
                'success' => true,
                'message' => 'Properti berhasil ditambahkan',
                'data' => [
                    'property_id' => $propertyId,
                    'kode' => $request->kode,
                    'nama_property' => $request->nama_property,
                    'sisa_kuota_iklan' => $staff->sisa_kuota_iklan - 1
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah properti',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengupdate properti
     */
    public function updateProperty(Request $request, $id)
    {
        try {
            // Ambil id_user dari request body atau query parameter
            $userId = $request->input('id_user') ?? $request->query('id_user');

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID diperlukan.'
                ], 400);
            }

            // Ambil data staff user
            $staff = DB::table('staff')->where('id_user', $userId)->first();
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil staff tidak ditemukan'
                ], 404);
            }

            // Cek apakah user sudah memiliki transaksi paket yang dikonfirmasi
            if (!$this->checkConfirmedTransaction($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum dapat menggunakan fitur ini. Silakan tunggu konfirmasi pembelian paket iklan Anda.'
                ], 403);
            }

            // Cek apakah properti milik staff
            $property = DB::table('property_db')
                ->where('id_property', $id)
                ->where('id_staff', $staff->id_staff)
                ->first();

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Properti tidak ditemukan'
                ], 404);
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'nama_property' => 'nullable|string|max:255',
                'tipe' => 'nullable|in:jual,sewa',
                'harga' => 'nullable|numeric|min:0',
                'lt' => 'nullable|numeric|min:0',
                'lb' => 'nullable|numeric|min:0',
                'kamar_tidur' => 'nullable|integer|min:0',
                'kamar_mandi' => 'nullable|integer|min:0',
                'lantai' => 'nullable|integer|min:0',
                'id_kategori_property' => 'nullable|exists:kategori_property,id_kategori_property',
                'id_provinsi' => 'nullable|exists:provinsi,id',
                'id_kabupaten' => 'nullable|exists:kabupaten,id',
                'id_kecamatan' => 'nullable|exists:kecamatan,id',
                'alamat' => 'nullable|string|max:500',
                'isi' => 'nullable|string',
                'keywords' => 'nullable|string|max:255',
                'surat' => 'nullable|string|max:50',
                'jenis_sewa' => 'nullable|string|max:50',
                'gambar' => 'nullable|array|max:10',
                'gambar.*' => 'image|mimes:jpeg,png,jpg|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update properti - hanya field yang diisi
            $updateData = [];

            if ($request->filled('nama_property')) {
                $updateData['nama_property'] = $request->nama_property;
                $updateData['slug_property'] = Str::slug($request->nama_property);
            }
            if ($request->filled('tipe')) {
                $updateData['tipe'] = $request->tipe;
            }
            if ($request->filled('harga')) {
                $updateData['harga'] = $request->harga;
            }
            if ($request->filled('lt')) {
                $updateData['lt'] = $request->lt;
            }
            if ($request->filled('lb')) {
                $updateData['lb'] = $request->lb;
            }
            if ($request->filled('kamar_tidur')) {
                $updateData['kamar_tidur'] = $request->kamar_tidur;
            }
            if ($request->filled('kamar_mandi')) {
                $updateData['kamar_mandi'] = $request->kamar_mandi;
            }
            if ($request->filled('lantai')) {
                $updateData['lantai'] = $request->lantai;
            }
            if ($request->filled('id_kategori_property')) {
                $updateData['id_kategori_property'] = $request->id_kategori_property;
            }
            if ($request->filled('id_provinsi')) {
                $updateData['id_provinsi'] = $request->id_provinsi;
            }
            if ($request->filled('id_kabupaten')) {
                $updateData['id_kabupaten'] = $request->id_kabupaten;
            }
            if ($request->filled('id_kecamatan')) {
                $updateData['id_kecamatan'] = $request->id_kecamatan;
            }
            if ($request->filled('alamat')) {
                $updateData['alamat'] = $request->alamat;
            }
            if ($request->filled('isi')) {
                $updateData['isi'] = $request->isi;
            }
            if ($request->filled('keywords')) {
                $updateData['keywords'] = $request->keywords;
            }
            if ($request->filled('surat')) {
                $updateData['surat'] = $request->surat;
            }
            if ($request->filled('jenis_sewa')) {
                $updateData['jenis_sewa'] = $request->jenis_sewa;
            }

            if (!empty($updateData)) {
                DB::table('property_db')
                    ->where('id_property', $id)
                    ->update($updateData);
            }

            // Update gambar jika ada
            if ($request->hasFile('gambar')) {
                $images = $request->file('gambar');
                foreach ($images as $key => $image) {
                    if (!empty($image)) {
                        $filename = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME), '-') . '-' . time() . $key . '.' . $image->getClientOriginalExtension();
                        $destinationPath = public_path('assets/upload/property/');
                        $image->move($destinationPath, $filename);

                        DB::table('property_img')->updateOrInsert([
                            'id_property' => $id,
                            'index_img' => $key
                        ], [
                            'gambar' => $filename
                        ]);
                    }
                }
            }

            // AUTO-GENERATE AI INSIGHTS (Harga Rata, Fasilitas Terdekat, Peta Map)
            // Hanya generate jika field masih kosong
            $this->generateAiInsights($id);

            return response()->json([
                'success' => true,
                'message' => 'Properti berhasil diperbarui',
                'data' => [
                    'property_id' => $id,
                    'kode' => $request->kode,
                    'nama_property' => $request->nama_property
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui properti',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus properti
     */
    public function deleteProperty(Request $request, $id)
    {
        try {
            // Ambil id_user dari request body atau query parameter
            $userId = $request->input('id_user') ?? $request->query('id_user');

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID diperlukan.'
                ], 400);
            }

            // Ambil data staff user
            $staff = DB::table('staff')->where('id_user', $userId)->first();
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil staff tidak ditemukan'
                ], 404);
            }

            // Cek apakah user sudah memiliki transaksi paket yang dikonfirmasi
            if (!$this->checkConfirmedTransaction($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum dapat menggunakan fitur ini. Silakan tunggu konfirmasi pembelian paket iklan Anda.'
                ], 403);
            }

            // Cek apakah properti milik staff
            $property = DB::table('property_db')
                ->where('id_property', $id)
                ->where('id_staff', $staff->id_staff)
                ->first();

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Properti tidak ditemukan'
                ], 404);
            }

            // Hapus gambar
            $images = DB::table('property_img')->where('id_property', $id)->get();
            foreach ($images as $image) {
                $filePath = public_path('assets/upload/property/' . $image->gambar);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Hapus data
            DB::table('property_img')->where('id_property', $id)->delete();
            DB::table('property_db')->where('id_property', $id)->delete();

            // Tambah kembali kuota iklan
            DB::table('staff')
                ->where('id_staff', $staff->id_staff)
                ->update([
                    'sisa_kuota_iklan' => DB::raw('sisa_kuota_iklan + 1')
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Properti berhasil dihapus',
                'data' => [
                    'property_id' => $id,
                    'sisa_kuota_iklan' => $staff->sisa_kuota_iklan + 1
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus properti',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mencari properti
     */
    public function searchProperties(Request $request)
    {
        try {
            // Ambil id_user dari request body atau query parameter
            $userId = $request->input('id_user') ?? $request->query('id_user');

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID diperlukan.'
                ], 400);
            }

            // Ambil data staff user
            $staff = DB::table('staff')->where('id_user', $userId)->first();
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil staff tidak ditemukan'
                ], 404);
            }

            $keywords = $request->keywords ?? '';
            $tipe = $request->tipe ?? '';

            // Query properti menggunakan helper function
            $query = $this->getPropertyQuery($staff->id_staff);

            if ($keywords) {
                $query->where(function ($q) use ($keywords) {
                    $q->where('property_db.nama_property', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('property_db.kode', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('property_db.isi', 'LIKE', '%' . $keywords . '%');
                });
            }

            if ($tipe && $tipe !== 'all') {
                $query->where('property_db.tipe', $tipe);
            }

            $properties = $query->orderBy('property_db.id_property', 'DESC')->get();

            // Group images for each property
            $propertyIds = $properties->pluck('id_property')->toArray();
            $images = DB::table('property_img')
                ->whereIn('id_property', $propertyIds)
                ->orderBy('id_property')
                ->orderBy('index_img')
                ->get()
                ->groupBy('id_property');

            // Transform data menggunakan helper function
            $properties = $properties->map(function ($property) use ($images) {
                $transformedProperty = $this->transformPropertyData($property, $images);
                if (!empty($transformedProperty['images'])) {
                    $transformedProperty['main_image_url'] = $transformedProperty['images'][0]['gambar'];
                } else {
                    $transformedProperty['main_image_url'] = null;
                }
                return $transformedProperty;
            });

            return response()->json([
                'success' => true,
                'message' => 'Pencarian properti berhasil',
                'data' => $properties
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari properti',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan data master untuk form
     */
    public function getMasterData(Request $request)
    {
        try {
            $kategori_property = DB::table('kategori_property')->orderBy('urutan', 'ASC')->get();
            $provinsi = DB::table('provinsi')->orderBy('nama', 'ASC')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data master berhasil diambil',
                'data' => [
                    'kategori_property' => $kategori_property,
                    'provinsi' => $provinsi
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data master',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan kabupaten berdasarkan provinsi
     */
    public function getKabupaten(Request $request, $provinsiId)
    {
        try {
            $kabupaten = DB::table('kabupaten')
                ->where('id_provinsi', $provinsiId)
                ->orderBy('nama', 'ASC')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar kabupaten berhasil diambil',
                'data' => $kabupaten
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar kabupaten',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan kecamatan berdasarkan kabupaten
     */
    public function getKecamatan(Request $request, $kabupatenId)
    {
        try {
            $kecamatan = DB::table('kecamatan')
                ->where('id_kabupaten', $kabupatenId)
                ->orderBy('nama', 'ASC')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar kecamatan berhasil diambil',
                'data' => $kecamatan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar kecamatan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate AI insights untuk property (Harga Rata, Fasilitas Terdekat, Peta Map)
     * Dipanggil saat tambah atau edit property dari Flutter app
     */
    protected function generateAiInsights($id_property)
    {
        try {
            // Ambil data property lengkap
            $property = DB::table('property_db')
                ->join('kategori_property', 'kategori_property.id_kategori_property', '=', 'property_db.id_kategori_property', 'LEFT')
                ->join('provinsi', 'provinsi.id', '=', 'property_db.id_provinsi', 'LEFT')
                ->join('kabupaten', 'kabupaten.id', '=', 'property_db.id_kabupaten', 'LEFT')
                ->join('kecamatan', 'kecamatan.id', '=', 'property_db.id_kecamatan', 'LEFT')
                ->select(
                    'property_db.*',
                    'kategori_property.nama_kategori_property',
                    'provinsi.nama as nama_provinsi',
                    'kabupaten.nama as nama_kabupaten',
                    'kecamatan.nama as nama_kecamatan'
                )
                ->where('property_db.id_property', $id_property)
                ->first();

            if (!$property) {
                Log::warning("Property tidak ditemukan untuk AI insights: ID {$id_property}");
                return;
            }

            $aiService = app(WaisakaAiService::class);
            $updateData = [];

            // 1. Generate Harga Rata-rata (jika belum ada)
            if (empty(trim($property->harga_rata ?? ''))) {
                $hargaRata = $this->getHargaRata($property, $aiService);
                if ($hargaRata) {
                    $updateData['harga_rata'] = $hargaRata;
                    Log::info("AI Harga Rata generated untuk property ID {$id_property}");
                    sleep(2); // Prevent Rate Limit
                }
            }

            // 2. Generate Fasilitas Terdekat (jika belum ada)
            if (empty(trim($property->fasilitas_terdekat ?? ''))) {
                $fasilitasTerdekat = $this->getFasilitasTerdekat($property, $aiService);
                if ($fasilitasTerdekat) {
                    $updateData['fasilitas_terdekat'] = $fasilitasTerdekat;
                    Log::info("AI Fasilitas Terdekat generated untuk property ID {$id_property}");
                    sleep(2); // Prevent Rate Limit
                }
            }

            // 3. Generate Peta Map (jika belum ada)
            if (empty(trim($property->peta_map ?? ''))) {
                $petaMap = $this->getPetaMap($property, $aiService);
                if ($petaMap) {
                    $updateData['peta_map'] = $petaMap;
                    Log::info("AI Peta Map generated untuk property ID {$id_property}");
                }
            }



            // Update database jika ada data yang di-generate
            if (!empty($updateData)) {
                DB::table('property_db')
                    ->where('id_property', $id_property)
                    ->update($updateData);

                Log::info("AI Insights berhasil disimpan untuk property ID {$id_property}", $updateData);
            }

        } catch (\Exception $e) {
            // Log error tapi jangan stop proses utama
            Log::error("Error generating AI insights untuk property ID {$id_property}: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get Harga Rata-rata dari AI Waisaka
     * Updated to match Home.php enrichHargaRata() implementation
     */
    protected function getHargaRata($property, WaisakaAiService $aiService)
    {
        try {
            $category = trim((string) ($property->nama_kategori_property ?? ''));
            $type = trim((string) ($property->tipe ?? ''));
            $alamat = trim((string) ($property->alamat ?? ''));
            $kecamatan = trim((string) ($property->nama_kecamatan ?? ''));
            $kabupaten = trim((string) ($property->nama_kabupaten ?? ''));
            $provinsi = trim((string) ($property->nama_provinsi ?? ''));
            $hargaListing = !empty($property->harga) ? (float) $property->harga : null;
            $luasTanah = !empty($property->lt) ? (int) $property->lt : null;
            $luasBangunan = !empty($property->lb) ? (int) $property->lb : null;

            // Build alamat lengkap sesuai format yang diharapkan AI
            // Format: alamat, kecamatan, kabupaten, provinsi
            $addressParts = [];
            if (!empty($alamat)) {
                $addressParts[] = $alamat;
            }
            if (!empty($kecamatan)) {
                $addressParts[] = 'Kecamatan ' . $kecamatan;
            }
            if (!empty($kabupaten)) {
                $addressParts[] = $kabupaten;
            }
            if (!empty($provinsi)) {
                $addressParts[] = $provinsi;
            }

            $alamatLengkap = implode(', ', array_filter($addressParts));

            if (empty($alamatLengkap) || empty($category)) {
                return null;
            }

            // Panggil AI dengan parameter sesuai signature di WaisakaAiService
            $priceSummary = $aiService->getAveragePriceSummary(
                $type,
                $category,
                $alamatLengkap,
                $hargaListing,
                $luasTanah,
                $luasBangunan
            );

            if (empty($priceSummary)) {
                return null;
            }

            // Check if data is available (strip tags only for checking)
            $cleanPriceSummary = trim(strip_tags($priceSummary));
            if (Str::startsWith($cleanPriceSummary, 'Data pasar untuk lokasi ini belum tersedia')) {
                return null;
            }

            // IMPORTANT: Return raw HTML, not stripped text (same as Home.php line 526)
            return $priceSummary;

        } catch (\Exception $e) {
            Log::error("Error getHargaRata: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Fasilitas Terdekat dari AI Waisaka
     * Updated to match Home.php enrichFasilitasTerdekat() implementation
     */
    protected function getFasilitasTerdekat($property, WaisakaAiService $aiService)
    {
        try {
            $alamat = trim((string) ($property->alamat ?? ''));
            $kecamatan = trim((string) ($property->nama_kecamatan ?? ''));
            $kabupaten = trim((string) ($property->nama_kabupaten ?? ''));

            // Build alamat lengkap sesuai format yang diharapkan AI
            // Format: alamat, kecamatan, kabupaten
            $addressParts = [];
            if (!empty($alamat)) {
                $addressParts[] = $alamat;
            }
            if (!empty($kecamatan)) {
                $addressParts[] = 'Kecamatan ' . $kecamatan;
            }
            if (!empty($kabupaten)) {
                $addressParts[] = $kabupaten;
            }

            $alamatLengkap = implode(', ', array_filter($addressParts));

            if (empty($alamatLengkap)) {
                return null;
            }

            // Panggil AI sesuai signature di WaisakaAiService
            $summary = $aiService->getNearbyFacilitiesSummary($alamatLengkap);
            if (empty($summary)) {
                return null;
            }

            // Format to HTML list if belum HTML
            if (stripos($summary, '<ul') === false) {
                $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', strip_tags($summary))));
                if (!empty($lines)) {
                    $items = array_map(fn($line) => '<li>' . htmlspecialchars($line) . '</li>', $lines);
                    $summary = "<ul>" . implode('', $items) . "</ul>";
                }
            }

            // Return HTML summary (same as Home.php line 577)
            return $summary;

        } catch (\Exception $e) {
            Log::error("Error getFasilitasTerdekat: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Peta Map (koordinat) dari AI Waisaka
     */
    protected function getPetaMap($property, WaisakaAiService $aiService)
    {
        try {
            $address = $property->alamat ?? '';
            $kecamatan = $property->nama_kecamatan ?? null;
            $kabupaten = $property->nama_kabupaten ?? null;
            $provinsi = $property->nama_provinsi ?? null;

            $addressParts = array_filter([$address, $kecamatan, $kabupaten, $provinsi]);
            $alamatLengkap = implode(', ', $addressParts);

            if (empty($alamatLengkap)) {
                return null;
            }

            // CUSTOM LOGIC: Gunakan askQuestion dengan prompt yang lebih fleksibel
            // untuk menghindari return null jika alamat spesifik tidak ketemu.
            $prompt = <<<PROMPT
Anda adalah AI Expert Geocoding. Cari koordinat (latitude, longitude) untuk alamat ini:
"{$alamatLengkap}"

Aturan:
1. Prioritaskan lokasi akurat.
2. JIKA lokasi spesifik tidak ditemukan, BERIKAN koordinat pusat kawasan/kecamatan/kota tersebut. JANGAN return kosong.
3. Output WAJIB JSON valid:
{
  "latitude": -6.xxxx,
  "longitude": 106.xxxx,
  "maps_query": "Nama Lokasi Ditemukan"
}
PROMPT;

            $raw = $aiService->askQuestion($prompt);

            if (empty($raw))
                return null;

            // Parse JSON
            $json = trim($raw);
            if (strpos($json, '```') !== false) {
                $json = preg_replace('/^```[a-z]*\n|\n```$/', '', $json);
            }
            $coordinates = json_decode($json, true);

            if (!$coordinates || !isset($coordinates['latitude'], $coordinates['longitude'])) {
                return null;
            }

            // Format sebagai HTML iframe string untuk disimpan di database (Sesuai Home.php)
            return sprintf(
                '<iframe src="https://www.google.com/maps?q=%1$s,%2$s&z=16&output=embed" width="100%%" height="350" style="border:0;" allowfullscreen loading="lazy"></iframe>',
                $coordinates['latitude'],
                $coordinates['longitude']
            );

        } catch (\Exception $e) {
            Log::error("Error getPetaMap: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Fasilitas Dekorasi dari AI Waisaka
     */
    protected function getFasilitasDekorasi($property, WaisakaAiService $aiService)
    {
        try {
            $category = trim((string) ($property->nama_kategori_property ?? ''));
            $type = trim((string) ($property->tipe ?? ''));
            $luasTanah = !empty($property->lt) ? (int) $property->lt : null;
            $luasBangunan = !empty($property->lb) ? (int) $property->lb : null;
            $bedrooms = !empty($property->kamar_tidur) ? (int) $property->kamar_tidur : null;
            $bathrooms = !empty($property->kamar_mandi) ? (int) $property->kamar_mandi : null;

            if (empty($category)) {
                return null;
            }

            $advice = $aiService->getDecorationAdvice(
                $type,
                $category,
                $luasTanah,
                $luasBangunan,
                $bedrooms,
                $bathrooms
            );

            if (empty($advice)) {
                return null;
            }

            // Format to HTML list if belum HTML
            if (stripos($advice, '<ul') === false) {
                $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', strip_tags($advice))));
                if (!empty($lines)) {
                    $items = array_map(fn($line) => '<li>' . htmlspecialchars($line) . '</li>', $lines);
                    $advice = "<ul>" . implode('', $items) . "</ul>";
                }
            }

            return $advice;

        } catch (\Exception $e) {
            Log::error("Error getFasilitasDekorasi: " . $e->getMessage());
            return null;
        }
    }

}
