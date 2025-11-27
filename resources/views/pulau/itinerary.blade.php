<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Itinerary - Kepulauan Seribu Adventure</title>
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
        .itinerary-hero {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .itinerary-hero h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Itinerary Section */
        .itinerary-section {
            padding: 80px 0;
            background: var(--light-gray);
        }

        .day-card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }

        .day-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .day-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .day-number {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin-right: 20px;
        }

        .day-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-blue);
        }

        .timeline-item {
            display: flex;
            margin-bottom: 20px;
            position: relative;
        }

        .time-badge {
            min-width: 80px;
            background: var(--gold);
            color: var(--dark-gray);
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
            margin-right: 15px;
            text-align: center;
        }

        .activity-content {
            flex: 1;
            padding: 15px;
            background: rgba(0,119,190,0.05);
            border-radius: 10px;
            border-left: 4px solid var(--primary-blue);
        }

        .activity-content h5 {
            color: var(--primary-blue);
            margin-bottom: 8px;
            font-weight: 600;
        }

        .activity-content p {
            margin: 0;
            color: var(--dark-gray);
        }

        .activity-icon {
            font-size: 1.2rem;
            color: var(--primary-blue);
            margin-right: 10px;
        }

        .included-section {
            padding: 60px 0;
            background: var(--white);
        }

        .included-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 15px;
            background: rgba(0,119,190,0.05);
            border-radius: 10px;
            transition: all 0.3s;
        }

        .included-item:hover {
            transform: translateX(10px);
            background: rgba(0,119,190,0.1);
        }

        .included-icon {
            font-size: 1.5rem;
            color: var(--gold);
            margin-right: 15px;
            width: 30px;
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
            .itinerary-hero h1 {
                font-size: 2rem;
            }

            .day-header {
                flex-direction: column;
                text-align: center;
            }

            .day-number {
                margin-right: 0;
                margin-bottom: 10px;
            }

            .timeline-item {
                flex-direction: column;
            }

            .time-badge {
                margin-right: 0;
                margin-bottom: 10px;
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
                        <a class="nav-link active" href="{{ route('pulau.itinerary') }}">Itinerary</a>
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
    <section class="itinerary-hero">
        <div class="container">
            <h1 data-aos="fade-down">Jadwal Perjalanan</h1>
            <p class="lead" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-calendar-alt"></i> Rincian kegiatan selama liburan Anda di Pulau Pramuka
            </p>
        </div>
    </section>

    <!-- Itinerary Section -->
    <section class="itinerary-section">
        <div class="container">
            <!-- Hari 1 -->
            <div class="day-card" data-aos="fade-up">
                <div class="day-header">
                    <div class="day-number">1</div>
                    <div class="day-title">Hari Pertama - Kedatangan & Eksplorasi</div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">06:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-shuttle-van activity-icon"></i>Jemputan</h5>
                        <p>Penjemputan dari hotel/meeting point di Jakarta</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">07:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-ship activity-icon"></i>Keberangkatan</h5>
                        <p>Tiba di pelabuhan Muara Angke dan boarding kapal</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">08:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-anchor activity-icon"></i>Perjalanan Laut</h5>
                        <p>Kapal berangkat menuju Pulau Pramuka (±2 jam)</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">10:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-Travel activity-icon"></i>Check-in</h5>
                        <p>Tiba di Pulau Pramuka, check-in Travelstay & istirahat</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">14:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-fish activity-icon"></i>Snorkeling Sesi 1</h5>
                        <p>Eksplorasi terumbu karang di spot terbaik</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">17:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-sun activity-icon"></i>Sunset Time</h5>
                        <p>Kembali ke Travelstay, menikmati sunset</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">18:30</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-utensils activity-icon"></i>Makan Malam</h5>
                        <p>Makan malam dan free time</p>
                    </div>
                </div>
            </div>

            <!-- Hari 2 -->
            <div class="day-card" data-aos="fade-up" data-aos-delay="100">
                <div class="day-header">
                    <div class="day-number">2</div>
                    <div class="day-title">Hari Kedua - Petualangan Penuh</div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">07:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-coffee activity-icon"></i>Sarapan</h5>
                        <p>Sarapan pagi dengan menu tradisional</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">08:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-fish activity-icon"></i>Snorkeling Sesi 2</h5>
                        <p>Spot snorkeling berbeda dengan kemarin</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">11:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-shower activity-icon"></i>Istirahat</h5>
                        <p>Kembali, mandi, dan persiapan check-out</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">12:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-umbrella-beach activity-icon"></i>Free Time</h5>
                        <p>Makan siang dan aktivitas bebas di pantai</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">14:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-ship activity-icon"></i>Keberangkatan</h5>
                        <p>Kapal berangkat kembali ke Jakarta</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">16:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-flag-checkered activity-icon"></i>Tiba di Jakarta</h5>
                        <p>Tiba di pelabuhan, antar ke lokasi tujuan</p>
                    </div>
                </div>
            </div>

            <!-- Hari 3 (Optional untuk 3H2M) -->
            <div class="day-card" data-aos="fade-up" data-aos-delay="200" style="border: 2px dashed var(--gold);">
                <div class="day-header">
                    <div class="day-number">3</div>
                    <div class="day-title">Hari Ketiga - Ekspansi Pulau <small>(Paket 3H2M)</small></div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">07:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-coffee activity-icon"></i>Sarapan</h5>
                        <p>Sarapan pagi dan persiapan aktivitas</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">08:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-globe-asia activity-icon"></i>Island Hopping</h5>
                        <p>Eksplorasi pulau-pulau sekitar</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">12:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-fire activity-icon"></i>BBQ Lunch</h5>
                        <p>BBQ lunch di pantai dengan suasana romantis</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">15:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-camera activity-icon"></i>Foto Session</h5>
                        <p>Foto underwater dan dokumentasi</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="time-badge">16:00</div>
                    <div class="activity-content">
                        <h5><i class="fas fa-suitcase activity-icon"></i>Persiapan Pulang</h5>
                        <p>Persiapan dan keberangkatan kembali</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Included Section -->
    <section class="included-section">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Yang Termasuk dalam Paket</h2>
            <div class="row">
                <div class="col-md-6" data-aos="fade-right" data-aos-delay="100">
                    <div class="included-item">
                        <div class="included-icon">
                            <i class="fas fa-shuttle-van"></i>
                        </div>
                        <div>
                            <h5>Transportasi</h5>
                            <p>Antar jemput dari hotel dan kapal ferri PP</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-aos="fade-left" data-aos-delay="200">
                    <div class="included-item">
                        <div class="included-icon">
                            <i class="fas fa-Travel"></i>
                        </div>
                        <div>
                            <h5>Akomodasi</h5>
                            <p>Travelstay nyaman dengan pemandangan laut</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-aos="fade-right" data-aos-delay="300">
                    <div class="included-item">
                        <div class="included-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div>
                            <h5>Makanan</h5>
                            <p>Sarapan pagi setiap hari</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-aos="fade-left" data-aos-delay="400">
                    <div class="included-item">
                        <div class="included-icon">
                            <i class="fas fa-swimmer"></i>
                        </div>
                        <div>
                            <h5>Snorkeling</h5>
                            <p>Peralatan lengkap dan pemandu berpengalaman</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5" data-aos="fade-up">
                <a href="{{ route('pulau.booking') }}" class="btn btn-lg" style="background: linear-gradient(135deg, var(--gold) 0%, var(--light-gold) 100%); color: var(--dark-gray); border: none; border-radius: 50px; padding: 15px 40px; font-weight: bold;">
                    <i class="fas fa-ticket-alt"></i> Pesan Paket Sekarang
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
    </script>
</body>
</html>
