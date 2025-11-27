<p class="alert alert-info">
   <strong>Tambah Promosi Baru</strong>
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

<form action="{{ asset('admin/promosi/tambah_proses') }}" method="post" accept-charset="utf-8" enctype="multipart/form-data">
{{ csrf_field() }}

<div class="row">
   <div class="col-md-8">
      <div class="form-group">
         <label>Judul Promosi <span class="text-danger">*</span></label>
         <input type="text" name="judul_promosi" class="form-control" placeholder="Judul promosi" value="{{ old('judul_promosi') }}" required>
      </div>

      <div class="form-group">
         <label>Deskripsi</label>
         <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi promosi">{{ old('deskripsi') }}</textarea>
      </div>

      <div class="form-group">
         <label>Link URL</label>
         <input type="text" name="link_url" class="form-control" placeholder="Contoh: /search/jual?order=newest&limit=9" value="{{ old('link_url') }}">
         <small class="text-muted">URL yang akan dibuka saat promosi diklik</small>
      </div>

      <div class="row">
         <div class="col-md-6">
            <div class="form-group">
               <label>Tanggal Mulai</label>
               <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai') }}">
            </div>
         </div>
         <div class="col-md-6">
            <div class="form-group">
               <label>Tanggal Selesai</label>
               <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai') }}">
            </div>
         </div>
      </div>
   </div>

   <div class="col-md-4">
      <div class="form-group">
         <label>Upload Gambar <span class="text-danger">*</span></label>
         <input type="file" name="gambar" class="form-control" required accept="image/*" onchange="previewImage(event)">
         <small class="text-muted">Format: JPG, PNG, GIF. Max 8MB</small>
         <div id="imagePreview" class="mt-2"></div>
      </div>

      <div class="form-group">
         <label>Status <span class="text-danger">*</span></label>
         <select name="status_promosi" class="form-control" required>
            <option value="Aktif" {{ old('status_promosi') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="Tidak Aktif" {{ old('status_promosi') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
         </select>
      </div>

      <div class="form-group">
         <label>Urutan Tampil <span class="text-danger">*</span></label>
         <input type="number" name="urutan" class="form-control" value="{{ old('urutan', 0) }}" required>
         <small class="text-muted">Urutan slideshow (1, 2, 3, ...)</small>
      </div>
   </div>
</div>

<div class="form-group">
   <button type="submit" class="btn btn-success">
      <i class="fa fa-save"></i> Simpan Promosi
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
      output.innerHTML = '<img src="'+reader.result+'" class="img-thumbnail" style="max-width: 100%;">';
   };
   reader.readAsDataURL(event.target.files[0]);
}
</script>

