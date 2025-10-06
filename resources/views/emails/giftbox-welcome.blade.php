<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sectorName }} Sektörü Ücretsiz Kitapçık - ConvState AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
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
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 20px;
        }
        .content {
            text-align: center;
            margin-bottom: 30px;
        }
        .greeting {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .download-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 18px;
            margin: 20px 0;
            transition: transform 0.3s ease;
        }
        .download-button:hover {
            transform: translateY(-2px);
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #7f8c8d;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://convstateai.com/imgs/ai-conversion-logo.svg" alt="ConvState AI Logo" class="logo">
        </div>

        <div class="content">
            <p class="greeting">Merhaba {{ $giftboxUser->name }} {{ $giftboxUser->surname }},</p>
            
            <p class="message">
                Sizin {{ $sectorName }} sektörünüze özel olarak ücretsiz kitapçık hazırlıyoruz. Yakında e-posta kutunuza göndereceğiz.
            </p>

        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} ConvState AI. Tüm hakları saklıdır.</p>
            <p>Bu e-posta {{ $giftboxUser->mail }} adresine gönderilmiştir.</p>
            <p style="margin-top: 15px;">
                <a href="{{ route('privacy-policy') }}">Gizlilik politikası</a> |
                <a href="{{ route('terms-of-service') }}">Kullanım şartları</a>
            </p>
        </div>
    </div>
</body>
</html>
