<?php
$bg = DB::table("heading")
    ->where("halaman", "Search")
    ->orderBy("id_heading", "DESC")
    ->first(); ?>
<style>
    .property-agent-card .small-map {
        width: 100%;
        height: 240px;
        overflow: hidden;
        border-radius: 0 0 .25rem .25rem;
    }

    .property-agent-card .small-map iframe {
        width: 100% !important;
        height: 240px !important;
        border: 0;
    }

    /* KPR Calculator Styles */
    #kprCalculatorForm .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.3rem;
    }

    #kprCalculatorForm input[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    #kprResult {
        animation: fadeIn 0.5s;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    #result_cicilan {
        font-size: 1.1rem;
    }

    .card-header {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    /* Swiper Custom Styles */
    .mainSwiper {
        width: 100%;
        height: auto;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .mainSwiper .swiper-slide {
        display: flex;
        justify-content: center;
        align-items: center;
        background: #f5f5f5;
    }

    .mainSwiper .property-image-wrapper {
        position: relative;
        width: 100%;
        cursor: pointer;
    }

    .mainSwiper .property-image-wrapper img {
        width: 100%;
        height: auto;
        max-height: 600px;
        object-fit: contain;
        display: block;
    }

    .mainSwiper .swiper-button-next,
    .mainSwiper .swiper-button-prev {
        background: rgba(255, 255, 255, 0.9);
        width: 44px;
        height: 44px;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .mainSwiper .swiper-button-next:after,
    .mainSwiper .swiper-button-prev:after {
        font-size: 20px;
        font-weight: bold;
        color: #333;
    }

    .mainSwiper .swiper-pagination-bullet {
        background: #fff;
        opacity: 0.7;
        width: 10px;
        height: 10px;
    }

    .mainSwiper .swiper-pagination-bullet-active {
        background: #007bff;
        opacity: 1;
    }

    /* Thumbnail Swiper Styles */
    .thumbSwiper {
        width: 100%;
        height: auto;
        padding: 0.5rem 0;
    }

    .thumbSwiper .swiper-slide {
        width: 110px !important;
        height: 80px !important;
        cursor: pointer;
        opacity: 0.5;
        transition: opacity 0.3s ease;
        border-radius: 4px;
        overflow: hidden;
        border: 2px solid transparent;
    }

    .thumbSwiper .swiper-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .thumbSwiper .swiper-slide-thumb-active {
        opacity: 1;
        border-color: #007bff;
    }

    .thumbSwiper .swiper-slide:hover {
        opacity: 0.8;
    }

    @media (max-width: 768px) {
        .mainSwiper .property-image-wrapper img {
            max-height: 400px;
        }

        .thumbSwiper .swiper-slide {
            width: 80px !important;
            height: 60px !important;
        }

        .mainSwiper .swiper-button-next,
        .mainSwiper .swiper-button-prev {
            width: 36px;
            height: 36px;
        }

        .mainSwiper .swiper-button-next:after,
        .mainSwiper .swiper-button-prev:after {
            font-size: 16px;
        }
    }
</style>
<!--Inner Header Start-->
<section class="wf100 p80 inner-header" style="background-image: url('{{ asset('assets/upload/image/'.$bg->gambar) }}'); background-position: bottom center;">
</section>
         <!--Service Area Start-->
         <section class="donation-join wf100">
			<div class="container px-4 px-lg-5" style="padding-top:50px;">
				<div class="row flex-column-reverse flex-lg-row" data-aos="fade-up">
					<div class="col-sm-12 col-md-12 col-lg-4 order-sm-1">
						<div class="card text-center property-agent-card">
							<div class="card-body">
							<div class="d-none d-lg-block d-xl-block text-center py-3">
								<h5><span class="badge bg-gray">{{ ucwords($property->nama_kategori_property) }} for {{ (($property->tipe=='jual') ? 'Sell' : 'Rent') }}</span></h5>
								<h1 style="font-weight: 400; font-size:28px;">{{ ucwords($property->nama_property) }}</h1>
								<h5 style="font-weight: 200;">{{ $property->nama_kabupaten.', '.$property->nama_provinsi }}</h5>
								<hr>
								<h6 style="font-weight: 200;">OFFERS OVER</h6>
									<p class="h3 mb-3 text-center" id="price"> Rp. {{ number_format($property->harga) }}{{ (($property->tipe=='sewa') ? ' / '.ucwords($property->jenis_sewa) : '') }}</p>

							</div>
							<div class="card text-start mb-3">
								<img src="{{ ($staff->gambar!="") ? asset('assets/upload/staff/thumbs/'.$staff->gambar) : asset('assets/aws/images/no-profile.png') }}" class="img-fluid" alt="Tim Marketing">
								<div class="card-body">
									<h5 class="card-title text-left"><a href="{{ asset('agent')."/".$staff->id_staff }}">{{ $staff->nama_staff }}</a></h5>
									<div class="card-text mb-2 text-left"><a href="#">{{ $staff->nama_kategori_staff }}</a>
								</div>

								<?php
        // Should use UTF8 for Whatsapp
        $msg =
            "Hello " .
            $staff->nama_staff .
            ", Saya tertarik dengan properti ini : " .
            url()->current() .
            " apakah masih tersedia ?";
        $msg = utf8_encode($msg);
        // Whatsapp patterns
        $nl = "%0D%0A"; // newline
        $space = "%20"; // space
        // Replace some Whatstapp tags
        $msg = str_replace(
            ["<b>", "<bold>", "</b>", "</bold>"],
            ["*", "*", "*", "*"],
            $msg,
        );
        // Replace newline to Whatsapp format
        $msg = str_replace(
            [" ", "<br>", "\n", "\r\n"],
            [$space, $nl, $nl, $nl],
            $msg,
        );
        ?>

								<div class="d-grid gap-2">
									<a href="tel:{{ substr_replace($staff->telepon, '62', 0, 1) }}" class="btn btn-outline-dark btn-block" style="margin-bottom:10px;" type="button">Call {{ $staff->nickname_staff }}</a>
									<a target="_blank" href="https://wa.me/{{ substr_replace($staff->telepon, '62', 0, 1) }}?text={{ $msg }}" class="btn btn-outline-success btn-block"  type="button">Whatsapp</a>
								</div>
							</div>
						</div>
						@if(!empty($property->peta_map))
						<div class="card mt-3 text-start">
							<div class="card-header">Peta Lokasi</div>
							<div class="card-body p-0">
								<div class="small-map">
									{!! $property->peta_map !!}
								</div>
							</div>
						</div>
						@endif

						@if($property->tipe == 'jual')
						<div class="card mt-3 text-start">
							<div class="card-header">
								<i class="fas fa-calculator"></i> Kalkulator KPR
							</div>
							<div class="card-body">
								<form id="kprCalculatorForm">
									<div class="mb-3">
										<label class="form-label small">Harga Properti</label>
										<input type="text" class="form-control form-control-sm" id="kpr_harga" value="{{ number_format($property->harga, 0, ',', '.') }}" readonly>
										<input type="hidden" id="kpr_harga_val" value="{{ $property->harga }}">
									</div>
									<div class="mb-3">
										<label class="form-label small">Uang Muka (DP) %</label>
										<input type="number" class="form-control form-control-sm" id="kpr_dp" value="20" min="0" max="100">
									</div>
									<div class="mb-3">
										<label class="form-label small">Tenor (Tahun)</label>
										<select class="form-select form-select-sm" id="kpr_tenor">
											<option value="5">5 Tahun</option>
											<option value="10" selected>10 Tahun</option>
											<option value="15">15 Tahun</option>
											<option value="20">20 Tahun</option>
											<option value="25">25 Tahun</option>
											<option value="30">30 Tahun</option>
										</select>
									</div>
									<div class="mb-3">
										<label class="form-label small">Bunga per Tahun (%)</label>
										<input type="number" class="form-control form-control-sm" id="kpr_bunga" value="8.5" step="0.1" min="0">
									</div>
									<button type="button" class="btn btn-primary btn-sm w-100" onclick="hitungKPR()">
										<i class="fas fa-calculator"></i> Hitung Cicilan
									</button>
								</form>

								<div id="kprResult" class="mt-3" style="display:none;">
									<hr>
									<h6 class="mb-3">Hasil Perhitungan:</h6>
									<div class="table-responsive">
										<table class="table table-sm table-borderless">
											<tr>
												<td class="small">Uang Muka:</td>
												<td class="small text-end"><strong id="result_dp">-</strong></td>
											</tr>
											<tr>
												<td class="small">Jumlah Pinjaman:</td>
												<td class="small text-end"><strong id="result_pinjaman">-</strong></td>
											</tr>
											<tr class="table-success">
												<td class="small"><strong>Cicilan/Bulan:</strong></td>
												<td class="small text-end"><strong id="result_cicilan" class="text-success">-</strong></td>
											</tr>
											<tr>
												<td class="small">Total Bayar:</td>
												<td class="small text-end"><strong id="result_total">-</strong></td>
											</tr>
										</table>
									</div>
									<div class="alert alert-info small mb-0" role="alert">
										<i class="fas fa-info-circle"></i> Perhitungan ini adalah estimasi. Hubungi kami untuk detail lebih lanjut.
									</div>
								</div>
							</div>
						</div>
						@endif
					</div>
				</div>
			</div>

			<?php if ($property->status == "0") {
       $statusName = "";
       $displayStatus = "display:none;";
   } else {
       $statusName = "Ter" . $property->tipe;
       $displayStatus = "";
   } ?>


			<div class="col-sm-12 col-md-12 col-lg-8 order-sm-2">
{{-- AWAL BLOK TOMBOL SHARE --}}
@php
    $shareText = "Saya tertarik dengan properti ini: " . url()->current();
    $encodedShareText = urlencode($shareText);
    $encodedUrl = urlencode(url()->current());
@endphp
<div class="d-flex justify-content-end">
    <a href="https://wa.me/?text={{ $encodedShareText }}" target="_blank" class="btn btn-success" style="margin-right: 10px;">
        <i class="fab fa-whatsapp"></i> Share to WhatsApp
    </a>
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}" target="_blank" class="btn btn-primary">
        <i class="fab fa-facebook"></i> Share to Facebook
    </a>
</div>
{{-- AKHIR BLOK TOMBOL SHARE --}}


				<div class="tab-content" id="nav-tabContent">
					<div class="tab-pane fade show active" id="photos" role="tabpanel" aria-labelledby="photos-tab">
						<!-- Main Swiper -->
						<div class="swiper mainSwiper" style="margin-bottom: 1rem;">
							<div class="swiper-wrapper">
								<?php foreach ($images as $key => $img) { ?>
								<div class="swiper-slide">
									<div class="property-image-wrapper" data-src="{{ asset('assets/upload/property/'.$img->gambar) }}" data-fancybox="gallery">
										<img src="{{ asset('assets/upload/property/'.$img->gambar) }}" alt="{{ $property->slug_property }}" class="img-fluid" onerror="this.onerror=null;" />
										<div class="top-right-badge d-flex flex-row" style="position: absolute; top: 10px; right: 10px; z-index: 10;">
											<span style="background-color:red;color:#fff;margin-right:15px;padding: 5px;{{ $displayStatus }}">{{ $statusName }}</span>
											<span style="background-color:hsla(0, 0%, 100%, 0.65);margin-right:10px;padding: 5px;">{{ (($property->tipe == 'jual') ? 'For Sell' : 'For Rent') }}</span>
										</div>
									</div>
								</div>
								<?php } ?>
							</div>
							<!-- Navigation buttons -->
							<div class="swiper-button-next"></div>
							<div class="swiper-button-prev"></div>
							<!-- Pagination -->
							<div class="swiper-pagination"></div>
						</div>

						<!-- Thumbnail Swiper -->
						<div class="swiper thumbSwiper">
							<div class="swiper-wrapper">
								<?php foreach ($images as $key => $img) { ?>
								<div class="swiper-slide">
									<img src="{{ asset('assets/upload/property/'.$img->gambar) }}" alt="thumbnail property" onerror="this.onerror=null;" />
								</div>
								<?php } ?>
							</div>
						</div>
					</div>

					<div class="tab-pane fade" id="video" role="tabpanel" aria-labelledby="video-tab">
						<div class="d-flex justify-content-center">
							<script async src="//www.instagram.com/embed.js"></script>
						</div>
					</div>
				</div>
				<nav>
					<div class="nav nav-tabs-agent justify-content-center" id="nav-tab" role="tablist">
					</div>
				</nav>
				<br>

				<div class="d-sm-block d-md-block d-lg-none text-center py-3">
					<h5><span class="badge bg-gray">{{ ucwords($property->nama_kategori_property) }} for {{ (($property->tipe=='jual') ? 'Sell' : 'Rent') }} </span></h5>
					<p style="font-weight: 400; font-size:28px; margin-top: 0;margin-bottom: 0.5rem;line-height: 1.2;font-family:'Playfair Display';">
					{{ $property->nama_property }}
					</p>
					<p class="h5" style="font-weight: 200;">{{ $property->nama_kabupaten.', '.$property->nama_provinsi }}</h5>
					<hr>
					<p class="h6" style="font-weight: 200;">OFFERS OVER</h6>
					<div class="row">
						<p class="h3 mb-3" id="prices"> Rp. {{ number_format($property->harga) }}{{ (($property->tipe=='sewa') ? ' / '.ucwords($property->jenis_sewa) : '') }}</p>
					</div>
				</div>

				<h2 style="font-size:28px;">
				{{ ucwords($property->nama_kategori_property) }} for {{ (($property->tipe=='jual') ? 'Sell' : 'Rent') }} in {{ $property->alamat }}, {{ $property->nama_kecamatan }} <span style="color:red;{{ $displayStatus }}">({{ $statusName }})<span>
				</h2>
				{!! $property->isi !!}
				<hr>
				<h3>Specification</h3>
				<div class="table-responsive">
					<table class="table table-borderless">
						<tbody>
							<tr>
								<td style="width: 25px;"><i class="fas fa-rectangle-list detail-icon"></i></td>
								<td style="width: 150px;">Listing Code </td>
								<td>: {{ $property->kode }}</td>
							</tr>
							<tr>
								<td style="width: 25px;"><i class="fas fa-building detail-icon"></i></td>
								<td style="width: 150px;">Building Size</td>
								<td>: {{ $property->lb }} m<sup>2</sup></td>
							</tr>
							<tr>
								<td style="width: 25px;"><i class="fas fa-expand detail-icon"></i></td>
								<td style="width: 150px;">Land Size </td>
								<td>:  {{ $property->lt }} m<sup>2</sup></td>
							</tr>
							<tr>
								<td style="width: 25px;"><i class="fas fa-bed detail-icon"></i></td>
								<td style="width: 150px;">Bedroom </td>
								<td>: {{ $property->kamar_tidur }}</td>
							</tr>
							<tr>
								<td style="width: 25px;"><i class="fas fa-bath detail-icon"></i></td>
								<td style="width: 150px;">Bathroom </td>
								<td>: {{ $property->kamar_mandi }}</td>
							</tr>
						</tbody>
					</table>
				</div>

				@if(!empty($property->harga_rata))
				<hr>
				<h3>Harga {{ ucfirst($property->tipe) }} Rata-rata</h3>
				<p>{!! nl2br(e($property->harga_rata)) !!}</p>
				@endif

				@if(!empty($property->fasilitas_terdekat))
				<hr>
				<h3>Fasilitas Terdekat</h3>
				<div class="card">
					<div class="card-body">
						{!! $property->fasilitas_terdekat !!}
					</div>
				</div>
				@endif

			</div>
		</div>
	</div>
</div>
</section>


         <!--Blog Start-->
         <section class="h2-news wf100 p80 blog">
            <div class="blog-grid">
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

		<script>
			$(function() {

				// Initialize Thumbnail Swiper
				const thumbSwiper = new Swiper('.thumbSwiper', {
					spaceBetween: 10,
					slidesPerView: 'auto',
					freeMode: true,
					watchSlidesProgress: true,
					breakpoints: {
						320: {
							slidesPerView: 3,
							spaceBetween: 8
						},
						480: {
							slidesPerView: 4,
							spaceBetween: 8
						},
						640: {
							slidesPerView: 5,
							spaceBetween: 10
						},
						768: {
							slidesPerView: 6,
							spaceBetween: 10
						},
						1024: {
							slidesPerView: 8,
							spaceBetween: 10
						}
					}
				});

				// Initialize Main Swiper
				const mainSwiper = new Swiper('.mainSwiper', {
					spaceBetween: 10,
					navigation: {
						nextEl: '.swiper-button-next',
						prevEl: '.swiper-button-prev',
					},
					pagination: {
						el: '.swiper-pagination',
						clickable: true,
						dynamicBullets: true,
					},
					thumbs: {
						swiper: thumbSwiper,
					},
					keyboard: {
						enabled: true,
					},
					mousewheel: false,
					loop: false,
				});

				// Customize Fancybox for lightbox gallery
				Fancybox.bind('[data-fancybox="gallery"]', {
					Carousel: {
						on: {
							change: (that) => {
								// Sync main swiper with fancybox
								mainSwiper.slideTo(that.page);
							},
						},
					},
					Toolbar: {
						display: {
							left: ["infobar"],
							middle: [],
							right: ["slideshow", "thumbs", "close"],
						},
					},
				});

			})

			// KPR Calculator Function
			function hitungKPR() {
				// Ambil nilai input
				const hargaProperti = parseFloat(document.getElementById('kpr_harga_val').value);
				const dpPersen = parseFloat(document.getElementById('kpr_dp').value);
				const tenor = parseInt(document.getElementById('kpr_tenor').value);
				const bungaTahunan = parseFloat(document.getElementById('kpr_bunga').value);

				// Validasi input
				if (isNaN(hargaProperti) || isNaN(dpPersen) || isNaN(tenor) || isNaN(bungaTahunan)) {
					alert('Mohon isi semua field dengan benar');
					return;
				}

				if (dpPersen < 0 || dpPersen > 100) {
					alert('DP harus antara 0-100%');
					return;
				}

				// Hitung nilai
				const dp = hargaProperti * (dpPersen / 100);
				const pinjaman = hargaProperti - dp;
				const bungaBulanan = bungaTahunan / 100 / 12;
				const jumlahBulan = tenor * 12;

				// Rumus cicilan menggunakan anuitas
				let cicilan = 0;
				if (bungaBulanan > 0) {
					cicilan = pinjaman * (bungaBulanan * Math.pow(1 + bungaBulanan, jumlahBulan)) /
							  (Math.pow(1 + bungaBulanan, jumlahBulan) - 1);
				} else {
					cicilan = pinjaman / jumlahBulan;
				}

				const totalBayar = (cicilan * jumlahBulan) + dp;

				// Format currency
				const formatRupiah = (angka) => {
					return 'Rp ' + angka.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
				};

				// Tampilkan hasil
				document.getElementById('result_dp').textContent = formatRupiah(dp);
				document.getElementById('result_pinjaman').textContent = formatRupiah(pinjaman);
				document.getElementById('result_cicilan').textContent = formatRupiah(cicilan);
				document.getElementById('result_total').textContent = formatRupiah(totalBayar);

				// Show result
				document.getElementById('kprResult').style.display = 'block';

				// Smooth scroll to result
				document.getElementById('kprResult').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
			}
		</script>
