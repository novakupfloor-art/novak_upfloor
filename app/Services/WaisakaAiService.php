<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WaisakaAiService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;
    protected int $timeout;
    protected int $retries;

    public function __construct()
    {
        $config = config('services.gemini', []);

        $this->apiKey = (string) ($config['key'] ?? '');
        $this->baseUrl = rtrim((string) ($config['base'] ?? 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $this->model = (string) ($config['model'] ?? 'gemini-2.5-flash-lite');
        $this->timeout = (int) ($config['timeout'] ?? 40);
        $this->retries = max(0, (int) ($config['retries'] ?? 3));
    }


    public function getMapCoordinateSummary(string $address, ?string $kecamatan = null, ?string $kabupaten = null, ?string $provinsi = null): ?array
    {
        $addressParts = array_filter([$address, $kecamatan, $kabupaten, $provinsi]);
        $alamatLengkap = implode(', ', $addressParts);

        $prompt = <<<PROMPT
Anda adalah AI Expert Geocoding Specialist yang bertugas mencari koordinat maps (latitude, longitude) dengan akurat.

🔍 Tugas anda adalah mencari koordinat (latitude, longitude) paling relevan dan akurat dari {$address} dengan teliti:
   • Jangan cari koordinat di luar {$alamatLengkap}
   • Jangan cari koordinat perkiraan
   • Jika tidak ditemukan resultnya maka cari dengan berdasarkan wilayah kecamatan atau kota atau kabupaten dari {$alamatLengkap}
   • Pilih hasil pencarian yang menampilkan koordinat yang akurat dengan {$alamatLengkap}
   • Verifikasi hasil pencarian dengan google maps
   • Jika BENAR-BENAR tidak ditemukan, return: {}


━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📤 CONTOH FORMAT OUTPUT (JSON MURNI):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

{
  "latitude": -6.9174639,
  "longitude": 107.6191228,
  "maps_query": "Perumahan Grand Wisata, Kalimantan"
}

PROMPT;

        $raw = $this->generateContent($prompt);
        if (empty($raw)) {
            return null;
        }

        $decoded = $this->decodeJson($raw);
        if (!$decoded) {
            Log::warning('WaisakaAiService: gagal mengurai koordinat Gemini', ['raw' => $raw]);
            return null;
        }

        if (!isset($decoded['latitude'], $decoded['longitude'])) {
            Log::warning('WaisakaAiService: respon koordinat tidak lengkap', ['decoded' => $decoded]);
            return null;
        }

        // Validasi numerik dan rentang Indonesia
        $lat = is_numeric($decoded['latitude']) ? (float) $decoded['latitude'] : null;
        $lng = is_numeric($decoded['longitude']) ? (float) $decoded['longitude'] : null;

        if ($lat === null || $lng === null) {
            Log::warning('WaisakaAiService: koordinat bukan numerik', ['decoded' => $decoded]);
            return null;
        }

        if ($lat < -11.5 || $lat > 6.5 || $lng < 95.0 || $lng > 145.0) {
            Log::warning('WaisakaAiService: koordinat di luar rentang Indonesia', ['lat' => $lat, 'lng' => $lng, 'decoded' => $decoded]);
            return null;
        }

        return [
            'latitude' => $lat,
            'longitude' => $lng,
            'maps_query' => isset($decoded['maps_query']) && is_string($decoded['maps_query']) && trim($decoded['maps_query']) !== ''
                ? trim($decoded['maps_query'])
                : $alamatLengkap,
        ];
    }

    public function getAveragePriceSummary(string $tipe, string $kategori, string $alamat, ?float $hargaListing = null, ?int $luasTanah = null, ?int $luasBangunan = null): ?string
    {
        $hargaText = $hargaListing !== null ? number_format($hargaListing, 0, ',', '.') : 'tidak diketahui';
        $tipeText = strtolower($tipe) === 'sewa' ? 'sewa' : 'jual';
        $ltText = $luasTanah !== null && $luasTanah > 0 ? $luasTanah . ' m²' : 'tidak diketahui';
        $lbText = $luasBangunan !== null && $luasBangunan > 0 ? $luasBangunan . ' m²' : 'tidak diketahui';

        $prompt = <<<PROMPT
Anda adalah AI Expert Analis Properti yang bertugas mencari harga pasar rata-rata properti dengan DATA SPESIFIK.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📊 DATA PROPERTI:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

🏠 Tipe Transaksi: {$tipeText}
🏘️ Kategori: {$kategori}
📍 Lokasi: {$alamat}
📐 Luas Tanah: {$ltText}
🏗️ Luas Bangunan: {$lbText}
💰 Harga Listing: Rp {$hargaText}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🔍 TUGAS PENCARIAN:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Cari harga pasar rata-rata {$tipeText} untuk {$kategori} dengan spesifikasi:
→ Lokasi: {$alamat}
→ Luas tanah: {$ltText}
→ Luas bangunan: {$lbText}

Sumber Data (Prioritas):
1. Rumah123.com - cari properti serupa dengan LT/LB yang mirip
2. 99.co - filter berdasarkan lokasi dan ukuran
3. Google Search - query: "harga {$kategori} {$alamat} LT {$ltText} LB {$lbText}"
4. OLX Properti - cari listing sejenis

Fokus: Cari properti dengan LUAS TANAH dan LUAS BANGUNAN yang SERUPA (±20%)

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📝 FORMAT OUTPUT (WAJIB):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

✅ MAKSIMAL 28 KATA (bukan 28 kalimat)
✅ Format Rupiah: Rp 500 juta, Rp 1,2 miliar, Rp 850 juta
✅ 1 kalimat ringkas dan informatif
✅ Sebutkan rentang harga berdasarkan ukuran properti

CONTOH OUTPUT YANG BENAR (tepat 18 kata):
"Harga rata-rata rumah untuk dijual di Bandung dengan LT 100 m² LB 160 m² berkisar Rp 800 juta hingga 1,5 miliar."

Jika data tidak tersedia:
"Data harga pasar untuk lokasi ini belum tersedia."

⚠️ PENTING: Hitung kata Anda! Maksimal 28 KATA!
PROMPT;

        return $this->generateContent($prompt);
    }

    public function getNearbyFacilitiesSummary(string $address): ?string
    {
        $prompt = <<<PROMPT
Anda adalah AI Expert Property yang bertugas mencari infrastruktur dan fasilitas di dalam area perumahan atau kompleks properti.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🏘️ AREA PERUMAHAN: "{$address}"
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

🎯 ATURAN PENCARIAN KRITIS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

✅ CARI infrastruktur dan fasilitas di dalam area perumahan "{$address}" dengan akurat.
✅ JANGAN CARI di luar area perumahan "{$address}".
✅ Cantumkan 8-12 fasilitas/infrastruktur yang terbaik di dalam area perumahan "{$address}".

❌ JANGAN cari fasilitas di:
   → Kecamatan 
   → Kabupaten   
   → Provinsi 
   → Area di luar perumahan.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📋 KATEGORI FASILITAS (Prioritas yang terbaik):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

1. 🌐 Internet Providers (dalam area)
2. 🚌 Transportasi Terdekat (halte, stasiun, terminal yang terdekat)
3. 🏫 Pendidikan (SD, SMP, SMA yang terbaik dan terdekat)
4. 🏥 Kesehatan (RS, Klinik, Puskesmas terbaik dan terdekat)
5. 🕌 Tempat Ibadah (Masjid, Gereja, dll dalam area)
6. 🛒 Komersial (Mall , Pasar, Kuliner, Minimarket di area perumahan)
7. 🏦 Keuangan (Bank, ATM di area perumahan)
8. 🏊 Rekreasi (Taman, Kolam Renang, GOR di area perumahan)
9. 🏢 Layanan Publik (Kantor Pos, Kelurahan di area perumahan)

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📤 FORMAT OUTPUT (HTML List):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

CONTOH OUTPUT YANG BENAR:
<ul>
<li>Internet: Myrepublic, FirstMedia, IndiHome</li>
<li>Transportasi: Halte Transjakarta, Stasiun KRL 2km</li>
<li>Pendidikan: BPK Penabur, SD Negeri 1 (500m), SMP Negeri 5 (1.2km)</li>
<li>Kesehatan: RS Hermina, RS Standard (1km), klinik Keluarga (400m)</li>
<li>Tempat Ibadah: Masjid Al-Ikhlas, Gereja HKBP (1km)</li>
<li>Komersial: Mall Laguboti, Alfamart (150m), Indomaret (300m), Giant (2km)</li>
<li>Keuangan: ATM BCA, Bank Standard Charter (1.5km)</li>
<li>Rekreasi: Taman Kota (600m), Lapangan Futsal (800m)</li>
<li>Layanan Publik: Kantor Pos (2.5km)</li>
</ul>

⚠️ ATURAN OUTPUT:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

✅ LANGSUNG mulai dengan <ul>
✅ JANGAN tambahkan kata sambutan
✅ JANGAN tambahkan penjelasan
✅ Setiap <li> harus memiliki kategori dan nama fasilitas
✅ Pilih yang terbaik
✅ Cantumkan jarak jika tersedia

Jika tidak ada data:
<ul><li>Data fasilitas untuk area ini sedang tidak tersedia</li></ul>

PROMPT;

        return $this->generateContent($prompt);
    }

    public function getDecorationAdvice(string $tipe, string $kategori, ?int $luasTanah = null, ?int $luasBangunan = null, ?int $bedrooms = null, ?int $bathrooms = null): ?string
    {
        $tipeText = strtolower($tipe) === 'sewa' ? 'disewa' : 'dijual';
        $ltText = $luasTanah !== null && $luasTanah > 0 ? $luasTanah . ' m²' : 'tidak diketahui';
        $lbText = $luasBangunan !== null && $luasBangunan > 0 ? $luasBangunan . ' m²' : 'tidak diketahui';
        $bedText = $bedrooms !== null ? $bedrooms . ' Kamar Tidur' : '';
        $bathText = $bathrooms !== null ? $bathrooms . ' Kamar Mandi' : '';

        $prompt = <<<PROMPT
Anda adalah AI Interior Designer Expert. Berikan saran dekorasi dan tata letak interior yang SPESIFIK untuk properti berikut:

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🏠 DATA PROPERTI:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
• Tipe: {$kategori} ({$tipeText})
• Luas Tanah: {$ltText}
• Luas Bangunan: {$lbText}
• Ruangan: {$bedText}, {$bathText}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🎨 TUGAS ANDA:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Berikan 3-5 ide dekorasi atau renovasi singkat yang dapat MENINGKATKAN NILAI properti atau KENYAMANAN penghuni.
Fokus pada pemanfaatan ruang (space saving), pencahayaan, dan tren desain terkini (Minimalis, Scandinavian, Industrial, dll) yang cocok dengan spesifikasi di atas.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📤 FORMAT OUTPUT (HTML List):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
<ul>
<li><strong>Konsep Ruang Tamu:</strong> Gunakan warna cat cerah...</li>
<li><strong>Optimasi Kamar Tidur:</strong> Pilih furniture multifungsi...</li>
<li><strong>Dapur Modern:</strong> Tambahkan backsplash motif...</li>
</ul>

⚠️ ATURAN:
• Langsung output <ul> tanpa pembuka/penutup.
• Maksimal 5 poin.
• Bahasa Indonesia yang menarik dan profesional.
PROMPT;

        return $this->generateContent($prompt);
    }

    public function askQuestion(string $prompt): ?string
    {
        return $this->generateContent($prompt);
    }

    public function parseSearchQuery(string $query): ?array
    {
        $prompt = <<<PROMPT
You are an AI Property Search Assistant. Your task is to extract search filters from a natural language query into a structured JSON format.

USER QUERY: "{$query}"

EXTRACT THESE FILTERS (if mentioned or implied):
- keywords: (string) The remaining search terms after extracting specific filters.
- harga_min: (number) Minimum price in IDR.
- harga_max: (number) Maximum price in IDR.
- lokasi: (string) Specific location name (city, district, area).
- kamar_tidur: (int) Number of bedrooms.
- kamar_mandi: (int) Number of bathrooms.
- lt_min: (int) Minimum land area (m2).
- lt_max: (int) Maximum land area (m2).
- lb_min: (int) Minimum building area (m2).
- lb_max: (int) Maximum building area (m2).
- kategori: (string) One of: "rumah", "apartemen", "ruko", "tanah".
- tipe: (string) One of: "jual", "sewa".
- surat: (string) Certificate type if mentioned (SHM, HGB).

RULES:
1. Convert all prices to full numbers (e.g., "3 Milyar" -> 3000000000).
2. If listing type is not specified, guess based on context (default to null if unsure).
3. "Keywords" should contain any descriptive terms not covered by other filters (e.g., "hook", "mewah", "dekat tol").
4. Return ONLY valid JSON. No markdown formatting.

EXAMPLE OUTPUT:
{
  "harga_max": 3000000000,
  "lokasi": "Kota Wisata",
  "kategori": "rumah",
  "tipe": "jual",
  "keywords": "siap huni"
}
PROMPT;

        $raw = $this->generateContent($prompt);
        return $this->decodeJson($raw);
    }

    public function generalChat(string $query): ?string
    {
        $prompt = <<<PROMPT
You are a helpful and professional AI Assistant for "Waisaka Property", a property marketplace application.
Your goal is to assist users with questions related to property, mortgages (KPR), investment, or general advice about buying/renting homes.

USER QUESTION: "{$query}"

INSTRUCTIONS:
1. Answer politely and professionally in Indonesian.
2. Keep the answer concise (max 3-4 paragraphs).
3. If the user asks about specific listings, explain that you can't search for specific ads right now, but you can give general advice.
4. Do NOT try to output JSON. Just plain text.

ANSWER:
PROMPT;

        return $this->generateContent($prompt);
    }

    protected function generateContent(string $prompt): ?string
    {
        if (empty($this->apiKey)) {
            Log::error('WaisakaAiService: GEMINI_API_KEY belum dikonfigurasi.');
            return null;
        }

        try {
            $response = Http::asJson()
                ->timeout($this->timeout > 0 ? $this->timeout : 60)
                ->retry($this->retries, 300)
                ->post($this->buildEndpoint(), [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'topP' => 0.8,
                        'maxOutputTokens' => 2048,
                    ],
                ])
                ->throw();
        } catch (RequestException $e) {
            Log::error('WaisakaAiService: Gemini API error', [
                'status' => optional($e->response)->status(),
                'body' => optional($e->response)->body(),
                'prompt_length' => strlen($prompt),
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::error('WaisakaAiService: gagal terhubung ke Gemini', [
                'error' => $e->getMessage(),
                'timeout' => $this->timeout,
            ]);
            return null;
        }

        $data = $response->json();
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$text) {
            Log::warning('WaisakaAiService: tidak ada teks yang dikembalikan', [
                'response' => $data,
                'model' => $this->model,
            ]);
            return null;
        }

        return trim($text);
    }

    protected function buildEndpoint(): string
    {
        return sprintf(
            '%s/models/%s:generateContent?key=%s',
            $this->baseUrl,
            $this->model,
            urlencode($this->apiKey)
        );
    }

    protected function decodeJson(string $raw): ?array
    {
        $raw = trim($raw);

        if (Str::startsWith($raw, '```')) {
            $raw = preg_replace('/^```[a-zA-Z0-9]*\n|\n```$/', '', $raw);
            $raw = trim((string) $raw);
        }

        if (!Str::startsWith($raw, '{')) {
            $start = strpos($raw, '{');
            $end = strrpos($raw, '}');
            if ($start === false || $end === false || $end <= $start) {
                return null;
            }
            $raw = substr($raw, $start, $end - $start + 1);
        }

        $decoded = json_decode($raw, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }
}
