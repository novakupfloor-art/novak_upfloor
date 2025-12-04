<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Services\WaisakaAiService;

class AiWaisakaSearchController extends Controller
{
    protected $aiService;

    public function __construct(WaisakaAiService $aiService)
    {
        $this->aiService = $aiService;
    }
    /**
     * Process AI Waisaka search request
     * Receives natural language query, parses it with AI, then delegates to standard search
     */
    public function processAiSearch(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'filters' => 'required|array',
                'filters.keywords' => 'nullable|string|max:500',
                'filters.listingType' => 'nullable|string|in:jual,sewa',
            ]);

            $filters = $validated['filters'];
            $keywords = $filters['keywords'] ?? '';

            Log::info('AI Waisaka Search Request', [
                'original_keywords' => $keywords,
                'filters' => $filters,
                'ip' => $request->ip()
            ]);

            // ✅ AI PARSING: Convert natural language to structured filters
            $aiParsedFilters = [];
            if (!empty($keywords)) {
                $aiParsedFilters = $this->aiService->parseSearchQuery($keywords);

                if ($aiParsedFilters) {
                    Log::info('Gemini Parsed Intent', [
                        'original' => $keywords,
                        'parsed' => $aiParsedFilters
                    ]);
                } else {
                    Log::warning('Gemini failed to parse query, using raw keywords');
                    $aiParsedFilters = ['keywords' => $keywords];
                }
            }

            // ✅ MAP AI FILTERS TO NEW SEARCH ENDPOINT PARAMETERS
            // Convert snake_case AI output to match MobilePropertyController::search() parameters
            $searchParams = [
                // Basic filters
                'tipe' => $aiParsedFilters['tipe'] ?? $filters['listingType'] ?? null,
                // Combine keywords with location if location is single word (no commas)
                'keywords' => $this->combineKeywords($aiParsedFilters),

                // Location - convert to string format if needed
                'location' => $this->formatLocation($aiParsedFilters['lokasi'] ?? null),

                // Category - support both name and ID
                'id_kategori_property' => $this->mapCategory($aiParsedFilters['kategori'] ?? null),

                // Price range
                'min_harga' => $aiParsedFilters['harga_min'] ?? null,
                'max_harga' => $aiParsedFilters['harga_max'] ?? null,

                // Land area
                'min_lt' => $aiParsedFilters['lt_min'] ?? null,
                'max_lt' => $aiParsedFilters['lt_max'] ?? null,

                // Building area
                'min_lb' => $aiParsedFilters['lb_min'] ?? null,
                'max_lb' => $aiParsedFilters['lb_max'] ?? null,

                // Rooms (exact match)
                'kamar_tidur' => $aiParsedFilters['kamar_tidur'] ?? null,
                'kamar_mandi' => $aiParsedFilters['kamar_mandi'] ?? null,

                // Certificate
                'certificates' => $aiParsedFilters['surat'] ?? null,

                // Pagination
                'page' => 1,
                'limit' => 20
            ];

            // Remove null values
            $searchParams = array_filter($searchParams, function ($value) {
                return !is_null($value) && $value !== '';
            });

            Log::info('AI Search Mapped Parameters', [
                'ai_filters' => $aiParsedFilters,
                'search_params' => $searchParams
            ]);

            // ✅ DELEGATE TO MOBILEPROPERTYCONTROLLER::SEARCH()
            // Create internal request
            $searchRequest = Request::create(
                '/api/v1/mobile/properties/search',
                'GET',
                $searchParams
            );

            // Call the search method directly
            $propertyController = app(MobilePropertyController::class);
            $searchResponse = $propertyController->search($searchRequest);

            // Get response data
            $responseData = $searchResponse->getData(true);

            // Add AI metadata to response
            if ($responseData['success']) {
                $responseData['ai_metadata'] = [
                    'original_query' => $keywords,
                    'parsed_filters' => $aiParsedFilters,
                    'search_timestamp' => now()->toISOString()
                ];
            }

            return response()->json($responseData, $searchResponse->status());

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
     * Format location string for search
     */
    private function formatLocation(?string $location): ?string
    {
        if (empty($location)) {
            return null;
        }


        // Only use location filter if it contains commas (proper format)
        // Single word locations will be handled by keywords
        return (strpos($location, ',') !== false) ? $location : null;
    }

    /**
     * Combine keywords with location if location is single word
     */
    private function combineKeywords(array $aiFilters): ?string
    {
        $keywords = $aiFilters['keywords'] ?? '';
        $location = $aiFilters['lokasi'] ?? '';

        // If location doesn't have commas, add it to keywords
        if (!empty($location) && strpos($location, ',') === false) {
            $keywords = trim($keywords . ' ' . $location);
        }

        return !empty($keywords) ? $keywords : null;
    }

    /**
     * Map category name to appropriate format
     */
    private function mapCategory(?string $category): ?string
    {
        if (empty($category)) {
            return null;
        }

        // Map common category names
        $categoryMap = [
            'rumah' => 'Rumah',
            'apartemen' => 'Apartemen',
            'ruko' => 'Ruko',
            'tanah' => 'Tanah',
            'villa' => 'Villa',
            'gudang' => 'Gudang',
            'kantor' => 'Kantor',
            'kost' => 'Kost'
        ];

        $lowerCategory = strtolower($category);
        return $categoryMap[$lowerCategory] ?? $category;
    }

    /**
     * Process General AI Chat
     */
    public function chat(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
            ]);

            $message = $request->input('message');

            // Call AI Service for general chat
            $response = $this->aiService->generalChat($message);

            if ($response) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'message' => $response,
                        'timestamp' => now()->toISOString()
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'AI tidak dapat memberikan respons saat ini.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('AI Chat Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    /**
     * Build search query based on filters
     * Uses snake_case keys matching Database Columns
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
                'kategori_property.slug_kategori_property',
                'kategori_property.nama_kategori_property',
                'staff.nama_staff',
                'provinsi.nama as nama_provinsi',
                'kabupaten.nama as nama_kabupaten',
                'kecamatan.nama as nama_kecamatan',
                'property_db.tanggal as created_at'
            )
            ->selectRaw("(CASE WHEN property_db.status = 0 THEN CONCAT('belum ter',property_db.tipe) ELSE CONCAT('sudah ter',property_db.tipe) END) AS nama_status")
            ->orderBy('property_db.tanggal', 'DESC');

        // Apply filters (using snake_case keys)

        if (!empty($filters['tipe'])) {
            $query->where('property_db.tipe', $filters['tipe']);
        }

        if (!empty($filters['lokasi'])) {
            $location = $filters['lokasi'];
            $query->where(function ($q) use ($location) {
                $q->where('provinsi.nama', 'LIKE', '%' . $location . '%')
                    ->orWhere('kabupaten.nama', 'LIKE', '%' . $location . '%')
                    ->orWhere('kecamatan.nama', 'LIKE', '%' . $location . '%')
                    ->orWhere('property_db.alamat', 'LIKE', '%' . $location . '%')
                    ->orWhere('property_db.nama_property', 'LIKE', '%' . $location . '%')
                    ->orWhere('property_db.kode', 'LIKE', '%' . $location . '%')
                    ->orWhere('property_db.isi', 'LIKE', '%' . $location . '%');
            });
        }

        if (!empty($filters['harga_min'])) {
            $query->where('property_db.harga', '>=', $filters['harga_min']);
        }

        if (!empty($filters['harga_max'])) {
            $query->where('property_db.harga', '<=', $filters['harga_max']);
        }

        if (!empty($filters['lt_min'])) {
            $query->where('property_db.lt', '>=', $filters['lt_min']);
        }

        if (!empty($filters['lt_max'])) {
            $query->where('property_db.lt', '<=', $filters['lt_max']);
        }

        if (!empty($filters['lb_min'])) {
            $query->where('property_db.lb', '>=', $filters['lb_min']);
        }

        if (!empty($filters['lb_max'])) {
            $query->where('property_db.lb', '<=', $filters['lb_max']);
        }

        if (!empty($filters['kamar_tidur'])) {
            $query->where('property_db.kamar_tidur', '>=', $filters['kamar_tidur']);
        }

        if (!empty($filters['kamar_mandi'])) {
            $query->where('property_db.kamar_mandi', '>=', $filters['kamar_mandi']);
        }

        if (!empty($filters['kategori'])) {
            $kategori = $filters['kategori'];
            $query->where('kategori_property.nama_kategori_property', 'LIKE', '%' . $kategori . '%');
        }

        if (!empty($filters['surat'])) {
            $query->where('property_db.surat', $filters['surat']);
        }

        if (!empty($filters['id_kategori_property'])) {
            $query->where('property_db.id_kategori_property', $filters['id_kategori_property']);
        }

        if (!empty($filters['id_provinsi'])) {
            $query->where('property_db.id_provinsi', $filters['id_provinsi']);
        }

        if (!empty($filters['id_kabupaten'])) {
            $query->where('property_db.id_kabupaten', $filters['id_kabupaten']);
        }

        if (!empty($filters['id_kecamatan'])) {
            $query->where('property_db.id_kecamatan', $filters['id_kecamatan']);
        }

        if (!empty($filters['keywords'])) {
            $keywords = $filters['keywords'];
            $query->where(function ($q) use ($keywords) {
                $q->where('property_db.nama_property', 'LIKE', '%' . $keywords . '%')
                    ->orWhere('property_db.kode', 'LIKE', '%' . $keywords . '%')
                    ->orWhere('property_db.isi', 'LIKE', '%' . $keywords . '%')
                    ->orWhere('property_db.alamat', 'LIKE', '%' . $keywords . '%')
                    ->orWhere('property_db.keywords', 'LIKE', '%' . $keywords . '%');
            });
        }

        // Log the generated SQL for debugging
        Log::info('AI Search SQL', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

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
                ->where(function ($q) use ($query) {
                    $q->where('provinsi.nama', 'LIKE', '%' . $query . '%')
                        ->orWhere('kabupaten.nama', 'LIKE', '%' . $query . '%')
                        ->orWhere('kecamatan.nama', 'LIKE', '%' . $query . '%');
                })
                ->distinct()
                ->limit(10)
                ->get();

            $suggestions = $locations->map(function ($location) {
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
