<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoş Geldiniz - ConvState AI</title>
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
        .welcome-title {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .welcome-subtitle {
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
        .features {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .feature-item {
            margin-bottom: 15px;
            padding-left: 20px;
            position: relative;
        }
        .feature-item:before {
            content: "✓";
            color: #27ae60;
            font-weight: bold;
            position: absolute;
            left: 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('imgs/ai-conversion-logo.svg') }}" alt="ConvState AI Logo" class="logo">
            <h1 class="welcome-title">Hoş Geldiniz!</h1>
            <p class="welcome-subtitle">ConvState AI ailesine katıldığınız için teşekkür ederiz</p>
        </div>

        <div class="content">
            <p class="greeting">Merhaba {{ $user->name }},</p>
            
            <p>ConvState AI platformuna başarıyla kayıt oldunuz! Artık yapay zeka destekli müşteri hizmetleri ve chatbot çözümlerimizden yararlanabilirsiniz.</p>

            <div class="features">
                <h3 style="color: #2c3e50; margin-bottom: 15px;">Platform Özellikleri:</h3>
                <div class="feature-item">Akıllı chatbot oluşturma ve yönetimi</div>
                <div class="feature-item">Müşteri etkileşim analizi</div>
                <div class="feature-item">Çok dilli destek</div>
                <div class="feature-item">Gerçek zamanlı istatistikler</div>
                <div class="feature-item">Özelleştirilebilir widget tasarımları</div>
                <div class="feature-item">API entegrasyonu</div>
            </div>

            <p>Hemen başlamak için aşağıdaki butona tıklayarak dashboard'a giriş yapabilirsiniz:</p>

            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="cta-button">Dashboard'a Git</a>
            </div>

            <p style="margin-top: 20px;">Herhangi bir sorunuz olursa, destek ekibimiz size yardımcı olmaktan mutluluk duyacaktır.</p>
        </div>

        <div class="social-links">
            <p style="text-align: center; margin-bottom: 15px;">Bizi takip edin:</p>
            <div style="text-align: center;">
                <a href="#">Twitter</a> |
                <a href="#">LinkedIn</a> |
                <a href="#">Facebook</a> |
                <a href="#">Instagram</a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} ConvState AI. Tüm hakları saklıdır.</p>
            <p>Bu e-posta {{ $user->email }} adresine gönderilmiştir.</p>
            <p style="margin-top: 10px;">
                <a href="#" style="color: #667eea;">E-posta tercihleri</a> |
                <a href="#" style="color: #667eea;">Gizlilik politikası</a> |
                <a href="#" style="color: #667eea;">Kullanım şartları</a>
            </p>
        </div>
    </div>
</body>
</html>
