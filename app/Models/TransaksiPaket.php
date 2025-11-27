<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPaket extends Model
{
    protected $table = 'transaksi_paket';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'id_user',  // ✅ FIXED: Changed from 'user_id' to 'id_user' (sesuai database)
        'id_staff',
        'paket_id',
        'kode_transaksi',
        'status_pembayaran',
        'bukti_pembayaran',
        'keterangan',  // ✅ ADDED: Missing field from database
        'dikonfirmasi_oleh',
        'tanggal_konfirmasi'
    ];

    protected $casts = [
        'tanggal_konfirmasi' => 'datetime',
    ];

    // Relationship dengan user
    // ✅ FIXED: Changed foreign key from 'user_id' to 'id_user'
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id_user');
    }

    // Relationship dengan staff
    public function staff()
    {
        return $this->belongsTo('App\Models\Staff', 'id_staff', 'id_staff');
    }

    // Relationship dengan paket iklan
    public function paketIklan()
    {
        return $this->belongsTo('App\Models\PaketIklan', 'paket_id', 'id');
    }
}
