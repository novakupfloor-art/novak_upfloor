<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketIklan extends Model
{
    protected $table = 'paket_iklan';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nama_paket',
        'harga',
        'kuota_iklan',
        'deskripsi',
        'is_active'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationship dengan transaksi paket
    public function transaksiPaket()
    {
        return $this->hasMany('App\Models\TransaksiPaket', 'paket_id', 'id');
    }

    // Scope untuk paket aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
