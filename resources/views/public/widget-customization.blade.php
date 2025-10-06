<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Widget Özelleştirme - ConvStateAI</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('imgs/ai-conversion-logo.svg') }}">
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
        
        
        .customization-panel {
            background: rgba(17, 24, 39, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #d1d5db;
            margin-bottom: 8px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(31, 41, 55, 0.8);
            border: 1px solid rgba(75, 85, 99, 0.5);
            border-radius: 8px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #8B5CF6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
        
        .form-select {
            width: 100%;
            padding: 12px 16px;
            background: rgba(31, 41, 55, 0.8);
            border: 1px solid rgba(75, 85, 99, 0.5);
            border-radius: 8px;
            color: white;
            font-size: 14px;
            cursor: pointer;
        }
        
        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        
        .form-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #8B5CF6;
        }
        
        .form-checkbox label {
            color: #d1d5db;
            font-size: 14px;
            cursor: pointer;
        }
        
        .color-picker {
            width: 100%;
            height: 40px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background: transparent;
        }
        
        .preview-button {
            background: linear-gradient(135deg, #8B5CF6, #A855F7);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .preview-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(139, 92, 246, 0.3);
        }
        
        .reset-button {
            background: rgba(75, 85, 99, 0.5);
            color: #d1d5db;
            border: 1px solid rgba(75, 85, 99, 0.5);
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 12px;
        }
        
        .reset-button:hover {
            background: rgba(75, 85, 99, 0.8);
        }
    </style>
</head>
<body class="bg-black text-white min-h-screen">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-effect">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <img src="{{ asset('imgs/ai-conversion-logo.svg') }}" alt="ConvStateAI Logo" class="w-10 h-10">
                    <span class="ml-3 text-xl font-bold">ConvState AI</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('index') }}" class="px-4 py-2 text-purple-glow hover:text-white transition-colors">Ana Sayfa</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg hover:from-purple-dark hover:to-purple-glow transition-all duration-300 transform hover:scale-105">
                        Hemen Başla
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-20 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">
                    <span class="gradient-text">Widget Test Ortamı</span>
                </h1>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                    Widget özelleştirme ayarlarını test edin. 
                    Sayfa yüklendikten sonra widget otomatik olarak açılacak.
                </p>
             

            <!-- Customization Panel -->
            <div class="max-w-4xl mx-auto">
                <div class="customization-panel rounded-2xl p-8">
                    <h2 class="text-2xl font-bold mb-6 gradient-text">Özelleştirme Paneli</h2>
                    
                    <form id="customizationForm">
                        <!-- AI İsmi -->
                        <div class="form-group">
                            <label class="form-label">AI Asistan İsmi</label>
                            <input type="text" id="aiName" class="form-input" value="ConvState AI" placeholder="AI asistanınızın ismi">
                        </div>

                        <!-- Hoşgeldin Mesajı -->
                        <div class="form-group">
                            <label class="form-label">Hoşgeldin Mesajı</label>
                            <textarea id="welcomeMessage" class="form-input" rows="3" placeholder="Müşterilerinize gösterilecek hoşgeldin mesajı">Merhaba! Ben ConvState AI, senin dijital asistanınım. Size nasıl yardımcı olabilirim?</textarea>
                        </div>

                        <!-- Davranış Biçimi -->
                        <div class="form-group">
                            <label class="form-label">Davranış Biçimi</label>
                            <select id="behaviorStyle" class="form-select">
                                <option value="friendly">Dostane</option>
                                <option value="professional">Profesyonel</option>
                                <option value="sales">Satış Uzmanı</option>
                                <option value="support">Destek Uzmanı</option>
                                <option value="casual">Samimi</option>
                            </select>
                        </div>

                        <!-- Tema Renkleri -->
                        <div class="form-group">
                            <label class="form-label">Ana Renk</label>
                            <input type="color" id="primaryColor" class="color-picker" value="#8B5CF6">
                        </div>

                        <div class="form-group">
                            <label class="form-label">İkincil Renk</label>
                            <input type="color" id="secondaryColor" class="color-picker" value="#A855F7">
                        </div>

                        <!-- Widget Pozisyonu -->
                        <div class="form-group">
                            <label class="form-label">Widget Pozisyonu</label>
                            <select id="widgetPosition" class="form-select">
                                <option value="bottom-right">Sağ Alt</option>
                                <option value="bottom-left">Sol Alt</option>
                                <option value="top-right">Sağ Üst</option>
                                <option value="top-left">Sol Üst</option>
                            </select>
                        </div>

                        <!-- Özellikler -->
                        <div class="form-group">
                            <label class="form-label">Gösterilecek Özellikler</label>
                            
                            <div class="form-checkbox">
                                <input type="checkbox" id="showCampaigns" checked>
                                <label for="showCampaigns">Kampanya Sekmesi</label>
                            </div>
                            
                            <div class="form-checkbox">
                                <input type="checkbox" id="showFAQ" checked>
                                <label for="showFAQ">SSS Sekmesi</label>
                            </div>
                            
                            <div class="form-checkbox">
                                <input type="checkbox" id="showOrderStatus" checked>
                                <label for="showOrderStatus">Sipariş Durumu</label>
                            </div>
                            
                            <div class="form-checkbox">
                                <input type="checkbox" id="showProductRecommendations" checked>
                                <label for="showProductRecommendations">Ürün Önerileri</label>
                            </div>
                            
                            <div class="form-checkbox">
                                <input type="checkbox" id="enableTTS" checked>
                                <label for="enableTTS">Sesli Yanıt</label>
                            </div>
                        </div>

                        <!-- Dil Ayarları -->
                        <div class="form-group">
                            <label class="form-label">Dil</label>
                            <select id="language" class="form-select">
                                <option value="tr">Türkçe</option>
                                <option value="en">English</option>
                                <option value="de">Deutsch</option>
                                <option value="fr">Français</option>
                            </select>
                        </div>

                        <!-- Butonlar -->
                        <button type="button" id="previewButton" class="preview-button">
                            🔄 Widget'ı Yenile
                        </button>
                        
                        <button type="button" id="resetButton" class="reset-button">
                            ↺ Varsayılanlara Sıfırla
                        </button>
                    </form>
                </div>
            </div>

            <!-- Features Section -->
            <div class="mt-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-4">Özelleştirme Seçenekleri</h2>
                    <p class="text-gray-400">Widget'ınızı tamamen kişiselleştirin</p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="glass-effect rounded-xl p-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Tema Özelleştirme</h3>
                        <p class="text-gray-300">Renkler, pozisyon ve görünüm ayarlarını istediğiniz gibi düzenleyin.</p>
                    </div>
                    
                    <div class="glass-effect rounded-xl p-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Davranış Ayarları</h3>
                        <p class="text-gray-300">AI'ın nasıl konuşacağını ve davranacağını belirleyin.</p>
                    </div>
                    
                    <div class="glass-effect rounded-xl p-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Özellik Kontrolü</h3>
                        <p class="text-gray-300">Hangi özelliklerin gösterileceğini seçin ve kontrol edin.</p>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="mt-20 text-center">
                <div class="glass-effect rounded-3xl p-12">
                    <h2 class="text-3xl font-bold mb-6">
                        <span class="gradient-text">Widget'ınızı Hazırladınız!</span>
                    </h2>
                    <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                        Artık widget'ınızı sitenize entegre etmeye hazırsınız. 
                        Hemen başlayın ve müşteri deneyimini dönüştürün.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient-to-r from-purple-glow to-neon-purple rounded-xl text-lg font-semibold hover:from-purple-dark hover:to-purple-glow transition-all duration-300 transform hover:scale-105">
                            Hemen Başla
                        </a>
                        <a href="{{ route('index') }}" class="px-8 py-4 border border-purple-glow text-purple-glow rounded-xl text-lg font-semibold hover:bg-purple-glow hover:text-white transition-all duration-300">
                            Ana Sayfaya Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        // Widget Configuration - Dinamik olarak yüklenecek
        let widgetConfig = {
            projectId: null,
            customizationToken: null,
            customization: {
                colors: {
                    primary: '#8B5CF6',
                    secondary: '#A855F7'
                },
                position: 'bottom-right',
                aiName: 'ConvState AI',
                welcomeMessage: 'Merhaba! Ben ConvState AI, senin dijital asistanınım. Size nasıl yardımcı olabilirim?',
                behaviorStyle: 'friendly',
                features: {
                    showCampaigns: true,
                    showFAQ: true,
                    showOrderStatus: true,
                    showProductRecommendations: true,
                    enableTTS: true
                },
                language: 'tr'
            }
        };

        // Form verilerini API'ye kaydet
        async function saveWidgetConfig() {
            try {
                const response = await fetch('/api/save-widget-config', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        projectId: widgetConfig.projectId,
                        customizationToken: widgetConfig.customizationToken,
                        config: widgetConfig.customization
                    })
                });

                const result = await response.json();
                if (result.success) {
                    console.log('Widget konfigürasyonu kaydedildi');
                } else {
                    console.warn('Konfigürasyon kaydedilemedi:', result.message);
                }
            } catch (error) {
                console.warn('Konfigürasyon kaydetme hatası:', error.message);
            }
        }

        // Widget'ı güncelle (yeniden oluşturmadan)
        async function updateWidget() {
            try {
                if (window.convstateai && window.convstateai.chat && window.convstateai.chat.updateConfig) {
                    await window.convstateai.chat.updateConfig(widgetConfig);
                    console.log('Widget güncellendi:', widgetConfig);
                } else if (window.convstateai && window.convstateai.chat && window.convstateai.chat.destroy) {
                    // Eğer updateConfig yoksa, destroy edip yeniden yükle
                    await window.convstateai.chat.destroy();
                    await initializeWidget();
                    console.log('Widget destroy edildi ve yeniden oluşturuldu');
                } else {
                    console.warn('Widget API mevcut değil');
                }
            } catch (error) {
                console.warn('Widget güncelleme hatası:', error.message);
            }
        }



        // Dinamik script import fonksiyonu
        function loadWidgetScript(projectId, customizationToken) {
            return new Promise((resolve, reject) => {
                // Mevcut script'i kaldır
                const existingScript = document.querySelector('script[src*="convstateai.min.js"]');
                if (existingScript) {
                    existingScript.remove();
                }
                
                // Yeni script oluştur
                const script = document.createElement('script');
                script.src = `http://127.0.0.1:8000/embed/convstateai.min.js`;
                script.onload = () => {
                    // Script yüklendikten sonra config'i ayarla
                    window.convstateaiConfig = {
                        projectId: projectId,
                        customizationToken: customizationToken
                    };
                    
                    // ConvStateAI objesinin hazır olmasını bekle
                    const checkConvStateAI = () => {
                        if (window.convstateai && window.convstateai.chat) {
                            resolve();
                        } else {
                            setTimeout(checkConvStateAI, 100);
                        }
                    };
                    checkConvStateAI();
                };
                script.onerror = () => {
                    reject(new Error('Widget script yüklenemedi'));
                };
                
                document.head.appendChild(script);
            });
        }

        // Rastgele proje yükle
        async function loadRandomProject() {
            try {
                const response = await fetch('/api/random-project');
                const result = await response.json();
                
                if (result.success) {
                    const projectData = result.data;
                    
                    // Widget config'i güncelle
                    widgetConfig.projectId = projectData.projectId.toString();
                    widgetConfig.customizationToken = projectData.customizationToken;
                    
                    // Dinamik script import ve widget başlatma
                    await loadWidgetScript(widgetConfig.projectId, widgetConfig.customizationToken);
                    await initializeWidget();
                    
                    console.log('Widget test ortamı hazırlandı');
                } else {
                    console.warn('Test ortamı hazırlanamadı:', result.message);
                }
            } catch (error) {
                console.warn('Test ortamı hazırlama hatası:', error.message);
            }
        }

        // Initialize Widget
        async function initializeWidget() {
            return new Promise((resolve, reject) => {
                // Clear existing widget
                const container = document.getElementById('widgetContainer');
                if (container) {
                    container.innerHTML = '';
                }
                
                // Initialize widget
                if (window.convstateai && window.convstateai.chat && window.convstateai.chat.load) {
                    window.convstateai.chat.load(widgetConfig).then(() => {
                        console.log('Widget loaded successfully with project:', widgetConfig.projectId);
                        resolve();
                    }).catch(error => {
                        console.error('Widget loading error:', error);
                        reject(error);
                    });
                } else {
                    reject(new Error('ConvStateAI widget script not loaded'));
                }
            });
        }

        // Update Widget Configuration
        async function updateWidgetConfig() {
            try {
                widgetConfig.customization = {
                    colors: {
                        primary: document.getElementById('primaryColor').value,
                        secondary: document.getElementById('secondaryColor').value
                    },
                    position: document.getElementById('widgetPosition').value,
                    aiName: document.getElementById('aiName').value,
                    welcomeMessage: document.getElementById('welcomeMessage').value,
                    behaviorStyle: document.getElementById('behaviorStyle').value,
                    features: {
                        showCampaigns: document.getElementById('showCampaigns').checked,
                        showFAQ: document.getElementById('showFAQ').checked,
                        showOrderStatus: document.getElementById('showOrderStatus').checked,
                        showProductRecommendations: document.getElementById('showProductRecommendations').checked,
                        enableTTS: document.getElementById('enableTTS').checked
                    },
                    language: document.getElementById('language').value
                };
                
                // Global config'i güncelle
                window.convstateaiConfig = widgetConfig;
                
                // Konfigürasyonu API'ye kaydet
                await saveWidgetConfig();
                
                // Widget'ı güncelle (yeniden oluşturmadan)
                await updateWidget();
                
                console.log('Widget configuration updated:', widgetConfig);
            } catch (error) {
                console.warn('Widget güncelleme hatası:', error.message);
            }
        }

        // Reset to Defaults
        async function resetToDefaults() {
            try {
                document.getElementById('aiName').value = 'ConvState AI';
                document.getElementById('welcomeMessage').value = 'Merhaba! Ben ConvState AI, senin dijital asistanınım. Size nasıl yardımcı olabilirim?';
                document.getElementById('behaviorStyle').value = 'friendly';
                document.getElementById('primaryColor').value = '#8B5CF6';
                document.getElementById('secondaryColor').value = '#A855F7';
                document.getElementById('widgetPosition').value = 'bottom-right';
                document.getElementById('showCampaigns').checked = true;
                document.getElementById('showFAQ').checked = true;
                document.getElementById('showOrderStatus').checked = true;
                document.getElementById('showProductRecommendations').checked = true;
                document.getElementById('enableTTS').checked = true;
                document.getElementById('language').value = 'tr';
                
                // Widget'ı varsayılan ayarlarla yeniden başlat
                await updateWidgetConfig();
                
                console.log('Widget configuration reset to defaults');
            } catch (error) {
                console.warn('Reset hatası:', error.message);
            }
        }

        // Event Listeners
        document.getElementById('previewButton').addEventListener('click', updateWidgetConfig);
        document.getElementById('resetButton').addEventListener('click', resetToDefaults);

        // Form değişikliklerini dinle
        document.getElementById('primaryColor').addEventListener('input', updateWidgetConfig);
        document.getElementById('secondaryColor').addEventListener('input', updateWidgetConfig);
        document.getElementById('widgetPosition').addEventListener('change', updateWidgetConfig);
        document.getElementById('aiName').addEventListener('input', updateWidgetConfig);
        document.getElementById('welcomeMessage').addEventListener('input', updateWidgetConfig);
        document.getElementById('behaviorStyle').addEventListener('change', updateWidgetConfig);
        document.getElementById('showCampaigns').addEventListener('change', updateWidgetConfig);
        document.getElementById('showFAQ').addEventListener('change', updateWidgetConfig);
        document.getElementById('showOrderStatus').addEventListener('change', updateWidgetConfig);
        document.getElementById('showProductRecommendations').addEventListener('change', updateWidgetConfig);
        document.getElementById('enableTTS').addEventListener('change', updateWidgetConfig);
        document.getElementById('language').addEventListener('change', updateWidgetConfig);

        // Initialize on page load with multiple checks
        let initAttempts = 0;
        const maxAttempts = 20; // 20 deneme (10 saniye)
        
        const tryInitializeWidget = () => {
            initAttempts++;
            console.log(`Widget başlatma denemesi ${initAttempts}/${maxAttempts}`);
            
            if (initAttempts >= maxAttempts) {
                console.warn('Widget başlatma timeout - maksimum deneme sayısına ulaşıldı');
                return;
            }
            
            // API'nin hazır olup olmadığını kontrol et
            fetch('/api/random-project')
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        console.log('API hazır, widget başlatılıyor...');
                        loadRandomProject();
                    } else {
                        console.log('API henüz hazır değil, tekrar deneniyor...');
                        setTimeout(tryInitializeWidget, 500); // 500ms sonra tekrar dene
                    }
                })
                .catch(error => {
                    console.log('API hatası, tekrar deneniyor...', error.message);
                    setTimeout(tryInitializeWidget, 500); // 500ms sonra tekrar dene
                });
        };
        
        // İlk denemeyi 2 saniye sonra yap
        setTimeout(tryInitializeWidget, 2000);
   
        // Alternatif: Window load event'i ile de dene
        window.addEventListener('load', function() {
            console.log('Window load event tetiklendi');
            setTimeout(() => {
                console.log('Window load sonrası widget başlatma denemesi');
                loadRandomProject();
            }, 3000); // 3 saniye sonra dene
        });

        // Sayfa yenilendiğinde widget'ı yenile
        window.addEventListener('beforeunload', function() {
            if (window.convstateai && window.convstateai.chat && window.convstateai.chat.destroy) {
                window.convstateai.chat.destroy();
            }
        });

        // Sayfa görünür olduğunda widget'ı kontrol et
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                console.log('Sayfa görünür oldu, widget kontrol ediliyor...');
                setTimeout(() => {
                    if (!window.convstateai || !window.convstateai.chat) {
                        console.log('Widget bulunamadı, yeniden yükleniyor...');
                        loadRandomProject();
                    }
                }, 1000);
            }
        });
    </script>
</body>
</html>
