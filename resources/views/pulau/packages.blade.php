
```<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paket Wisata - Kepulauan Seribu Adventure</title>
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
        .packages-hero {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .packages-hero h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Packages Section */
        .packages-section {
            padding: 80px 0;
            background: var(--light-gray);
        }

        .package-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s;
            height: 100%;
            position: relative;
        }

        .package-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }

        .package-card.featured {
            border: 3px solid var(--gold);
            transform: scale(1.05);
        }

        .package-card.featured:hover {
            transform: scale(1.05) translateY(-10px);
        }

        .featured-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--gold);
            color: var(--dark-gray);
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 0.9rem;
            z-index: 1;
        }

        .package-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
            padding: 30px;
            text-align: center;
        }

        .package-card.featured .package-header {
            background: linear-gradient(135deg, var(--gold) 0%, var(--light-gold) 100%);
            color: var(--dark-gray);
        }

        .package-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .package-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .package-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .package-body {
            padding: 30px;
        }

        .package-price {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-blue);
            text-align: center;
            margin-bottom: 20px;
        }

        .package-card.featured .package-price {
            color: var(--dark-gray);
        }

        .package-price-label {
            font-size: 0.9rem;
            color: #666;
            text-align: center;
            margin-bottom: 30px;
        }

        .package-features {
            list-style: none;
            padding: 0;
            margin-bottom: 30px;
        }

        .package-features li {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
        }

        .package-features li:last-child {
            border-bottom: none;
        }

        .package-features i {
            color: var(--primary-blue);
            margin-right: 15px;
            width: 20px;
        }

        .package-card.featured .package-features i {
            color: var(--gold);
        }

        .package-button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 1.1rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .package-button-primary {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
        }

        .package-button-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,119,190,0.3);
            color: var(--white);
        }

        .package-button-gold {
            background: linear-gradient(135deg, var(--gold) 0%, var(--light-gold) 100%);
            color: var(--dark-gray);
        }

        .package-button-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,215,0,0.3);
            color: var(--dark-gray);
        }

        /* Comparison Table */
        .comparison-section {
            padding: 80px 0;
            background: var(--white);
        }

        .comparison-table {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .comparison-table table {
            margin: 0;
        }

        .comparison-table th {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
            padding: 20px;
            text-align: center;
            font-weight: bold;
        }

        .comparison-table td {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .comparison-table tr:last-child td {
            border-bottom: none;
        }

        .check-icon {
            color: #28a745;
            font-size: 1.2rem;
        }

        .times-icon {
            color: #dc3545;
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
            .packages-hero h1 {
                font-size: 2rem;
            }

            .package-card.featured {
                transform: scale(1);
            }

            .package-card.featured:hover {
                transform: translateY(-10px);
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
                        <a class="nav-link active" href="{{ route('pulau.packages') }}">Paket</a>
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
    <section class="packages-hero">
        <div class="container">
            <h1 data-aos="fade-down">Paket Wisata Kami</h1>
            <p class="lead" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-box"></i> Pilih paket yang sesuai dengan kebutuhan liburan Anda
            </p>
        </div>
    </section>

    <!-- Packages Section -->
    <section class="packages-section">
        <div class="container">
            <div class="row">
                <!-- Paket 2 Hari 1 Malam -->
                <div class="col-lg-6 mb-4" data-aos="fade-right">
                    <div class="package-card">
                        <div class="package-header">
                            <div class="package-icon">
                                <i class="fas fa-water"></i>
                            </div>
                            <div class="package-title">2 Hari 1 Malam</div>
                            <div class="package-subtitle">Petualangan Singkat</div>
                        </div>
                        <div class="package-body">
                            <div class="package-price">Rp 450K</div>
                            <div class="package-price-label">mulai dari 20 orang</div>

                            <ul class="package-features">
                                <li>
                                    <i class="fas fa-check"></i>
                                    Transportasi dari Jakarta (PP)
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    Kapal ferri nyaman & aman
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    Akomodasi Travelstay 1 malam
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    Sarapan pagi (1 hari)
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    Peralatan snorkeling lengkap
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    Pemandu wisata berpengalaman
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    Snorkeling di 2 spot terbaik
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    Asuransi perjalanan dasar
                                </li>
                            </ul>

                            <a href="{{ route('pulau.booking') }}" class="package-button package-button-primary">
                                <i class="fas fa-ticket-alt"></i> Pesan Sekarang
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Paket 3 Hari 2 Malam -->
                <div class="col-lg-6 mb-4" data-aos="fade-left">
                    <div class="package-card featured">
                        <div class="featured-badge">
                            <i class="fas fa-crown"></i> POPULER
                        </div>
                        <div class="package-header">
                            <div class="package-icon">
                                <i class="fas fa-gem"></i>
                            </div>
                            <div class="package-title">3 Hari 2 Malam</div>
                            <div class="package-subtitle">Petualangan Lengkap</div>
                        </div>
                        <div class="package-body">
                            <div class="package-price">Rp 700K</div>
                            <div class="package-price-label">mulai dari 20 orang</div>

                            <ul class="package-features">
                                <li>
                                    <i class="fas fa-check"></i>
                                    Semua fasilitas paket 2H1M
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    Akomodasi Travelstay 2 malam
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    Sarapan pagi (2 hari)
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    Snorkeling di 4 spot terbaik
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    Sunset beach picnic
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    Wisata pulau sekitar
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    BBQ dinner
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    Foto underwater gratis (5 shot)
                                </li>
                            </ul>

                            <a href="{{ route('pulau.booking') }}" class="package-button package-button-gold">
                                <i class="fas fa-crown"></i> Pesan Paket Ini
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Price Tiers -->
            <div class="row mt-5">
                <div class="col-12" data-aos="fade-up">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Informasi Harga</h5>
                        <p class="mb-2">Harga berbeda berdasarkan jumlah peserta:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Paket 2H1M:</strong>
                                <ul class="mb-0">
                                    <li>20+ orang: Rp 450.000/orang</li>
                                    <li>15 orang: Rp 510.000/orang</li>
                                    <li>10 orang: Rp 540.000/orang</li>
                                    <li>5 orang: Rp 620.000/orang</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <strong>Paket 3H2M:</strong>
                                <ul class="mb-0">
                                    <li>20+ orang: Rp 700.000/orang</li>
                                    <li>15 orang: Rp 786.000/orang</li>
                                    <li>10 orang: Rp 790.000/orang</li>
                                    <li>5 orang: Rp 940.000/orang</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Comparison Section -->
    <section class="comparison-section">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Perbandingan Paket</h2>
            <div class="comparison-table" data-aos="fade-up">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Fasilitas</th>
                            <th>2 Hari 1 Malam</th>
                            <th>3 Hari 2 Malam</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Transportasi Jakarta PP</strong></td>
                            <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                            <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        </tr>
                        <tr>
                            <td><strong>Kapal Ferri</strong></td>
                            <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                            <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        </tr>
                        <tr>
                            <td><strong>Travelstay</strong></td>
                            <td class="text-center">1 malam</td>
                            <td class="text-center">2 malam</td>
                        </tr>
                        <tr>
                            <td><strong>Sarapan</strong></td>
                            <td class="text-center">1 hari</td>
                            <td class="text-center">2 hari</td>
                        </tr>
                        <tr>
                            <td><strong>Spot Snorkeling</strong></td>
                            <td class="text-center">2 spot</td>
                            <td class="text-center">4 spot</td>
                        </tr>
                        <tr>
                            <td><strong>Sunset Picnic</strong></td>
                            <td class="text-center"><i class="fas fa-times times-icon"></i></td>
                            <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        </tr>
                        <tr>
                            <td><strong>BBQ Dinner</strong></td>
                            <td class="text-center"><i class="fas fa-times times-icon"></i></td>
                            <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                        </tr>
                        <tr>
                            <td><strong>Foto Underwater</strong></td>
                            <td class="text-center"><i class="fas fa-times times-icon"></i></td>
                            <td class="text-center">5 shot gratis</td>
                        </tr>
                        <tr>
                            <td><strong>Asuransi</strong></td>
                            <td class="text-center">Dasar</td>
                            <td class="text-center">Dasar</td>
                        </tr>
                    </tbody>
                </table>
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
