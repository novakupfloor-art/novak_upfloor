<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri - Kepulauan Seribu Adventure</title>
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
        .gallery-hero {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .gallery-hero h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Gallery Section */
        .gallery-section {
            padding: 80px 0;
            background: var(--light-gray);
        }

        .gallery-item {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .gallery-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .gallery-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .gallery-caption {
            padding: 20px;
        }

        .gallery-caption h5 {
            color: var(--primary-blue);
            font-weight: bold;
            margin-bottom: 10px;
        }

        .gallery-caption p {
            margin: 0;
            color: var(--dark-gray);
            font-size: 0.9rem;
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
            .gallery-hero h1 {
                font-size: 2rem;
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
                        <a class="nav-link" href="{{ route('pulau.index') }}#experience">Pengalaman</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pulau.packages') }}">Paket</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pulau.contact') }}">Informasi</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gallery-hero">
        <div class="container">
            <h1 data-aos="fade-down">Galeri Wisata</h1>
            <p class="lead" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-camera"></i> Dokumentasi keindahan Pulau Pramuka
            </p>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="gallery-item">
                        <img src="{{ asset('assets/pulau/assets/pulau-pramuka-kepulauan-seribu-jakarta-indonesia-jakarta.jpeg') }}" alt="Pulau Pramuka View">
                        <div class="gallery-caption">
                            <h5><i class="fas fa-mountain"></i> Panorama Pulau Pramuka</h5>
                            <p>Pemandangan spektakuler dari atas bukit menampakkan keindahan alam yang memesona</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="gallery-item">
                        <img src="{{ asset('assets/pulau/assets/snorkling-di-pulau-pramuka-kep-seribu.jpg') }}" alt="Snorkeling">
                        <div class="gallery-caption">
                            <h5><i class="fas fa-fish"></i> Snorkeling Adventure</h5>
                            <p>Jelajahi keindahan bawah laut dengan terumbu karang yang masih alami</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="gallery-item">
                        <img src="{{ asset('assets/pulau/assets/pantai-gusung-pertrik-pulau-pramuka-kepulauan-seribu-637x478.jpeg') }}" alt="Pantai">
                        <div class="gallery-caption">
                            <h5><i class="fas fa-umbrella-beach"></i> Pantai Gusung Pertrik</h5>
                            <p>Pantai pasir putih dengan air laut yang jernih dan tenang</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="gallery-item">
                        <img src="{{ asset('assets/pulau/assets/Pulau-Pramuka.jpg') }}" alt="Pulau Pramuka">
                        <div class="gallery-caption">
                            <h5><i class="fas fa-anchor"></i> Dermaga Pulau Pramuka</h5>
                            <p>Dermaga utama menjadi pintu gerbang menuju petualangan seru</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="gallery-item">
                        <img src="{{ asset('assets/pulau/assets/1_TbPs3rRgvmYh9Cj9EYjvDA.jpg') }}" alt="Underwater">
                        <div class="gallery-caption">
                            <h5><i class="fas fa-scuba-diving"></i> Underwater Paradise</h5>
                            <p>Keindahan bawah laut dengan ikan-ikan warna-warni yang menakjubkan</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                    <div class="gallery-item">
                        <img src="{{ asset('assets/pulau/assets/pulau-pramuka-kepulauan-seribu-jakarta-indonesia-jakarta.jpeg') }}" alt="Sunset">
                        <div class="gallery-caption">
                            <h5><i class="fas fa-sun"></i> Sunset Romance</h5>
                            <p>Matahari terbenam yang romantis di ujung pulau yang memesona</p>
                        </div>
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>
</html>
```

<file_path>
htdocs\novak_upfloor\resources\views\pulau\itinerary.blade.php
</file_path>

<edit_description>
Membuat view itinerary minimal
</edit_description>
```
