<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promosi extends Model
{
    use HasFactory;

    protected $table = 'promosi';
    protected $primaryKey = 'id_promosi';
    
    protected $fillable = [
        'judul_promosi',
        'deskripsi',
        'gambar',
        'link_url',
        'status_promosi',
        'urutan',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Scope untuk promosi aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status_promosi', 'Aktif');
    }

    /**
     * Scope untuk promosi yang sedang berlangsung
     */
    public function scopeBerlangsung($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('status_promosi', 'Aktif')
                     ->where(function($q) use ($today) {
                         $q->whereNull('tanggal_mulai')
                           ->orWhere('tanggal_mulai', '<=', $today);
                     })
                     ->where(function($q) use ($today) {
                         $q->whereNull('tanggal_selesai')
                           ->orWhere('tanggal_selesai', '>=', $today);
                     });
    }

    /**
     * Get promosi untuk slideshow
     */
    public static function getSlideshow()
    {
        return self::berlangsung()
                   ->orderBy('urutan', 'ASC')
                   ->get();
    }
}

