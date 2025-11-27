<?php 
$bg   = DB::table('heading')->where('halaman','About')->orderBy('id_heading','DESC')->first();
 ?>
<!--Inner Header Start-->
<section class="wf100 p80 inner-header" style="background-image: url('{{ asset('assets/upload/image/'.$bg->gambar) }}'); background-position: bottom center;">
   <div class="container">
      <h1>{{ $title }}</h1>
   </div>
</section>
<!--Inner Header End--> 
<!--About Start-->
<section class="wf100 about">
   <!--About Txt Video Start-->
   <div class="about-video-section wf100">
      <div class="container">
         <div class="row">
            <div class="col-lg-7">
               <div class="about-text">
                  <h2>{{ $site_config->nama_singkat }}</h2>
                  <?php echo $site_config->tentang ?>
                  
               </div>
            </div>
            <div class="col-lg-5">
               <a href="#"><img src="{{ asset('assets/upload/image/'.$site_config->gambar) }}" alt="{{ $site_config->nama_singkat }}" class="img img-fluid img-thumbnail"></a>
            </div>
         </div>
      </div>
   </div>
   <!--About Txt Video End--> 
<?php 
$bg2   = DB::table('heading')->where('halaman','Team')->orderBy('id_heading','DESC')->first();
 ?>
<!--Inner Header Start-->
<section id="staff" class="wf100 p100 inner-header" style="background-image: url('{{ asset('assets/upload/image/'.$bg2->gambar) }}'); background-position: bottom center;">
   <div class="container text-center">
      <h1>{{ $bg2->judul_heading }}</h1>
   </div>
</section>
   <!--Causes Start-->
<style>
   .agent-card {
      min-height: 380px;
      display: flex;
      flex-direction: column;
      margin-bottom: 30px;
   }
   .agent-card .event-post {
      height: 100%;
      display: flex;
      flex-direction: column;
   }
   .agent-card .event-thumb {
      height: 280px;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #f8f9fa;
   }
   .agent-card .event-thumb img {
      width: 100%;
      height: 280px;
      object-fit: cover;
   }
   .agent-card .event-txt {
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 20px 10px;
      min-height: 100px;
   }
   .agent-card .event-txt h6 {
      min-height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 10px;
   }
</style>
<section class="wf100 p30 events justify-content-center">
   <div class="event-grid-2">
      <div class="container">
         <div class="row text-center  d-flex justify-content-center">
            <?php foreach($kategori_staff as $kategori_staff) { 
               $id_kategori_staff = $kategori_staff->id_kategori_staff;
               $staff    = DB::table('staff')->where(array('status_staff'=>'Ya','id_kategori_staff'=>$id_kategori_staff))->orderBy('urutan','ASC')->get();
               if($staff) {
               ?>
            <div class="col-lg-12">
               <div class="about-text">
             <h2>{{ $kategori_staff->nama_kategori_staff }}</h2>
                  <p><?php echo nl2br($kategori_staff->keterangan) ?></p>
                  <br><br>
               </div>
            </div>
            <?php foreach($staff as $staff) { ?>
            <!--Blog Post Start-->
            <div class="col-lg-3 col-md-6">
               <div class="agent-card">
                  <div class="event-post">
                     <div class="event-thumb"> <a href="{{ asset('agent')."/".$staff->id_staff }}"><i class="fas fa-link"></i></a> <img src="{{ asset('assets/upload/staff/'.$staff->gambar) }}" alt="{{ $staff->nama_staff }}"></div>
                     <div class="event-txt">
                        <h6><a href="#">{{ $staff->nama_staff }}</a></h6>
                        <p class="venue"><span>{{ $staff->jabatan }}</span></p>
                     </div>
                  </div>
               </div>
            </div>
            <!--Blog Post End--> 
            <?php } ?>
            <?php }} ?>
      </div>
   </div>
</section>
<!--Causes End--> 

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

         <?php foreach($layanan as $layanan) { ?>
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
