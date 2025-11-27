<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
// use Intervention\Image\Facades\Image; // Package tidak tersedia
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

class User extends Controller
{

    public function index()
    {
    	if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
		$user 	= DB::table('users')->orderBy('id_user','DESC')->get();

		$data = array(  'title'     => 'Pengguna Website',
						'user'      => $user,
                        'content'   => 'admin/user/index'
                    );
        return view('admin/layout/wrapper',$data);
    }

    
    public function edit($id_user)
    {
        if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
        $user   = DB::table('users')->where('id_user',$id_user)->orderBy('id_user','DESC')->first();

        $data = array(  'title'     => 'Edit Pengguna Website',
                        'user'      => $user,
                        'content'   => 'admin/user/edit'
                    );
        return view('admin/layout/wrapper',$data);
    }

   public function proses(Request $request)
    {
        $site   = DB::table('konfigurasi')->first();
        
        if(isset($_POST['hapus'])) {
            $id_usernya       = $request->id_user;
            for($i=0; $i < sizeof($id_usernya);$i++) {
                $this->_deleteUserData($id_usernya[$i]);
            }
            return redirect('admin/user')->with(['sukses' => 'Data telah dihapus']);
        }
    }


    public function tambah(Request $request)
    {
    	if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
    	request()->validate([
                            'nama'     => 'required',
					        'username' => 'required|unique:users',
					        'password' => 'required',
                            'email'    => 'required',
                            'gambar'   => 'file|image|mimes:jpeg,png,jpg|max:8024',
					        ]);
        // UPLOAD START
        $image                  = $request->file('gambar');
        if(!empty($image)) {
            $filenamewithextension  = $request->file('gambar')->getClientOriginalName();
            $filename               = pathinfo($filenamewithextension, PATHINFO_FILENAME);
            $input['nama_file']     = Str::slug($filename, '-').'-'.time().'.'.$image->getClientOriginalExtension();
            
            // Upload gambar asli
            $destinationPath = './assets/upload/user/';
            $image->move($destinationPath, $input['nama_file']);
            
            // Buat thumbnail menggunakan GD library
            try {
                $this->createThumbnail($destinationPath . $input['nama_file'], './assets/upload/user/thumbs/' . $input['nama_file'], 150, 150);
            } catch (\Exception $e) {
                // Log error but continue with user creation
                \Log::warning('Failed to create thumbnail: ' . $e->getMessage());
            }
            // END UPLOAD
            DB::table('users')->insert([
                'nama'          => $request->nama,
                'email'	        => $request->email,
                'username'   	=> $request->username,
                'password'      => sha1($request->password),
                'akses_level'   => $request->akses_level,
                'gambar'        => $input['nama_file']
            ]);
        }else{
             DB::table('users')->insert([
                'nama'          => $request->nama,
                'email'         => $request->email,
                'username'      => $request->username,
                'password'      => sha1($request->password),
                'akses_level'   => $request->akses_level
            ]);
        }
        return redirect('admin/user')->with(['sukses' => 'Data telah ditambah']);
    }

    // edit
    public function proses_edit(Request $request)
    {
    	if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
    	request()->validate([
					        'nama'     => 'required',
                            'username' => 'required',
                            'password' => 'required|min:6', // Password wajib diisi, minimal 6 karakter
                            'email'    => 'required',
                            'gambar'   => 'file|image|mimes:jpeg,png,jpg|max:8024',
					        ]);
        // UPLOAD START
        $image                  = $request->file('gambar');
        if(!empty($image)) {
            // UPLOAD START
            $filenamewithextension  = $request->file('gambar')->getClientOriginalName();
            $filename               = pathinfo($filenamewithextension, PATHINFO_FILENAME);
            $input['nama_file']     = Str::slug($filename, '-').'-'.time().'.'.$image->getClientOriginalExtension();
            
            // Upload gambar asli
            $destinationPath = './assets/upload/user/';
            $image->move($destinationPath, $input['nama_file']);
            
            // Buat thumbnail menggunakan GD library
            $this->createThumbnail($destinationPath . $input['nama_file'], './assets/upload/user/thumbs/' . $input['nama_file'], 150, 150);
            // END UPLOAD
            $slug_user = Str::slug($request->nama, '-');
            
            // Update data user dengan gambar
            DB::table('users')->where('id_user',$request->id_user)->update([
                'nama'          => $request->nama,
                'email'         => $request->email,
                'username'      => $request->username,
                'password'      => sha1($request->password),
                'akses_level'   => $request->akses_level,
                'gambar'        => $input['nama_file']
            ]);
        }else{
            $slug_user = Str::slug($request->nama, '-');
            
            // Update data user tanpa gambar
            DB::table('users')->where('id_user',$request->id_user)->update([
                'nama'          => $request->nama,
                'email'         => $request->email,
                'username'      => $request->username,
                'password'      => sha1($request->password),
                'akses_level'   => $request->akses_level
            ]);
        }
        return redirect('admin/user')->with(['sukses' => 'Data telah diupdate']);
    }


     // --- FUNGSI BARU UNTUK MENAMPILKAN HALAMAN SIGN UP ---
    public function signup()
    {
        // Cukup tampilkan halaman view untuk pendaftaran
        return view('admin/user/signup');
    }


    // --- FUNGSI PROSES SIGN UP DIPERBARUI ---
    public function proses_signup(Request $request)
    {
    	request()->validate([
            'nama'     => 'required',
            'username' => 'required|unique:users,username|unique:users_verification,username',
            'password' => 'required|min:6',
            'email'    => 'required|email|unique:users,email|unique:users_verification,email',
            'gambar'   => 'nullable|file|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $token = Str::random(60); // Buat token verifikasi acak

        // Siapkan data untuk disimpan di tabel verifikasi
        $data = [
            'nama'          => $request->nama,
            'email'	        => $request->email,
            'username'   	=> $request->username,
            'password'      => sha1($request->password),
            'token'         => $token,
            'tanggal_expired' => Carbon::now()->addDays(3), // Set expired 3 hari dari sekarang
            'created_at'    => now(),
            'updated_at'    => now()
        ];

        // Proses upload gambar jika ada
        $image = $request->file('gambar');
        if(!empty($image)) {
            $filenamewithextension  = $request->file('gambar')->getClientOriginalName();
            $filename               = pathinfo($filenamewithextension, PATHINFO_FILENAME);
            $input['nama_file']     = Str::slug($filename, '-').'-'.time().'.'.$image->getClientOriginalExtension();
            $destinationPath        = './assets/upload/user/';
            $image->move($destinationPath, $input['nama_file']);
            $data['gambar'] = $input['nama_file'];
        }

        // Simpan data ke tabel users_verification
        DB::table('users_verification')->insert($data);

        // Kirim email verifikasi menggunakan Mailable class
        try {
            $user = new \stdClass();
            $user->nama = $request->nama;
            $user->email = $request->email;
            
            Mail::to($request->email)->send(new \App\Mail\VerificationEmail($user, $token));
        } catch (\Exception $e) {
            \Log::error('Email verification failed to send: '.$e->getMessage());
        }

        return redirect('login')->with(['sukses' => 'Pendaftaran berhasil. Silakan cek email Anda untuk link aktivasi yang berlaku selama 3 hari.']);
    }

    // --- FUNGSI BARU UNTUK MENANGANI VERIFIKASI ---
    public function verifyEmail(Request $request)
    {
        $token = $request->get('token');
        $verification_data = DB::table('users_verification')->where('token', $token)->first();

        // Cek jika token tidak ada
        if(!$verification_data) {
            return redirect('login')->with(['warning' => 'Token verifikasi tidak valid.']);
        }

        // Cek jika token sudah expired
        if(Carbon::now()->gt($verification_data->tanggal_expired)) {
            // Hapus data yang sudah expired
            DB::table('users_verification')->where('token', $token)->delete();
            return redirect('login')->with(['warning' => 'Waktu pendaftaran Anda sudah berakhir (expired). Silakan daftar kembali.']);
        }

        // Jika valid, pindahkan data ke tabel users dan dapatkan ID-nya
        $newUserId = DB::table('users')->insertGetId([
            'nama'          => $verification_data->nama,
            'email'         => $verification_data->email,
            'username'      => $verification_data->username,
            'password'      => $verification_data->password,
            'gambar'        => $verification_data->gambar,
            'akses_level'   => 'User'
        ]);

        // Ambil urutan terakhir dari tabel staff
        $lastUrutan = DB::table('staff')->max('urutan') ?? 0;
        $newUrutan = $lastUrutan + 1;

        // Buat entri staff baru untuk user ini dan dapatkan ID-nya
        $newStaffId = DB::table('staff')->insertGetId([
            'id_user'           => $newUserId,
            'nama_staff'        => $verification_data->nama,
            'email'             => $verification_data->email,
            'status_staff'      => 'Tidak', // "Tidak" berarti belum dikonfirmasi oleh admin
            'slug_staff'        => Str::slug($verification_data->nama, '-'),
            'id_kategori_staff' => 1, // Asumsi default kategori staff adalah 1 (misal: "Agent Baru")
            'nickname_staff'    => $verification_data->nama, // Gunakan nama sebagai nickname default
            'id_provinsi'       => 1, // Default provinsi (bisa disesuaikan)
            'id_kabupaten'      => 1, // Default kabupaten (bisa disesuaikan)
            'id_kecamatan'      => 1, // Default kecamatan (bisa disesuaikan)
            'total_kuota_iklan' => 0,
            'sisa_kuota_iklan'  => 0,
            'urutan'            => $newUrutan // Set urutan sebagai yang terakhir
        ]);

        // Ambil data paket yang dibeli
        $paket = DB::table('paket_iklan')->where('id', $verification_data->paket_id)->first();

        // Buat entri transaksi untuk pembelian paket
        if ($paket) {
            DB::table('transaksi_paket')->insert([
                'id_user'           => $newUserId,
                'id_staff'          => $newStaffId,
                'paket_id'          => $verification_data->paket_id,
                'kode_transaksi'    => 'WPM-' . strtoupper(Str::random(8)),
                'status_pembayaran' => 'pending', // Menunggu konfirmasi admin
                'bukti_pembayaran'  => $verification_data->gambar, // Path dari kolom gambar
                'keterangan'        => 'Pembelian paket ' . $paket->nama_paket . ' saat registrasi', // ✅ ADDED: Required field
                'created_at'        => now(),
                'updated_at'        => now()
            ]);
        }

        // Hapus data dari tabel verifikasi
        DB::table('users_verification')->where('token', $token)->delete();

        // Redirect ke halaman website utama dengan pesan sukses yang informatif
        return redirect('/')->with(['sukses' => 'Verifikasi berhasil! Akun Anda sudah aktif. Silakan login untuk melihat status konfirmasi pembelian paket Anda.']);
    }

    // Delete (FUNGSI YANG DIPERBAIKI)
    public function delete($id_user)
    {
        if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
        
        $this->_deleteUserData($id_user);

    	return redirect('admin/user')->with(['sukses' => 'Data pengguna beserta semua data terkait telah berhasil dihapus.']);
    }

    // FUNGSI PRIVATE UNTUK LOGIKA PENGHAPUSAN
    private function _deleteUserData($id_user)
    {
        // 1. Ambil data user & staff
        $user = DB::table('users')->where('id_user', $id_user)->first();
        if (!$user) {
            return; // Jika user tidak ada, hentikan proses
        }
        $staff = DB::table('staff')->where('id_user', $id_user)->first();

        // 2. Hapus data properti dan gambar properti (jika user adalah staff)
        if ($staff) {
            $properties = DB::table('property_db')->where('id_staff', $staff->id_staff)->get();
            foreach ($properties as $property) {
                $propertyImages = DB::table('property_img')->where('id_property', $property->id_property)->get();
                foreach ($propertyImages as $image) {
                    File::delete(public_path('assets/upload/property/' . $image->gambar));
                }
                DB::table('property_img')->where('id_property', $property->id_property)->delete();
            }
            DB::table('property_db')->where('id_staff', $staff->id_staff)->delete();
            
            // Hapus foto staff
            File::delete(public_path('assets/upload/staff/' . $staff->gambar));
            File::delete(public_path('assets/upload/staff/thumbs/' . $staff->gambar));
        }

        // 3. Hapus data transaksi dan bukti pembayaran
        $transactions = DB::table('transaksi_paket')->where('id_user', $id_user)->get();
        foreach ($transactions as $transaction) {
            File::delete(public_path('assets/upload/bukti/' . $transaction->bukti_pembayaran));
        }
        DB::table('transaksi_paket')->where('id_user', $id_user)->delete();

        // 4. Hapus foto profil user
        File::delete(public_path('assets/upload/user/' . $user->gambar));
        File::delete(public_path('assets/upload/user/thumbs/' . $user->gambar));

        // 5. Hapus data dari tabel staff dan users
        DB::table('staff')->where('id_user', $id_user)->delete();
        DB::table('users')->where('id_user', $id_user)->delete();
    }

    /**
     * Membuat thumbnail menggunakan GD library
     */
    private function createThumbnail($sourcePath, $destinationPath, $width, $height)
    {
        try {
            // Pastikan folder tujuan ada
            $destinationDir = dirname($destinationPath);
            if (!file_exists($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }

            // Dapatkan informasi gambar
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) {
                return false;
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to create thumbnail directory or get image info: ' . $e->getMessage());
            return false;
        }

        try {
            $sourceWidth = $imageInfo[0];
            $sourceHeight = $imageInfo[1];
            $mimeType = $imageInfo['mime'];

            // Buat resource gambar berdasarkan tipe
            switch ($mimeType) {
                case 'image/jpeg':
                    $sourceImage = imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($sourcePath);
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($sourcePath);
                    break;
                default:
                    return false;
            }

            if (!$sourceImage) {
                return false;
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to create image resource: ' . $e->getMessage());
            return false;
        }

        try {
            // Hitung rasio untuk resize proporsional
            $ratio = min($width / $sourceWidth, $height / $sourceHeight);
            $newWidth = (int)($sourceWidth * $ratio);
            $newHeight = (int)($sourceHeight * $ratio);

            // Buat gambar baru
            $thumbnail = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency untuk PNG
            if ($mimeType == 'image/png') {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
                imagefilledrectangle($thumbnail, 0, 0, $newWidth, $newHeight, $transparent);
            }

            // Resize gambar
            imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

            // Simpan thumbnail
            $result = false;
            switch ($mimeType) {
                case 'image/jpeg':
                    $result = imagejpeg($thumbnail, $destinationPath, 90);
                    break;
                case 'image/png':
                    $result = imagepng($thumbnail, $destinationPath, 9);
                    break;
                case 'image/gif':
                    $result = imagegif($thumbnail, $destinationPath);
                    break;
            }

            // Bersihkan memory
            imagedestroy($sourceImage);
            imagedestroy($thumbnail);

            return $result;
        } catch (\Exception $e) {
            \Log::warning('Failed to process thumbnail: ' . $e->getMessage());
            return false;
        }
    }
}
