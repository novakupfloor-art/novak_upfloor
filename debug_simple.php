<?php

// Simple debug script
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing AI Search with 'cari rumah dijual di kota wisata'\n\n";

// Check database first
echo "1. Checking database for properties...\n";
$count = \Illuminate\Support\Facades\DB::table('property_db')->count();
echo "Total properties in DB: $count\n\n";

// Check for "Kota Wisata"
$kotaWisata = \Illuminate\Support\Facades\DB::table('property_db')
    ->where('alamat', 'LIKE', '%Kota Wisata%')
    ->orWhere('nama_property', 'LIKE', '%Kota Wisata%')
    ->count();
echo "Properties with 'Kota Wisata': $kotaWisata\n\n";

// Test manual search
echo "2. Testing manual search with location='Kota Wisata'...\n";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create(
    '/api/v1/mobile/properties/search',
    'GET',
    ['location' => 'Kota Wisata']
);

$response = $kernel->handle($request);
$data = json_decode($response->getContent(), true);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Total: " . ($data['total'] ?? 0) . "\n\n";

// Test with keywords
echo "3. Testing manual search with keywords='kota wisata'...\n";
$request2 = Illuminate\Http\Request::create(
    '/api/v1/mobile/properties/search',
    'GET',
    ['keywords' => 'kota wisata']
);

$response2 = $kernel->handle($request2);
$data2 = json_decode($response2->getContent(), true);

echo "Status: " . $response2->getStatusCode() . "\n";
echo "Total: " . ($data2['total'] ?? 0) . "\n\n";

// Test AI search
echo "4. Testing AI search...\n";
$request3 = Illuminate\Http\Request::create(
    '/api/v1/ai-waisaka/search',
    'POST',
    [],
    [],
    [],
    ['CONTENT_TYPE' => 'application/json'],
    json_encode([
        'filters' => [
            'keywords' => 'cari rumah dijual di kota wisata'
        ]
    ])
);

$response3 = $kernel->handle($request3);
$data3 = json_decode($response3->getContent(), true);

echo "Status: " . $response3->getStatusCode() . "\n";
echo "Total: " . ($data3['total'] ?? 0) . "\n";

if (isset($data3['ai_metadata']['parsed_filters'])) {
    echo "AI Parsed: " . json_encode($data3['ai_metadata']['parsed_filters']) . "\n";
}

echo "\nDone!\n";
