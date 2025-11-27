<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Transaksi extends Controller
{
    /**
     * Menampilkan daftar semua transaksi paket iklan.
     */
    public function index()
    {
        if(Session::get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}

        // Ambil transaksi pending untuk tabel pending
        $transaksi_pending = DB::table('transaksi_paket')
            ->join('users', 'transaksi_paket.id_user', '=', 'users.id_user')
            ->join('paket_iklan', 'transaksi_paket.paket_id', '=', 'paket_iklan.id')
            ->select(
                'transaksi_paket.*', 
                'users.nama as nama_user', 
                'users.email as email_user',
                'paket_iklan.nama_paket',
                'paket_iklan.harga',
                'paket_iklan.kuota_iklan'
            )
            ->where('transaksi_paket.status_pembayaran', 'pending')
            ->orderBy('transaksi_paket.created_at', 'asc')
            ->get();

        // Ambil transaksi yang sudah dikonfirmasi atau direject (tidak termasuk pending)
        // Urutkan berdasarkan status: confirmed dulu, lalu rejected, lalu created_at desc
        $transaksi_all = DB::table('transaksi_paket')
            ->join('users', 'transaksi_paket.id_user', '=', 'users.id_user')
            ->join('paket_iklan', 'transaksi_paket.paket_id', '=', 'paket_iklan.id')
            ->leftJoin('users as admin', 'transaksi_paket.dikonfirmasi_oleh', '=', 'admin.id_user')
            ->select(
                'transaksi_paket.*', 
                'users.nama as nama_user', 
                'users.email as email_user',
                'paket_iklan.nama_paket',
                'paket_iklan.harga',
                'paket_iklan.kuota_iklan',
                'admin.nama as nama_admin'
            )
            ->whereIn('transaksi_paket.status_pembayaran', ['confirmed', 'rejected'])
            ->orderByRaw("
                CASE 
                    WHEN transaksi_paket.status_pembayaran = 'confirmed' THEN 1
                    WHEN transaksi_paket.status_pembayaran = 'rejected' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('transaksi_paket.created_at', 'desc')
            ->paginate(15);

        // Hitung statistik
        $stats = [
            'pending' => DB::table('transaksi_paket')->where('status_pembayaran', 'pending')->count(),
            'confirmed' => DB::table('transaksi_paket')->where('status_pembayaran', 'confirmed')->count(),
            'rejected' => DB::table('transaksi_paket')->where('status_pembayaran', 'rejected')->count(),
            'total' => DB::table('transaksi_paket')->count()
        ];

        $data = [
            'title'             => 'Manajemen Transaksi Paket Iklan',
            'transaksi_pending' => $transaksi_pending,
            'transaksi_all'     => $transaksi_all,
            'stats'             => $stats,
            'content'           => 'admin/transaksi/index'
        ];
        return view('admin/layout/wrapper', $data);
    }

    /**
     * Mengkonfirmasi pembayaran dan menambah kuota user.
     */
    public function confirm($id)
    {
        if(Session::get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}

        $transaksi = DB::table('transaksi_paket')->where('id', $id)->first();
        if(!$transaksi) {
            return redirect('admin/transaksi')->with(['warning' => 'Data transaksi tidak ditemukan.']);
        }

        $paket = DB::table('paket_iklan')->where('id', $transaksi->paket_id)->first();
        if(!$paket) {
            return redirect('admin/transaksi')->with(['warning' => 'Data paket iklan tidak ditemukan.']);
        }

        $staff = DB::table('staff')->where('id_user', $transaksi->id_user)->first();
        if(!$staff) {
            return redirect('admin/transaksi')->with(['warning' => 'Profil staff untuk user ini tidak ditemukan. Pastikan user telah verifikasi email.']);
        }

        // --- LOGIKA KONFIRMASI PEMBELIAN PAKET IKLAN ---
        
        // 1. Dapatkan urutan terakhir dan tentukan urutan baru
        $lastOrder = DB::table('staff')->max('urutan');
        $newOrder = $lastOrder + 1;

        // 2. Hitung kuota baru
        $newTotalKuota = $staff->total_kuota_iklan + $paket->kuota_iklan;
        $newSisaKuota = $staff->sisa_kuota_iklan + $paket->kuota_iklan;

        // 3. Update tabel staff: status selalu "Ya" saat konfirmasi, urutan, kuota, dan id_transaksi
        DB::table('staff')->where('id_staff', $staff->id_staff)->update([
            'status_staff'      => 'Ya', // Selalu set "Ya" saat konfirmasi pembelian
            'urutan'            => $newOrder,
            'total_kuota_iklan' => $newTotalKuota,
            'sisa_kuota_iklan'  => $newSisaKuota,
            'id_transaksi'      => $id  // Update id_transaksi dengan ID transaksi terakhir
        ]);

        // 5. Update paket_id di tabel users
        DB::table('users')->where('id_user', $transaksi->id_user)->update([
            'paket_id' => $transaksi->paket_id
        ]);

        // 6. Update status transaksi menjadi 'confirmed'
        DB::table('transaksi_paket')->where('id', $id)->update([
            'status_pembayaran' => 'confirmed',
            'dikonfirmasi_oleh' => Session::get('id_user'),
            'tanggal_konfirmasi' => now()
        ]);

        // 7. Log aktivitas
        \Log::info('Paket iklan dikonfirmasi', [
            'transaksi_id' => $id,
            'id_user' => $transaksi->id_user,
            'staff_id' => $staff->id_staff,
            'paket_id' => $transaksi->paket_id,
            'kuota_tambahan' => $paket->kuota_iklan,
            'total_kuota_baru' => $newTotalKuota,
            'sisa_kuota_baru' => $newSisaKuota,
            'status_staff_baru' => 'Ya',
            'admin_id' => Session::get('id_user')
        ]);

        $message = 'Transaksi berhasil dikonfirmasi. ';
        $message .= 'Profil staff telah diaktifkan, kuota diperbarui (+' . $paket->kuota_iklan . '), ';
        $message .= 'dan paket_id telah disimpan ke data user. ';
        $message .= 'Status staff: Ya (Sisa kuota: ' . $newSisaKuota . ')';

        return redirect('admin/transaksi')->with(['sukses' => $message]);
    }

    /**
     * Menolak pembayaran.
     */
    public function reject($id)
    {
        if(Session::get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}

        DB::table('transaksi_paket')->where('id', $id)->update([
            'status_pembayaran' => 'rejected',
            'dikonfirmasi_oleh' => Session::get('id_user'),
            'tanggal_konfirmasi' => now()
        ]);

        return redirect('admin/transaksi')->with(['sukses' => 'Transaksi telah ditolak.']);
    }

    /**
     * Menandai pembayaran sebagai tidak terverifikasi.
     */
    public function unverify($id)
    {
        if(Session::get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}

        DB::table('transaksi_paket')->where('id', $id)->update([
            'status_pembayaran' => 'unverified',
            'dikonfirmasi_oleh' => Session::get('id_user'),
            'tanggal_konfirmasi' => now()
        ]);

        return redirect('admin/transaksi')->with(['sukses' => 'Transaksi telah ditandai sebagai tidak terverifikasi.']);
    }

    /**
     * Update status staff berdasarkan sisa kuota iklan.
     * Dipanggil ketika ada perubahan kuota iklan (misalnya setelah iklan dibuat).
     */
    public static function updateStaffStatusByQuota($userId)
    {
        $staff = DB::table('staff')->where('id_user', $userId)->first();
        
        if (!$staff) {
            return false;
        }

        $sisaKuota = $staff->sisa_kuota_iklan;
        $statusStaff = ($sisaKuota > 0) ? 'Ya' : 'Tidak';

        // Update status staff jika berubah
        if ($staff->status_staff !== $statusStaff) {
            DB::table('staff')->where('id_user', $userId)->update([
                'status_staff' => $statusStaff
            ]);

            \Log::info('Status staff diupdate berdasarkan kuota', [
                'id_user' => $userId,
                'staff_id' => $staff->id_staff,
                'sisa_kuota_iklan' => $sisaKuota,
                'status_lama' => $staff->status_staff,
                'status_baru' => $statusStaff
            ]);

            return true;
        }

        return false;
    }

    /**
     * Update status staff untuk semua staff berdasarkan kuota mereka.
     * Fungsi ini bisa dipanggil dari cron job atau admin panel.
     */
    public static function updateAllStaffStatusByQuota()
    {
        $staffList = DB::table('staff')
            ->where('status_staff', 'Ya')
            ->where('sisa_kuota_iklan', '<=', 0)
            ->get();

        $updatedCount = 0;

        foreach ($staffList as $staff) {
            DB::table('staff')->where('id_staff', $staff->id_staff)->update([
                'status_staff' => 'Tidak'
            ]);

            \Log::info('Status staff diupdate ke Tidak (kuota habis)', [
                'id_user' => $staff->id_user,
                'staff_id' => $staff->id_staff,
                'sisa_kuota_iklan' => $staff->sisa_kuota_iklan
            ]);

            $updatedCount++;
        }

        return $updatedCount;
    }
}
