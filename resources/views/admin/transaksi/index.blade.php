<!-- Statistik Cards -->
<div class="row mb-3">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>{{ $stats['pending'] }}</h3>
        <p>Menunggu Konfirmasi</p>
      </div>
      <div class="icon">
        <i class="fas fa-clock"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>{{ $stats['confirmed'] }}</h3>
        <p>Dikonfirmasi</p>
      </div>
      <div class="icon">
        <i class="fas fa-check"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-danger">
      <div class="inner">
        <h3>{{ $stats['rejected'] }}</h3>
        <p>Ditolak</p>
      </div>
      <div class="icon">
        <i class="fas fa-times"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>{{ $stats['total'] }}</h3>
        <p>Total Transaksi</p>
      </div>
      <div class="icon">
        <i class="fas fa-list"></i>
      </div>
    </div>
  </div>
</div>

<!-- Section Transaksi Menunggu Konfirmasi -->
<?php 
if($transaksi_pending->count() > 0): 
?>
<div class="card card-warning">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fas fa-clock"></i> Transaksi Menunggu Konfirmasi ({{ $transaksi_pending->count() }})
    </h3>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="bg-warning">
          <tr>
            <th width="3%">No</th>
            <th width="12%">Tanggal</th>
            <th width="15%">Nama User</th>
            <th width="15%">Paket</th>
            <th width="10%">Harga</th>
            <th width="8%">Kuota</th>
            <th width="12%">Bukti Bayar</th>
            <th width="25%">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $i=1; foreach($transaksi_pending as $transaksi): ?>
          <tr>
            <td class="text-center"><?php echo $i ?></td>
            <td>
              <small>
                <?php echo date('d-m-Y', strtotime($transaksi->created_at)) ?><br>
                <?php echo date('H:i', strtotime($transaksi->created_at)) ?>
              </small>
            </td>
            <td>
              <strong><?php echo $transaksi->nama_user ?></strong><br>
              <small class="text-muted"><?php echo $transaksi->email_user ?></small>
            </td>
            <td><?php echo $transaksi->nama_paket ?></td>
            <td>
              <strong>Rp <?php echo number_format($transaksi->harga, 0, ',', '.') ?></strong>
            </td>
            <td class="text-center">
              <span class="badge bg-primary"><?php echo $transaksi->kuota_iklan ?></span>
            </td>
            <td class="text-center">
              <?php if($transaksi->bukti_pembayaran): ?>
                <img src="{{ asset('storage/bukti_pembayaran/'.$transaksi->bukti_pembayaran) }}" 
                     class="img-thumbnail" 
                     style="width: 80px; height: 60px; object-fit: cover; cursor: pointer;"
                     data-bs-toggle="modal" 
                     data-bs-target="#buktiModal<?php echo $transaksi->id ?>"
                     title="Klik untuk melihat gambar penuh">
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="btn-group">
                <a href="{{ url('admin/transaksi/confirm/'.$transaksi->id) }}" 
                   class="btn btn-success btn-sm" 
                   onclick="return confirm('Konfirmasi pembayaran ini?')">
                  <i class="fa fa-check"></i> Konfirmasi
                </a>
                <a href="{{ url('admin/transaksi/reject/'.$transaksi->id) }}" 
                   class="btn btn-danger btn-sm" 
                   onclick="return confirm('Tolak pembayaran ini?')">
                  <i class="fa fa-times"></i> Tolak
                </a>
              </div>
            </td>
          </tr>
          <?php $i++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Section Transaksi yang Sudah Diproses -->
<div class="card card-info">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fas fa-list"></i> Transaksi yang Sudah Diproses (Dikonfirmasi/Ditolak)
    </h3>
  </div>
  <div class="card-body">

<div class="table-responsive mailbox-messages">
  <table id="example1" class="display table table-bordered table-sm" cellspacing="0" width="100%">
    <thead>
      <tr class="bg-info">
        <th width="3%">No</th>
        <th width="12%">Tanggal</th>
        <th width="15%">Nama User</th>
        <th width="15%">Paket</th>
        <th width="10%">Harga</th>
        <th width="8%">Kuota</th>
        <th width="10%">Status</th>
        <th width="12%">Bukti Bayar</th>
        <th width="15%">Aksi</th>
      </tr>
    </thead>
    <tbody>

      <?php $i=1; foreach($transaksi_all as $transaksi) { ?>

      <tr class="odd gradeX">
        <td class="text-center"><?php echo $i ?></td>
        <td>
          <small>
            <?php echo date('d-m-Y', strtotime($transaksi->created_at)) ?><br>
            <?php echo date('H:i', strtotime($transaksi->created_at)) ?>
          </small>
        </td>
        <td>
          <strong><?php echo $transaksi->nama_user ?></strong><br>
          <small class="text-muted"><?php echo $transaksi->email_user ?></small>
        </td>
        <td><?php echo $transaksi->nama_paket ?></td>
        <td>
          <strong>Rp <?php echo number_format($transaksi->harga, 0, ',', '.') ?></strong>
        </td>
        <td class="text-center">
          <span class="badge bg-primary"><?php echo $transaksi->kuota_iklan ?></span>
        </td>
        <td>
          <?php 
          $status = $transaksi->status_pembayaran;
          $badge_class = '';
          switch($status) {
            case 'pending': $badge_class = 'bg-warning'; break;
            case 'confirmed': $badge_class = 'bg-success'; break;
            case 'rejected': $badge_class = 'bg-danger'; break;
            default: $badge_class = 'bg-secondary';
          }
          ?>
          <span class="badge <?php echo $badge_class ?>"><?php echo ucfirst($status) ?></span>
          <?php if($transaksi->nama_admin): ?>
            <br><small class="text-muted">by <?php echo $transaksi->nama_admin ?></small>
          <?php endif; ?>
        </td>
        <td class="text-center">
          <?php if($transaksi->bukti_pembayaran): ?>
            <img src="{{ asset('storage/bukti_pembayaran/'.$transaksi->bukti_pembayaran) }}" 
                 class="img-thumbnail" 
                 style="width: 80px; height: 60px; object-fit: cover; cursor: pointer;"
                 data-bs-toggle="modal" 
                 data-bs-target="#buktiModal<?php echo $transaksi->id ?>"
                 title="Klik untuk melihat gambar penuh">
          <?php else: ?>
            <span class="text-muted">-</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if($status == 'pending'): ?>
            <div class="btn-group">
              <a href="{{ url('admin/transaksi/confirm/'.$transaksi->id) }}" class="btn btn-success btn-sm" onclick="return confirm('Konfirmasi pembayaran ini?')">
                <i class="fa fa-check"></i> Konfirmasi
              </a>
              <a href="{{ url('admin/transaksi/reject/'.$transaksi->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Tolak pembayaran ini?')">
                <i class="fa fa-times"></i> Tolak
              </a>
            </div>
          <?php elseif($status == 'confirmed'): ?>
            <span class="text-success"><i class="fas fa-check-circle"></i> Dikonfirmasi</span>
            <?php if($transaksi->tanggal_konfirmasi): ?>
              <br><small class="text-muted"><?php echo date('d-m-Y H:i', strtotime($transaksi->tanggal_konfirmasi)) ?></small>
            <?php endif; ?>
          <?php elseif($status == 'rejected'): ?>
            <span class="text-danger"><i class="fas fa-times-circle"></i> Ditolak</span>
            <?php if($transaksi->tanggal_konfirmasi): ?>
              <br><small class="text-muted"><?php echo date('d-m-Y H:i', strtotime($transaksi->tanggal_konfirmasi)) ?></small>
            <?php endif; ?>
          <?php endif; ?>
        </td>
      </tr>

      <?php $i++; } ?>

    </tbody>
  </table>
</div>

<!-- Modal untuk menampilkan bukti pembayaran -->
<?php foreach($transaksi_all as $transaksi): ?>
  <?php if($transaksi->bukti_pembayaran): ?>
  <div class="modal fade" id="buktiModal<?php echo $transaksi->id ?>" tabindex="-1" aria-labelledby="buktiModalLabel<?php echo $transaksi->id ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="buktiModalLabel<?php echo $transaksi->id ?>">Bukti Pembayaran - <?php echo $transaksi->nama_user ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <img src="{{ asset('assets/upload/bukti/'.$transaksi->bukti_pembayaran) }}" class="img-fluid" alt="Bukti Pembayaran" style="max-height: 500px;">
          <hr>
          <p><strong>Kode Transaksi:</strong> <?php echo $transaksi->kode_transaksi ?></p>
          <p><strong>Paket:</strong> <?php echo $transaksi->nama_paket ?></p>
          <p><strong>Harga:</strong> Rp <?php echo number_format($transaksi->harga, 0, ',', '.') ?></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <a href="{{ asset('assets/upload/bukti/'.$transaksi->bukti_pembayaran) }}" target="_blank" class="btn btn-primary">
            <i class="fas fa-download"></i> Download
          </a>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
<?php endforeach; ?>

    <div class="clearfix"><hr></div>
    <div class="pull-right">
      {{ $transaksi_all->links() }}
    </div>
  </div>
</div>
