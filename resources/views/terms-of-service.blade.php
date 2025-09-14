<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kullanım Şartları - ConvStateAI</title>
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
                <span class="gradient-text">Kullanım</span> Şartları
            </h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                ConvStateAI platformunu kullanarak aşağıdaki şartları kabul etmiş olursunuz. Lütfen dikkatlice okuyun.
            </p>
            <p class="text-sm text-gray-400 mt-4">Son güncelleme: {{ date('d.m.Y') }}</p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-20 relative">
        <div class="max-w-4xl mx-auto px-4">
            <div class="glass-effect rounded-3xl p-8 md:p-12">
                <div class="prose prose-invert max-w-none">
                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">1. Hizmet Tanımı</h2>
                    <p class="text-gray-300 mb-6">
                        ConvStateAI, yapay zeka destekli chatbot, knowledge base yönetimi, kampanya yönetimi ve analitik hizmetleri sunan bir platformdur. Bu hizmetler web sitesi, mobil uygulama ve API aracılığıyla erişilebilir.
                    </p>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">2. Hesap Oluşturma ve Kullanım</h2>
                    <p class="text-gray-300 mb-6">
                        Hizmetlerimizi kullanmak için geçerli bir hesap oluşturmanız gerekir:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>18 yaşından büyük olmalısınız veya yasal vasiyetiniz olmalıdır</li>
                        <li>Doğru ve güncel bilgiler vermelisiniz</li>
                        <li>Hesap güvenliğinizden siz sorumlusunuz</li>
                        <li>Hesabınızı başkalarına devredemezsiniz</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">3. Kullanım Kuralları</h2>
                    <p class="text-gray-300 mb-6">
                        Platformumuzu kullanırken aşağıdaki kurallara uymalısınız:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>Yasal ve etik sınırlar içinde kalmalısınız</li>
                        <li>Başkalarının haklarını ihlal etmemelisiniz</li>
                        <li>Platform güvenliğini tehdit etmemelisiniz</li>
                        <li>Spam, zararlı içerik veya dolandırıcılık yapmamalısınız</li>
                        <li>Telif hakkı ihlali yapmamalısınız</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">4. Fikri Mülkiyet Hakları</h2>
                    <p class="text-gray-300 mb-6">
                        ConvStateAI platformu ve içeriği aşağıdaki haklara sahiptir:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>Platform yazılımı ve teknolojisi</li>
                        <li>Tasarım, logo ve marka kimliği</li>
                        <li>Dokümantasyon ve eğitim materyalleri</li>
                        <li>API ve entegrasyon araçları</li>
                    </ul>
                    <p class="text-gray-300 mb-6">
                        Kullanıcılar tarafından oluşturulan içerikler kullanıcıya aittir, ancak platformda yayınlama izni verilmiş olur.
                    </p>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">5. Ödeme ve Abonelik</h2>
                    <p class="text-gray-300 mb-6">
                        Ücretli planlarımız için aşağıdaki şartlar geçerlidir:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>Fiyatlar aylık/yıllık olarak önceden ödenir</li>
                        <li>Otomatik yenileme varsayılan olarak aktiftir</li>
                        <li>İptal işlemi bir sonraki fatura döneminde geçerli olur</li>
                        <li>Ücret iadesi yapılmaz, ancak kullanılmayan süre için kredi verilebilir</li>
                        <li>Fiyat değişiklikleri 30 gün önceden bildirilir</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">6. Hizmet Kalitesi ve Uptime</h2>
                    <p class="text-gray-300 mb-6">
                        Hizmet kalitemizi sürekli iyileştirmeye çalışırız:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>%99.9 uptime hedefi (planlı bakım hariç)</li>
                        <li>7/24 teknik destek</li>
                        <li>Düzenli güvenlik güncellemeleri</li>
                        <li>Performans optimizasyonları</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">7. Veri ve Gizlilik</h2>
                    <p class="text-gray-300 mb-6">
                        Veri işleme ve gizlilik konularında:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>KVKK ve GDPR uyumluluğu sağlanır</li>
                        <li>Veri güvenliği endüstri standartlarında</li>
                        <li>Üçüncü taraf paylaşımı sadece izinle</li>
                        <li>Veri saklama süreleri şeffaf şekilde belirtilir</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">8. Sorumluluk Sınırları</h2>
                    <p class="text-gray-300 mb-6">
                        ConvStateAI'ın sorumluluğu şu şekilde sınırlandırılmıştır:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>Dolaylı zararlar için sorumluluk kabul edilmez</li>
                        <li>Toplam sorumluluk aylık ödeme tutarı ile sınırlıdır</li>
                        <li>Force majeure durumları için sorumluluk yoktur</li>
                        <li>Kullanıcı hatalarından kaynaklanan zararlar kapsam dışıdır</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">9. Hizmet Sonlandırma</h2>
                    <p class="text-gray-300 mb-6">
                        Hizmet aşağıdaki durumlarda sonlandırılabilir:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>Kullanıcı talebi üzerine</li>
                        <li>Şart ihlali durumunda</li>
                        <li>Ödeme yapılmaması durumunda</li>
                        <li>Yasal zorunluluk durumunda</li>
                    </ul>
                    <p class="text-gray-300 mb-6">
                        Hizmet sonlandırıldığında verileriniz 30 gün içinde silinir.
                    </p>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">10. Değişiklikler ve Güncellemeler</h2>
                    <p class="text-gray-300 mb-6">
                        Bu şartlar zaman zaman güncellenebilir:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>Önemli değişiklikler 30 gün önceden bildirilir</li>
                        <li>Kullanıcılar değişiklikleri kabul etmek zorundadır</li>
                        <li>Kabul edilmeyen değişiklikler için hizmet sonlandırılabilir</li>
                        <li>Güncel şartlar her zaman web sitemizde yayınlanır</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">11. Uyuşmazlık Çözümü</h2>
                    <p class="text-gray-300 mb-6">
                        Uyuşmazlık durumunda aşağıdaki süreç izlenir:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>Öncelikle dostane çözüm aranır</li>
                        <li>Gerekirse arabuluculuk süreci başlatılır</li>
                        <li>Son çare olarak Türkiye mahkemeleri yetkilidir</li>
                        <li>Türkiye hukuku uygulanır</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">12. İletişim</h2>
                    <p class="text-gray-300 mb-6">
                        Bu şartlar hakkında sorularınız için:
                    </p>
                    <div class="bg-gray-800 rounded-lg p-6">
                        <p class="text-gray-300 mb-2"><strong>E-posta:</strong> legal@convstateai.com</p>
                        <p class="text-gray-300 mb-2"><strong>Adres:</strong> ConvStateAI Hukuk Departmanı</p>
                        <p class="text-gray-300"><strong>Telefon:</strong> +90 (XXX) XXX XX XX</p>
                    </div>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">13. Kabul</h2>
                    <p class="text-gray-300">
                        ConvStateAI platformunu kullanarak bu kullanım şartlarını kabul etmiş olursunuz. Şartları kabul etmiyorsanız, lütfen platformu kullanmayın.
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
                    <a href="{{ route('terms-of-service') }}" class="text-purple-glow">Kullanım Şartları</a>
                    <a href="{{ route('cookies') }}" class="hover:text-purple-glow transition-colors">Çerezler</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('current-year').textContent = new Date().getFullYear();
    </script>
</body>
</html>
