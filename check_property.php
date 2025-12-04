<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$prop = DB::table('property_db')->where('id_property', 8)->first();

if ($prop) {
    echo "ID: " . $prop->id_property . PHP_EOL;
    echo "Nama: " . $prop->nama_property . PHP_EOL;
    echo "Status: " . $prop->status . PHP_EOL;
    echo "Harga Rata Length: " . strlen($prop->harga_rata ?? '') . PHP_EOL;
    echo "Harga Rata: " . ($prop->harga_rata ?? 'NULL') . PHP_EOL;
    echo "---" . PHP_EOL;
    echo "Fasilitas Terdekat Length: " . strlen($prop->fasilitas_terdekat ?? '') . PHP_EOL;
    echo "Peta Map Length: " . strlen($prop->peta_map ?? '') . PHP_EOL;
} else {
    echo "Property ID 8 tidak ditemukan atau status = 0" . PHP_EOL;
}
