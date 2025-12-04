<?php

// Debug script for AI search
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║         AI SEARCH DEBUG - Kota Wisata                    ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

// Test 1: Check if there are any properties with "Kota Wisata" in location
echo "1. Checking database for 'Kota Wisata' properties...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$properties = DB::table('property_db')
    ->join('provinsi', 'provinsi.id', '=', 'property_db.id_provinsi', 'LEFT')
    ->join('kabupaten', 'kabupaten.id', '=', 'property_db.id_kabupaten', 'LEFT')
    ->join('kecamatan', 'kecamatan.id', '=', 'property_db.id_kecamatan', 'LEFT')
    ->select(
        'property_db.id_property',
        'property_db.nama_property',
        'property_db.alamat',
        'property_db.tipe',
        'kecamatan.nama as kecamatan',
        'kabupaten.nama as kabupaten',
        'provinsi.nama as provinsi'
    )
    ->where(function ($q) {
        $q->where('property_db.alamat', 'LIKE', '%Kota Wisata%')
            ->orWhere('kecamatan.nama', 'LIKE', '%Kota Wisata%')
            ->orWhere('kabupaten.nama', 'LIKE', '%Kota Wisata%')
            ->orWhere('provinsi.nama', 'LIKE', '%Kota Wisata%');
    })
    ->limit(5)
    ->get();

echo "Found " . $properties->count() . " properties with 'Kota Wisata'\n\n";

foreach ($properties as $prop) {
    echo "ID: {$prop->id_property}\n";
    echo "Nama: {$prop->nama_property}\n";
    echo "Alamat: {$prop->alamat}\n";
    echo "Tipe: {$prop->tipe}\n";
    echo "Lokasi: {$prop->kecamatan}, {$prop->kabupaten}, {$prop->provinsi}\n";
    echo "---\n";
}

// Test 2: Test AI Search with the exact query
echo "\n2. Testing AI Search API...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$request = Illuminate\Http\Request::create(
    '/api/v1/ai-waisaka/search',
    'POST',
    [],
    [],
    [],
    ['CONTENT_TYPE' => 'application/json'],
    json_encode([
        'filters' => [
            'keywords' => 'cari rumah dijual di kota wisata',
            'listingType' => 'jual'
        ]
    ])
);

$response = $kernel->handle($request);
$data = json_decode($response->getContent(), true);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
echo "Total Results: " . ($data['total'] ?? 0) . "\n\n";

if (isset($data['ai_metadata'])) {
    echo "AI Parsed Filters:\n";
    print_r($data['ai_metadata']['parsed_filters']);
    echo "\n";
}

// Test 3: Test manual search with same parameters
echo "\n3. Testing Manual Search with AI-parsed parameters...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$searchRequest = Illuminate\Http\Request::create(
    '/api/v1/mobile/properties/search',
    'GET',
    [
        'tipe' => 'jual',
        'id_kategori_property' => 'Rumah',
        'location' => 'Kota Wisata',
        'keywords' => 'kota wisata'
    ]
);

$propertyController = app(App\Http\Controllers\Api\MobilePropertyController::class);
$searchResponse = $propertyController->search($searchRequest);
$searchData = $searchResponse->getData(true);

echo "Status: " . $searchResponse->getStatusCode() . "\n";
echo "Success: " . ($searchData['success'] ? 'true' : 'false') . "\n";
echo "Total Results: " . ($searchData['total'] ?? 0) . "\n\n";

if (isset($searchData['data']) && count($searchData['data']) > 0) {
    echo "Sample result:\n";
    $sample = $searchData['data'][0];
    echo "  ID: {$sample['id_property']}\n";
    echo "  Nama: {$sample['nama_property']}\n";
    echo "  Alamat: {$sample['alamat']}\n";
}

// Test 4: Test with just location filter
echo "\n4. Testing with location filter only...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$locationRequest = Illuminate\Http\Request::create(
    '/api/v1/mobile/properties/search',
    'GET',
    [
        'location' => 'Kota Wisata'
    ]
);

$locationResponse = $propertyController->search($locationRequest);
$locationData = $locationResponse->getData(true);

echo "Status: " . $locationResponse->getStatusCode() . "\n";
echo "Total Results: " . ($locationData['total'] ?? 0) . "\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Debug completed!\n";
