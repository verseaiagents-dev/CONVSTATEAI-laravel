<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aboneliğiniz Aktif - ConvState AI</title>
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
            color: #27ae60;
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
        .plan-details {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }
        .plan-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .plan-price {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .plan-period {
            font-size: 16px;
            opacity: 0.9;
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
            content: "✨";
            position: absolute;
            left: 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
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
        .next-billing {
            background-color: #e8f5e8;
            border: 1px solid #27ae60;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .billing-date {
            color: #27ae60;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('imgs/ai-conversion-logo.svg') }}" alt="ConvState AI Logo" class="logo">
            <h1 class="title">Aboneliğiniz Aktif!</h1>
            <p class="subtitle">Premium özelliklere erişiminiz başladı</p>
        </div>

        <div class="content">
            <p class="greeting">Merhaba {{ $user->name }},</p>
            
            <p>Tebrikler! ConvState AI premium aboneliğiniz başarıyla aktifleştirildi. Artık platformun tüm gelişmiş özelliklerinden yararlanabilirsiniz.</p>

            <div class="plan-details">
                <div class="plan-name">{{ $plan->name ?? 'Premium Plan' }}</div>
                <div class="plan-price">{{ $plan->price ?? '29' }} ₺/ay</div>
                <div class="plan-period">{{ $plan->billing_cycle ?? 'Aylık' }}</div>
            </div>

            <div class="features">
                <h3 style="color: #2c3e50; margin-bottom: 15px;">Premium Özellikler:</h3>
                <div class="feature-item">Sınırsız chatbot oluşturma</div>
                <div class="feature-item">Gelişmiş analitik ve raporlama</div>
                <div class="feature-item">Öncelikli destek hizmeti</div>
                <div class="feature-item">API erişimi ve entegrasyon</div>
                <div class="feature-item">Özel widget tasarımları</div>
                <div class="feature-item">Çoklu dil desteği</div>
                <div class="feature-item">Gelişmiş AI modeli erişimi</div>
                <div class="feature-item">Yedekleme ve geri yükleme</div>
            </div>

            <div class="next-billing">
                <p><strong>Sonraki Faturalama:</strong> <span class="billing-date">{{ $subscription->next_billing_date ?? 'Bir sonraki ay' }}</span></p>
                <p style="margin-top: 10px; font-size: 14px;">Aboneliğinizi istediğiniz zaman iptal edebilir veya değiştirebilirsiniz.</p>
            </div>

            <p>Hemen premium özellikleri keşfetmeye başlayın:</p>

            <div style="text-align: center;">
                <a href="{{ $dashboardUrl }}" class="cta-button">Dashboard'a Git</a>
            </div>

            <p style="margin-top: 20px;">Herhangi bir sorunuz olursa, premium destek ekibimiz size yardımcı olmaktan mutluluk duyacaktır.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} ConvState AI. Tüm hakları saklıdır.</p>
            <p>Bu e-posta {{ $user->email }} adresine gönderilmiştir.</p>
            <p style="margin-top: 10px;">
                <a href="#" style="color: #27ae60;">Abonelik yönetimi</a> |
                <a href="#" style="color: #27ae60;">Fatura geçmişi</a> |
                <a href="#" style="color: #27ae60;">Destek</a>
            </p>
        </div>
    </div>
</body>
</html>
