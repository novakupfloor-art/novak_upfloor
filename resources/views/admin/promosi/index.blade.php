<p class="alert alert-info">
   <strong>Data Promosi Slideshow</strong>
</p>

@if(session('sukses'))
<p class="alert alert-success">{{ session('sukses') }}</p>
@endif

<p class="text-right">
   <a href="{{ asset('admin/promosi/tambah') }}" class="btn btn-success">
      <i class="fa fa-plus-circle"></i> Tambah Promosi Baru
   </a>
</p>

<form action="{{ asset('admin/promosi/proses') }}" method="post" accept-charset="utf-8">
{{ csrf_field() }}

<div class="table-responsive mailbox-messages">
<table class="table table-striped table-bordered" id="example1">
<thead>
   <tr class="bg-info">
      <th width="5%">No</th>
      <th width="15%">Gambar</th>
      <th width="20%">Judul Promosi</th>
      <th width="15%">Link URL</th>
      <th width="10%">Status</th>
      <th width="8%">Urutan</th>
      <th width="12%">Periode</th>
      <th width="15%">Action</th>
   </tr>
</thead>
<tbody>
   <?php $no=1; foreach($promosi as $row) { ?>
   <tr>
      <td>{{ $no }}</td>
      <td>
         @if($row->gambar && file_exists(public_path('assets/upload/promosi/'.$row->gambar)))
            <img src="{{ asset('assets/upload/promosi/'.$row->gambar) }}" class="img img-thumbnail" style="max-width: 150px;">
         @else
            <span class="badge badge-warning">No Image</span>
         @endif
      </td>
      <td>
         <strong>{{ $row->judul_promosi }}</strong>
         @if($row->deskripsi)
         <br><small class="text-muted">{{ Str::limit($row->deskripsi, 100) }}</small>
         @endif
      </td>
      <td>
         @if($row->link_url)
            <a href="{{ asset($row->link_url) }}" target="_blank" class="btn btn-sm btn-info">
               <i class="fa fa-link"></i> Lihat Link
            </a>
         @else
            <span class="text-muted">-</span>
         @endif
      </td>
      <td>
         @if($row->status_promosi == 'Aktif')
            <span class="badge badge-success">Aktif</span>
         @else
            <span class="badge badge-secondary">Tidak Aktif</span>
         @endif
      </td>
      <td class="text-center">
         <span class="badge badge-primary">{{ $row->urutan }}</span>
      </td>
      <td>
         @if($row->tanggal_mulai && $row->tanggal_selesai)
            <small>
               {{ date('d/m/Y', strtotime($row->tanggal_mulai)) }}<br>
               s/d<br>
               {{ date('d/m/Y', strtotime($row->tanggal_selesai)) }}
            </small>
         @else
            <span class="text-muted">Tidak terbatas</span>
         @endif
      </td>
      <td>
         <div class="btn-group">
            <a href="{{ asset('admin/promosi/edit/'.$row->id_promosi) }}" class="btn btn-warning btn-sm" title="Edit">
               <i class="fa fa-edit"></i> Edit
            </a>
            <a href="{{ asset('admin/promosi/delete/'.$row->id_promosi) }}" class="btn btn-danger btn-sm delete-link" title="Hapus" onclick="return confirm('Yakin ingin menghapus promosi ini?')">
               <i class="fa fa-trash"></i> Hapus
            </a>
         </div>
      </td>
   </tr>
   <?php $no++; } ?>
</tbody>
</table>
</div>

</form>

