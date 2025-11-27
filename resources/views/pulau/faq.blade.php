
```<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Kepulauan Seribu Adventure</title>
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
        .faq-hero {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .faq-hero h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* FAQ Section */
        .faq-section {
            padding: 80px 0;
            background: var(--light-gray);
        }

        .faq-card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .faq-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .faq-header {
            padding: 25px 30px;
            background: var(--white);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s;
        }

        .faq-header:hover {
            background: rgba(0,119,190,0.05);
        }

        .faq-header.active {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
        }

        .faq-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 1.2rem;
            color: var(--white);
            flex-shrink: 0;
        }

        .faq-header.active .faq-icon {
            background: var(--white);
            color: var(--primary-blue);
        }

        .faq-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark-gray);
            flex: 1;
        }

        .faq-header.active .faq-title {
            color: var(--white);
        }

        .faq-toggle {
            font-size: 1.5rem;
            color: var(--primary-blue);
            transition: all 0.3s;
        }

        .faq-header.active .faq-toggle {
            color: var(--white);
            transform: rotate(180deg);
        }

        .faq-body {
            padding: 30px;
            display: none;
            background: var(--white);
        }

        .faq-body.show {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Categories */
        .categories-section {
            padding: 60px 0;
            background: var(--white);
        }

        .category-card {
            background: linear-gradient(135deg, var(--light-gray) 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .category-icon {
            font-size: 3rem;
            color: var(--primary-blue);
            margin-bottom: 15px;
        }

        .category-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--dark-gray);
            margin-bottom: 10px;
        }

        .category-count {
            font-size: 1.1rem;
            color: var(--primary-blue);
            font-weight: 600;
        }

        /* CTA Section */
        .cta-section {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
            text-align: center;
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
            box-shadow: 0 4px 15px rgba(255,215,0,0.3);
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255,215,0,0.4);
            color: var(--dark-gray);
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
            .faq-hero h1 {
                font-size: 2rem;
            }

            .faq-header {
                padding: 20px;
            }

            .faq-title {
                font-size: 1.1rem;
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
                        <a class="nav-link active" href="{{ route('pulau.faq') }}">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pulau.contact') }}">Informasi</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="faq-hero">
        <div class="container">
            <h1 data-aos="fade-down">Frequently Asked Questions</h1>
            <p class="lead" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-question-circle"></i> Temukan jawaban untuk pertanyaan umum tentang liburan Anda
            </p>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="category-card" onclick="filterFAQ('booking')">
                        <div class="category-icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div class="category-title">Pemesanan</div>
                        <div class="category-count">5 Pertanyaan</div>
                    </div>
                </div>
                <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="category-card" onclick="filterFAQ('payment')">
                        <div class="category-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="category-title">Pembayaran</div>
                        <div class="category-count">4 Pertanyaan</div>
                    </div>
                </div>
                <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="category-card" onclick="filterFAQ('travel')">
                        <div class="category-icon">
                            <i class="fas fa-ship"></i>
                        </div>
                        <div class="category-title">Perjalanan</div>
                        <div class="category-count">6 Pertanyaan</div>
                    </div>
                </div>
                <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="category-card" onclick="filterFAQ('safety')">
                        <div class="category-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="category-title">Keselamatan</div>
                        <div class="category-count">5 Pertanyaan</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <!-- Booking FAQs -->
            <div class="faq-card" data-aos="fade-up" data-category="booking">
                <div class="faq-header" onclick="toggleFAQ(this)">
                    <div style="display: flex; align-items: center;">
                        <div class="faq-icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div class="faq-title">Bagaimana cara memesan paket wisata?</div>
                    </div>
                    <div class="faq-toggle">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="faq-body">
                    <p>Anda bisa memesan melalui website kami di halaman booking, atau hubungi langsung melalui WhatsApp/telepon. Proses pemesanan sangat mudah:</p>
                    <ol>
                        <li>Pilih paket yang sesuai kebutuhan</li>
                        <li>Isi form pemesanan online</li>
                        <li>Tunggu konfirmasi dari tim kami (maksimal 24 jam)</li>
                        <li>Lakukan pembayaran sesuai instruksi</li>
                        <li>Nikmati liburan Anda!</li>
                    </ol>
                </div>
            </div>

            <div class="faq-card" data-aos="fade-up" data-aos-delay="100" data-category="booking">
                <div class="faq-header" onclick="toggleFAQ(this)">
                    <div style="display: flex; align-items: center;">
                        <div class="faq-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="faq-title">Kapan harus melakukan pemesanan?</div>
                    </div>
                    <div class="faq-toggle">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="faq-body">
                    <p>Kami merekomendasikan pemesanan minimal 7 hari sebelum keberangkatan untuk kelancaran persiapan. Namun, untuk high season (libur sekolah, lebaran, natal) disarankan memesan 1 bulan sebelumnya karena kuota terbatas.</p>
                </div>
            </div>

            <!-- Payment FAQs -->
            <div class="faq-card" data-aos="fade-up" data-aos-delay="200" data-category="payment">
                <div class="faq-header" onclick="toggleFAQ(this)">
                    <div style="display: flex; align-items: center;">
                        <div class="faq-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="faq-title">Metode pembayaran apa saja yang tersedia?</div>
                    </div>
                    <div class="faq-toggle">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="faq-body">
                    <p>Kami menerima berbagai metode pembayaran:</p>
                    <ul>
                        <li>Transfer Bank (BCA, Mandiri, BNI, BRI)</li>
                        <li>E-Wallet (GoPay, OVO, Dana, LinkAja)</li>
                        <li>Virtual Account</li>
                        <li>Credit Card (melalui payment gateway)</li>
                        <li>Cash di kantor</li>
                    </ul>
                </div>
            </div>

            <!-- Travel FAQs -->
            <div class="faq-card" data-aos="fade-up" data-aos-delay="300" data-category="travel">
                <div class="faq-header" onclick="toggleFAQ(this)">
                    <div style="display: flex; align-items: center;">
                        <div class="faq-icon">
                            <i class="fas fa-ship"></i>
                        </div>
                        <div class="faq-title">Bagaimana kondisi kapal yang digunakan?</div>
                    </div>
                    <div class="faq-toggle">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="faq-body">
                    <p>Kapal kami dilengkapi dengan:</p>
                    <ul>
                        <li>Safety equipment lengkap (life jacket, life raft)</li>
                        <li>Navigasi modern (GPS, radar)</li>
                        <li>Komunikasi (radio, satellite phone)</li>
                        <li>Toilet dan tempat duduk yang nyaman</li>
                        <li>Kapten dan crew berpengalaman</li>
                    </ul>
                    <p>Semua kapal telah melalui inspeksi keselamatan rutin dan memiliki sertifikat resmi dari pihak berwenang.</p>
                </div>
            </div>

            <div class="faq-card" data-aos="fade-up" data-aos-delay="400" data-category="travel">
                <div class="faq-header" onclick="toggleFAQ(this)">
                    <div style="display: flex; align-items: center;">
                        <div class="faq-icon">
                            <i class="fas fa-Travel"></i>
                        </div>
                        <div class="faq-title">Bagaimana kondisi akomodasi di pulau?</div>
                    </div>
                    <div class="faq-toggle">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="faq-body">
                    <p>Travelstay kami menyediakan:</p>
                    <ul>
                        <li>Kamar bersih dengan ventilasi baik</li>
                        <li>Kipas angin/AC (tergantung paket)</li>
                        <li>Kamar mandi dalam dengan air tawar</li>
                        <li>Terbuka ke pemandangan laut</li>
                        <li>Area umum untuk bersantai</li>
                    </ul>
                    <p>Standar kebersihan selalu kami jaga dengan regular cleaning dan sanitasi.</p>
                </div>
            </div>

            <!-- Safety FAQs -->
            <div class="faq-card" data-aos="fade-up" data-aos-delay="500" data-category="safety">
                <div class="faq-header" onclick="toggleFAQ(this)">
                    <div style="display: flex; align-items: center;">
                        <div class="faq-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="faq-title">Apakah tersedia asuransi perjalanan?</div>
                    </div>
                    <div class="faq-toggle">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="faq-body">
                    <p>Ya, semua paket kami sudah termasuk asuransi perjalanan dasar yang meliputi:</p>
                    <ul>
                        <li>Kecelakaan selama perjalanan</li>
                        <li>Evakuasi medis darurat</li>
                        <li>Kehilangan barang pribadi (batasan)</li>
                    </ul>
                    <p>Anda juga bisa upgrade ke asuransi comprehensive dengan biaya tambahan.</p>
                </div>
            </div>

            <div class="faq-card" data-aos="fade-up" data-aos-delay="600" data-category="safety">
                <div class="faq-header" onclick="toggleFAQ(this)">
                    <div style="display: flex; align-items: center;">
                        <div class="faq-icon">
                            <i class="fas fa-swimmer"></i>
                        </div>
                        <div class="faq-title">Apakah harus bisa berenang untuk snorkeling?</div>
                    </div>
                    <div class="faq-toggle">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="faq-body">
                    <p>Tidak harus bisa berenang! Kami menyediakan:</p>
                    <ul>
                        <li>Life jacket untuk semua peserta</li>
                        <li>Pemandu yang selalu mendampingi</li>
                        <li>Spot snorkeling dengan arus tenang</li>
                        <li>Ban pelampung jika diperlukan</li>
                        <li>Basic swimming instruction</li>
                    </ul>
                    <p>Keselamatan Anda adalah prioritas utama kami.</p>
                </div>
            </div>

            <div class="faq-card" data-aos="fade-up" data-aos-delay="700">
                <div class="faq-header" onclick="toggleFAQ(this)">
                    <div style="display: flex; align-items: center;">
                        <div class="faq-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="faq-title">Bagaimana dengan menu makanan?</div>
                    </div>
                    <div class="faq-toggle">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="faq-body">
                    <p>Kami menyediakan:</p>
                    <ul>
                        <li>Sarapan pagi dengan menu tradisional Indonesia</li>
                        <li>Menu halal</li>
                        <li>Air mineral gratis</li>
                        <li>Opsi makanan diet (informasikan saat booking)</li>
                        <li>Snack dan buah-buahan</li>
                    </ul>
                    <p>Untuk makan siang dan malam, Anda bisa memesan tambahan atau mencoba warung lokal di pulau.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 data-aos="fade-up">Masih Punya Pertanyaan?</h2>
            <p class="lead" data-aos="fade-up" data-aos-delay="200">
                Tim customer service kami siap membantu Anda 24/7
            </p>
            <div data-aos="fade-up" data-aos-delay="400">
                <a href="{{ route('pulau.contact') }}" class="btn-primary-custom">
                    <i class="fas fa-phone"></i> Hubungi Kami Sekarang
                </a>
                <a href="https://wa.me/62812XXXXXXX" class="btn-primary-custom ms-3" target="_blank">
                    <i class="fab fa-whatsapp"></i> Chat WhatsApp
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

        // Toggle FAQ
        function toggleFAQ(element) {
            const body = element.nextElementSibling;
            const isActive = element.classList.contains('active');

            // Close all FAQs
            document.querySelectorAll('.faq-header').forEach(header => {
                header.classList.remove('active');
                header.nextElementSibling.classList.remove('show');
            });

            // Open clicked FAQ if it was closed
            if (!isActive) {
                element.classList.add('active');
                body.classList.add('show');
            }
        }

        // Filter FAQ by category
        function filterFAQ(category) {
            const allCards = document.querySelectorAll('.faq-card');

            allCards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');
                if (category === 'all' || cardCategory === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

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
