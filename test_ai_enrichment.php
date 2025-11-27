<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Property;
use App\Services\WaisakaAiService;
use Illuminate\Support\Facades\DB;

// 1. Get a property
$property = DB::table('property_db')
    ->join('kecamatan', 'kecamatan.id', '=', 'property_db.id_kecamatan', 'LEFT')
    ->join('kabupaten', 'kabupaten.id', '=', 'property_db.id_kabupaten', 'LEFT')
    ->join('provinsi', 'provinsi.id', '=', 'property_db.id_provinsi', 'LEFT')
    ->join('kategori_property', 'kategori_property.id_kategori_property', '=', 'property_db.id_kategori_property', 'LEFT')
    ->select(
        'property_db.*',
        'kecamatan.nama as nama_kecamatan',
        'kabupaten.nama as nama_kabupaten',
        'provinsi.nama as nama_provinsi',
        'kategori_property.nama_kategori_property'
    )
    ->where('property_db.status', 1)
    ->orderBy('property_db.id_property', 'desc')
    ->first();

if (!$property) {
    echo "No property found.\n";
    exit;
}

echo "Property ID: " . $property->id_property . "\n";
echo "Nama: " . $property->nama_property . "\n";
echo "Alamat: " . $property->alamat . "\n";
echo "Kecamatan: " . $property->nama_kecamatan . "\n";
echo "Kabupaten: " . $property->nama_kabupaten . "\n";
echo "Provinsi: " . $property->nama_provinsi . "\n";
echo "LT: " . $property->lt . " m2, LB: " . $property->lb . " m2\n";
echo "Harga: " . number_format($property->harga) . "\n\n";

$aiService = new WaisakaAiService();

// 2. Test Harga Rata
echo "--- TESTING HARGA RATA ---\n";
$alamatLengkap = implode(', ', array_filter([
    $property->alamat,
    'Kecamatan ' . $property->nama_kecamatan,
    $property->nama_kabupaten,
    $property->nama_provinsi
]));

$price = $aiService->getAveragePriceSummary(
    $property->tipe,
    $property->nama_kategori_property,
    $alamatLengkap,
    $property->harga,
    $property->lt,
    $property->lb
);
echo "Result:\n" . $price . "\n\n";

// 3. Test Fasilitas Terdekat
echo "--- TESTING FASILITAS TERDEKAT ---\n";
$facilities = $aiService->getNearbyFacilitiesSummary($alamatLengkap);
echo "Result:\n" . $facilities . "\n\n";

// 4. Test Peta Map (Custom Logic for API)
echo "--- TESTING PETA MAP (CUSTOM LOGIC) ---\n";

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
echo "Raw Response:\n" . $raw . "\n\n";

// Parse JSON
$json = trim($raw);
if (strpos($json, '```') !== false) {
    $json = preg_replace('/^```[a-z]*\n|\n```$/', '', $json);
}
$coords = json_decode($json, true);

if ($coords && isset($coords['latitude'], $coords['longitude'])) {
    echo "Latitude: " . $coords['latitude'] . "\n";
    echo "Longitude: " . $coords['longitude'] . "\n";
    echo "Maps Query: " . ($coords['maps_query'] ?? '') . "\n";

    // Generate Iframe string
    $iframe = sprintf(
        '<iframe src="https://www.google.com/maps?q=%1$s,%2$s&z=16&output=embed" width="100%%" height="350" style="border:0;" allowfullscreen loading="lazy"></iframe>',
        $coords['latitude'],
        $coords['longitude']
    );
    echo "Iframe: " . $iframe . "\n";
} else {
    echo "Result: NULL (Coordinates not found or Invalid JSON)\n";
}
