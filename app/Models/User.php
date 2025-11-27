<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    protected $fillable = [
        'nama',
        'email', 
        'username',
        'password',
        'akses_level',
        'gambar',
        'paket_id',
        'approved_by'
    ];

    // kategori
    public function login($username,$password)
    {
        $query = DB::table('users')
            ->select('*')
            ->where(array(  'users.username'	=> $username,
                            'users.password'    => sha1($password)))
            ->orderBy('id_user','DESC')
            ->first();
        return $query;
    }

    // Relationship dengan staff
    public function staff()
    {
        return $this->hasOne('App\Models\Staff', 'id_user', 'id_user');
    }

    // Relationship dengan transaksi paket
    // ✅ FIXED: Changed foreign key from 'user_id' to 'id_user' (sesuai database)
    public function transaksiPaket()
    {
        return $this->hasMany('App\Models\TransaksiPaket', 'id_user', 'id_user');
    }
}
