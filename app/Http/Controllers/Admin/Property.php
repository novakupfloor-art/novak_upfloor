<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Image;
use App\Models\Property as PropertyModel;
use App\Models\Staff as StaffModel;
use App\Services\WaisakaAiService;

class Property extends Controller
{
    // Main page
    public function index()
    {
    	if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
    	$myproperty 	    = new PropertyModel();
		$property 			= $myproperty->semua();
        $tipe               = 'all';
		$kategori_property  = DB::table('kategori_property')->orderBy('urutan','ASC')->get();

        // Tambahkan statistik seperti di API
        $totalProperties = DB::table('property_db')->count();
        $activeProperties = DB::table('property_db')->where('status', 1)->count();
        $totalStaff = DB::table('staff')->count();

		$data = array(  'title'				=> 'Data Property',
						'property'		    => $property,
						'kategori_property'	=> $kategori_property,
                        'tipe'              => $tipe,
                        'statistics'        => [
                            'total_properties' => $totalProperties,
                            'active_properties' => $activeProperties,
                            'total_staff' => $totalStaff
                        ],
                        'content'			=> 'admin/property/index'
                    );
        return view('admin/layout/wrapper',$data);
    }

    // Main page
    public function detail($id_property)
    {
        if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
        $myproperty = new PropertyModel();
        $property   = $myproperty->detail($id_property);
        $images     = DB::table('property_img')->where('id_property',$property->id_property)->orderBy('id_property_img')->get();
        $mystaff        = new Staff();
        $staff          = $mystaff->detail($property->id_staff);

        $gambar = [];
        foreach($images as $key => $img) {
            $gambar[$key] = $img;
        }

        $data = array(  'title'             => $property->nama_property,
                        'property'          => $property,
                        'gambar'            => $gambar,
                        'staff'             => $staff,
                        'content'           => 'admin/property/detail'
                    );
        return view('admin/layout/wrapper',$data);
    }

    // Cari
    public function cari(Request $request)
    {
        if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
        $myproperty        = new PropertyModel();
        $keywords          = $request->keywords;
        $tipe              = $request->tipe;
        $property          = $myproperty->cari($keywords,$tipe);
        $kategori_property = DB::table('kategori_property')->orderBy('urutan','ASC')->get();
        $data = array(  'title'             => 'Data Property',
                        'property'          => $property,
                        'tipe'              => $tipe,
                        'kategori_property' => $kategori_property,
                        'content'           => 'admin/property/index'
                    );
        return view('admin/layout/wrapper',$data);
    }

    // Proses
    public function proses(Request $request)
    {
        $site = DB::table('konfigurasi')->first();
        // PROSES HAPUS MULTIPLE
        if(isset($_POST['hapus'])) {
            $id_property = $request->id_property;
            for($i=0; $i < sizeof($id_property);$i++) {
                DB::table('property_db')->where('id_property',$id_property[$i])->delete();
            }
            return redirect('admin/property')->with(['sukses' => 'Data telah dihapus']);
        // PROSES SETTING DRAFT
        }elseif(isset($_POST['update'])) {
            $id_property = $request->id_property;
            for($i=0; $i < sizeof($id_property);$i++) {
                DB::table('property_db')->where('id_property',$id_property[$i])->update([
                        'id_property'          => Session()->get('id_property'),
                        'id_kategori_property' => $request->id_kategori_property
                    ]);
            }
            return redirect('admin/property')->with(['sukses' => 'Data kategori telah diubah']);
        }
    }

    //Kategori
    public function kategori($id_kategori_property)
    {
        if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
        $myproperty        = new PropertyModel();
        $property          = $myproperty->all_kategori_property($id_kategori_property);
        $tipe              = 'all';
        $kategori_property = DB::table('kategori_property')->orderBy('urutan','ASC')->get();

        $data = array(  'title'             => 'Data Property',
                        'property'          => $property,
                        'tipe'              => $tipe,
                        'kategori_property' => $kategori_property,
                        'content'           => 'admin/property/index'
                    );
        return view('admin/layout/wrapper',$data);
    }

    // Tambah
    public function tambah()
    {
        if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
        $kategori_property = DB::table('kategori_property')->orderBy('urutan','ASC')->get();
        $staff = DB::table('staff')->orderBy('nama_staff','ASC')->get();
        $provinsi = DB::table('provinsi')->orderBy('nama','ASC')->get();

        $data = array(  'title'             => 'Tambah Property',
                        'kategori_property' => $kategori_property,
                        'provinsi'          => $provinsi,
                        'staff'             => $staff,
                        'content'           => 'admin/property/tambah'
                    );
        return view('admin/layout/wrapper',$data);
    }

    // edit
    public function edit($id_property)
    {
        if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
        $myproperty        = new PropertyModel();
        $property          = $myproperty->detail($id_property);
        $kategori_property = DB::table('kategori_property')->orderBy('urutan','ASC')->get();
        $staff             = DB::table('staff')->orderBy('nama_staff','ASC')->get();
        $images     = DB::table('property_img')->where('id_property',$property->id_property)->orderBy('id_property_img')->get();
        $provinsi   = DB::table('provinsi')->orderBy('nama','ASC')->get();
        $kabupaten  = DB::table('kabupaten')->where('id_provinsi',$property->id_provinsi)->orderBy('nama','ASC')->get();
        $kecamatan  = DB::table('kecamatan')->where('id_kabupaten',$property->id_kabupaten)->orderBy('nama','ASC')->get();

        $gambar = [];
        foreach($images as $key => $img) {
            $gambar[$key] = $img;
        }

        for($i=0;$i<10;$i++) {
            $gambar_v[$i] = isset($gambar[$i]) ? $gambar[$i]->gambar : ''; 
        }

        $data = array(  'title'             => 'Edit Property',
                        'property'          => $property,
                        'kategori_property' => $kategori_property,
                        'provinsi'          => $provinsi,
                        'kabupaten'         => $kabupaten,
                        'kecamatan'         => $kecamatan,
                        'gambar'            => $gambar_v,
                        'staff'             => $staff,
                        'content'           => 'admin/property/edit'
                    );
        return view('admin/layout/wrapper',$data);
    }

    // tambah
    public function tambah_proses(Request $request)
    {
        if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
        request()->validate([
                    'kode'          => 'required|unique:property_db',
                    'nama_property' => 'required|unique:property_db',
                    'lt'            => 'required|numeric',
                    'lb'            => 'required|numeric',
                    'harga'         => 'required|numeric',
                    'id_provinsi'   => 'required',
                    'id_kabupaten'  => 'required',
                    'id_kecamatan'  => 'required',
                    'harga'         => 'required|numeric',
                    'lantai'        => 'numeric',
                    'keywords'      => 'required',
                ]);
        
        $slug_property = Str::slug($request->nama_property);
        $id_property = DB::table('property_db')->insertGetId([
            'id_kategori_property' => $request->id_kategori_property,
            'kode'                 => $request->kode,
            'slug_property'        => $slug_property,
            'nama_property'        => $request->nama_property,
            'tipe'                 => $request->tipe,
            'jenis_sewa'           => $request->jenis_sewa,
            'harga'                => $request->harga,
            'status'               => $request->status,
            'surat'                => $request->surat,
            'lt'                   => $request->lt,
            'lb'                   => $request->lb,
            'isi'                  => $request->isi,
            'kamar_tidur'          => $request->kamar_tidur,
            'kamar_mandi'          => $request->kamar_mandi,
            'lantai'               => $request->lantai,
            'id_staff'             => $request->id_staff,
            'alamat'               => $request->alamat,
            'id_provinsi'          => $request->id_provinsi,
            'id_kabupaten'         => $request->id_kabupaten,
            'id_kecamatan'         => $request->id_kecamatan,
            'keywords'             => $request->keywords
        ]);

        // UPLOAD START
        $images = $request->file('gambar');
        foreach($request->gambar as $key => $val) {
            $image = $images[$key];
            if(!empty($image)) {
                $filenamewithextension  = $image->getClientOriginalName();
                $filename               = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                $input['nama_file']     = Str::slug($filename, '-').'-'.time().'.'.$image->getClientOriginalExtension();
                $destinationPath = './assets/upload/property/';
                $image->move($destinationPath, $input['nama_file']);
            }

            DB::table('property_img')->insert([
                'id_property'   => $id_property,
                'gambar'        => $input['nama_file'],
                'index_img'     => $key
            ]);
        }

        // END UPLOAD

        // AUTO-GENERATE AI INSIGHTS (Harga Rata, Fasilitas Terdekat, Peta Map)
        $this->generateAiInsights($id_property);

        return redirect('admin/property')->with(['sukses' => 'Data telah ditambah']);
    }

    // edit
    public function edit_proses(Request $request)
    {
        if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
        request()->validate([
                                'kode'          => 'required',
                                'nama_property' => 'required',
                                'lt'            => 'required|numeric',
                                'lb'            => 'required|numeric',
                                'harga'         => 'required|numeric',
                                'id_provinsi'   => 'required',
                                'id_kabupaten'  => 'required',
                                'id_kecamatan'  => 'required',
                                'harga'         => 'required|numeric',
                                'lantai'        => 'numeric',
                                'keywords'      => 'required',
                            ]);
       
        $slug_property = Str::slug($request->nama_property);
        DB::table('property_db')->where('id_property',$request->id_property)->update([
            'id_kategori_property' => $request->id_kategori_property,
            'kode'                 => $request->kode,
            'slug_property'        => $slug_property,
            'nama_property'        => $request->nama_property,
            'tipe'                 => $request->tipe,
            'jenis_sewa'           => $request->jenis_sewa,
            'harga'                => $request->harga,
            'status'               => $request->status,
            'surat'                => $request->surat,
            'lt'                   => $request->lt,
            'lb'                   => $request->lb,
            'isi'                  => $request->isi,
            'kamar_tidur'          => $request->kamar_tidur,
            'kamar_mandi'          => $request->kamar_mandi,
            'lantai'               => $request->lantai,
            'id_staff'             => $request->id_staff,
            'alamat'               => $request->alamat,
            'id_provinsi'          => $request->id_provinsi,
            'id_kabupaten'         => $request->id_kabupaten,
            'id_kecamatan'         => $request->id_kecamatan,
            'keywords'             => $request->keywords
        ]);
        
        // UPLOAD START
        $images = $request->file('gambar');
        if($request->gambar) {
            foreach($request->gambar as $key => $val) {
                $image = $images[$key];
                if(!empty($image)) {
                    $filenamewithextension  = $image->getClientOriginalName();
                    $filename               = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                    $input['nama_file']     = Str::slug($filename, '-').'-'.time().'.'.$image->getClientOriginalExtension();
                    $destinationPath = './assets/upload/property/';
                    $image->move($destinationPath, $input['nama_file']);
                }

                DB::table('property_img')->updateOrInsert([
                    'id_property'   => $request->id_property,
                    'index_img'     => $key
                ],[
                    'gambar'        => $input['nama_file']
                ]);
            }
        }

        // END UPLOAD

        // AUTO-GENERATE AI INSIGHTS (Harga Rata, Fasilitas Terdekat, Peta Map)
        // Hanya generate jika field masih kosong
        $this->generateAiInsights($request->id_property);

        return redirect('admin/property')->with(['sukses' => 'Data telah ditambah']);
    }

    // Delete
    public function delete($id_property)
    {
        if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
        DB::table('property_db')->where('id_property',$id_property)->delete();
        DB::table('property_img')->where('id_property',$id_property)->delete();
        return redirect('admin/property')->with(['sukses' => 'Data telah dihapus']);
    }

    /**
     * Generate AI insights untuk property (Harga Rata, Fasilitas Terdekat, Peta Map)
     * Dipanggil saat tambah atau edit property
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
                }
            }

            // 2. Generate Fasilitas Terdekat (jika belum ada)
            if (empty(trim($property->fasilitas_terdekat ?? ''))) {
                $fasilitasTerdekat = $this->getFasilitasTerdekat($property, $aiService);
                if ($fasilitasTerdekat) {
                    $updateData['fasilitas_terdekat'] = $fasilitasTerdekat;
                    Log::info("AI Fasilitas Terdekat generated untuk property ID {$id_property}");
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
     */
    protected function getHargaRata($property, WaisakaAiService $aiService)
    {
        try {
            $tipe = $property->tipe ?? 'Jual';
            $kategori = $property->nama_kategori_property ?? 'Properti';

            // Susun alamat lengkap
            $addressParts = array_filter([
                $property->alamat,
                $property->nama_kecamatan,
                $property->nama_kabupaten,
                $property->nama_provinsi
            ]);
            $alamatLengkap = implode(', ', $addressParts);

            if (empty($alamatLengkap)) {
                return null;
            }

            $hargaListing = is_numeric($property->harga ?? null) ? (float) $property->harga : null;
            $luasTanah = is_numeric($property->lt ?? null) ? (int) $property->lt : null;
            $luasBangunan = is_numeric($property->lb ?? null) ? (int) $property->lb : null;

            // Panggil AI Service
            $priceSummary = $aiService->getAveragePriceSummary(
                $tipe,
                $kategori,
                $alamatLengkap,
                $hargaListing,
                $luasTanah,
                $luasBangunan
            );

            if (empty($priceSummary)) {
                return null;
            }

            // Bersihkan hasil
            $cleanPriceSummary = trim(strip_tags($priceSummary));

            // Jangan simpan jika data tidak tersedia
            if (Str::startsWith($cleanPriceSummary, 'Data harga pasar untuk lokasi ini belum tersedia') ||
                Str::startsWith($cleanPriceSummary, 'Data pasar untuk lokasi ini belum tersedia')) {
                return null;
            }

            return $cleanPriceSummary;

        } catch (\Exception $e) {
            Log::error("Error getHargaRata: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Fasilitas Terdekat dari AI Waisaka
     */
    protected function getFasilitasTerdekat($property, WaisakaAiService $aiService)
    {
        try {
            // Susun alamat lengkap
            $addressParts = array_filter([
                $property->alamat,
                $property->nama_kecamatan,
                $property->nama_kabupaten,
                $property->nama_provinsi
            ]);
            $alamatLengkap = implode(', ', $addressParts);

            if (empty($alamatLengkap)) {
                return null;
            }

            // Panggil AI Service
            $summary = $aiService->getNearbyFacilitiesSummary($alamatLengkap);

            if (empty($summary)) {
                return null;
            }

            // Format to HTML list jika belum HTML
            if (stripos($summary, '<ul') === false) {
                $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', strip_tags($summary))));
                if (!empty($lines)) {
                    $items = array_map(fn($line) => '<li>' . htmlspecialchars($line) . '</li>', $lines);
                    $summary = "<ul>" . implode('', $items) . "</ul>";
                }
            }

            // Jangan simpan jika data tidak tersedia
            if (stripos($summary, 'Data fasilitas untuk area ini sedang tidak tersedia') !== false ||
                stripos($summary, 'tidak tersedia') !== false) {
                return null;
            }

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

            if (empty($address)) {
                return null;
            }

            // Panggil AI Service untuk mendapatkan koordinat
            $coordinates = $aiService->getMapCoordinateSummary($address, $kecamatan, $kabupaten, $provinsi);

            if (!$coordinates || !isset($coordinates['latitude'], $coordinates['longitude'])) {
                return null;
            }

            // Format sebagai JSON string untuk disimpan di database
            return json_encode([
                'latitude' => $coordinates['latitude'],
                'longitude' => $coordinates['longitude'],
                'maps_query' => $coordinates['maps_query'] ?? ''
            ]);

        } catch (\Exception $e) {
            Log::error("Error getPetaMap: " . $e->getMessage());
            return null;
        }
    }
}
