<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Staff extends Model
{
    protected $table = 'staff';
    protected $primaryKey = 'id_staff';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_kategori_staff',
        'slug_staff',
        'nama_staff',
        'jabatan',
        'pendidikan',
        'expertise',
        'email',
        'telepon',
        'isi',
        'gambar',
        'status_staff',
        'keywords',
        'urutan',
        'nickname_staff',
        'id_provinsi',
        'id_kabupaten',
        'id_kecamatan',
        'total_kuota_iklan',
        'sisa_kuota_iklan'
    ];

    // Method untuk mengambil semua data staff
    public function semua()
    {
        $query = DB::table('staff')
            ->join('kategori_staff', 'kategori_staff.id_kategori_staff', '=', 'staff.id_kategori_staff', 'LEFT')
            ->join('users', 'users.id_user', '=', 'staff.id_user', 'LEFT')
            ->join('provinsi', 'provinsi.id', '=', 'staff.id_provinsi', 'LEFT')
            ->join('kabupaten', 'kabupaten.id', '=', 'staff.id_kabupaten', 'LEFT')
            ->join('kecamatan', 'kecamatan.id', '=', 'staff.id_kecamatan', 'LEFT')
            ->select(
                'staff.*', 
                'kategori_staff.nama_kategori_staff', 
                'users.nama as nama_user',
                'users.email as email_user',
                'provinsi.nama as nama_provinsi', 
                'kabupaten.nama as nama_kabupaten', 
                'kecamatan.nama as nama_kecamatan'
            )
            ->orderBy('staff.urutan', 'ASC')
            ->orderBy('staff.id_staff', 'DESC')
            ->get();
        return $query;
    }

    // Method untuk mengambil detail staff berdasarkan ID
    public function detail($id_staff)
    {
        $query = DB::table('staff')
            ->join('kategori_staff', 'kategori_staff.id_kategori_staff', '=', 'staff.id_kategori_staff', 'LEFT')
            ->join('users', 'users.id_user', '=', 'staff.id_user', 'LEFT')
            ->join('provinsi', 'provinsi.id', '=', 'staff.id_provinsi', 'LEFT')
            ->join('kabupaten', 'kabupaten.id', '=', 'staff.id_kabupaten', 'LEFT')
            ->join('kecamatan', 'kecamatan.id', '=', 'staff.id_kecamatan', 'LEFT')
            ->select(
                'staff.*', 
                'kategori_staff.nama_kategori_staff', 
                'users.nama as nama_user',
                'users.email as email_user',
                'provinsi.nama as nama_provinsi', 
                'kabupaten.nama as nama_kabupaten', 
                'kecamatan.nama as nama_kecamatan'
            )
            ->where('staff.id_staff', $id_staff)
            ->first();
        return $query;
    }

    // Method untuk mencari staff berdasarkan keywords
    public function cari($keywords)
    {
        $query = DB::table('staff')
            ->join('kategori_staff', 'kategori_staff.id_kategori_staff', '=', 'staff.id_kategori_staff', 'LEFT')
            ->join('users', 'users.id_user', '=', 'staff.id_user', 'LEFT')
            ->join('provinsi', 'provinsi.id', '=', 'staff.id_provinsi', 'LEFT')
            ->join('kabupaten', 'kabupaten.id', '=', 'staff.id_kabupaten', 'LEFT')
            ->join('kecamatan', 'kecamatan.id', '=', 'staff.id_kecamatan', 'LEFT')
            ->select(
                'staff.*', 
                'kategori_staff.nama_kategori_staff', 
                'users.nama as nama_user',
                'users.email as email_user',
                'provinsi.nama as nama_provinsi', 
                'kabupaten.nama as nama_kabupaten', 
                'kecamatan.nama as nama_kecamatan'
            )
            ->whereRaw("(staff.nama_staff LIKE '%".$keywords."%' OR staff.jabatan LIKE '%".$keywords."%' OR staff.expertise LIKE '%".$keywords."%' OR staff.isi LIKE '%".$keywords."%')")
            ->orderBy('staff.urutan', 'ASC')
            ->orderBy('staff.id_staff', 'DESC')
            ->get();
        return $query;
    }

    // Relationship dengan user
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id_user');
    }

    // Relationship dengan transaksi paket
    public function transaksiPaket()
    {
        return $this->hasMany('App\Models\TransaksiPaket', 'id_staff', 'id_staff');
    }
}
