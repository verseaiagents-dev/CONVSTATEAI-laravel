<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ConvStateAI - Uygulama Güncelleniyor</title>
    <link rel="icon" type="image/svg+xml" href="public/imgs/ai-conversion-logo.svg">
    <link rel="shortcut icon" type="image/svg+xml" href="public/imgs/ai-conversion-logo.svg">
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
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'spin-slow': 'spin 3s linear infinite',
                        'bounce-slow': 'bounce 2s infinite'
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
        
        /* Loading Animation */
        .loading-dots {
            display: inline-block;
        }
        .loading-dots::after {
            content: '';
            animation: dots 1.5s steps(4, end) infinite;
        }
        
        @keyframes dots {
            0%, 20% { content: ''; }
            40% { content: '.'; }
            60% { content: '..'; }
            80%, 100% { content: '...'; }
        }
        
        /* Progress Bar Animation */
        .progress-bar {
            background: linear-gradient(90deg, #8B5CF6, #A855F7, #EC4899);
            background-size: 200% 100%;
            animation: progress-flow 2s ease-in-out infinite;
        }
        
        @keyframes progress-flow {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Floating Elements */
        .floating-element {
            animation: float 6s ease-in-out infinite;
        }
        .floating-element:nth-child(2) {
            animation-delay: -2s;
        }
        .floating-element:nth-child(3) {
            animation-delay: -4s;
        }
    </style>
</head>
<body class="bg-black text-white min-h-screen flex items-center justify-center relative overflow-hidden">
    <!-- Background Effects -->
    <div class="absolute inset-0">
        <div class="absolute top-20 left-20 w-72 h-72 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20 floating-element"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20 floating-element"></div>
        <div class="absolute bottom-20 left-1/2 w-80 h-80 bg-purple-dark rounded-full mix-blend-multiply filter blur-xl opacity-20 floating-element"></div>
    </div>
    
    <!-- Main Content -->
    <div class="relative z-10 max-w-4xl mx-auto px-4 text-center">
        <!-- Logo -->
        <div class="mb-12">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-r from-purple-glow to-neon-purple rounded-full mb-6 animate-pulse-slow">
                <img src="public/imgs/ai-conversion-logo.svg" alt="ConvStateAI Logo" class="w-16 h-16">
            </div>
            <h1 class="text-4xl md:text-6xl font-bold mb-4">
                <span class="gradient-text">ConvStateAI</span>
            </h1>
        </div>
        
        <!-- Update Message -->
        <div class="glass-effect rounded-3xl p-8 md:p-12 mb-8 max-w-2xl mx-auto">
            <div class="mb-8">
                <h2 class="text-2xl md:text-3xl font-bold mb-4">
                    Uygulama Güncelleniyor<span class="loading-dots"></span>
                </h2>
                <p class="text-lg md:text-xl text-gray-300 mb-6">
                    Daha iyi bir deneyim için sistemimizi güncelliyoruz. 
                    Lütfen birkaç dakika bekleyin.
                </p>
            </div>
            
            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="w-full bg-gray-700 rounded-full h-3 mb-4">
                    <div class="progress-bar h-3 rounded-full w-3/4"></div>
                </div>
                <p class="text-sm text-gray-400">Güncelleme %75 tamamlandı</p>
            </div>
            
            <!-- Features Being Updated -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="flex items-center justify-center space-x-2 text-sm">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-gray-300">AI Chat Sistemi</span>
                </div>
                <div class="flex items-center justify-center space-x-2 text-sm">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-gray-300">Widget Entegrasyonu</span>
                </div>
                <div class="flex items-center justify-center space-x-2 text-sm">
                    <div class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></div>
                    <span class="text-gray-300">Veritabanı Optimizasyonu</span>
                </div>
            </div>
            
            <!-- Estimated Time -->
            <div class="text-center">
                <p class="text-gray-400 mb-2">Tahmini süre</p>
                <p class="text-2xl font-bold text-purple-glow">3-5 dakika</p>
            </div>
        </div>
        
        <!-- Status Messages -->
        <div class="space-y-4 max-w-2xl mx-auto">
            <div class="glass-effect rounded-2xl p-4 flex items-center space-x-3">
                <div class="w-8 h-8 bg-gradient-to-r from-purple-glow to-neon-purple rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <div class="text-left">
                    <p class="font-semibold">Sistem güncellemeleri uygulanıyor</p>
                    <p class="text-sm text-gray-400">Yeni özellikler ve performans iyileştirmeleri yükleniyor</p>
                </div>
            </div>
            
            <div class="glass-effect rounded-2xl p-4 flex items-center space-x-3">
                <div class="w-8 h-8 bg-gradient-to-r from-purple-glow to-neon-purple rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-white animate-bounce-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-left">
                    <p class="font-semibold">Veri güvenliği sağlanıyor</p>
                    <p class="text-sm text-gray-400">Tüm verileriniz güvenli bir şekilde korunuyor</p>
                </div>
            </div>
        </div>
        
        <!-- Contact Info -->
        <div class="mt-12 text-center">
            <p class="text-gray-400 mb-4">Acil durumlar için</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="mailto:destek@convstateai.com" class="px-6 py-3 glass-effect rounded-xl hover:bg-white hover:text-black transition-all duration-300">
                    📧 destek@convstateai.com
                </a>
                <a href="tel:+905551234567" class="px-6 py-3 glass-effect rounded-xl hover:bg-white hover:text-black transition-all duration-300">
                    📞 +90 555 123 45 67
                </a>
            </div>
        </div>
    </div>
    
    <!-- Auto Refresh Script -->
    <script>
        // Sayfa yüklendiğinde 30 saniye sonra otomatik yenileme
        setTimeout(function() {
            window.location.reload();
        }, 30000);
        
        // Her 10 saniyede bir durum kontrolü
        setInterval(function() {
            // Burada gerçek bir API çağrısı yapılabilir
            console.log('Sistem durumu kontrol ediliyor...');
        }, 10000);
        
        // Progress bar animasyonu
        let progress = 0;
        const progressBar = document.querySelector('.progress-bar');
        const progressText = document.querySelector('.text-sm.text-gray-400');
        
        setInterval(function() {
            progress += Math.random() * 2;
            if (progress > 100) progress = 100;
            
            progressBar.style.width = progress + '%';
            progressText.textContent = `Güncelleme %${Math.round(progress)} tamamlandı`;
            
            if (progress >= 100) {
                progressText.textContent = 'Güncelleme tamamlandı! Yönlendiriliyorsunuz...';
                setTimeout(function() {
                    window.location.href = '/';
                }, 2000);
            }
        }, 1000);
    </script>
</body>
</html>
