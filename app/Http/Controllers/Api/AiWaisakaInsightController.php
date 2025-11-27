<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AiWaisakaInsightController extends Controller
{
    /**
     * Get market insight for specific location and property type
     */
    public function marketInsight(Request $request)
    {
        try {
            $validated = $request->validate([
                'location' => 'required|string|max:255',
                'propertyType' => 'required|string|in:rumah,apartemen,ruko,tanah',
                'listingType' => 'nullable|string|in:jual,sewa'
            ]);

            $location = $validated['location'];
            $propertyType = $validated['propertyType'];
            $listingType = $validated['listingType'] ?? 'jual';

            // Get market data
            $marketData = $this->getMarketData($location, $propertyType, $listingType);

            // Generate AI-powered insights
            $insights = $this->generateInsights($marketData, $location, $propertyType, $listingType);

            return response()->json([
                'success' => true,
                'message' => 'Market insight berhasil diambil',
                'data' => [
                    'location' => $location,
                    'propertyType' => $propertyType,
                    'listingType' => $listingType,
                    'marketData' => $marketData,
                    'insights' => $insights,
                    'generatedAt' => now()->toISOString()
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data insight tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('AI Waisaka Market Insight Error', [
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
     * Get market data from database
     */
    private function getMarketData($location, $propertyType, $listingType)
    {
        $query = DB::table('property_db')
            ->join('kategori_property', 'kategori_property.id_kategori_property', '=', 'property_db.id_kategori_property')
            ->join('provinsi', 'provinsi.id', '=', 'property_db.id_provinsi')
            ->join('kabupaten', 'kabupaten.id', '=', 'property_db.id_kabupaten')
            ->select(
                'property_db.harga',
                'property_db.lt',
                'property_db.lb',
                'property_db.kamar_tidur',
                'property_db.kamar_mandi',
                'property_db.tanggal',
                'provinsi.nama as provinsi',
                'kabupaten.nama as kabupaten',
                'kategori_property.nama_kategori_property'
            )
            ->where('property_db.status', 1)
            ->where('property_db.tipe', $listingType)
            ->where(function($q) use ($location) {
                $q->where('provinsi.nama', 'LIKE', '%' . $location . '%')
                  ->orWhere('kabupaten.nama', 'LIKE', '%' . $location . '%')
                  ->orWhere('property_db.alamat', 'LIKE', '%' . $location . '%');
            });

        if ($propertyType !== 'all') {
            $query->where('kategori_property.nama_kategori_property', $propertyType);
        }

        $properties = $query->get();

        if ($properties->isEmpty()) {
            return $this->generateDefaultMarketData($location, $propertyType, $listingType);
        }

        // Calculate statistics
        $prices = $properties->pluck('harga')->filter()->toArray();
        $landAreas = $properties->pluck('lt')->filter()->toArray();
        $buildingAreas = $properties->pluck('lb')->filter()->toArray();

        return [
            'totalProperties' => $properties->count(),
            'averagePrice' => count($prices) > 0 ? array_sum($prices) / count($prices) : 0,
            'minPrice' => count($prices) > 0 ? min($prices) : 0,
            'maxPrice' => count($prices) > 0 ? max($prices) : 0,
            'medianPrice' => count($prices) > 0 ? $this->calculateMedian($prices) : 0,
            'averageLandArea' => count($landAreas) > 0 ? array_sum($landAreas) / count($landAreas) : 0,
            'averageBuildingArea' => count($buildingAreas) > 0 ? array_sum($buildingAreas) / count($buildingAreas) : 0,
            'pricePerSquareMeter' => count($landAreas) > 0 ? array_sum($prices) / array_sum($landAreas) : 0,
            'recentListings' => $properties->sortByDesc('tanggal')->take(5)->values(),
            'distribution' => $this->calculateDistribution($prices)
        ];
    }

    /**
     * Generate default market data when no properties found
     */
    private function generateDefaultMarketData($location, $propertyType, $listingType)
    {
        $defaultData = [
            'rumah' => [
                'jual' => ['avg' => 800000000, 'min' => 300000000, 'max' => 2000000000],
                'sewa' => ['avg' => 25000000, 'min' => 10000000, 'max' => 75000000]
            ],
            'apartemen' => [
                'jual' => ['avg' => 500000000, 'min' => 150000000, 'max' => 1500000000],
                'sewa' => ['avg' => 3500000, 'min' => 1500000, 'max' => 10000000]
            ],
            'ruko' => [
                'jual' => ['avg' => 1200000000, 'min' => 500000000, 'max' => 3000000000],
                'sewa' => ['avg' => 45000000, 'min' => 20000000, 'max' => 100000000]
            ],
            'tanah' => [
                'jual' => ['avg' => 300000000, 'min' => 100000000, 'max' => 800000000],
                'sewa' => ['avg' => 5000000, 'min' => 2000000, 'max' => 15000000]
            ]
        ];

        $data = $defaultData[$propertyType][$listingType] ?? $defaultData['rumah']['jual'];

        return [
            'totalProperties' => 0,
            'averagePrice' => $data['avg'],
            'minPrice' => $data['min'],
            'maxPrice' => $data['max'],
            'medianPrice' => $data['avg'],
            'averageLandArea' => 120,
            'averageBuildingArea' => 90,
            'pricePerSquareMeter' => $data['avg'] / 90,
            'recentListings' => [],
            'distribution' => [
                'low' => 0, 'medium' => 0, 'high' => 0, 'luxury' => 0
            ]
        ];
    }

    /**
     * Calculate median value
     */
    private function calculateMedian($array)
    {
        sort($array);
        $count = count($array);
        $middle = floor($count / 2);

        if ($count % 2 == 0) {
            return ($array[$middle - 1] + $array[$middle]) / 2;
        } else {
            return $array[$middle];
        }
    }

    /**
     * Calculate price distribution
     */
    private function calculateDistribution($prices)
    {
        if (empty($prices)) {
            return ['low' => 0, 'medium' => 0, 'high' => 0, 'luxury' => 0];
        }

        $min = min($prices);
        $max = max($prices);
        $range = $max - $min;

        $low = $min + ($range * 0.25);
        $medium = $min + ($range * 0.5);
        $high = $min + ($range * 0.75);

        $distribution = [
            'low' => 0,
            'medium' => 0,
            'high' => 0,
            'luxury' => 0
        ];

        foreach ($prices as $price) {
            if ($price <= $low) {
                $distribution['low']++;
            } elseif ($price <= $medium) {
                $distribution['medium']++;
            } elseif ($price <= $high) {
                $distribution['high']++;
            } else {
                $distribution['luxury']++;
            }
        }

        return $distribution;
    }

    /**
     * Generate AI-powered insights
     */
    private function generateInsights($marketData, $location, $propertyType, $listingType)
    {
        $insights = [];

        // Price trend insight
        if ($marketData['totalProperties'] > 0) {
            $avgPrice = $marketData['averagePrice'];
            $pricePerSqm = $marketData['pricePerSquareMeter'];

            $insights[] = [
                'type' => 'price_trend',
                'title' => 'Tren Harga',
                'description' => "Rata-rata harga {$propertyType} di {$location} adalah " . $this->formatCurrency($avgPrice),
                'recommendation' => $avgPrice < 1000000000 ? "Harga tergolong terjangkau, baik untuk investasi" : "Harga premium, pertimbangkan lokasi strategis",
                'confidence' => 85
            ];

            // Price per square meter insight
            $insights[] = [
                'type' => 'value_analysis',
                'title' => 'Analisis Nilai',
                'description' => "Harga per meter persegi: " . $this->formatCurrency($pricePerSqm),
                'recommendation' => $pricePerSqm < 15000000 ? "Nilai properti bagus untuk investasi jangka panjang" : "Properti premium dengan nilai investasi tinggi",
                'confidence' => 80
            ];
        }

        // Market activity insight
        if ($marketData['totalProperties'] >= 5) {
            $insights[] = [
                'type' => 'market_activity',
                'title' => 'Aktivitas Pasar',
                'description' => "Terdapat {$marketData['totalProperties']} properti aktif di {$location}",
                'recommendation' => "Pasar aktif dengan banyak pilihan, waktu yang baik untuk membeli",
                'confidence' => 90
            ];
        }

        // Distribution insight
        $distribution = $marketData['distribution'];
        $maxSegment = array_keys($distribution, max($distribution))[0];

        $segmentNames = [
            'low' => 'terjangkau',
            'medium' => 'menengah',
            'high' => 'premium',
            'luxury' => 'mewah'
        ];

        $insights[] = [
            'type' => 'market_segment',
            'title' => 'Segmen Pasar',
            'description' => "Pasaran didominasi oleh properti kelas {$segmentNames[$maxSegment]}",
            'recommendation' => "Fokus pada segmen {$segmentNames[$maxSegment]} untuk hasil terbaik",
            'confidence' => 75
        ];

        return $insights;
    }

    /**
     * Format currency to Indonesian Rupiah
     */
    private function formatCurrency($amount)
    {
        if ($amount >= 1000000000) {
            return 'Rp ' . number_format($amount / 1000000000, 1) . ' Miliar';
        } elseif ($amount >= 1000000) {
            return 'Rp ' . number_format($amount / 1000000, 1) . ' Juta';
        } else {
            return 'Rp ' . number_format($amount, 0);
        }
    }
}
