<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Promosi as PromosiModel;
use Image;

class Promosi extends Controller
{
    // Halaman index - daftar promosi
    public function index()
    {
        $promosi = PromosiModel::orderBy('urutan', 'ASC')
                              ->orderBy('id_promosi', 'DESC')
                              ->get();

        $data = array(
            'title' => 'Data Promosi',
            'promosi' => $promosi,
            'content' => 'admin/promosi/index'
        );
        return view('admin/layout/wrapper', $data);
    }

    // Halaman tambah promosi
    public function tambah()
    {
        $data = array(
            'title' => 'Tambah Promosi',
            'content' => 'admin/promosi/tambah'
        );
        return view('admin/layout/wrapper', $data);
    }

    // Proses tambah promosi
    public function tambah_proses(Request $request)
    {
        $request->validate([
            'judul_promosi' => 'required|max:200',
            'gambar' => 'required|image|mimes:jpeg,jpg,png,gif|max:8192',
            'status_promosi' => 'required|in:Aktif,Tidak Aktif',
            'urutan' => 'required|integer',
        ]);

        // Upload gambar
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . Str::slug($request->judul_promosi) . '.' . $file->getClientOriginalExtension();
            
            // Simpan gambar original
            $file->move(public_path('assets/upload/promosi'), $filename);
        }

        // Simpan ke database
        PromosiModel::create([
            'judul_promosi' => $request->judul_promosi,
            'deskripsi' => $request->deskripsi,
            'gambar' => $filename ?? '',
            'link_url' => $request->link_url,
            'status_promosi' => $request->status_promosi,
            'urutan' => $request->urutan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        return redirect('admin/promosi')->with('sukses', 'Promosi berhasil ditambahkan');
    }

    // Halaman edit promosi
    public function edit($id_promosi)
    {
        $promosi = PromosiModel::findOrFail($id_promosi);

        $data = array(
            'title' => 'Edit Promosi',
            'promosi' => $promosi,
            'content' => 'admin/promosi/edit'
        );
        return view('admin/layout/wrapper', $data);
    }

    // Proses edit promosi
    public function edit_proses(Request $request)
    {
        $request->validate([
            'id_promosi' => 'required',
            'judul_promosi' => 'required|max:200',
            'status_promosi' => 'required|in:Aktif,Tidak Aktif',
            'urutan' => 'required|integer',
        ]);

        $promosi = PromosiModel::findOrFail($request->id_promosi);
        $filename = $promosi->gambar;

        // Upload gambar baru jika ada
        if ($request->hasFile('gambar')) {
            $request->validate([
                'gambar' => 'image|mimes:jpeg,jpg,png,gif|max:8192',
            ]);

            $file = $request->file('gambar');
            $filename = time() . '_' . Str::slug($request->judul_promosi) . '.' . $file->getClientOriginalExtension();
            
            // Hapus gambar lama
            if (file_exists(public_path('assets/upload/promosi/' . $promosi->gambar))) {
                unlink(public_path('assets/upload/promosi/' . $promosi->gambar));
            }

            // Simpan gambar baru
            $file->move(public_path('assets/upload/promosi'), $filename);
        }

        // Update database
        $promosi->update([
            'judul_promosi' => $request->judul_promosi,
            'deskripsi' => $request->deskripsi,
            'gambar' => $filename,
            'link_url' => $request->link_url,
            'status_promosi' => $request->status_promosi,
            'urutan' => $request->urutan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        return redirect('admin/promosi')->with('sukses', 'Promosi berhasil diupdate');
    }

    // Delete promosi
    public function delete($id_promosi)
    {
        $promosi = PromosiModel::findOrFail($id_promosi);

        // Hapus gambar
        if (file_exists(public_path('assets/upload/promosi/' . $promosi->gambar))) {
            unlink(public_path('assets/upload/promosi/' . $promosi->gambar));
        }

        // Hapus dari database
        $promosi->delete();

        return redirect('admin/promosi')->with('sukses', 'Promosi berhasil dihapus');
    }
}

