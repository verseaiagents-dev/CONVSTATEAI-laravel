<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Çerez Politikası - ConvStateAI</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('imgs/ai-conversion-logo.svg') }}">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('imgs/ai-conversion-logo.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'purple-glow': '#8B5CF6',
                        'purple-dark': '#4C1D95',
                        'neon-purple': '#A855F7'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' }
                        },
                        glow: {
                            '0%': { boxShadow: '0 0 20px #8B5CF6' },
                            '100%': { boxShadow: '0 0 40px #8B5CF6, 0 0 60px #8B5CF6' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #8B5CF6, #A855F7, #EC4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-black text-white">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-effect">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="{{ route('index') }}">
                        <img src="{{ asset('imgs/ai-conversion-logo.svg') }}" alt="ConvStateAI Logo" class="w-10 h-10">
                        <span class="ml-3 text-xl font-bold">ConvStateAI</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="px-4 py-2 text-purple-glow hover:text-white transition-colors">Giriş Yap</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute top-20 left-20 w-72 h-72 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float"></div>
            <div class="absolute top-40 right-20 w-96 h-96 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float" style="animation-delay: -2s;"></div>
        </div>
        
        <div class="relative z-10 max-w-4xl mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                <span class="gradient-text">Çerez</span> Politikası
            </h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                Web sitemizde ve hizmetlerimizde çerezler ve benzer teknolojiler nasıl kullanılıyor? Detayları burada bulabilirsiniz.
            </p>
            <p class="text-sm text-gray-400 mt-4">Son güncelleme: {{ date('d.m.Y') }}</p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-20 relative">
        <div class="max-w-4xl mx-auto px-4">
            <div class="glass-effect rounded-3xl p-8 md:p-12">
                <div class="prose prose-invert max-w-none">
                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">1. Çerez Nedir?</h2>
                    <p class="text-gray-300 mb-6">
                        Çerezler, web sitelerinin tarayıcınıza gönderdiği küçük metin dosyalarıdır. Bu dosyalar cihazınızda saklanır ve web sitesine her ziyaret ettiğinizde tarayıcınız tarafından sunucuya geri gönderilir.
                    </p>
                    <p class="text-gray-300 mb-8">
                        Çerezler, web sitelerinin kullanıcı deneyimini iyileştirmek, site performansını analiz etmek ve kişiselleştirilmiş içerik sunmak için kullanılır.
                    </p>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">2. Hangi Çerezleri Kullanıyoruz?</h2>
                    
                    <h3 class="text-xl font-semibold mb-4 text-purple-glow">Zorunlu Çerezler (Essential Cookies)</h3>
                    <p class="text-gray-300 mb-6">
                        Bu çerezler web sitesinin temel işlevlerini yerine getirmek için gereklidir ve devre dışı bırakılamaz:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li><strong>Session Cookies:</strong> Oturum yönetimi ve güvenlik</li>
                        <li><strong>Authentication Cookies:</strong> Giriş durumu ve kimlik doğrulama</li>
                        <li><strong>CSRF Protection:</strong> Güvenlik token'ları</li>
                        <li><strong>Language Preferences:</strong> Dil tercihleri</li>
                    </ul>

                    <h3 class="text-xl font-semibold mb-4 text-purple-glow">Analitik Çerezler (Analytics Cookies)</h3>
                    <p class="text-gray-300 mb-6">
                        Web sitesi kullanımını analiz etmek ve performansı iyileştirmek için kullanılır:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li><strong>Google Analytics:</strong> Ziyaretçi sayısı, sayfa görüntülemeleri</li>
                        <li><strong>Performance Monitoring:</strong> Sayfa yükleme süreleri, hata oranları</li>
                        <li><strong>User Behavior:</strong> Kullanıcı etkileşimleri ve navigasyon</li>
                        <li><strong>Conversion Tracking:</strong> Hedef tamamlama oranları</li>
                    </ul>

                    <h3 class="text-xl font-semibold mb-4 text-purple-glow">Fonksiyonel Çerezler (Functional Cookies)</h3>
                    <p class="text-gray-300 mb-6">
                        Gelişmiş özellikler ve kişiselleştirme için kullanılır:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li><strong>User Preferences:</strong> Tema, font boyutu, layout tercihleri</li>
                        <li><strong>Chatbot Settings:</strong> Chatbot konfigürasyonu ve geçmiş</li>
                        <li><strong>Form Data:</strong> Form doldurma yardımcıları</li>
                        <li><strong>Personalization:</strong> İçerik önerileri ve hedefli mesajlar</li>
                    </ul>

                    <h3 class="text-xl font-semibold mb-4 text-purple-glow">Pazarlama Çerezleri (Marketing Cookies)</h3>
                    <p class="text-gray-300 mb-6">
                        Reklam ve pazarlama amaçlı kullanılır (sadece izin verdiğinizde):
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li><strong>Retargeting:</strong> Daha önce ziyaret ettiğiniz sayfalara yönelik reklamlar</li>
                        <li><strong>Social Media:</strong> Sosyal medya platformları entegrasyonu</li>
                        <li><strong>Email Marketing:</strong> E-posta kampanya performansı</li>
                        <li><strong>Affiliate Tracking:</strong> Ortaklık programı takibi</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">3. Üçüncü Taraf Çerezler</h2>
                    <p class="text-gray-300 mb-6">
                        Web sitemizde aşağıdaki üçüncü taraf hizmetler kullanılmaktadır:
                    </p>
                    <div class="bg-gray-800 rounded-lg p-6 mb-8">
                        <h4 class="font-semibold mb-4 text-purple-glow">Google Services</h4>
                        <ul class="space-y-2 text-sm text-gray-300">
                            <li><strong>Google Analytics:</strong> Web sitesi analizi ve raporlama</li>
                            <li><strong>Google Tag Manager:</strong> Çerez ve tracking yönetimi</li>
                            <li><strong>Google Fonts:</strong> Tipografi ve font yükleme</li>
                        </ul>
                    </div>

                    <div class="bg-gray-800 rounded-lg p-6 mb-8">
                        <h4 class="font-semibold mb-4 text-purple-glow">Social Media</h4>
                        <ul class="space-y-2 text-sm text-gray-300">
                            <li><strong>Facebook Pixel:</strong> Facebook reklam takibi</li>
                            <li><strong>LinkedIn Insight:</strong> LinkedIn kampanya analizi</li>
                            <li><strong>Twitter Pixel:</strong> Twitter reklam performansı</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">4. Çerez Yönetimi</h2>
                    <p class="text-gray-300 mb-6">
                        Çerez tercihlerinizi aşağıdaki yollarla yönetebilirsiniz:
                    </p>
                    
                    <h3 class="text-xl font-semibold mb-4 text-purple-glow">Tarayıcı Ayarları</h3>
                    <p class="text-gray-300 mb-6">
                        Çoğu tarayıcıda çerezleri devre dışı bırakabilir veya silebilirsiniz:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li><strong>Chrome:</strong> Ayarlar > Gizlilik ve Güvenlik > Çerezler</li>
                        <li><strong>Firefox:</strong> Ayarlar > Gizlilik ve Güvenlik > Çerezler</li>
                        <li><strong>Safari:</strong> Tercihler > Gizlilik > Çerezler</li>
                        <li><strong>Edge:</strong> Ayarlar > Çerezler ve site izinleri</li>
                    </ul>

                    <h3 class="text-xl font-semibold mb-4 text-purple-glow">Çerez Onay Yöneticisi</h3>
                    <p class="text-gray-300 mb-6">
                        Web sitemizde çerez onay yöneticisi bulunmaktadır. Bu araç ile:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>Analitik çerezleri açıp kapatabilirsiniz</li>
                        <li>Pazarlama çerezlerini kontrol edebilirsiniz</li>
                        <li>Fonksiyonel çerezleri yönetebilirsiniz</li>
                        <li>Çerez tercihlerinizi kaydedebilirsiniz</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">5. Çerez Süreleri</h2>
                    <p class="text-gray-300 mb-6">
                        Çerezler farklı sürelerde saklanır:
                    </p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-gray-300 border-collapse">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="text-left py-3 px-4 font-semibold text-purple-glow">Çerez Türü</th>
                                    <th class="text-left py-3 px-4 font-semibold text-purple-glow">Süre</th>
                                    <th class="text-left py-3 px-4 font-semibold text-purple-glow">Açıklama</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-gray-700">
                                    <td class="py-3 px-4">Session Cookies</td>
                                    <td class="py-3 px-4">Oturum süresi</td>
                                    <td class="py-3 px-4">Tarayıcı kapatıldığında silinir</td>
                                </tr>
                                <tr class="border-b border-gray-700">
                                    <td class="py-3 px-4">Authentication</td>
                                    <td class="py-3 px-4">30 gün</td>
                                    <td class="py-3 px-4">Giriş durumu hatırlama</td>
                                </tr>
                                <tr class="border-b border-gray-700">
                                    <td class="py-3 px-4">Analytics</td>
                                    <td class="py-3 px-4">2 yıl</td>
                                    <td class="py-3 px-4">Google Analytics veri saklama</td>
                                </tr>
                                <tr class="border-b border-gray-700">
                                    <td class="py-3 px-4">Preferences</td>
                                    <td class="py-3 px-4">1 yıl</td>
                                    <td class="py-3 px-4">Kullanıcı tercihleri</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4">Marketing</td>
                                    <td class="py-3 px-4">90 gün</td>
                                    <td class="py-3 px-4">Reklam ve pazarlama</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">6. Mobil Uygulama Çerezleri</h2>
                    <p class="text-gray-300 mb-6">
                        Mobil uygulamamızda da benzer teknolojiler kullanılmaktadır:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li><strong>Device ID:</strong> Cihaz tanımlayıcıları</li>
                        <li><strong>App Analytics:</strong> Uygulama kullanım verileri</li>
                        <li><strong>Push Notifications:</strong> Bildirim tercihleri</li>
                        <li><strong>Offline Storage:</strong> Çevrimdışı veri saklama</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">7. GDPR ve KVKK Uyumluluğu</h2>
                    <p class="text-gray-300 mb-6">
                        Çerez politikamız GDPR ve KVKK gerekliliklerine uygun olarak hazırlanmıştır:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>Açık ve şeffaf bilgilendirme</li>
                        <li>Açık rıza alma mekanizması</li>
                        <li>Çerez tercihlerini değiştirme hakkı</li>
                        <li>Veri silme ve erişim hakları</li>
                        <li>Çerez kullanımının gerekçeleri</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">8. Çerez Güvenliği</h2>
                    <p class="text-gray-300 mb-6">
                        Çerez güvenliği için aşağıdaki önlemler alınmıştır:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>HTTPS protokolü ile şifreli iletişim</li>
                        <li>Secure flag ile güvenli çerezler</li>
                        <li>HttpOnly flag ile JavaScript erişimi engelleme</li>
                        <li>SameSite attribute ile CSRF koruması</li>
                        <li>Düzenli güvenlik güncellemeleri</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">9. Çerez Politikası Güncellemeleri</h2>
                    <p class="text-gray-300 mb-6">
                        Bu çerez politikası zaman zaman güncellenebilir. Önemli değişiklikler olduğunda:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>Web sitemizde duyuru yapılır</li>
                        <li>E-posta ile bildirim gönderilir</li>
                        <li>Çerez onay banner'ı güncellenir</li>
                        <li>Değişiklik tarihi belirtilir</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">10. İletişim ve Sorular</h2>
                    <p class="text-gray-300 mb-6">
                        Çerez politikamız hakkında sorularınız için:
                    </p>
                    <div class="bg-gray-800 rounded-lg p-6">
                        <p class="text-gray-300 mb-2"><strong>E-posta:</strong> cookies@convstateai.com</p>
                        <p class="text-gray-300 mb-2"><strong>Adres:</strong> ConvStateAI Veri Koruma Ofisi</p>
                        <p class="text-gray-300"><strong>Telefon:</strong> +90 (XXX) XXX XX XX</p>
                    </div>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">11. Faydalı Kaynaklar</h2>
                    <p class="text-gray-300 mb-6">
                        Çerezler hakkında daha fazla bilgi için:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li><a href="https://www.allaboutcookies.org" class="text-purple-glow hover:text-neon-purple transition-colors">All About Cookies</a> - Çerezler hakkında kapsamlı bilgi</li>
                        <li><a href="https://gdpr.eu/cookies" class="text-purple-glow hover:text-neon-purple transition-colors">GDPR.eu</a> - GDPR ve çerezler</li>
                        <li><a href="https://kvkk.gov.tr" class="text-purple-glow hover:text-neon-purple transition-colors">KVKK</a> - Türkiye veri koruma kurumu</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">12. Onay</h2>
                    <p class="text-gray-300">
                        Web sitemizi kullanarak bu çerez politikasını kabul etmiş olursunuz. Çerez kullanımını istemiyorsanız, tarayıcı ayarlarınızdan çerezleri devre dışı bırakabilirsiniz, ancak bu durumda bazı özellikler düzgün çalışmayabilir.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-16 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center mb-4">
                        <img src="{{ asset('imgs/ai-conversion-logo.svg') }}" alt="ConvStateAI Logo" class="w-10 h-10">
                        <span class="ml-3 text-xl font-bold">ConvStateAI</span>
                    </div>
                    <p class="text-gray-400 mb-4">Yapay zeka ile geleceği şekillendiriyoruz.</p>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Ürün</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-purple-glow transition-colors">Özellikler</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Şirket</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-purple-glow transition-colors">Kariyer</a></li>
                        <li><a href="#" class="hover:text-purple-glow transition-colors">Blog</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Destek</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-purple-glow transition-colors">Yardım Merkezi</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm mb-4 md:mb-0">
                    © <span id="current-year"></span> ConvStateAI. Tüm hakları saklıdır.
                </p>
                
                <div class="flex space-x-6 text-sm text-gray-400">
                    <a href="{{ route('privacy-policy') }}" class="hover:text-purple-glow transition-colors">Gizlilik Politikası</a>
                    <a href="{{ route('terms-of-service') }}" class="hover:text-purple-glow transition-colors">Kullanım Şartları</a>
                    <a href="{{ route('cookies') }}" class="text-purple-glow">Çerezler</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('current-year').textContent = new Date().getFullYear();
    </script>
</body>
</html>
