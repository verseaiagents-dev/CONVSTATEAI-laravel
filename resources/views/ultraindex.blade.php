<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ConvStateAI - Satış odaklı Müşteri Asistanı</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('imgs/favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('imgs/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('imgs/favicon.ico') }}">
    
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
                                            <img src="{{ asset('imgs/ai-conversion-logo.svg') }}" alt="ConvStateAI Logo" class="w-10 h-10">
                        <span class="ml-3 text-xl font-bold">ConvStateAI</span>
                </div>
             
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg hover:from-purple-dark hover:to-purple-glow transition-all duration-300 transform hover:scale-105">
                            Panele Giriş
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-purple-glow hover:text-white transition-colors">Giriş Yap</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section (Awareness Stage) -->
    <section class="min-h-screen flex items-center justify-center relative overflow-hidden pt-20">
        <!-- Background Effects -->
        <div class="absolute inset-0">
            <div class="absolute top-20 left-20 w-72 h-72 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float"></div>
            <div class="absolute top-40 right-20 w-96 h-96 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float" style="animation-delay: -2s;"></div>
            <div class="absolute bottom-20 left-1/2 w-80 h-80 bg-purple-dark rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float" style="animation-delay: -4s;"></div>
        </div>
        
        <div class="relative z-10 max-w-6xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="text-center lg:text-left">
            <h1 class="text-4xl sm:text-5xl md:text-7xl font-bold mb-6">
                        <span class="gradient-text">Conv State AI</span> <div class="h-3 sm:h-5"></div> ile
                        Müşteri Deneyimini
                        <br>Dönüştürün
            </h1>
                    <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-2xl">
                        E-ticaret sitenize entegre edin, müşterilerinizle 7/24 akıllı sohbet edin. 
                        Kampanyalar, ürün önerileri ve anında destek ile satışlarınızı artırın.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start items-center mb-8">
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient-to-r from-purple-glow to-neon-purple rounded-xl text-lg font-semibold hover:from-purple-dark hover:to-purple-glow transition-all duration-300 transform hover:scale-105 animate-glow">
                            Ücretsiz Dene
                        </a>
                        <button onclick="scrollToDemo()" class="px-8 py-4 glass-effect rounded-xl text-lg font-semibold hover:bg-white hover:text-black transition-all duration-300">
                            Demo İzle
                </button>
            </div>
                    <div class="flex flex-wrap items-center justify-center lg:justify-start space-x-8 text-sm text-gray-400">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                            <span>7 gün ücretsiz</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                            <span>5 dakikada kurulum</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                            <span>Kredi kartı yok</span>
                        </div>
                    </div>
                </div>
                
                <!-- Right Content - Widget Preview -->
                <div class="relative">
                    <div class="glass-effect rounded-3xl p-6 max-w-md mx-auto">
                        <div class="bg-gray-900 rounded-2xl p-4 h-96 relative overflow-hidden">
                            <!-- Mock Website -->
                            <div class="h-full bg-gradient-to-b from-gray-800 to-gray-900 rounded-lg relative">
                                <!-- Website Header -->
                                <div class="h-12 bg-gray-700 rounded-t-lg flex items-center px-4">
                                    <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-4"></div>
                                    <div class="text-xs text-gray-300">www.mağazam.com</div>
                                </div>
                                
                                <!-- Website Content -->
                                <div class="p-4 text-center">
                                    <div class="text-white text-sm mb-4">E-ticaret Mağazası</div>
                                    <div class="grid grid-cols-2 gap-2 mb-4">
                                        <div class="bg-gray-600 h-16 rounded"></div>
                                        <div class="bg-gray-600 h-16 rounded"></div>
                                    </div>
                                    <div class="text-xs text-gray-400">Ürünler yükleniyor...</div>
                                </div>
                                
                                <!-- Chat Widget -->
                                <div class="absolute bottom-4 right-4 w-16 h-16 bg-gradient-to-r from-purple-glow to-neon-purple rounded-full flex items-center justify-center animate-pulse">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                                
                                <!-- Chat Bubble -->
                                <div class="absolute bottom-20 right-4 bg-white text-gray-800 p-3 rounded-lg shadow-lg max-w-xs">
                                    <div class="text-xs font-semibold mb-1">ConvState AI</div>
                                    <div class="text-sm">Merhaba! Size nasıl yardımcı olabilirim? 🛍️</div>
                                    <div class="absolute bottom-0 right-0 w-0 h-0 border-l-8 border-r-8 border-t-8 border-l-transparent border-r-transparent border-t-white transform translate-y-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Elements -->
                    <div class="absolute -top-4 -left-4 w-8 h-8 bg-green-400 rounded-full flex items-center justify-center animate-bounce">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="absolute -bottom-4 -right-4 w-8 h-8 bg-blue-400 rounded-full flex items-center justify-center animate-bounce" style="animation-delay: 0.5s;">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section (Interest Stage) -->
    <section id="features" class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">
                    Neden <span class="gradient-text">ConvStateAI</span>?
                </h2>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                    E-ticaret sitenize güçlü Conv State AI'ı entegre edin, müşteri deneyimini dönüştürün
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 - AI Chat -->
                <div class="glass-effect rounded-2xl p-8 hover:transform hover:scale-105 transition-all duration-300 group">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-glow to-neon-purple rounded-2xl flex items-center justify-center mb-6 group-hover:animate-pulse-slow">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">AI Destekli Chat</h3>
                    <p class="text-gray-300">Müşterilerinizle 7/24 akıllı sohbet edin. Doğal dil işleme ile anında yanıt verin.</p>
                </div>

                <!-- Feature 2 - Kampanya Yönetimi -->
                <div class="glass-effect rounded-2xl p-8 hover:transform hover:scale-105 transition-all duration-300 group">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-glow to-neon-purple rounded-2xl flex items-center justify-center mb-6 group-hover:animate-pulse-slow">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Kampanya Yönetimi</h3>
                    <p class="text-gray-300">AI destekli kampanya önerileri ile müşterilerinize kişiselleştirilmiş teklifler sunun.</p>
                </div>

                <!-- Feature 3 - Ürün Önerileri -->
                <div class="glass-effect rounded-2xl p-8 hover:transform hover:scale-105 transition-all duration-300 group">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-glow to-neon-purple rounded-2xl flex items-center justify-center mb-6 group-hover:animate-pulse-slow">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Akıllı Ürün Önerileri</h3>
                    <p class="text-gray-300">Müşteri tercihlerine göre otomatik ürün önerileri ile satışlarınızı artırın.</p>
                </div>

                <!-- Feature 4 - Kargo Takip -->
                <div class="glass-effect rounded-2xl p-8 hover:transform hover:scale-105 transition-all duration-300 group">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-glow to-neon-purple rounded-2xl flex items-center justify-center mb-6 group-hover:animate-pulse-slow">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Kargo Takip Entegrasyonu</h3>
                    <p class="text-gray-300">Sipariş takip numarası ile kargo durumunu anında sorgulayın ve müşteriye bildirin.</p>
                </div>

                <!-- Feature 5 - FAQ Sistemi -->
                <div class="glass-effect rounded-2xl p-8 hover:transform hover:scale-105 transition-all duration-300 group">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-glow to-neon-purple rounded-2xl flex items-center justify-center mb-6 group-hover:animate-pulse-slow">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Sık Sorulan Sorular</h3>
                    <p class="text-gray-300">Otomatik FAQ sistemi ile müşteri sorularına anında yanıt verin.</p>
                </div>

                <!-- Feature 6 - Kolay Entegrasyon -->
                <div class="glass-effect rounded-2xl p-8 hover:transform hover:scale-105 transition-all duration-300 group">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-glow to-neon-purple rounded-2xl flex items-center justify-center mb-6 group-hover:animate-pulse-slow">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">5 Dakikada Kurulum</h3>
                    <p class="text-gray-300">Tek satır kod ile sitenize entegre edin. Hiçbir teknik bilgi gerekmez.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Section -->
    <section id="demo" class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">
                    <span class="gradient-text">Canlı Demo İzleyin </span>
                </h2>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                    ConvStateAI widget'ının nasıl çalıştığını görün ve müşteri deneyimini keşfedin
                </p>
            </div>
            
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Demo Video/Preview -->
                <div class="relative">
                    <div class="glass-effect rounded-3xl p-6">
                        <div class="bg-gray-900 rounded-2xl p-4 h-96 relative overflow-hidden">
                            <!-- Mock E-commerce Site with Widget -->
                            <div class="h-full bg-gradient-to-b from-gray-800 to-gray-900 rounded-lg relative">
                                <!-- Browser Header -->
                                <div class="h-12 bg-gray-700 rounded-t-lg flex items-center px-4">
                                    <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-4"></div>
                                    <div class="text-xs text-gray-300">www.örnek-mağaza.com</div>
                                </div>
                                
                                <!-- Website Content -->
                                <div class="p-4">
                                    <div class="text-white text-sm mb-4 font-semibold">Elektronik Mağazası</div>
                                    <div class="grid grid-cols-2 gap-2 mb-4">
                                        <div class="bg-gray-600 h-12 rounded flex items-center justify-center">
                                            <span class="text-xs text-gray-300">📱 Telefon</span>
                                        </div>
                                        <div class="bg-gray-600 h-12 rounded flex items-center justify-center">
                                            <span class="text-xs text-gray-300">💻 Laptop</span>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-400 text-center">Ürünler yükleniyor...</div>
                                </div>
                                
                                <!-- Chat Widget Interface -->
                                <div class="absolute bottom-4 right-4 w-20 h-20 bg-gradient-to-r from-purple-glow to-neon-purple rounded-full flex items-center justify-center animate-pulse cursor-pointer">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                                
                                <!-- Chat Messages -->
                                <div class="absolute bottom-24 right-4 space-y-2 max-w-xs">
                                    <div class="bg-white text-gray-800 p-3 rounded-lg shadow-lg">
                                        <div class="text-xs font-semibold mb-1">ConvState AI</div>
                                        <div class="text-sm">Merhaba! Size nasıl yardımcı olabilirim? 🛍️</div>
                                    </div>
                                    <div class="bg-gray-100 text-gray-800 p-3 rounded-lg ml-8">
                                        <div class="text-sm">Telefon modelleri hakkında bilgi alabilir miyim?</div>
                                    </div>
                                    <div class="bg-white text-gray-800 p-3 rounded-lg shadow-lg">
                                        <div class="text-xs font-semibold mb-1">ConvState AI</div>
                                        <div class="text-sm">Tabii! Size en uygun telefon modellerini önerebilirim. Hangi bütçe aralığında düşünüyorsunuz? 📱</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Demo Features -->
                <div class="space-y-8">
                    <div class="glass-effect rounded-2xl p-6">
                        <h3 class="text-xl font-semibold mb-4 text-purple-glow">Gerçek Zamanlı Sohbet</h3>
                        <p class="text-gray-300 mb-4">Müşterilerinizle anında iletişim kurun. AI destekli yanıtlar ile 7/24 hizmet verin.</p>
                        <div class="flex items-center text-sm text-green-400">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Anında yanıt</span>
                        </div>
                    </div>
                    
                    <div class="glass-effect rounded-2xl p-6">
                        <h3 class="text-xl font-semibold mb-4 text-purple-glow">Akıllı Ürün Önerileri</h3>
                        <p class="text-gray-300 mb-4">Müşteri tercihlerine göre otomatik ürün önerileri ile satışlarınızı artırın.</p>
                        <div class="flex items-center text-sm text-green-400">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Kişiselleştirilmiş öneriler</span>
                        </div>
                    </div>
                    
                    <div class="glass-effect rounded-2xl p-6">
                        <h3 class="text-xl font-semibold mb-4 text-purple-glow">Kampanya Entegrasyonu</h3>
                        <p class="text-gray-300 mb-4">Aktif kampanyalarınızı otomatik olarak müşterilere duyurun ve ilgi çekin.</p>
                        <div class="flex items-center text-sm text-green-400">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Otomatik kampanya duyuruları</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Proof Section -->
    <section class="py-16 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Binlerce E-ticaret Sitesi Bizi Tercih Ediyor</h2>
                <p class="text-gray-400 text-lg">Türkiye'nin önde gelen e-ticaret platformları güveniyor</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8 items-center opacity-60">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-300 mb-2">TechStore</div>
                    <div class="text-sm text-gray-400">Elektronik</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-300 mb-2">FashionHub</div>
                    <div class="text-sm text-gray-400">Moda</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-300 mb-2">HomeDecor</div>
                    <div class="text-sm text-gray-400">Ev & Yaşam</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-300 mb-2">BeautyShop</div>
                    <div class="text-sm text-gray-400">Kozmetik</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-300 mb-2">SportZone</div>
                    <div class="text-sm text-gray-400">Spor</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Try It Yourself Section (Decision Stage) -->
    <section id="pricing" class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">
                    <span class="gradient-text">Sizde</span> Deneyin
                </h2>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto mb-8">
                    ConvStateAI'ın gücünü keşfedin ve e-ticaret sitenizi dönüştürün
                </p>
            </div>
            
            <div class="flex justify-center">
                <div class="glass-effect rounded-3xl p-12 max-w-2xl text-center relative overflow-hidden">
                    <!-- Background Effects -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
                    <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
                    
                    <div class="relative z-10">
                        <h3 class="text-2xl font-bold mb-4">ConvStateAI'ı Hemen Deneyin</h3>
                        <p class="text-gray-300 mb-8 text-lg">
                            7 gün ücretsiz deneme ile tüm özelliklerimizi keşfedin. 
                            E-ticaret sitenize 5 dakikada entegre edin ve farkı görün.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-8">
                            <a href="{{ route('subscription.plans') }}" class="px-8 py-4 bg-gradient-to-r from-purple-glow to-neon-purple rounded-xl text-lg font-semibold hover:from-purple-dark hover:to-purple-glow transition-all duration-300 transform hover:scale-105 animate-glow">
                                Sizde Deneyin
                            </a>
                            <button onclick="scrollToDemo()" class="px-8 py-4 glass-effect rounded-xl text-lg font-semibold hover:bg-white hover:text-black transition-all duration-300">
                                Demo İzle
                            </button>
                        </div>
                        
                        <div class="flex flex-wrap items-center justify-center space-x-8 text-sm text-gray-400">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                <span>7 gün ücretsiz</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                <span>5 dakikada kurulum</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                <span>Kredi kartı yok</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

   

    <!-- Testimonials Section -->
    <section class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">
                    Müşterilerimiz <span class="gradient-text">Ne Diyor?</span>
                </h2>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                    ConvStateAI ile müşteri deneyimini dönüştüren e-ticaret sitelerinin deneyimleri
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="glass-effect rounded-2xl p-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-glow to-neon-purple rounded-full flex items-center justify-center text-white font-bold">
                            A
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold">Ahmet Yılmaz</h4>
                            <p class="text-sm text-gray-400">TechStore Kurucusu</p>
                        </div>
                    </div>
                    <p class="text-gray-300 mb-4">
                        "ConvStateAI sayesinde müşteri hizmetlerimiz %300 arttı. Conv State AI'ı ile 7/24 müşteri desteği sağlıyoruz."
                    </p>
                    <div class="flex text-yellow-400">
                        ⭐⭐⭐⭐⭐
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="glass-effect rounded-2xl p-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-glow to-neon-purple rounded-full flex items-center justify-center text-white font-bold">
                            E
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold">Elif Demir</h4>
                            <p class="text-sm text-gray-400">FashionHub CEO</p>
                        </div>
                    </div>
                    <p class="text-gray-300 mb-4">
                        "Kampanya entegrasyonu sayesinde satışlarımız %150 arttı. Müşterilerimiz artık aktif kampanyalarımızı anında öğreniyor."
                    </p>
                    <div class="flex text-yellow-400">
                        ⭐⭐⭐⭐⭐
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="glass-effect rounded-2xl p-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-glow to-neon-purple rounded-full flex items-center justify-center text-white font-bold">
                            M
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold">Mehmet Kaya</h4>
                            <p class="text-sm text-gray-400">HomeDecor Sahibi</p>
                        </div>
                    </div>
                    <p class="text-gray-300 mb-4">
                        "5 dakikada kurulum yaptık ve hemen çalışmaya başladı. Müşteri memnuniyetimiz %200 arttı."
                    </p>
                    <div class="flex text-yellow-400">
                        ⭐⭐⭐⭐⭐
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section (Action Stage) -->
    <section class="py-20 relative">
        <div class="max-w-4xl mx-auto text-center px-4">
            <div class="glass-effect rounded-3xl p-12 relative overflow-hidden">
                <!-- Background Effects -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
                
                <div class="relative z-10">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6">
                        Conv State AI'ını <span class="gradient-text">Bugün</span> Deneyin
                    </h2>
                    <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                        7 gün ücretsiz deneme ile ConvStateAI'ın gücünü keşfedin. 
                        E-ticaret sitenize 5 dakikada entegre edin, satışlarınızı artırın.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-6">
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient-to-r from-purple-glow to-neon-purple rounded-xl text-lg font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105 animate-glow">
                            Ücretsiz Dene
                        </a>
                        <button onclick="scrollToDemo()" class="px-8 py-4 glass-effect rounded-xl text-lg font-semibold hover:bg-white hover:text-black transition-all duration-300">
                            Demo İzle
                        </button>
                    </div>
                    <div class="flex flex-wrap items-center justify-center space-x-8 text-sm text-gray-400">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                            <span>7 gün ücretsiz</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                            <span>5 dakikada kurulum</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                            <span>Kredi kartı yok</span>
                        </div>
                    </div>
                    @guest
                    <p class="text-sm text-gray-400 mt-6">
                        Zaten hesabınız var mı? <a href="{{ route('login') }}" class="text-purple-glow hover:text-neon-purple">Giriş yapın</a>
                    </p>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="py-16 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center mb-4">
                        <img src="{{ asset('imgs/ai-conversion-logo.svg') }}" alt="ConvStateAI Logo" class="w-10 h-10">
                        <span class="ml-3 text-xl font-bold">ConvStateAI</span>
                    </div>
                    <p class="text-gray-400 mb-4">Yapay zeka ile geleceği şekillendiriyoruz.</p>
                    <div class="flex space-x-4">
                         
                        <a href="#" class="text-gray-400 hover:text-purple-glow transition-colors">
                         <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                              <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                          </svg>
                      
                        </a>
                        <a href="#" class="text-gray-400 hover:text-purple-glow transition-colors">
                         <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                              <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                          </svg>
                        </a>
                      
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Ürün</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#features" class="hover:text-purple-glow transition-colors">Özellikler</a></li>
                        <li><a href="#demo" class="hover:text-purple-glow transition-colors">Demo</a></li>
                        <li><a href="#pricing" class="hover:text-purple-glow transition-colors">Fiyatlandırma</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Şirket</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-purple-glow transition-colors">Hakkımızda</a></li>
                        <li><a href="#" class="hover:text-purple-glow transition-colors">Kariyer</a></li>
                        <li><a href="#" class="hover:text-purple-glow transition-colors">Blog</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Destek</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-purple-glow transition-colors">Yardım Merkezi</a></li>
                        <li><a href="#" class="hover:text-purple-glow transition-colors">Dokümantasyon</a></li>
                        <li><a href="#" class="hover:text-purple-glow transition-colors">İletişim</a></li>
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
                    <a href="{{ route('cookies') }}" class="hover:text-purple-glow transition-colors">Çerezler</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Smooth Scrolling -->
    <script>
     document.getElementById('current-year').textContent = new Date().getFullYear();

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all glass-effect elements
        document.querySelectorAll('.glass-effect').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });


        // Demo scroll fonksiyonu
        function scrollToDemo() {
            document.getElementById('demo').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

    </script>
</body>
</html>
