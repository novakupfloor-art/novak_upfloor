<!--Slider Start - Banner Promosi-->
         <style>
            #home-slider {
               margin-top: 0;
            }
            #home-slider .item {
               max-height: 400px;
               min-height: 400px;
               overflow: hidden;
               display: flex;
               align-items: center;
               justify-content: center;
               background-color: #f8f9fa;
               padding: 20px;
            }
            #home-slider .item img {
               width: 85%;
               height: auto;
               max-height: 360px;
               object-fit: contain;
               object-position: center;
            }
            @media (max-width: 768px) {
               #home-slider {
                  margin-top: 0;
               }
               #home-slider .item {
                  max-height: 320px;
                  min-height: 320px;
                  padding: 15px;
               }
               #home-slider .item img {
                  width: 85%;
                  max-height: 290px;
               }
            }
            @media (max-width: 480px) {
               #home-slider {
                  margin-top: 0;
               }
               #home-slider .item {
                  max-height: 250px;
                  min-height: 250px;
                  padding: 10px;
               }
               #home-slider .item img {
                  width: 85%;
                  max-height: 230px;
               }
            }
            /* Styling untuk tab link Jual, Sewa, Kontraktor */
            .btn-home {
               text-decoration: none;
               transition: all 0.3s ease;
            }
            .btn-home:hover {
               text-decoration: none;
               opacity: 0.8;
            }
            .btn-home.active {
               background-color: #28a745 !important;
               color: white !important;
               font-weight: bold;
            }
         </style>
         <section id="home-slider" class="owl-carousel owl-theme wf100">
            @if(count($promosi) > 0)
               @foreach($promosi as $promo)
               <div class="item">
                  @if($promo->link_url)
                  <a href="{{ asset($promo->link_url) }}">
                     <img src="{{ asset('assets/upload/promosi/'.$promo->gambar) }}" alt="{{ $promo->judul_promosi }}" title="{{ $promo->judul_promosi }}">
                  </a>
                  @else
                  <img src="{{ asset('assets/upload/promosi/'.$promo->gambar) }}" alt="{{ $promo->judul_promosi }}" title="{{ $promo->judul_promosi }}">
                  @endif
               </div>
               @endforeach
            @else
               @foreach($slider as $slider)
               <div class="item">
                  <div class="slider-caption h3slider">
                     <div class="container">
                        @if($slider->status_text=="Ya")
                        <strong>{{ strip_tags($slider->isi) }}</strong>
                        <h1>{{ $slider->judul_galeri }}</h1>
                        @endif
                     </div>
                  </div>
                  <img src="{{ asset('assets/upload/image/'.$slider->gambar) }}" alt="">
               </div>
               @endforeach
            @endif
         </section>
         <!--Slider End-->
         <!--Service Area Start-->
         <section class="donation-join wf100">
            <div class="container text-center">
               <div class="row">
                  <div class="col-sm-12 col-lg-12">
                     <div class="" style="width:75%;margin:auto;">
                        <form method="GET" action="{{ asset('search/jual') }}" accept-charset="UTF-8" id="mainSearch">
                           <input id="order" name="order" type="hidden" value="newest">
                           <input id="limit" name="limit" type="hidden" value="9">
                           <div class="p-2">
                              <div class="btn-group btn-group-toggle" data-toggle="buttons" style="margin-bottom:5px;">
                                 <a class="btn btn-home" href="{{ asset('search/jual?order=newest&limit=9') }}">Jual</a>
                                 <a class="btn btn-home" href="{{ asset('search/sewa?order=newest&limit=9') }}">Sewa</a>
                                 <a class="btn btn-home" href="{{ asset('search2/done') }}">Kontraktor</a>
                              </div>
                              <input type="hidden" name="listing_type" id="listing_type" value="1">
                              <input id="location" name="location" class="form-control py-2" type="text" placeholder="Search Properti" aria-label="Search Properti">
                              <div class="d-flex justify-content-between py-2">
                                 <button type="button" class="btn btn-home" data-toggle="collapse"
                                       data-target="#collapseExample" aria-expanded="false"
                                       aria-controls="collapseExample">
                                       Filter <i class="fas fa-sliders-h" aria-hidden="true"></i>
                                 </button>
                                 <button type="submit" class="btn btn-success">Search</button>
                              </div>
                              <div class="collapse" id="collapseExample">
                                 <div class="card card-body overflow-auto" style="max-height: 250px;">
                                    <div class="row">
                                       <div class="col-lg-12 col-sm-12">
                                          <div class="input-group mb-3">
                                             <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-house-user"></i></span>
                                             </div>
                                             <select name="id_kategori_property" id="id_kategori_property" class="form-control select2">
                                                <option value="">Type</option>
                                                <?php foreach (
                                                    $kategori_property
                                                    as $kategori_property
                                                ) { ?>
                                                <option value="<?php echo $kategori_property->id_kategori_property; ?>"><?php echo $kategori_property->nama_kategori_property; ?></option>
                                                <?php } ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="col-lg-6 col-sm-12">
                                          <div class="input-group mb-3">
                                             <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                                             </div>
                                             <select class="form-control" id="price_from" name="price_from"><option selected="selected" value="">Min Price</option><option value="50000000">50,000,000</option><option value="100000000">100,000,000</option><option value="500000000">500,000,000</option><option value="1000000000">1,000,000,000</option><option value="3000000000">3,000,000,000</option><option value="5000000000">5,000,000,000</option><option value="7000000000">7,000,000,000</option><option value="10000000000">10,000,000,000</option><option value="15000000000">15,000,000,000</option><option value="20000000000">20,000,000,000</option><option value="30000000000">30,000,000,000</option><option value="50000000000">50,000,000,000</option><option value="75000000000">75,000,000,000</option><option value="100000000000">100,000,000,000</option></select>
                                          </div>
                                       </div>
                                       <div class="col-lg-6 col-sm-12">
                                          <div class="input-group mb-3">
                                             <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                                             </div>
                                             <select class="form-control" id="price_to" name="price_to"><option selected="selected" value="">Max Price</option><option value="50000000">50,000,000</option><option value="100000000">100,000,000</option><option value="500000000">500,000,000</option><option value="1000000000">1,000,000,000</option><option value="3000000000">3,000,000,000</option><option value="5000000000">5,000,000,000</option><option value="7000000000">7,000,000,000</option><option value="10000000000">10,000,000,000</option><option value="15000000000">15,000,000,000</option><option value="20000000000">20,000,000,000</option><option value="30000000000">30,000,000,000</option><option value="50000000000">50,000,000,000</option><option value="75000000000">75,000,000,000</option><option value="100000000000">100,000,000,000</option></select>
                                          </div>
                                       </div>
                                       <div class="col-lg-6 col-sm-12">
                                          <div class="input-group mb-3">
                                             <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-bed"></i></span>
                                             </div>
                                             <select class="form-control" id="bedrooms" name="bedrooms"><option selected="selected" value="">Bedroom</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5+">5+</option></select>
                                          </div>
                                       </div>
                                       <div class="col-lg-6 col-sm-12">
                                          <div class="input-group mb-3">
                                             <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-bath"></i></span>
                                             </div>
                                             <select class="form-control" id="bathrooms" name="bathrooms"><option selected="selected" value="">Bathroom</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7+">7+</option></select>
                                          </div>
                                       </div>

                                       <div class="col-lg-6 col-sm-12">
                                          <div class="input-group mb-3">
                                             <div class="input-group-prepend">
                                                <span class="input-group-text">LT</span>
                                             </div>
                                             <input class="form-control" id="landsize_from" placeholder="Min Land Area (m²)" name="landsize_from" type="text" value="">
                                          </div>
                                       </div>
                                       <div class="col-lg-6 col-sm-12">
                                          <div class="input-group mb-3">
                                             <div class="input-group-prepend">
                                                <span class="input-group-text">LT</span>
                                             </div>
                                             <input class="form-control" id="landsize_to" placeholder="Max Land Area (m²)" name="landsize_to" type="text" value="">
                                          </div>
                                       </div>
                                       <div class="col-lg-6 col-sm-12">
                                          <div class="input-group mb-3">
                                             <div class="input-group-prepend">
                                                <span class="input-group-text">LB</span>
                                             </div>
                                             <input class="form-control" id="buildingsize_from" placeholder="Min Bld/Fl Area (m²)" name="buildingsize_from" type="text" value="">
                                          </div>
                                       </div>
                                       <div class="col-lg-6 col-sm-12">
                                          <div class="input-group mb-3">
                                             <div class="input-group-prepend">
                                                <span class="input-group-text">LB</span>
                                             </div>
                                             <input class="form-control" id="buildingsize_to" placeholder="Max Bld/Fl Area (m²)" name="buildingsize_to" type="text" value="">
                                          </div>
                                       </div>
                                       <div class="col-lg-12 col-sm-12">
                                          <div class="d-grid">
                                             <button id="applyFilter" type="submit" class="btn btn-success">Apply Filter</button>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </form>

                        <script>
                        $(function() {

                           // Form submit untuk filter search di homepage
                           $('#mainSearch').submit(function(e) {
                              e.preventDefault();

                              // Default ke halaman Properti Dijual
                              let action = base_url + "search/jual";

                              var values = {};
                              if($("#id_kategori_property").val() != ""){
                                 values["id_kategori_property"] = $("#id_kategori_property").val();
                              }
                              if($("#location").val() != ""){
                                 values["location"] = $("#location").val();
                              }

                              if($("#office").val() != ""){
                                 if($("#office").val() === undefined || $("#office").val() == "undefined"){
                                 } else {
                                 values["office"] = $("#office").val();
                                 }
                              }
                              if($("#price_from").val() != ""){
                                 values["price_from"] = $("#price_from").val();
                              }
                              if($("#price_to").val() != ""){
                                 values["price_to"] = $("#price_to").val();
                              }
                              if($("#bedrooms").val() != ""){
                                 values["bedrooms"] = $("#bedrooms").val();
                              }
                              if($("#bathrooms").val() != ""){
                                 values["bathrooms"] = $("#bathrooms").val();
                              }
                              if($("#landsize_from").val() != ""){
                                 values["landsize_from"] = $("#landsize_from").val();
                              }
                              if($("#landsize_to").val() != ""){
                                 values["landsize_to"] = $("#landsize_to").val();
                              }
                              if($("#buildingsize_from").val() != ""){
                                 values["buildingsize_from"] = $("#buildingsize_from").val();
                              }
                              if($("#buildingsize_to").val() != ""){
                                 values["buildingsize_to"] = $("#buildingsize_to").val();
                              }
                              if($("#order").val() != ""){
                                 values["order"] = $("#order").val();
                              }
                              if($("#limit").val() != ""){
                                 values["limit"] = $("#limit").val();
                              }

                              // Set default order and limit jika belum terset
                              if(!values["order"]) {
                                 values["order"] = "newest";
                              }
                              if(!values["limit"]) {
                                 values["limit"] = "9";
                              }

                              // Build URL parameters
                              const params = new URLSearchParams(values);
                              const finalUrl = action + "?" + params.toString();

                              console.log('Final redirect URL: ' + finalUrl); // Debug

                              // Redirect ke halaman Properti Dijual atau Properti Disewakan
                              window.location.href = finalUrl;

                              return false; // Prevent default form submission
                           });

                           var xhr;
                           $('#location').autoComplete({
                              minChars: 1,
                              source: function(term, response){
                                 try { xhr.abort(); } catch(e){}
                                 xhr = $.getJSON(base_url + 'listing/location', {'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content'), q: term }, function(data){

                                    response(data);
                                 });
                              },
                              renderItem: function (item, search){
                                 search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                                 var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
                                 return '<div class="autocomplete-suggestion" data-district="' + item[0] + '" data-city="' + item[0] + '" data-province="' + item[1] + '" data-val="' + search + '">' + item[0].replace(re, "<b>$1</b>") + ' <span style="color:#999;font-size:13px;">' + item[1] + ', </span> ' + '</span></div>';
                              },

                              onSelect: function(e, term, item){

                                 $('#location').val(item.data('city') + ', ' + item.data('province'));
                              }
                           });
                        });

                        </script>

                     </div>
                  </div>
               </div>
            </div>
         </section>

         <!--Iklan Terbaru Section Start-->
         <section class="about wf100 reveal" style="margin-top:50px;">
            <div class="container px-4 px-lg-5">
               <div class="row">
                  <div class="col-sm-12" style="padding:0px;">
                     <h1 style="font-size:28px;">Iklan Terbaru</h1>
                  </div>
                  <div class="col-sm-12" style="padding:0px;margin-bottom:20px;">
                     <p>Properti terbaru yang tersedia</p>
                  </div>
               <div class="row gx-4 gx-lg-5">
                  <?php foreach ($properties as $property) {

                      $image = DB::table("property_img")
                          ->where("id_property", $property->id_property)
                          ->orderBy("id_property_img")
                          ->first();
                      $labelType =
                          $property->tipe == "jual" ? "For Sell" : "For Rent";
                      if ($property->status == "0") {
                          $statusName = "";
                          $displayStatus = "display:none;";
                      } else {
                          $statusName = "Ter" . $property->tipe;
                          $displayStatus = "";
                      }
                      ?>
                  <div class="col-sm-12 col-md-4 col-lg-4 p-2">
                     <div class="card">
                        <a href="{{ asset('properti')."/".$property->id_property."/".$property->slug_property }}" title="{{ $property->nama_property }}">
                           <div class="zoom">
                              <img src="{{ asset('assets/upload/property/'.$image->gambar) }}" class="card-img-top" alt="{{ $property->nama_property }}" onerror="this.onerror=null;">
                           </div>
                           <span class="top-left-badge" id="badgelisting" style="background-color: hsla(0, 0%, 100%, 0.65);">{{ $labelType }}</span>
                           <span class="top-left-badge-2" id="badgelisting" style="background-color: hsla(0, 0%, 100%, 0.65);{{ $displayStatus }}">{{ $statusName }}</span>
                           <div class="card-body d-flex flex-column" style="height: 200px;">
                              <div class="text-truncate-container">
                                 <p class="font-weight-bold" style="font-size: 1.1rem;margin:0;">{{ $property->nama_property }} <span style="color:red;{{ $displayStatus }}">({{ $statusName }})<span></p>
                              </div>
                              <p class="card-text font-italic" style="margin:0;"> {{ ucwords(strtolower($property->nama_kabupaten.', '.$property->nama_provinsi)) }}</p>
                              <ul class="list-inline" style="margin:0;">
                                 <li class="list-inline-item"><i class="fas fa-bed"></i> {{ $property->kamar_tidur }}</li>
                                 <li class="list-inline-item"><i class="fas fa-bath"></i> {{ $property->kamar_mandi }}</li>
                                 <li class="list-inline-item"><i class="fas fa-expand"></i> {{ $property->lt }} m<sup>2</sup></li>
                                 <li class="list-inline-item"><i class="fas fa-building"></i> {{ $property->lb }} m<sup>2</sup></li>
                              </ul>
                              <div class=" bottom-0 end-0" style="position:absolute;bottom:0;right:0;margin-right:20px;">
                                 <p class="font-weight-bold text-align-right" style="font-size:20px;align-self:end;"> Rp. {{ number_format($property->harga) }}{{ (($property->tipe=='sewa') ? ' / '.ucwords($property->jenis_sewa) : '') }}</p>
                              </div>
                           </div>
                        </a>
                     </div>
                  </div>
                  <?php
                  } ?>
					</div>

                  <div class="row">
                     <div class="col-sm-12 text-center" style="padding:20px 0px;">
                        <a href="{{ asset('search/jual') }}" class="btn btn-success btn-lg">Lihat Selengkapnya</a>
                     </div>
                  </div>

               </div>
            </div>
         </section>
         <!-- <section class="home2-about wf100 p100 gallery" style="background: url({{ asset('assets/aws/images/news-artilcesbg.jpg') }}) no-repeat; background-size: cover;">
            <div class="container reveal">
               <div class="row">
                  <div class="col-md-5">
                        <div class="video-img"> <a href="https://youtu.be/{{ $video->video }}" data-rel="prettyPhoto" title="{{ $video->judul }}"><i class="fas fa-play"></i></a> <img src="{{ asset('assets/upload/image/'.$video->gambar) }}" alt=""> </div>
                  </div>
                  <div class="col-md-7">
                     <div class="h2-about-txt">
                        <h3></h3>
                        <h2>{{ $video->judul }}</h2>
                        <p><?php echo strip_tags($video->keterangan); ?></p>
                     </div>
                  </div>
               </div>
            </div>
         </section> -->
         <!--About Section End-->

         <!--Blog Start-->
         <section class="h2-news wf100 p80 blog">
            <div class="blog-grid reveal">
               <div class="container">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="section-title-2">
                           <h5>Baca update kami</h5>
                           <h2>Berita & Updates</h2>
                        </div>
                     </div>
                     <div class="col-md-6"> <a href="{{ asset('berita') }}" class="view-more">Lihat berita lainnya</a> </div>
                     <div class="col-md-12">
                        <hr>
                     </div>
                  </div>
                  <div class="row" style="background-color: white; padding-top: 20px; padding-bottom: 20px; border-radius: 5px;">
                     <?php foreach ($berita as $berita) { ?>
                     <!--Blog Small Post Start-->
                     <div class="col-md-4 col-sm-6" >
                        <div class="blog-post">
                           <div class="blog-thumb"> <a href="{{ asset('berita/read/'.$berita->slug_berita) }}"><i class="fas fa-link"></i></a> <img src="{{ asset('assets/upload/image/thumbs/'.$berita->gambar) }}" alt="<?php echo $berita->judul_berita; ?>"> </div>
                           <div class="post-txt">
                              <h5><a href="{{ asset('berita/read/'.$berita->slug_berita) }}"><?php echo $berita->judul_berita; ?></a></h5>
                              <ul class="post-meta">
                                 <li> <a href="{{ asset('berita/read/'.$berita->slug_berita) }}"><i class="fas fa-calendar-alt"></i> {{ tanggal('tanggal_id',$berita->tanggal_post)}}</a> </li>
                                 <li> <a href="{{ asset('berita/kategori/'.$berita->slug_kategori) }}"><i class="fas fa-sitemap"></i> {{ $berita->nama_kategori }}</a> </li>
                              </ul>
                              <p><?php echo \Illuminate\Support\Str::limit(
                                  strip_tags($berita->isi),
                                  100,
                                  $end = "...",
                              ); ?></p>
                              <a href="{{ asset('berita/read/'.$berita->slug_berita) }}" class="read-post">Baca detail</a>
                           </div>
                        </div>
                     </div>
                     <!--Blog Small Post End-->
                     <?php } ?>
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

<!--Testimonials Start-->
<section class="testimonials-section bg-white wf100 p80" style="display:none;">
   <div class="container">
      <div class="row">
         <div class="col-md-12">
            <div class="section-title-2 text-center">
               <h2>Download</h2>
            </div>
            <div id="testimonials" class="owl-carousel owl-theme">
               <?php
               $kategori_download = DB::table("kategori_download")
                   ->orderBy("kategori_download.urutan", "ASC")
                   ->get();
               foreach ($kategori_download as $kategori_download) { ?>
               <!--testimonials box start-->
               <div class="item">
                  <h4><?php echo $kategori_download->nama_kategori_download; ?></h4>
                  <hr>
                  <?php echo \Illuminate\Support\Str::limit(
                      strip_tags($kategori_download->keterangan),
                      100,
                      $end = "...",
                  ); ?>
                  <div class="tuser">
                     <a href="{{ asset('download/kategori/'.$kategori_download->slug_kategori_download) }}" class="btn btn-success"><i class="fa fa-laptop"></i> Lihat Detail</a>
                  </div>
               </div>
               <!--testimonials box End-->
               <?php }
               ?>
            </div>
         </div>
      </div>
   </div>
</section>
<!--Testimonials End-->
