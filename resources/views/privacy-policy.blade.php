<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gizlilik Politikası - ConvStateAI</title>
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
                <span class="gradient-text">Gizlilik</span> Politikası
            </h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                Kişisel verilerinizin güvenliği bizim için çok önemli. Bu politika, verilerinizi nasıl topladığımızı, kullandığımızı ve koruduğumuzu açıklar.
            </p>
            <p class="text-sm text-gray-400 mt-4">Son güncelleme: {{ date('d.m.Y') }}</p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-20 relative">
        <div class="max-w-4xl mx-auto px-4">
            <div class="glass-effect rounded-3xl p-8 md:p-12">
                <div class="prose prose-invert max-w-none">
                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">1. Toplanan Bilgiler</h2>
                    <p class="text-gray-300 mb-6">
                        ConvStateAI olarak, hizmetlerimizi geliştirmek ve size daha iyi deneyim sunmak için aşağıdaki bilgileri toplarız:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>Hesap oluştururken verdiğiniz kişisel bilgiler (ad, e-posta, telefon)</li>
                        <li>Platform kullanım verileri ve analitik bilgiler</li>
                        <li>Chatbot etkileşimleri ve müşteri destek kayıtları</li>
                        <li>Teknik bilgiler (IP adresi, tarayıcı türü, cihaz bilgileri)</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">2. Bilgilerin Kullanımı</h2>
                    <p class="text-gray-300 mb-6">
                        Topladığımız bilgileri aşağıdaki amaçlarla kullanırız:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>Hizmetlerimizi sağlamak ve iyileştirmek</li>
                        <li>Müşteri desteği ve iletişim</li>
                        <li>Güvenlik ve dolandırıcılık önleme</li>
                        <li>Yasal yükümlülükleri yerine getirmek</li>
                        <li>Pazarlama ve iletişim (izin verdiğiniz durumlarda)</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">3. Bilgi Paylaşımı</h2>
                    <p class="text-gray-300 mb-6">
                        Kişisel verilerinizi üçüncü taraflarla paylaşmayız, ancak aşağıdaki durumlar hariç:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>Açık izniniz olduğunda</li>
                        <li>Yasal zorunluluk durumunda</li>
                        <li>Hizmet sağlayıcılarımızla (veri işleme anlaşmaları kapsamında)</li>
                        <li>İş satışı veya birleşmesi durumunda</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">4. Veri Güvenliği</h2>
                    <p class="text-gray-300 mb-6">
                        Verilerinizi korumak için endüstri standardı güvenlik önlemleri kullanırız:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>SSL/TLS şifreleme</li>
                        <li>Güvenli veri merkezleri</li>
                        <li>Düzenli güvenlik denetimleri</li>
                        <li>Çalışan eğitimleri ve erişim kontrolleri</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">5. Çerezler ve Takip Teknolojileri</h2>
                    <p class="text-gray-300 mb-6">
                        Web sitemizde ve hizmetlerimizde çerezler ve benzer teknolojiler kullanırız. Bu teknolojiler:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>Oturum yönetimi ve güvenlik</li>
                        <li>Kullanıcı tercihleri ve ayarlar</li>
                        <li>Analitik ve performans ölçümü</li>
                        <li>Kişiselleştirilmiş içerik ve reklamlar</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">6. Veri Saklama</h2>
                    <p class="text-gray-300 mb-6">
                        Kişisel verilerinizi, hizmet sağlama amacıyla gerekli olduğu sürece saklarız. Hesabınızı kapattığınızda, verileriniz 30 gün içinde güvenli şekilde silinir.
                    </p>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">7. KVKK Aydınlatma Metni</h2>
                    <p class="text-gray-300 mb-6">
                        Bu aydınlatma metni, 6698 sayılı Kişisel Verilerin Korunması Kanunu'nun ("KVKK") 10. maddesi ile Aydınlatma Yükümlülüğünün Yerine Getirilmesinde Uyulacak Usul ve Esaslar Hakkında Tebliğ kapsamında veri sorumlusu sıfatıyla Kadir Burak Durmazlar (Şahıs Şirketi) tarafından hazırlanmıştır.
                    </p>

                    <h3 class="text-xl font-bold mb-4 text-purple-glow">7.1. Veri Sorumlusu Bilgileri</h3>
                    <div class="bg-gray-800 rounded-lg p-6 mb-6">
                        <p class="text-gray-300 mb-2"><strong>Şirket Ünvanı:</strong> Kadir Burak Durmazlar (Şahıs Şirketi)</p>
                        <p class="text-gray-300 mb-2"><strong>Adres:</strong> Osmaniye Mah. Sevgi Sokak No:5 Alpu / Eskişehir</p>
                        <p class="text-gray-300 mb-2"><strong>Telefon:</strong> +90 545 852 76 93</p>
                        <p class="text-gray-300 mb-2"><strong>E-posta:</strong> kadirdurmazlar@gmail.com</p>
                        <p class="text-gray-300"><strong>Web Sitesi:</strong> https://convstateai.com</p>
                    </div>

                    <h3 class="text-xl font-bold mb-4 text-purple-glow">7.2. İşlenen Kişisel Veri Kategorileri</h3>
                    <p class="text-gray-300 mb-4">ConvStateAI olarak işlediğimiz kişisel veriler:</p>
                    <ul class="list-disc list-inside text-gray-300 mb-6 space-y-2">
                        <li><strong>Kimlik Verileri:</strong> Ad Soyad, kullanıcı ID, müşteri hesabı bilgileri</li>
                        <li><strong>İletişim Verileri:</strong> E-posta, telefon numarası, IP adresi, giriş/çıkış logları</li>
                        <li><strong>İşlem Güvenliği Verileri:</strong> IP adresi, tarayıcı bilgileri, giriş-çıkış kayıtları, sistem logları</li>
                        <li><strong>Hizmet Kullanım Verileri:</strong> Kullanıcının ConvStateAI paneli üzerinden gerçekleştirdiği işlemler, müşteri temsilcisi sohbet kayıtları</li>
                        <li><strong>Finansal Veriler:</strong> Üyelik/abonelik ödemelerine ilişkin fatura ve ödeme bilgileri</li>
                    </ul>
                    <div class="bg-blue-900/20 border border-blue-500/30 rounded-lg p-4 mb-6">
                        <p class="text-blue-300 text-sm"><strong>Not:</strong> Kişisel veriler, 3. parti yazılımlar veya harici hizmetler üzerinde kullanılmamaktadır. Tüm veriler ConvStateAI altyapısı üzerinde işlenmektedir.</p>
                    </div>

                    <h3 class="text-xl font-bold mb-4 text-purple-glow">7.3. Kişisel Verilerin İşlenme Amaçları</h3>
                    <p class="text-gray-300 mb-4">Kişisel verileriniz aşağıdaki amaçlarla işlenmektedir:</p>
                    <ul class="list-disc list-inside text-gray-300 mb-6 space-y-2">
                        <li>ConvStateAI üyelerinin kendi websitelerinde müşteri temsilcisi olarak hizmet verebilmesi</li>
                        <li>Ürün/hizmet aboneliklerinin oluşturulması ve yönetilmesi</li>
                        <li>Kullanıcı desteği ve müşteri memnuniyet süreçlerinin yürütülmesi</li>
                        <li>Hizmetin güvenliğinin ve sistem bütünlüğünün sağlanması</li>
                        <li>Faturalandırma ve yasal yükümlülüklerin yerine getirilmesi</li>
                    </ul>

                    <h3 class="text-xl font-bold mb-4 text-purple-glow">7.4. Kişisel Verilerin Aktarımı</h3>
                    <ul class="list-disc list-inside text-gray-300 mb-6 space-y-2">
                        <li><strong>Yurt içi aktarım:</strong> Yasal yükümlülükler çerçevesinde; resmi kurumlar, mahkemeler, vergi daireleri ve ödeme alınan bankalarla paylaşılabilir.</li>
                        <li><strong>Yurt dışı aktarım:</strong> Şu anda herhangi bir yurt dışı veri aktarımı yapılmamaktadır.</li>
                    </ul>

                    <h3 class="text-xl font-bold mb-4 text-purple-glow">7.5. Kişisel Verilerin Toplanma Yöntemi ve Hukuki Sebep</h3>
                    <p class="text-gray-300 mb-4">Kişisel verileriniz;</p>
                    <ul class="list-disc list-inside text-gray-300 mb-4 space-y-2">
                        <li>ConvStateAI web sitesi (https://convstateai.com) üzerinden üyelik kaydı sırasında</li>
                        <li>Elektronik formlar, e-posta, destek talepleri ve abonelik işlemleri aracılığıyla otomatik yollarla toplanmaktadır.</li>
                    </ul>
                    <p class="text-gray-300 mb-4"><strong>Hukuki sebepler:</strong></p>
                    <ul class="list-disc list-inside text-gray-300 mb-6 space-y-2">
                        <li>KVKK md.5 uyarınca sözleşmenin kurulması ve ifası</li>
                        <li>Hukuki yükümlülüklerin yerine getirilmesi</li>
                        <li>Meşru menfaat kapsamında hizmetin güvenliği ve geliştirilmesi</li>
                    </ul>

                    <h3 class="text-xl font-bold mb-4 text-purple-glow">7.6. İlgili Kişi Hakları</h3>
                    <p class="text-gray-300 mb-4">KVKK md.11 kapsamında kişisel veri sahipleri olarak aşağıdaki haklara sahipsiniz:</p>
                    <ul class="list-disc list-inside text-gray-300 mb-6 space-y-2">
                        <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
                        <li>İşlenmişse buna ilişkin bilgi talep etme</li>
                        <li>İşlenme amacını ve amacına uygun kullanılıp kullanılmadığını öğrenme</li>
                        <li>Yurt içinde veya yurt dışında aktarıldığı üçüncü kişileri bilme</li>
                        <li>Eksik veya yanlış işlenmişse düzeltilmesini isteme</li>
                        <li>KVKK'ya aykırı işlenmişse silinmesini veya yok edilmesini isteme</li>
                        <li>İşlenen verilerin yalnızca otomatik sistemlerle analiz edilmesi suretiyle aleyhinize bir sonucun ortaya çıkmasına itiraz etme</li>
                        <li>Zarara uğramanız halinde tazminat talep etme</li>
                    </ul>

                    <h3 class="text-xl font-bold mb-4 text-purple-glow">7.7. Başvuru Yöntemleri</h3>
                    <p class="text-gray-300 mb-4">Belirtilen haklarınızı kullanmak için:</p>
                    <div class="bg-gray-800 rounded-lg p-6 mb-6">
                        <p class="text-gray-300 mb-2"><strong>Web:</strong> https://convstateai.com</p>
                        <p class="text-gray-300"><strong>E-posta:</strong> kadirdurmazlar@gmail.com</p>
                    </div>
                    <p class="text-gray-300 mb-6">Başvurularınız, en geç 30 gün içinde ücretsiz olarak sonuçlandırılacaktır. Ancak, işlemin ayrıca bir maliyet gerektirmesi halinde Kişisel Verileri Koruma Kurulu tarafından belirlenen tarifedeki ücret esas alınacaktır.</p>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">8. Haklarınız (Genel)</h2>
                    <p class="text-gray-300 mb-6">
                        KVKK ve GDPR kapsamında aşağıdaki haklara sahipsiniz:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mb-8 space-y-2">
                        <li>Verilerinize erişim hakkı</li>
                        <li>Verilerinizi düzeltme hakkı</li>
                        <li>Verilerinizi silme hakkı</li>
                        <li>Veri işlemeye itiraz hakkı</li>
                        <li>Veri taşınabilirliği hakkı</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">9. İletişim</h2>
                    <p class="text-gray-300 mb-6">
                        Gizlilik politikamız ve KVKK haklarınız hakkında sorularınız için bizimle iletişime geçebilirsiniz:
                    </p>
                    <div class="bg-gray-800 rounded-lg p-6">
                        <p class="text-gray-300 mb-2"><strong>E-posta:</strong> kadirdurmazlar@gmail.com</p>
                        <p class="text-gray-300 mb-2"><strong>Adres:</strong> Osmaniye Mah. Sevgi Sokak No:5 Alpu / Eskişehir</p>
                        <p class="text-gray-300 mb-2"><strong>Telefon:</strong> +90 545 852 76 93</p>
                        <p class="text-gray-300"><strong>Web Sitesi:</strong> https://convstateai.com</p>
                    </div>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">10. Politika Güncellemeleri</h2>
                    <p class="text-gray-300 mb-6">
                        Bu gizlilik politikasını zaman zaman güncelleyebiliriz. Önemli değişiklikler olduğunda size e-posta ile bildirim göndeririz. Güncel politika her zaman web sitemizde yayınlanır.
                    </p>

                    <h2 class="text-2xl font-bold mb-6 text-purple-glow">11. Onay</h2>
                    <p class="text-gray-300">
                        Hizmetlerimizi kullanarak bu gizlilik politikasını ve KVKK aydınlatma metnini kabul etmiş olursunuz. Politikamız hakkında herhangi bir sorunuz varsa, lütfen bizimle iletişime geçin.
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
                    <a href="{{ route('privacy-policy') }}" class="text-purple-glow">Gizlilik Politikası</a>
                    <a href="{{ route('terms-of-service') }}" class="hover:text-purple-glow transition-colors">Kullanım Şartları</a>
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
