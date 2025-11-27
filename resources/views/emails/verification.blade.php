<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Waisaka Property</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        .message {
            color: #666;
            margin-bottom: 25px;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .verify-button:hover {
            box-shadow: 0 6px 8px rgba(0,0,0,0.15);
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        .url-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            word-break: break-all;
            font-size: 12px;
            color: #666;
            margin: 15px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }
        .footer p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        .footer .company-name {
            font-weight: bold;
            color: #667eea;
        }
        .warning {
            color: #856404;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 12px;
            border-radius: 4px;
            margin: 20px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🏠 Waisaka Property</h1>
            <p style="margin: 5px 0 0 0; font-size: 14px;">Verifikasi Email Anda</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Halo, {{ $userName ?? 'User' }}! 👋
            </div>

            <p class="message">
                Selamat datang di <strong>Waisaka Property</strong>! Terima kasih telah mendaftar. 
                Kami sangat senang Anda bergabung dengan kami.
            </p>

            <p class="message">
                Untuk mengaktifkan akun Anda dan mulai menggunakan layanan kami, 
                silakan klik tombol verifikasi di bawah ini:
            </p>

            <!-- Verification Button -->
            <div class="button-container">
                <a href="{{ $verificationUrl ?? url('mobile/verify-email?token=' . $verificationToken) }}" 
                   class="verify-button">
                   ✓ Aktivasi Akun Saya
                </a>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <p><strong>📌 Informasi Penting:</strong></p>
                <p>• Link verifikasi ini berlaku selama <strong>3 hari</strong></p>
                <p>• Setelah verifikasi, Anda dapat langsung login</p>
                <p>• Jika tidak mendaftar, abaikan email ini</p>
            </div>

            <!-- Alternative URL -->
            <p style="font-size: 14px; color: #666; margin-top: 25px;">
                <strong>Tidak bisa klik tombol di atas?</strong><br>
                Salin dan tempel URL berikut di browser Anda:
            </p>
            <div class="url-box">
                {{ $verificationUrl ?? url('mobile/verify-email?token=' . $verificationToken) }}
            </div>

            <!-- Warning -->
            <div class="warning">
                ⚠️ <strong>Perhatian:</strong> Jika Anda tidak melakukan pendaftaran di Waisaka Property, 
                mohon abaikan email ini. Akun tidak akan dibuat tanpa verifikasi.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin-bottom: 10px;">
                <strong>Butuh bantuan?</strong>
            </p>
            <p>
                Hubungi kami di: 
                <a href="mailto:technicaldevelopmentwaisaka@gmail.com" style="color: #667eea; text-decoration: none;">
                    technicaldevelopmentwaisaka@gmail.com
                </a>
            </p>
            <p style="margin-top: 15px;">
                Terima kasih,<br>
                <span class="company-name">Team Waisaka Property</span>
            </p>
            <p style="font-size: 12px; color: #999; margin-top: 15px;">
                © {{ date('Y') }} Waisaka Property. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
