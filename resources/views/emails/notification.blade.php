<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - ConvState AI</title>
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
            color: #2c3e50;
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
        .notification-box {
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .notification-title {
            color: #2c3e50;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .notification-message {
            color: #555;
            font-size: 16px;
            line-height: 1.8;
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
        .info-box {
            background-color: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .info-icon {
            color: #2196f3;
            font-size: 18px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ config('app.url') }}/imgs/ai-conversion-logo.svg" alt="ConvState AI Logo" class="logo">
            <h1 class="title">{{ $title }}</h1>
            <p class="subtitle">ConvState AI Bildirimi</p>
        </div>

        <div class="content">
            <p class="greeting">Merhaba {{ $userName }},</p>
            
            <div class="notification-box">
                <div class="notification-title">{{ $title }}</div>
                <div class="notification-message">
                    {!! nl2br(e($emailMessage)) !!}
                </div>
            </div>

            @if($actionUrl && $actionText)
                <p>Daha fazla bilgi için aşağıdaki butona tıklayabilirsiniz:</p>
                
                <div style="text-align: center;">
                    <a href="{{ $actionUrl }}" class="cta-button">{{ $actionText }}</a>
                </div>
            @endif

            <div class="info-box">
                <span class="info-icon">ℹ️</span>
                <strong>Bilgi:</strong> Bu e-posta ConvState AI platformundan otomatik olarak gönderilmiştir.
            </div>

            <p>Herhangi bir sorunuz olursa, destek ekibimiz size yardımcı olmaktan mutluluk duyacaktır.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} ConvState AI. Tüm hakları saklıdır.</p>
            <p style="margin-top: 10px;">
                <a href="#" style="color: #3498db;">E-posta tercihleri</a> |
                <a href="#" style="color: #3498db;">Gizlilik politikası</a> |
                <a href="#" style="color: #3498db;">Kullanım şartları</a>
            </p>
        </div>
    </div>
</body>
</html>
