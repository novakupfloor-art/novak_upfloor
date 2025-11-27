
```<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kepulauan Seribu Adventure - Pulau Pramuka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #0077be;
            --secondary-blue: #005f8a;
            --ocean-blue: #1e90ff;
            --gold: #ffffff;
            --light-gold: #d1d5db;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --dark-gray: #333333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-gray);
            line-height: 1.6;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: all 0.3s;
        }

        .navbar-brand {
            color: var(--white) !important;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .navbar-nav .nav-link {
            color: var(--white) !important;
            margin: 0 15px;
            transition: all 0.3s;
        }

        .navbar-nav .nav-link:hover {
            color: var(--gold) !important;
        }

        /* Hero Section */
        .hero-section {
            height: 100vh;
            background: linear-gradient(rgba(0,119,190,0.4), rgba(0,95,138,0.6)), url('{{ asset('assets/pulau/assets/pulau-pramuka-kepulauan-seribu-jakarta-indonesia-jakarta.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--white);
            position: relative;
        }

        .hero-content h1 {
            font-size: 4rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            animation: fadeInDown 1s;
        }

        .hero-content p {
            font-size: 1.5rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            animation: fadeInUp 1s;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--gold) 0%, var(--light-gold) 100%);
            color: var(--dark-gray);
            padding: 15px 40px;
            border: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 1.1rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(209,213,219,0.3);
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(209,213,219,0.4);
            color: var(--dark-gray);
        }

        /* Section Styles */
        .section-padding {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-bottom: 50px;
            position: relative;
            font-weight: bold;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--light-gold);
        }

        /* Location Section */
        .location-section {
            background: var(--light-gray);
        }

        /* Experience Section */
        .experience-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s;
            height: 100%;
        }

        .experience-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .experience-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .experience-card-body {
            padding: 25px;
        }

        .experience-card h3 {
            color: var(--primary-blue);
            margin-bottom: 15px;
            font-weight: bold;
        }

        /* Services Section */
        .services-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .service-item {
            background: var(--white);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            border-left: 4px solid var(--light-gold);
        }

        .service-item:hover {
            transform: translateX(10px);
            box-shadow: 0 5px 25px rgba(0,0,0,0.12);
        }

        .service-icon {
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-bottom: 15px;
        }

        /* Pricing Section */
        .pricing-card {
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s;
            height: 100%;
        }

        .pricing-card.featured {
            border: 2px solid var(--light-gold);
            transform: scale(1.05);
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .pricing-card.featured:hover {
            transform: scale(1.05) translateY(-10px);
        }

        .pricing-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
            padding: 20px;
            border-radius: 10px;
            margin: -30px -30px 20px -30px;
            text-align: center;
        }

        .pricing-card.featured .pricing-header {
            background: var(--light-gold);
            color: var(--dark-gray);
            font-weight: bold;
            border: 1px solid var(--light-gold);
            border-bottom: 2px solid var(--light-gold);
        }

        .price {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-blue);
        }

        .pricing-card.featured .price {
            color: var(--dark-gray);
        }

        /* Testimonials Section */
        .testimonials-section {
            background: var(--light-gray);
        }

        .testimonial-card {
            background: var(--white);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            position: relative;
        }

        .testimonial-card::before {
            content: '"';
            font-size: 4rem;
            color: var(--light-gold);
            position: absolute;
            top: -10px;
            left: 20px;
            opacity: 0.3;
        }

        .testimonial-content {
            margin-top: 20px;
            font-style: italic;
        }

        .testimonial-author {
            text-align: right;
            font-weight: bold;
            color: var(--primary-blue);
            margin-top: 15px;
        }

        /* Contact Section */
        .contact-section {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
        }

        .contact-section .section-title {
            color: var(--white);
        }

        .contact-section .section-title::after {
            background: var(--gold);
        }

        .contact-info {
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }

        .contact-item {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .contact-icon {
            font-size: 1.5rem;
            color: var(--light-gold);
            margin-right: 15px;
            width: 40px;
        }

        /* Footer */
        footer {
            background: var(--dark-gray);
            color: var(--white);
            padding: 30px 0;
            text-align: center;
        }

        footer a {
            color: var(--white);
            transition: color 0.3s;
        }

        footer a:hover {
            color: var(--light-gold);
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-content p {
                font-size: 1.2rem;
            }

            .section-title {
                font-size: 2rem;
            }
        }

        /* Scroll to top button */
        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--light-gold);
            color: var(--dark-gray);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            transition: all 0.3s;
            z-index: 999;
        }

        .scroll-top.show {
            opacity: 1;
        }

        .scroll-top:hover {
            background: #e5e7eb;
            transform: translateY(-5px);
        }

        .alert-warning {
            background: #f3f4f6;
            border: 1px solid var(--light-gold);
            color: var(--dark-gray);
        }

        .text-warning {
            color: var(--light-gold) !important;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('pulau.index') }}">
                <i class="fas fa-anchor"></i> Kepulauan Seribu Adventure
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pulau.index') }}#Travel">Travel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pulau.index') }}#location">Lokasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pulau.index') }}#experience">Pengalaman</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pulau.index') }}#services">Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pulau.index') }}#pricing">Harga</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pulau.index') }}#contact">Informasi</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="Travel" class="hero-section">
        <div class="hero-content">
            <h1 data-aos="fade-down">Explore Kepulauan Seribu</h1>
            <p class="lead" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-map-marker-alt"></i> Pulau Pramuka - Petualangan Laut Terbaik di Jakarta
            </p>
        </div>
    </section>

    <!-- Location Section -->
    <section id="location" class="location-section section-padding">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">
                <i class="fas fa-map-pin"></i> Lokasi Wisata
            </h2>
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <img src="{{ asset('assets/pulau/assets/Pulau-Pramuka.jpg') }}" alt="Pulau Pramuka" class="img-fluid rounded shadow">
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <h3 class="mb-3">Pulau Pramuka - Surga di Teluk Jakarta</h3>
                    <p>
                        Pulau Pramuka merupakan salah satu pulau terindah di kepulauan Seribu yang terletak di Teluk Jakarta.
                        Pulau ini menawarkan pengalaman liburan yang sempurna dengan keindahan alam laut yang masih asri
                        dan terumbu karang yang memukau.
                    </p>
                    <p>
                        Dengan pantai pasir putih yang halus dan air laut yang jernih, Pulau Pramuka menjadi destinasi
                        favorit untuk melarikan diri dari hiruk pikuk kota Jakarta. Nikmati matahari terbenam yang romantis
                        dan keindahan bawah laut yang menakjubkan.
                    </p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-primary"></i> Terumbu karang yang masih alami</li>
                        <li><i class="fas fa-check-circle text-primary"></i> Pantai pasir putih yang eksotis</li>
                        <li><i class="fas fa-check-circle text-primary"></i> Akses mudah dari Jakarta</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Experience Section -->
    <section id="experience" class="experience-section section-padding">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">
                <i class="fas fa-star"></i> Pengalaman Utama
            </h2>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="experience-card">
                        <img src="{{ asset('assets/pulau/assets/snorkling-di-pulau-pramuka-kep-seribu.jpg') }}" alt="Snorkeling">
                        <div class="experience-card-body">
                            <h3><i class="fas fa-fish"></i> Snorkeling di Terumbu Karang</h3>
                            <p>
                                Jelajahi keindahan bawah laut dengan snorkeling langsung ke terumbu karang yang spektakuler.
                                Anda akan melihat berbagai jenis ikan warna-warni, bulu babi, dan terumbu karang yang masih hidup.
                            </p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Peralatan lengkap</li>
                                <li><i class="fas fa-check text-success"></i> Pemandu berpengalaman</li>
                                <li><i class="fas fa-check text-success"></i> Spot terumbu karang terbaik</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="experience-card">
                        <img src="{{ asset('assets/pulau/assets/pantai-gusung-pertrik-pulau-pramuka-kepulauan-seribu-637x478.jpeg') }}" alt="Pantai">
                        <div class="experience-card-body">
                            <h3><i class="fas fa-umbrella-beach"></i> Pantai Pasir Putih</h3>
                            <p>
                                Bersantai di pantai dengan pasir putih yang halus dan air laut yang jernih.
                                Sempurna untuk berfoto, berenang, atau sekadar menikmati keindahan alam.
                            </p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Pasir putih lembut</li>
                                <li><i class="fas fa-check text-success"></i> Air laut jernih</li>
                                <li><i class="fas fa-check text-success"></i> Spot sunset romantis</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="experience-card">
                        <img src="{{ asset('assets/pulau/assets/1_TbPs3rRgvmYh9Cj9EYjvDA.jpg') }}" alt="Diving">
                        <div class="experience-card-body">
                            <h3><i class="fas fa-scuba-diving"></i> Diving & Underwater Photography</h3>
                            <p>
                                Untuk para penggembira olahraga bawah laut, tersedia paket diving dengan instruktur bersertifikat
                                internasional. Foto underwater profesional juga tersedia.
                            </p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Instruktur bersertifikat</li>
                                <li><i class="fas fa-check text-success"></i> Foto profesional</li>
                                <li><i class="fas fa-check text-success"></i> Prewedding underwater</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section section-padding">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">
                <i class="fas fa-concierge-bell"></i> Layanan Kami
            </h2>
            <div class="row">
                <div class="col-lg-6" data-aos="fade-right" data-aos-delay="100">
                    <div class="service-item">
                        <div class="service-icon">
                            <i class="fas fa-shuttle-van"></i>
                        </div>
                        <h4>Transportasi</h4>
                        <p>
                            <strong>Jemputan dari hotel/lokasi terpilih di Jakarta</strong><br>
                            Kapal ferri yang nyaman dan aman<br>
                            Antar-jemput ke/dari pelabuhan Muara Angke<br>
                            Driver profesional dan berpengalaman
                        </p>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                    <div class="service-item">
                        <div class="service-icon">
                            <i class="fas fa-hotel"></i>
                        </div>
                        <h4>Penginapan Travelstay</h4>
                        <p>
                            <strong>Akomodasi sederhana namun nyaman</strong><br>
                            Lokasi dekat pantai dengan pemandangan laut<br>
                            Fasilitas kamar bersih dan terawat<br>
                            Rumah makan dengan hidangan lokal
                        </p>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-right" data-aos-delay="300">
                    <div class="service-item">
                        <div class="service-icon">
                            <i class="fas fa-coffee"></i>
                        </div>
                        <h4>Sarapan Pagi</h4>
                        <p>
                            <strong>Menu sarapan tradisional dan lezat</strong><br>
                            Makanan segar dengan bahan lokal<br>
                            Minuman hangat dan kesegaran<br>
                            Persiapan energi untuk aktivitas seharian
                        </p>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="400">
                    <div class="service-item">
                        <div class="service-icon">
                            <i class="fas fa-camera"></i>
                        </div>
                        <h4>Foto Underwater</h4>
                        <p>
                            <strong>Fotografi profesional di bawah laut</strong><br>
                            Drone untuk foto aerial pantai<br>
                            Editing dan penyerahan foto berkualitas tinggi<br>
                            Paket foto pre-wedding tersedia
                        </p>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-right" data-aos-delay="500">
                    <div class="service-item">
                        <div class="service-icon">
                            <i class="fas fa-swimmer"></i>
                        </div>
                        <h4>Snorkeling & Water Sports</h4>
                        <p>
                            <strong>Peralatan snorkeling lengkap</strong><br>
                            Pemandu snorkeling berpengalaman<br>
                            Asuransi keselamatan termasuk<br>
                            Berbagai spot snorkeling terbaik
                        </p>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="600">
                    <div class="service-item">
                        <div class="service-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h4>Pemandu Lokal</h4>
                        <p>
                            <strong>Guide wisata berpengetahuan</strong><br>
                            Bahasa Indonesia, Inggris, dan Mandarin<br>
                            Penjelasan detail tentang flora & fauna<br>
                            Rekomendasi makanan & aktivitas lokal
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing-section section-padding">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">
                <i class="fas fa-tags"></i> Paket Harga
            </h2>
            <div class="row">
                <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="pricing-card">
                        <div class="pricing-header">
                            <h3>Paket 2 Hari 1 Malam</h3>
                            <p class="mb-0">Petualangan Singkat</p>
                        </div>
                        <div class="text-center mb-4">
                            <span class="price">Rp 450.000</span>
                            <p class="text-muted">mulai dari 20 orang</p>
                        </div>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-users text-primary"></i> 20 orang: Rp 450.000/orang</li>
                            <li><i class="fas fa-users text-primary"></i> 15 orang: Rp 510.000/orang</li>
                            <li><i class="fas fa-users text-primary"></i> 10 orang: Rp 540.000/orang</li>
                            <li><i class="fas fa-users text-primary"></i> 9 orang: Rp 580.000/orang</li>
                            <li><i class="fas fa-users text-primary"></i> 8 orang: Rp 640.000/orang</li>
                            <li><i class="fas fa-users text-primary"></i> 5 orang: Rp 620.000/orang</li>
                        </ul>
                        <div class="mt-4">
                            <h5><i class="fas fa-check text-success"></i> Termasuk:</h5>
                            <ul class="small">
                                <li>✅ Transportasi darat</li>
                                <li>✅ Kapal ferri PP</li>
                                <li>✅ Akomodasi 1 malam</li>
                                <li>✅ Sarapan pagi</li>
                                <li>✅ Peralatan snorkeling</li>
                                <li>✅ Pemandu wisata</li>
                                <li>✅ Asuransi dasar</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="pricing-card featured">
                        <div class="pricing-header">
                            <h3><i class="fas fa-crown"></i> Paket 3 Hari 2 Malam</h3>
                            <p class="mb-0">Petualangan Lengkap</p>
                        </div>
                        <div class="text-center mb-4">
                            <span class="price">Rp 700.000</span>
                            <p class="text-muted">mulai dari 20 orang</p>
                        </div>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-users text-primary"></i> 20 orang: Rp 700.000/orang</li>
                            <li><i class="fas fa-users text-primary"></i> 15 orang: Rp 786.000/orang</li>
                            <li><i class="fas fa-users text-primary"></i> 10 orang: Rp 790.000/orang</li>
                            <li><i class="fas fa-users text-primary"></i> 9 orang: Rp 795.000/orang</li>
                            <li><i class="fas fa-users text-primary"></i> 8 orang: Rp 930.000/orang</li>
                            <li><i class="fas fa-users text-primary"></i> 5 orang: Rp 940.000/orang</li>
                        </ul>
                        <div class="mt-4">
                            <h5><i class="fas fa-check text-success"></i> Termasuk:</h5>
                            <ul class="small">
                                <li>✅ Transportasi darat</li>
                                <li>✅ Kapal ferri PP</li>
                                <li>✅ Akomodasi 2 malam</li>
                                <li>✅ Sarapan pagi (2 hari)</li>
                                <li>✅ Peralatan snorkeling</li>
                                <li>✅ Pemandu wisata</li>
                                <li>✅ Asuransi dasar</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle"></i> Tidak Termasuk:</h5>
                        <ul class="mb-0">
                            <li>❌ Makan siang & malam (opsional)</li>
                            <li>❌ Foto underwater profesional</li>
                            <li>❌ Aktivitas ekstra (diving, jet ski)</li>
                            <li>❌ Pengeluaran pribadi</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="alert alert-success">
                        <h5><i class="fas fa-gift"></i> Promo Spesial:</h5>
                        <ul class="mb-0">
                            <li>🎉 Diskon 10% untuk grup 20+ orang</li>
                            <li>🎉 Hemat 15% untuk 5 keluarga</li>
                            <li>🎉 Diskon 5% booking 1 bulan sebelumnya</li>
                            <li>🎉 Voucher Rp 100.000 per referral</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials-section section-padding">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">
                <i class="fas fa-comments"></i> Testimoni Pelanggan
            </h2>
            <div class="row">
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            "Liburan terbaik bersama keluarga! Pemandangan laut yang spektakuler dan pemandu yang ramah."
                        </div>
                        <div class="testimonial-author">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <br>
                            - Ibu Siti, Jakarta
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            "Snorkeling di sini seru banget! Ikan-ikan berwarna-warni banyak sekali. Pasti balik lagi!"
                        </div>
                        <div class="testimonial-author">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <br>
                            - Budi, Tangerang
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            "Paket 3 hari 2 malam sangat worth it. Akomodasi bersih, makanan enak, dan pelayanan memuaskan."
                        </div>
                        <div class="testimonial-author">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <br>
                            - Keluarga Wijaya
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section section-padding">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">
                <i class="fas fa-envelope"></i> Informasi Komunikasi
            </h2>
            <div class="row">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="contact-info">
                        <h4 class="mb-4">Hubungi Kami</h4>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <strong>Telepon:</strong> +62 812-XXXX-XXXX
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div>
                                <strong>WhatsApp:</strong> +62 812-XXXX-XXXX
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <strong>Email:</strong> info@Kepulauan-seribu.com
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div>
                                <strong>Website:</strong> www.Kepulauan-seribu.com
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <strong>Lokasi Kantor:</strong><br>
                                Jl. Dermaga Muara Angke, Jakarta Utara
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="contact-info">
                        <h4 class="mb-4">Mengapa Memilih Kami?</h4>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-warning"></i> Pengalaman lebih dari 10 tahun
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-warning"></i> Harga kompetitif dan transparan
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-warning"></i> Pemandu profesional
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-warning"></i> Keselamatan prioritas utama
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-warning"></i> Fleksibel dalam menyesuaikan paket
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-warning"></i> Standar kebersihan terjaga
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-warning"></i> Paket keluarga spesial
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-warning"></i> Gratis konsultasi
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p class="mb-0">
                <i class="fas fa-copyright"></i> 2024 Kepulauan Seribu Adventure - Petualangan Laut Anda Menanti! 🌊⛵
            </p>
            <p class="mb-0 mt-2">
                <a href="#" class="me-3"><i class="fab fa-facebook"></i></a>
                <a href="#" class="me-3"><i class="fab fa-instagram"></i></a>
                <a href="#" class="me-3"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </p>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <div class="scroll-top" id="scrollTop">
        <i class="fas fa-arrow-up"></i>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Scroll to top button
        const scrollTopBtn = document.getElementById('scrollTop');

        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollTopBtn.classList.add('show');
            } else {
                scrollTopBtn.classList.remove('show');
            }
        });

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                navbar.style.padding = '0.5rem 0';
            } else {
                navbar.style.padding = '1rem 0';
            }
        });
    </script>
</body>
</html>
