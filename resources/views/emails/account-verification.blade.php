<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-posta Doğrulama - ConvState AI</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 20px;
        }
        .title {
            color: #3498db;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #7f8c8d;
            font-size: 16px;
        }
        .content {
            margin-bottom: 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .verification-box {
            background-color: #e3f2fd;
            border: 2px solid #2196f3;
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
            text-align: center;
        }
        .verification-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .verification-text {
            font-size: 18px;
            color: #1976d2;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.3s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #7f8c8d;
            font-size: 14px;
        }
        .benefits {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .benefits h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .benefit-item {
            margin-bottom: 10px;
            padding-left: 20px;
            position: relative;
        }
        .benefit-item:before {
            content: "🔐";
            position: absolute;
            left: 0;
        }
        .expiry-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('imgs/ai-conversion-logo.svg') }}" alt="ConvState AI Logo" class="logo">
            <h1 class="title">E-posta Doğrulama</h1>
            <p class="subtitle">Hesabınızı doğrulayarak güvenliği artırın</p>
        </div>

        <div class="content">
            <p class="greeting">Merhaba {{ $user->name }},</p>
            
            <p>ConvState AI hesabınızı güvenli hale getirmek için e-posta adresinizi doğrulamanız gerekiyor. Bu işlem hesabınızın güvenliği için önemlidir.</p>

            <div class="verification-box">
                <div class="verification-icon">📧</div>
                <div class="verification-text">E-posta Adresinizi Doğrulayın</div>
                <p style="color: #1976d2; margin-bottom: 20px;">Doğrulama işlemi tamamlandıktan sonra tüm özelliklere erişebilirsiniz.</p>
            </div>

            <p>E-posta adresinizi doğrulamak için aşağıdaki butona tıklayın:</p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="cta-button">E-posta Adresimi Doğrula</a>
            </div>

            <p style="margin-top: 20px;">Eğer buton çalışmıyorsa, aşağıdaki linki tarayıcınıza kopyalayabilirsiniz:</p>
            <p style="word-break: break-all; background-color: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 12px;">
                {{ $verificationUrl }}
            </p>

            <div class="expiry-notice">
                <strong>⚠️ Önemli:</strong> Doğrulama linki 24 saat içinde geçerliliğini yitirecektir.
            </div>

            <div class="benefits">
                <h3>E-posta Doğrulama Faydaları:</h3>
                <div class="benefit-item">Hesap güvenliği artırılır</div>
                <div class="benefit-item">Şifre sıfırlama özelliği aktif olur</div>
                <div class="benefit-item">Önemli bildirimler alabilirsiniz</div>
                <div class="benefit-item">Hesap kurtarma işlemleri kolaylaşır</div>
                <div class="benefit-item">Platform güvenlik standartlarına uygunluk</div>
            </div>

            <p>Herhangi bir sorunuz olursa, destek ekibimiz size yardımcı olmaktan mutluluk duyacaktır.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} ConvState AI. Tüm hakları saklıdır.</p>
            <p>Bu e-posta {{ $user->email }} adresine gönderilmiştir.</p>
            <p style="margin-top: 10px;">
                <a href="#" style="color: #3498db;">E-posta tercihleri</a> |
                <a href="{{ route('privacy-policy') }}" style="color: #3498db;">Gizlilik politikası</a> |
                <a href="{{ route('terms-of-service') }}" style="color: #3498db;">Kullanım şartları</a>
            </p>
        </div>
    </div>
</body>
</html>
