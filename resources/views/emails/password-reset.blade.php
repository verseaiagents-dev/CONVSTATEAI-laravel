<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Sıfırlama - ConvState AI</title>
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
            color: #e74c3c;
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
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .warning-icon {
            color: #f39c12;
            font-size: 20px;
            margin-right: 10px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
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
        .security-tips {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .security-tips h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .tip-item {
            margin-bottom: 10px;
            padding-left: 20px;
            position: relative;
        }
        .tip-item:before {
            content: "🔒";
            position: absolute;
            left: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('imgs/ai-conversion-logo.svg') }}" alt="ConvState AI Logo" class="logo">
            <h1 class="title">Şifre Sıfırlama</h1>
            <p class="subtitle">Hesabınızın güvenliği için şifrenizi sıfırlayın</p>
        </div>

        <div class="content">
            <p class="greeting">Merhaba {{ $userName }},</p>
            
            <p>ConvState AI hesabınız için şifre sıfırlama talebinde bulundunuz. Bu işlemi siz yapmadıysanız, bu e-postayı görmezden gelebilirsiniz.</p>

            <div class="warning-box">
                <span class="warning-icon">⚠️</span>
                <strong>Güvenlik Uyarısı:</strong> Şifre sıfırlama linki 60 dakika içinde geçerliliğini yitirecektir.
            </div>

            <p>Şifrenizi sıfırlamak için aşağıdaki butona tıklayın:</p>

            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="cta-button">Şifremi Sıfırla</a>
            </div>

            <p style="margin-top: 20px;">Eğer buton çalışmıyorsa, aşağıdaki linki tarayıcınıza kopyalayabilirsiniz:</p>
            <p style="word-break: break-all; background-color: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 12px;">
                {{ $resetUrl }}
            </p>

            <div class="security-tips">
                <h3>Güvenlik İpuçları:</h3>
                <div class="tip-item">Güçlü bir şifre kullanın (büyük/küçük harf, rakam ve özel karakterler)</div>
                <div class="tip-item">Şifrenizi kimseyle paylaşmayın</div>
                <div class="tip-item">Farklı hesaplar için farklı şifreler kullanın</div>
                <div class="tip-item">Şifrenizi düzenli olarak değiştirin</div>
                <div class="tip-item">İki faktörlü doğrulamayı etkinleştirin</div>
            </div>

            <p>Herhangi bir sorunuz olursa, destek ekibimiz size yardımcı olmaktan mutluluk duyacaktır.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} ConvState AI. Tüm hakları saklıdır.</p>
            <p>Bu e-posta güvenlik amaçlı gönderilmiştir.</p>
            <p style="margin-top: 10px;">
                <a href="#" style="color: #e74c3c;">E-posta tercihleri</a> |
                <a href="{{ route('privacy-policy') }}" style="color: #e74c3c;">Gizlilik politikası</a> |
                <a href="{{ route('terms-of-service') }}" style="color: #e74c3c;">Kullanım şartları</a>
            </p>
        </div>
    </div>
</body>
</html>
