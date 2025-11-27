# Waisaka Property Mobile App

Aplikasi mobile untuk properti Waisaka yang dibangun dengan Flutter mengikuti **Rules Metode Pembangunan** yang menekankan kesederhanaan, efisiensi, dan keamanan.

## 🎯 Filosofi Pembangunan

Aplikasi ini dibangun mengikuti 3 filosofi utama:
1. **Komunikasi Langsung** - API publik tanpa autentikasi yang tidak perlu
2. **Pemisahan Tanggung Jawab** - Struktur kode yang jelas (Models, Services, Screens, Widgets)
3. **Struktur Data Dapat Diprediksi** - Response JSON yang flat dan konsisten

## 🚀 Quick Start

### 1. Clone Repository

```bash
git clone <repository-url>
cd hp_upgrade_waisaka
```

### 2. Setup Environment Variables

**PENTING:** Aplikasi ini menggunakan file `.env` untuk konfigurasi sensitif (Aturan 8).

```bash
# Copy file template
cp .env.example .env

# Edit .env dan isi dengan nilai yang sesuai
nano .env
```

Lihat [ENVIRONMENT_SETUP.md](ENVIRONMENT_SETUP.md) untuk panduan lengkap.

### 3. Install Dependencies

```bash
flutter pub get
```

### 4. Run Application

```bash
flutter run
```

## 📁 Struktur Project

```
lib/
├── config/          # Konfigurasi terpusat (app_config.dart)
├── models/          # Data models (Property, Article, User, dll)
├── services/        # API & business logic services
├── screens/         # UI screens (Home, Dashboard, Auth, dll)
├── widgets/         # Reusable UI components
├── providers/       # State management (Provider)
└── utils/           # Helper functions
```

## ⚙️ Environment Variables

File `.env` berisi konfigurasi penting:

```env
BASE_URL=http://your-backend-url/waisakaproperty.com/public/api/v1
IMAGE_BASE_URL=http://your-backend-url/waisakaproperty.com/public/assets/upload
GEMINI_API_KEY=your_gemini_api_key_here
```

⚠️ **JANGAN commit file `.env` ke repository!**

## 🔧 Technologies

- **Flutter** - UI Framework
- **Provider** - State Management
- **HTTP** - API Communication
- **flutter_dotenv** - Environment Configuration
- **flutter_secure_storage** - Secure local storage

## 📚 Documentation

- [Environment Setup Guide](ENVIRONMENT_SETUP.md) - Panduan setup .env
- [Rules Metode Pembangunan](../Documentation/Rules%20Metode%20Pembangunan.md) - Panduan teknis development

## 🔐 Security Notes

1. File `.env` sudah masuk `.gitignore` - JANGAN commit!
2. API keys tidak boleh hardcoded di source code
3. Gunakan `.env.example` sebagai template untuk tim
4. Backend API URL diambil dari `.env` untuk fleksibilitas

## 🤝 Contributing

Sebelum contribute, pastikan:
1. ✅ Setup `.env` dengan benar
2. ✅ Follow struktur project yang ada
3. ✅ Ikuti Rules Metode Pembangunan
4. ✅ Tidak hardcode konfigurasi sensitif

## 📝 License

Private - Waisaka Property
