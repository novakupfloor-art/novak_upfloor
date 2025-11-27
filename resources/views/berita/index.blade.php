<?php
$bg = DB::table("heading")
    ->where("halaman", "Berita")
    ->orderBy("id_heading", "DESC")
    ->first(); ?>
<!--Inner Header Start-->
<section class="wf100 p80 inner-header" style="background-image: url('{{ asset('assets/upload/image/'.$bg->gambar) }}'); background-position: bottom center;">
   <div class="container">
      <h1>{{ $title }}</h1>
   </div>
</section>
<!--Inner Header End-->
<!--Blog Start-->
<section class="wf100 p80 blog">
   <div class="blog-grid">
      <div class="container">
         <div class="row">
            <?php foreach ($berita as $notberita) { ?>
            <!--Blog Small Post Start-->
            <div class="col-md-6 col-sm-12">
               <div class="blog-post">
                  <div class="blog-thumb"> <a href="{{ asset('berita/read/'.$notberita->slug_berita) }}"><i class="fas fa-link"></i></a> <img src="{{ asset('assets/upload/image/thumbs/'.$notberita->gambar) }}" alt="<?php echo $notberita->judul_berita; ?>"> </div>
                  <div class="post-txt">
                     <h5><a href="{{ asset('berita/read/'.$notberita->slug_berita) }}"><?php echo $notberita->judul_berita; ?></a></h5>
                     <ul class="post-meta">
                        <li> <a href="{{ asset('berita/read/'.$notberita->slug_berita) }}"><i class="fas fa-calendar-alt"></i> {{ tanggal('tanggal_id',$notberita->tanggal_post)}}</a> </li>
                        <li> <a href="{{ asset('berita/read/'.$notberita->slug_berita) }}"><i class="fas fa-comments"></i> {{ $notberita->nama_kategori }}</a> </li>
                     </ul>
                     <p><?php echo \Illuminate\Support\Str::limit(
                         strip_tags($notberita->isi),
                         100,
                         $end = "...",
                     ); ?></p>
                     <a href="{{ asset('berita/read/'.$notberita->slug_berita) }}" class="read-post">Baca detail</a>
                  </div>
               </div>
            </div>
            <!--Blog Small Post End-->
            <?php } ?>
         </div>

         <div class="gt-pagination">
            {{ $berita->links() }}
         </div>

      </div>
   </div>
</section>
<!--Blog End-->

<!--Service Area Start-->
<style>
   .katalog-layanan {
      min-height: 500px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      margin-top: 30px;
   }
   .katalog-layanan img {
      width: 100%;
      height: 280px;
      object-fit: cover;
      border-radius: 8px;
   }
   .katalog-layanan .volbox {
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding-top: 20px;
   }
   .katalog-layanan .volbox h6 {
      min-height: 60px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      font-weight: bold;
   }
   .katalog-layanan .volbox p {
      flex-grow: 1;
      font-size: 1rem;
      padding: 10px 15px;
   }
   .katalog-layanan .volbox a {
      font-size: 1.1rem;
      padding: 10px 20px;
   }
</style>
<section class="about wf100" style="padding-top: 50px;">
   <div class="container text-center reveal">
      <div class="row">

         <?php foreach ($layanan as $layanan) { ?>
            <div class="col-md-4 col-sm-12">
               <div class="katalog-layanan">
                  <img src="{{ asset('assets/upload/image/thumbs/'.$layanan->gambar) }}" alt="{{ $layanan->judul_berita }}" class="img img-thumbnail img-fluid">
                  <div class="volbox">
                     <h6>{{ $layanan->judul_berita }}</h6>
                     <p>{{ $layanan->keywords }}</p>
                     <a href="{{ $layanan->link_berita }}">Lihat detail</a>
                  </div>
               </div>
            </div>
            <!--box  end -->
         <?php } ?>

      </div>
   </div>
</section>
<!--Service Area End-->
