<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiWaisakaSearchController extends Controller
{
    /**
     * Process AI Waisaka search request
     * Receives JSON data from frontend and processes it for precise property search
     */
    public function processAiSearch(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'filters' => 'required|array',
                'filters.listingType' => 'required|string|in:jual,sewa',
                'filters.location' => 'nullable|string|max:255',
                'filters.minPrice' => 'nullable|numeric|min:0',
                'filters.maxPrice' => 'nullable|numeric|min:0',
                'filters.minLandArea' => 'nullable|numeric|min:0',
                'filters.maxLandArea' => 'nullable|numeric|min:0',
                'filters.minBuildingArea' => 'nullable|numeric|min:0',
                'filters.maxBuildingArea' => 'nullable|numeric|min:0',
                'filters.bedrooms' => 'nullable|integer|min:0',
                'filters.bathrooms' => 'nullable|integer|min:0',
                'filters.propertyType' => 'nullable|string|in:rumah,apartemen,ruko,tanah',
                'filters.certificate' => 'nullable|string|in:SHM,HGB,Lainnya',
                // ✅ TAMBAHAN: Field baru untuk validasi
                'filters.categoryId' => 'nullable|integer|exists:kategori_property,id_kategori_property',
                'filters.provinceId' => 'nullable|integer|exists:provinsi,id',
                'filters.districtId' => 'nullable|integer|exists:kabupaten,id',
                'filters.subDistrictId' => 'nullable|integer|exists:kecamatan,id',
                'filters.keywords' => 'nullable|string|max:255',
            ]);

            $filters = $validated['filters'];

            // ✅ TAMBAHAN: Validasi price range
            if (!empty($filters['minPrice']) && !empty($filters['maxPrice'])) {
                if ($filters['minPrice'] > $filters['maxPrice']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Harga minimum tidak boleh lebih besar dari harga maksimum'
                    ], 422);
                }
            }

            // ✅ TAMBAHAN: Validasi land area range
            if (!empty($filters['minLandArea']) && !empty($filters['maxLandArea'])) {
                if ($filters['minLandArea'] > $filters['maxLandArea']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Luas tanah minimum tidak boleh lebih besar dari luas tanah maksimum'
                    ], 422);
                }
            }

            // ✅ TAMBAHAN: Validasi building area range
            if (!empty($filters['minBuildingArea']) && !empty($filters['maxBuildingArea'])) {
                if ($filters['minBuildingArea'] > $filters['maxBuildingArea']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Luas bangunan minimum tidak boleh lebih besar dari luas bangunan maksimum'
                    ], 422);
                }
            }

            // Log the search request for debugging
            Log::info('AI Waisaka Search Request', [
                'filters' => $filters,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Build the search query
            $query = $this->buildSearchQuery($filters);

            // Execute search
            $properties = $query->get();

            // Get property images
            $propertyIds = $properties->pluck('id_property')->toArray();
            $images = collect();
            
            if (!empty($propertyIds)) {
                $images = DB::table('property_img')
                    ->whereIn('id_property', $propertyIds)
                    ->orderBy('id_property')
                    ->orderBy('index_img')
                    ->get()
                    ->groupBy('id_property');
            }

            // Transform data inline
            $transformedProperties = $properties->map(function($property) use ($images) {
                // Process images
                $propertyImages = [];
                if ($images && $images->has($property->id_property)) {
                    $propertyImages = $images->get($property->id_property)->map(function($img) {
                        return [
                            'gambar' => asset('assets/upload/property/' . $img->gambar),
                            'index_img' => (int) $img->index_img
                        ];
                    })->toArray();
                }

                // Set main image URL
                $mainImageUrl = null;
                if (!empty($propertyImages)) {
                    $mainImageUrl = $propertyImages[0]['gambar'];
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
                    'nama_kategori_property' => $property->nama_kategori_property ?? '',
                    'nama_provinsi' => $property->nama_provinsi ?? '',
                    'nama_kabupaten' => $property->nama_kabupaten ?? '',
                    'nama_kecamatan' => $property->nama_kecamatan ?? '',
                    'nama_staff' => $property->nama_staff ?? '',
                    'images' => $propertyImages,
                    'main_image_url' => $mainImageUrl,
                    // Additional fields
                    'slug_property' => $property->slug_property ?? '',
                    'surat' => $property->surat ?? 'SHM',
                    'lantai' => $property->lantai ? (int) $property->lantai : 1,
                    'jenis_sewa' => $property->jenis_sewa ?? '',
                    'status' => (int) ($property->status ?? 0),
                    'isi' => $property->isi ?? '',
                    'keywords' => $property->keywords ?? '',
                    'id_kategori_property' => isset($property->id_kategori_property) ? (int) $property->id_kategori_property : null,
                    'id_provinsi' => isset($property->id_provinsi) ? (int) $property->id_provinsi : null,
                    'id_kabupaten' => isset($property->id_kabupaten) ? (int) $property->id_kabupaten : null,
                    'id_kecamatan' => isset($property->id_kecamatan) ? (int) $property->id_kecamatan : null,
                    // AI Insights dari Waisaka AI
                    'harga_rata' => $property->harga_rata ?? null,
                    'fasilitas_terdekat' => $property->fasilitas_terdekat ?? null,
                    'peta_map' => $property->peta_map ?? null
                ];
            });

            // Log search results
            Log::info('AI Waisaka Search Results', [
                'filters_applied' => $filters,
                'results_count' => $transformedProperties->count(),
                'property_ids' => $propertyIds
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pencarian AI Waisaka berhasil',
                'data' => $transformedProperties,
                'search_metadata' => [
                    'filters_applied' => $filters,
                    'total_results' => $transformedProperties->count(),
                    'search_timestamp' => now()->toISOString()
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data pencarian tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('AI Waisaka Search Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build search query based on filters
     */
    private function buildSearchQuery(array $filters)
    {
        $query = DB::table('property_db')
            ->join('kategori_property', 'kategori_property.id_kategori_property', '=', 'property_db.id_kategori_property', 'LEFT')
            ->join('staff', 'staff.id_staff', '=', 'property_db.id_staff', 'LEFT')
            ->join('provinsi', 'provinsi.id', '=', 'property_db.id_provinsi', 'LEFT')
            ->join('kabupaten', 'kabupaten.id', '=', 'property_db.id_kabupaten', 'LEFT')
            ->join('kecamatan', 'kecamatan.id', '=', 'property_db.id_kecamatan', 'LEFT')
            ->select(
                'property_db.*',
                'kategori_property.nama_kategori_property',
                'staff.nama_staff',
                'provinsi.nama as nama_provinsi',
                'kabupaten.nama as nama_kabupaten',
                'kecamatan.nama as nama_kecamatan',
                'property_db.tanggal as created_at'
            )
            ->where('property_db.status', 1) // Only active properties
            ->orderBy('property_db.tanggal', 'DESC');

        // Apply filters
        if (!empty($filters['listingType'])) {
            $query->where('property_db.tipe', $filters['listingType']);
        }

        // ✅ PERBAIKAN: Location search yang lebih komprehensif
        if (!empty($filters['location'])) {
            $location = $filters['location'];
            $query->where(function($q) use ($location) {
                $q->where('provinsi.nama', 'LIKE', '%' . $location . '%')
                  ->orWhere('kabupaten.nama', 'LIKE', '%' . $location . '%')
                  ->orWhere('kecamatan.nama', 'LIKE', '%' . $location . '%')
                  ->orWhere('property_db.alamat', 'LIKE', '%' . $location . '%')
                  ->orWhere('property_db.nama_property', 'LIKE', '%' . $location . '%') // ✅ TAMBAHAN
                  ->orWhere('property_db.kode', 'LIKE', '%' . $location . '%') // ✅ TAMBAHAN
                  ->orWhere('property_db.isi', 'LIKE', '%' . $location . '%'); // ✅ TAMBAHAN
            });
        }

        if (!empty($filters['minPrice'])) {
            $query->where('property_db.harga', '>=', $filters['minPrice']);
        }

        if (!empty($filters['maxPrice'])) {
            $query->where('property_db.harga', '<=', $filters['maxPrice']);
        }

        if (!empty($filters['minLandArea'])) {
            $query->where('property_db.lt', '>=', $filters['minLandArea']);
        }

        if (!empty($filters['maxLandArea'])) {
            $query->where('property_db.lt', '<=', $filters['maxLandArea']);
        }

        if (!empty($filters['minBuildingArea'])) {
            $query->where('property_db.lb', '>=', $filters['minBuildingArea']);
        }

        if (!empty($filters['maxBuildingArea'])) {
            $query->where('property_db.lb', '<=', $filters['maxBuildingArea']);
        }

        if (!empty($filters['bedrooms'])) {
            $query->where('property_db.kamar_tidur', '>=', $filters['bedrooms']);
        }

        if (!empty($filters['bathrooms'])) {
            $query->where('property_db.kamar_mandi', '>=', $filters['bathrooms']);
        }

        if (!empty($filters['propertyType'])) {
            $propertyType = $filters['propertyType'];
            $query->where('kategori_property.nama_kategori_property', 'LIKE', '%' . $propertyType . '%');
        }

        if (!empty($filters['certificate'])) {
            $query->where('property_db.surat', $filters['certificate']);
        }

        // ✅ TAMBAHAN: Filter berdasarkan categoryId
        if (!empty($filters['categoryId'])) {
            $query->where('property_db.id_kategori_property', $filters['categoryId']);
        }

        // ✅ TAMBAHAN: Filter berdasarkan provinceId
        if (!empty($filters['provinceId'])) {
            $query->where('property_db.id_provinsi', $filters['provinceId']);
        }

        // ✅ TAMBAHAN: Filter berdasarkan districtId
        if (!empty($filters['districtId'])) {
            $query->where('property_db.id_kabupaten', $filters['districtId']);
        }

        // ✅ TAMBAHAN: Filter berdasarkan subDistrictId
        if (!empty($filters['subDistrictId'])) {
            $query->where('property_db.id_kecamatan', $filters['subDistrictId']);
        }

        // ✅ TAMBAHAN: Filter berdasarkan keywords untuk pencarian yang lebih luas
        if (!empty($filters['keywords'])) {
            $keywords = $filters['keywords'];
            $query->where(function($q) use ($keywords) {
                $q->where('property_db.nama_property', 'LIKE', '%' . $keywords . '%')
                  ->orWhere('property_db.kode', 'LIKE', '%' . $keywords . '%')
                  ->orWhere('property_db.isi', 'LIKE', '%' . $keywords . '%')
                  ->orWhere('property_db.alamat', 'LIKE', '%' . $keywords . '%')
                  ->orWhere('property_db.keywords', 'LIKE', '%' . $keywords . '%');
            });
        }

        return $query;
    }

    /**
     * Get search suggestions based on popular searches
     */
    public function getSearchSuggestions(Request $request)
    {
        try {
            $query = $request->query('q', '');
            
            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ], 200);
            }

            // Get location suggestions
            $locations = DB::table('property_db')
                ->join('provinsi', 'provinsi.id', '=', 'property_db.id_provinsi', 'LEFT')
                ->join('kabupaten', 'kabupaten.id', '=', 'property_db.id_kabupaten', 'LEFT')
                ->join('kecamatan', 'kecamatan.id', '=', 'property_db.id_kecamatan', 'LEFT')
                ->select('provinsi.nama as provinsi', 'kabupaten.nama as kabupaten', 'kecamatan.nama as kecamatan')
                ->where('property_db.status', 1)
                ->where(function($q) use ($query) {
                    $q->where('provinsi.nama', 'LIKE', '%' . $query . '%')
                      ->orWhere('kabupaten.nama', 'LIKE', '%' . $query . '%')
                      ->orWhere('kecamatan.nama', 'LIKE', '%' . $query . '%');
                })
                ->distinct()
                ->limit(10)
                ->get();

            $suggestions = $locations->map(function($location) {
                return $location->kabupaten ?: $location->provinsi;
            })->unique()->values();

            return response()->json([
                'success' => true,
                'data' => $suggestions
            ], 200);

        } catch (\Exception $e) {
            Log::error('AI Waisaka Search Suggestions Error', [
                'error' => $e->getMessage(),
                'query' => $request->query('q')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil saran pencarian'
            ], 500);
        }
    }
}
