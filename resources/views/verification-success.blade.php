<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Berhasil - Waisaka Property</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out 0.2s both;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        .success-icon svg {
            width: 40px;
            height: 40px;
            stroke: white;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }
        
        .warning-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }
        
        .warning-icon svg {
            width: 40px;
            height: 40px;
            stroke: white;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }
        
        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        p {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .info-box h3 {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .info-box p {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }
        
        .info-box p:last-child {
            margin-bottom: 0;
        }
        
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        
        .button-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            box-shadow: none;
            margin-left: 10px;
        }
        
        .button-secondary:hover {
            background: #f8f9fa;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 14px;
            color: #999;
        }
        
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .button {
                display: block;
                margin-bottom: 10px;
            }
            
            .button-secondary {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @if(isset($sukses))
            <!-- Success State -->
            <div class="success-icon">
                <svg viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            
            <h1>✅ Verifikasi Berhasil!</h1>
            <p>{{ $sukses }}</p>
            
            <div class="info-box">
                <h3>🎉 Akun Anda Sudah Aktif</h3>
                <p><strong>Email:</strong> {{ $email }}</p>
                <p>Anda sekarang dapat login ke aplikasi Waisaka Property dan mulai menggunakan layanan kami.</p>
            </div>
            
            <div style="margin-bottom: 20px;">
                <p style="font-size: 14px; color: #666; margin-bottom: 15px;">
                    <strong>Langkah Selanjutnya:</strong>
                </p>
                <p style="font-size: 14px; color: #666; text-align: left; padding-left: 20px;">
                    1. Buka aplikasi Waisaka Property di HP Anda<br>
                    2. Klik "Sudah Verifikasi? Login di Sini"<br>
                    3. Masukkan email dan password Anda<br>
                    4. Mulai jelajahi properti impian Anda!
                </p>
            </div>
            
            <a href="#" onclick="closeWindow()" class="button">Tutup Halaman Ini</a>
            
        @else
            <!-- Warning State -->
            <div class="warning-icon">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            
            <h1>⚠️ Verifikasi Gagal</h1>
            <p>{{ $warning }}</p>
            
            <div class="info-box">
                <h3>Kemungkinan Penyebab:</h3>
                <p>• Link verifikasi sudah expired (lebih dari 3 hari)</p>
                <p>• Link sudah pernah digunakan sebelumnya</p>
                <p>• Email sudah terdaftar di sistem</p>
                <p>• Token verifikasi tidak valid</p>
            </div>
            
            <div style="margin-bottom: 20px;">
                <p style="font-size: 14px; color: #666;">
                    <strong>Solusi:</strong><br>
                    @if(!empty($email) && ($warning_type != 'email_already_registered' || $warning_type != 'token_missing' || $warning_type != 'token_expired'))
                        Silakan klik <a href="{{url('api/v1/mobile/resend-verification-web/'.$email)}}" style="color: #667eea; text-decoration: underline;">kirim ulang email verifikasi</a> 
                        untuk mendapatkan link verifikasi baru.
                   @else
                        Silakan daftar ulang melalui aplikasi untuk mendapatkan email verifikasi baru.
                    @endif
                </p>
            </div>
            
            <a href="#" onclick="closeWindow()" class="button">Tutup Halaman Ini</a>

        @endif
    </div>
    
    <script>
        function closeWindow() {
            // Try to close the window
            window.close();
            
            // If window.close() doesn't work (some browsers block it)
            setTimeout(function() {
                // Show a message
                alert('Silakan tutup tab browser ini secara manual dan kembali ke aplikasi Waisaka Property.');
            }, 100);
            
            return false;
        }
        
        // Auto close after 10 seconds if success
        @if(session('sukses'))
        setTimeout(function() {
            var confirmed = confirm('Verifikasi berhasil! Tutup halaman ini dan kembali ke aplikasi?');
            if (confirmed) {
                window.close();
            }
        }, 10000);
        @endif
    </script>
</body>
</html>
