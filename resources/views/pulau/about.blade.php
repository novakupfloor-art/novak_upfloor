<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Kepulauan Seribu Adventure</title>
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
        .about-hero {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
            padding: 80px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .about-content {
            position: relative;
            z-index: 1;
        }

        .about-hero h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Content Section */
        .content-section {
            padding: 80px 0;
            background: var(--white);
        }

        .about-card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 30px;
            transition: all 0.3s;
        }

        .about-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .story-card {
            border-left: 4px solid var(--gold);
        }

        .mission-card {
            border-left: 4px solid var(--primary-blue);
        }

        .values-card {
            border-left: 4px solid #28a745;
        }

        .team-section {
            padding: 80px 0;
            background: var(--light-gray);
        }

        .team-member {
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            transition: all 0.3s;
        }

        .team-member:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .team-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 3rem;
            color: var(--white);
        }

        .stats-section {
            padding: 60px 0;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
        }

        .stat-item {
            text-align: center;
            margin-bottom: 30px;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: var(--gold);
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.2rem;
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
            .about-hero h1 {
                font-size: 2rem;
            }

            .about-card {
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
                        <a class="nav-link active" href="{{ route('pulau.about') }}">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pulau.index') }}#experience">Pengalaman</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pulau.index') }}#pricing">Harga</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pulau.contact') }}">Informasi</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="about-content">
                <h1 data-aos="fade-down">Tentang Kami</h1>
                <p class="lead" data-aos="fade-up" data-aos-delay="200">
                    <i class="fas fa-anchor"></i> Pengalaman 10+ tahun membawa petualangan laut terbaik untuk Anda
                </p>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="content-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4" data-aos="fade-right">
                    <div class="about-card story-card">
                        <h3><i class="fas fa-book"></i> Kisah Kami</h3>
                        <p>
                            Berdiri sejak 2014, Kepulauan Seribu Adventure dimulai dari kecintaan kami terhadap keindahan laut Indonesia.
                            Berawal dari kelompok kecil yang sering menjelajahi pulau-pulau di Kepulauan Seribu, kami berkembang
                            menjadi operator wisata terpercaya yang telah melayani ribuan wisatawan.
                        </p>
                        <p>
                            Setiap perjalanan kami dirancang dengan penuh perhatian pada detail, keselamatan, dan kepuasan Anda.
                            Kami tidak hanya menawarkan liburan, tetapi juga menciptakan kenangan tak terlupakan.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up">
                    <div class="about-card mission-card">
                        <h3><i class="fas fa-bullseye"></i> Misi Kami</h3>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-check text-primary"></i>
                                <strong>Menyediakan</strong> pengalaman liburan laut yang aman dan menyenangkan
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check text-primary"></i>
                                <strong>Melestarikan</strong> keindahan alam bawah laut melalui ekowisata
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check text-primary"></i>
                                <strong>Memberdayakan</strong> masyarakat lokal pulau
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check text-primary"></i>
                                <strong>Menciptakan</strong> kesadaran tentang pentingnya konservasi laut
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-left">
                    <div class="about-card values-card">
                        <h3><i class="fas fa-heart"></i> Nilai-nilai Kami</h3>
                        <div class="value-item mb-3">
                            <h5><i class="fas fa-shield-alt text-success"></i> Keselamatan Pertama</h5>
                            <p class="mb-0">Keselamatan Anda adalah prioritas utama kami</p>
                        </div>
                        <div class="value-item mb-3">
                            <h5><i class="fas fa-leaf text-success"></i> Keanekaragaman Hayati</h5>
                            <p class="mb-0">Melindungi ekosistem laut untuk generasi mendatang</p>
                        </div>
                        <div class="value-item mb-3">
                            <h5><i class="fas fa-hands-helping text-success"></i> Pelayanan Terbaik</h5>
                            <p class="mb-0">Tim profesional yang siap melayani Anda</p>
                        </div>
                        <div class="value-item">
                            <h5><i class="fas fa-smile text-success"></i> Kepuasan Pelanggan</h5>
                            <p class="mb-0">Kebahagiaan Anda adalah kesuksesan kami</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Pencapaian Kami</h2>
            <div class="row">
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-item">
                        <div class="stat-number" data-count="10000">0</div>
                        <div class="stat-label">Wisatawan Puas</div>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-item">
                        <div class="stat-number" data-count="15">0</div>
                        <div class="stat-label">Pulau Dieksplorasi</div>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-item">
                        <div class="stat-number" data-count="98">0</div>
                        <div class="stat-label">% Rating Kepuasan</div>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-item">
                        <div class="stat-number" data-count="50">0</div>
                        <div class="stat-label">Tim Profesional</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Tim Kami</h2>
            <div class="row">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="team-member">
                        <div class="team-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h5>Capt. Ahmad Rizki</h5>
                        <p class="text-muted">Founder & Tour Leader</p>
                        <p>10+ tahun pengalaman menyelam dan memimpin ekspedisi laut</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="team-member">
                        <div class="team-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h5>Sarah Wijaya</h5>
                        <p class="text-muted">Operations Manager</p>
                        <p>Spesialis dalam logistik dan manajemen wisata pulau</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="team-member">
                        <div class="team-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h5>Budi Santoso</h5>
                        <p class="text-muted">Dive Master</p>
                        <p>PADI certified instructor dengan 1000+ jam menyelam</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="team-member">
                        <div class="team-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h5>Lisa Amelia</h5>
                        <p class="text-muted">Customer Relations</p>
                        <p>Dedikasi untuk memberikan pengalaman terbaik bagi setiap pelanggan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="content-section">
        <div class="container text-center">
            <div class="about-card" data-aos="fade-up">
                <h2 class="mb-4">Siap Berpetualang Bersama Kami?</h2>
                <p class="lead mb-4">
                    Bergabunglah dengan ribuan wisatawan yang telah merasakan keajaiban Kepulauan Seribu bersama kami
                </p>
                <a href="{{ route('pulau.booking') }}" class="btn btn-lg" style="background: linear-gradient(135deg, var(--gold) 0%, var(--light-gold) 100%); color: var(--dark-gray); border: none; border-radius: 50px; padding: 15px 40px; font-weight: bold;">
                    <i class="fas fa-ticket-alt"></i> Pesan Sekarang
                </a>
                <a href="{{ route('pulau.contact') }}" class="btn btn-lg btn-outline-primary ms-3" style="border-radius: 50px; padding: 15px 40px;">
                    <i class="fas fa-phone"></i> Hubungi Kami
                </a>
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

        // Counter animation for stats
        const counters = document.querySelectorAll('.stat-number');
        const speed = 200;

        const countUp = (counter) => {
            const target = +counter.getAttribute('data-count');
            const count = +counter.innerText;
            const increment = target / speed;

            if (count < target) {
                counter.innerText = Math.ceil(count + increment);
                setTimeout(() => countUp(counter), 10);
            } else {
                counter.innerText = target;
            }
        };

        // Start counting when stats section is in view
        const observerOptions = {
            threshold: 0.5
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    countUp(counter);
                    observer.unobserve(counter);
                }
            });
        }, observerOptions);

        counters.forEach(counter => {
            observer.observe(counter);
        });
    </script>
</body>
</html>
