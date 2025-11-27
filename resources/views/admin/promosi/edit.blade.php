<p class="alert alert-info">
   <strong>Edit Promosi</strong>
</p>

@if ($errors->any())
<div class="alert alert-danger">
   <ul>
      @foreach ($errors->all() as $error)
         <li>{{ $error }}</li>
      @endforeach
   </ul>
</div>
@endif

<form action="{{ asset('admin/promosi/edit_proses') }}" method="post" accept-charset="utf-8" enctype="multipart/form-data">
{{ csrf_field() }}
<input type="hidden" name="id_promosi" value="{{ $promosi->id_promosi }}">

<div class="row">
   <div class="col-md-8">
      <div class="form-group">
         <label>Judul Promosi <span class="text-danger">*</span></label>
         <input type="text" name="judul_promosi" class="form-control" placeholder="Judul promosi" value="{{ old('judul_promosi', $promosi->judul_promosi) }}" required>
      </div>

      <div class="form-group">
         <label>Deskripsi</label>
         <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi promosi">{{ old('deskripsi', $promosi->deskripsi) }}</textarea>
      </div>

      <div class="form-group">
         <label>Link URL</label>
         <input type="text" name="link_url" class="form-control" placeholder="Contoh: /search/jual?order=newest&limit=9" value="{{ old('link_url', $promosi->link_url) }}">
         <small class="text-muted">URL yang akan dibuka saat promosi diklik</small>
      </div>

      <div class="row">
         <div class="col-md-6">
            <div class="form-group">
               <label>Tanggal Mulai</label>
               <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai', $promosi->tanggal_mulai ? $promosi->tanggal_mulai->format('Y-m-d') : '') }}">
            </div>
         </div>
         <div class="col-md-6">
            <div class="form-group">
               <label>Tanggal Selesai</label>
               <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai', $promosi->tanggal_selesai ? $promosi->tanggal_selesai->format('Y-m-d') : '') }}">
            </div>
         </div>
      </div>
   </div>

   <div class="col-md-4">
      <div class="form-group">
         <label>Upload Gambar Baru</label>
         <input type="file" name="gambar" class="form-control" accept="image/*" onchange="previewImage(event)">
         <small class="text-muted">Format: JPG, PNG, GIF. Max 8MB<br>Kosongkan jika tidak ingin mengubah gambar</small>
         
         <div class="mt-2">
            <strong>Gambar Saat Ini:</strong><br>
            @if($promosi->gambar && file_exists(public_path('assets/upload/promosi/'.$promosi->gambar)))
               <img src="{{ asset('assets/upload/promosi/'.$promosi->gambar) }}" class="img-thumbnail" style="max-width: 100%;">
            @else
               <span class="badge badge-warning">No Image</span>
            @endif
         </div>
         
         <div id="imagePreview" class="mt-2"></div>
      </div>

      <div class="form-group">
         <label>Status <span class="text-danger">*</span></label>
         <select name="status_promosi" class="form-control" required>
            <option value="Aktif" {{ old('status_promosi', $promosi->status_promosi) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="Tidak Aktif" {{ old('status_promosi', $promosi->status_promosi) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
         </select>
      </div>

      <div class="form-group">
         <label>Urutan Tampil <span class="text-danger">*</span></label>
         <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $promosi->urutan) }}" required>
         <small class="text-muted">Urutan slideshow (1, 2, 3, ...)</small>
      </div>
   </div>
</div>

<div class="form-group">
   <button type="submit" class="btn btn-success">
      <i class="fa fa-save"></i> Update Promosi
   </button>
   <a href="{{ asset('admin/promosi') }}" class="btn btn-secondary">
      <i class="fa fa-arrow-left"></i> Kembali
   </a>
</div>

</form>

<script>
function previewImage(event) {
   var reader = new FileReader();
   reader.onload = function(){
      var output = document.getElementById('imagePreview');
      output.innerHTML = '<strong>Preview Gambar Baru:</strong><br><img src="'+reader.result+'" class="img-thumbnail" style="max-width: 100%;">';
   };
   reader.readAsDataURL(event.target.files[0]);
}
</script>

