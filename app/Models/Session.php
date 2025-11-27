<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Session extends Model
{
    protected $table = 'sessions';
    
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'session_id',
        'user_id',
        'staf_id',
        'expires_at',
        'device_info',
        'ip_address',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'staf_id' => 'integer',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship dengan User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /**
     * Relationship dengan Staff (melalui user)
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'user_id', 'id_user');
    }

    /**
     * Generate unique session ID
     */
    public static function generateSessionId(): string
    {
        return bin2hex(random_bytes(30)); // 60 karakter
    }

    /**
     * Create new session
     */
    public static function createSession(array $data): self
    {
        return self::create([
            'session_id' => self::generateSessionId(),
            'user_id' => $data['user_id'],
            'staf_id' => $data['staf_id'] ?? null,
            'expires_at' => now()->addDays(7), // Session berlaku 7 hari
            'device_info' => $data['device_info'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Validate session
     */
    public static function validateSession(string $sessionId): ?self
    {
        $session = self::where('session_id', $sessionId)
            ->where('is_active', true)
            ->where('expires_at', '>', now()) // Session belum expired
            ->first();

        if ($session) {
            // Update last activity (update updated_at)
            $session->update(['updated_at' => now()]);
        }

        return $session;
    }

    /**
     * Invalidate session (Hard Delete)
     */
    public function invalidate(): bool
    {
        $sessionId = $this->session_id;
        $userId = $this->user_id;
        
        // Log before deletion for security monitoring
        \Log::info("Session hard deleted: {$sessionId} for user {$userId}");
        
        // Hard delete - hapus data dari database
        $result = $this->delete();
        
        return $result;
    }

    /**
     * Invalidate all sessions for user (Hard Delete)
     */
    public static function invalidateUserSessions(int $userId): int
    {
        $sessions = self::where('user_id', $userId)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->get();
            
        $count = $sessions->count();
        
        if ($count > 0) {
            // Log before deletion for security monitoring
            \Log::info("All sessions hard deleted for user {$userId}, count: {$count}");
            
            // Hard delete - hapus semua session user dari database
            self::where('user_id', $userId)
                ->where('is_active', true)
                ->where('expires_at', '>', now())
                ->delete();
        }
        
        return $count;
    }

    /**
     * Clean expired sessions (Hard Delete)
     */
    public static function cleanExpiredSessions(): int
    {
        $expiredSessions = self::where('expires_at', '<', now())
            ->get();
            
        $count = $expiredSessions->count();
        
        if ($count > 0) {
            // Log before deletion
            \Log::info("Expired sessions hard deleted, count: {$count}");
            
            // Hard delete expired sessions
            self::where('expires_at', '<', now())
                ->delete();
        }
        
        return $count;
    }

    /**
     * Check if session is valid
     */
    public function isValid(): bool
    {
        return $this->is_active && $this->expires_at->isAfter(now());
    }

    /**
     * Get session data for mobile app
     */
    public function getMobileData(): array
    {
        // Get purchased packages for this user
        $purchasedPackages = DB::table('transaksi_paket as tp')
            ->join('paket_iklan as pk', 'tp.paket_id', '=', 'pk.id')
            ->where('tp.user_id', $this->user_id)
            ->where('tp.status_pembayaran', 'confirmed')
            ->select([
                'tp.id as transaction_id',
                'tp.paket_id',
                'tp.kode_transaksi',
                'tp.status_pembayaran',
                'tp.created_at as purchase_date',
                'pk.nama_paket',
                'pk.harga',
                'pk.kuota_iklan',
                'pk.deskripsi'
            ])
            ->orderBy('tp.created_at', 'desc')
            ->get()
            ->map(function ($package) {
                return [
                    'transaction_id' => $package->transaction_id,
                    'paket_id' => $package->paket_id,
                    'kode_transaksi' => $package->kode_transaksi,
                    'status_pembayaran' => $package->status_pembayaran,
                    'purchase_date' => $package->purchase_date,
                    'nama_paket' => $package->nama_paket,
                    'harga' => (float) $package->harga,
                    'kuota_iklan' => $package->kuota_iklan,
                    'deskripsi' => $package->deskripsi
                ];
            });

        return [
            'session_id' => $this->session_id,
            'user_id' => $this->user_id,
            'staf_id' => $this->staf_id,
            'device_info' => $this->device_info,
            'expires_at' => $this->expires_at->format('Y-m-d H:i:s'),
            'is_active' => $this->isValid(),
            'purchased_packages' => $purchasedPackages->toArray()
        ];
    }
}
