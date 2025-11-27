<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Kami - Kepulauan Seribu Adventure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #0077be;
            --secondary-blue: #005f8a;
            --gold: #ffd700;
            --light-gold: #ffed4e;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --dark-gray: #333333;
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
        .contact-hero {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .contact-hero h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Contact Section */
        .contact-section {
            padding: 80px 0;
            background: var(--light-gray);
        }

        .contact-card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 30px;
            transition: all 0.3s;
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid #e9ecef;
        }

        .contact-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 1.5rem;
            color: var(--white);
        }

        .contact-content h5 {
            color: var(--primary-blue);
            margin-bottom: 5px;
            font-weight: bold;
        }

        .contact-content p {
            margin: 0;
            font-size: 1.1rem;
        }

        /* Form Section */
        .form-section {
            padding: 80px 0;
            background: var(--white);
        }

        .form-card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(0,119,190,0.25);
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
            padding: 15px 40px;
            border: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 1.1rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(0,119,190,0.3);
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,119,190,0.4);
            color: var(--white);
        }

        /* Map Section */
        .map-section {
            padding: 0;
            height: 400px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            position: relative;
        }

        .map-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255,255,255,0.95);
            padding: 20px 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
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
            color: var(--gold);
        }

        @media (max-width: 768px) {
            .contact-hero h1 {
                font-size: 2rem;
            }

            .contact-card {
                padding: 25px;
            }

            .form-card {
                padding: 25px;
            }
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
                        <a class="nav-link" href="{{ route('pulau.index') }}">Travel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pulau.about') }}">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pulau.packages') }}">Paket</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pulau.index') }}#experience">Pengalaman</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('pulau.contact') }}">Informasi</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="container">
            <h1 data-aos="fade-down">Hubungi Kami</h1>
            <p class="lead" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-phone"></i> Kami siap membantu mewujudkan liburan impian Anda
            </p>
        </div>
    </section>

    <!-- Contact Information Section -->
    <section class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="contact-card">
                        <h3 class="mb-4">
                            <i class="fas fa-info-circle"></i> Informasi Komunikasi
                        </h3>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-content">
                                <h5>Telepon</h5>
                                <p>+62 812-XXXX-XXXX</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div class="contact-content">
                                <h5>WhatsApp</h5>
                                <p>+62 812-XXXX-XXXX</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-content">
                                <h5>Email</h5>
                                <p>info@Kepulauan-seribu.com</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="contact-content">
                                <h5>Website</h5>
                                <p>www.Kepulauan-seribu.com</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-content">
                                <h5>Alamat Kantor</h5>
                                <p>Jl. Dermaga Muara Angke, Jakarta Utara</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-left">
                    <div class="contact-card">
                        <h3 class="mb-4">
                            <i class="fas fa-clock"></i> Jam Operasional
                        </h3>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-sun"></i>
                            </div>
                            <div class="contact-content">
                                <h5>Senin - Jumat</h5>
                                <p>08:00 - 20:00 WIB</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-calendar-week"></i>
                            </div>
                            <div class="contact-content">
                                <h5>Sabtu</h5>
                                <p>09:00 - 18:00 WIB</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-Travel"></i>
                            </div>
                            <div class="contact-content">
                                <h5>Minggu</h5>
                                <p>10:00 - 16:00 WIB</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-ship"></i>
                            </div>
                            <div class="contact-content">
                                <h5>Keberangkatan Kapal</h5>
                                <p>Setiap hari: 07:00 - 09:00 WIB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="form-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8" data-aos="fade-up">
                    <div class="form-card">
                        <h3 class="text-center mb-4">
                            <i class="fas fa-paper-plane"></i> Kirim Pesan
                        </h3>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form>
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap *</label>
                                    <input type="text" class="form-control" name="name" required placeholder="Masukkan nama lengkap">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" required placeholder="email@example.com">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telepon/WhatsApp *</label>
                                    <input type="tel" class="form-control" name="phone" required placeholder="+62 812-XXXX-XXXX">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Subjek</label>
                                    <select class="form-select" name="subject">
                                        <option value="">Pilih subjek</option>
                                        <option value="booking">Informasi Pemesanan</option>
                                        <option value="package">Detail Paket</option>
                                        <option value="custom">Paket Kustom</option>
                                        <option value="other">Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Pesan *</label>
                                    <textarea class="form-control" name="message" rows="5" required placeholder="Tuliskan pesan Anda di sini..."></textarea>
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn-primary-custom">
                                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="map-overlay">
            <h4><i class="fas fa-map-marked-alt"></i> Lokasi Kami</h4>
            <p class="mb-0">Dermaga Muara Angke, Jakarta Utara</p>
            <small>Kunjungi kantor kami untuk konsultasi langsung</small>
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
    </script>
</body>
</html>
