<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ConvStateAI - Satış odaklı Müşteri Asistanı</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('imgs/ai-conversion-logo.svg') }}">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('imgs/ai-conversion-logo.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '24557115803931045');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=24557115803931045&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-PYZ8FQ8JV2"></script>


</script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-PYZ8FQ8JV2');
</script>

</script>
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
        
        /* Landing Page Chat Bubble Styles */
        .landing-notification-widget {
            position: absolute;
            bottom: 20px;
            right: 80px; /* Logo için daha fazla alan bırak */
            z-index: 1000;
            animation: slideInFromRight 0.5s ease-out;
        }
        
        .landing-notification-widget-slide-in {
            animation: slideInFromRight 0.5s ease-out;
        }
        
        .landing-notification-speech-bubble {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            max-width: 280px;
            position: relative;
            overflow: hidden;
        }
        
        .landing-notification-content {
            padding: 12px 16px;
            position: relative;
        }
        
        .landing-notification-header {
            margin-bottom: 4px;
        }
        
        .landing-notification-sender {
            font-size: 12px;
            font-weight: 600;
            color: #8B5CF6;
            background: linear-gradient(135deg, #8B5CF6, #A855F7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .landing-notification-message {
            font-size: 14px;
            color: #374151;
            line-height: 1.4;
        }
        
        .landing-notification-close-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background: none;
            border: none;
            font-size: 18px;
            color: #9CA3AF;
            cursor: pointer;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        
        .landing-notification-close-btn:hover {
            background: #F3F4F6;
            color: #6B7280;
        }
        
        /* Speech bubble tail */
        .landing-notification-speech-bubble::after {
            content: '';
            position: absolute;
            bottom: -8px;
            right: 20px;
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-top: 8px solid white;
        }
        
        @keyframes slideInFromRight {
            0% {
                transform: translateX(100%);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
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
                        <span class="ml-3 text-xl font-bold ">ConvState AI</span>
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
            <h2 class="text-5xl sm:text-5xl md:text-7xl font-bold mb-6">
                        <span class="gradient-text">ConvState AI</span><div class="h-3 sm:h-5"></div>  
                        ile Müşteri Sayını
                        Çoğalt
            </h2>
                    <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-2xl">
                        E-ticaret sitenize entegre edin, müşterilerinizle 7/24 akıllı sohbet edin. 
                        Kampanyalar, ürün önerileri ve anında destek ile satışlarınızı artırın.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start items-center mb-8">
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient-to-r from-purple-glow to-neon-purple rounded-xl text-lg font-semibold hover:from-purple-dark hover:to-purple-glow transition-all duration-300 transform hover:scale-105 animate-glow inline-block">
                            Hemen Başla
                        </a>
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
                                <div class="absolute bottom-4 right-4 w-16 h-16  rounded-full flex items-center justify-center animate-pulse">
                                    <img src="{{ asset('imgs/ai-conversion-logo.svg') }}" alt="ConvStateAI" class="w-16 h-16">
                                </div>
                                
                                <!-- Chat Bubble -->
                                <div class="landing-notification-widget landing-notification-widget-slide-in">
                                    <div class="landing-notification-speech-bubble">
                                        <div class="landing-notification-content">
                                            <div class="landing-notification-header">
                                                <span class="landing-notification-sender">ConvState AI</span>
                                            </div>
                                            <div class="landing-notification-message">Sizin için ürün seçebilirim 🛍️</div>
                                        </div>
                                        <button class="landing-notification-close-btn" title="Kapat">×</button>
                                    </div>
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
                    E-ticaret sitenize güçlü ConvState AI'ı entegre edin, müşteri deneyimini dönüştürün
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
                    <span class="gradient-text">E-ticaret sitenize entegre edin </span>
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
                                
                                <!-- Chat Messages - Removed for cleaner demo -->
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
                <h2 class="text-3xl font-bold mb-4">Yüzlerce E-ticaret Sitesi Bizi Tercih Ediyor</h2>
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


    <!-- Testimonials Section -->
    <section class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            
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
                        "ConvStateAI sayesinde müşteri hizmetlerimiz %300 arttı. ConvState AI'ı ile 7/24 müşteri desteği sağlıyoruz."
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
                        <span class="gradient-text"> Sizde Kullanın</span> 
                    </h2>
                    <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                         ConvStateAI'ın gücünü keşfedin ve e-ticaret sitenizi dönüştürün
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-6">
                         <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient-to-r from-purple-glow to-neon-purple rounded-xl text-lg font-semibold hover:from-purple-dark hover:to-purple-glow transition-all duration-300 transform hover:scale-105 animate-glow inline-block">
                              Hemen Başla
                          </a>
                    </div>

               
                 {{--    <div class="flex flex-wrap items-center justify-center space-x-8 text-sm text-gray-400">
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
                    </div>---}}
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

                        <li><a href="#pricing" class="hover:text-purple-glow transition-colors">Fiyatlandırma</a></li>
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
                          <!-- Address Information -->
                <div class="mt-10 text-center md:text-right">
                    <div class="inline-block text-sm text-gray-400">
                        <div class="flex items-center justify-center md:justify-end mb-1">
                            <svg class="w-4 h-4 mr-2 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                       
                        <p class="text-gray-400 mb-1">Osmaniye Mah. Sevgi Sokak No:5 Alpu / Eskişehir</p>
                        </div> <p class="text-gray-400">
                            <svg class="w-4 h-4 inline mr-1 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            +90 545 852 76 93
                        </p>
                    </div>
                </div>
                </div>
                
            </div>
            
            <div class="border-t border-gray-800 pt-8">
                <!-- Copyright and Links -->
                <div class="flex flex-col md:flex-row justify-between items-center mb-6">
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

        // Landing page chat bubble close functionality
        document.addEventListener('DOMContentLoaded', function() {
            const closeButtons = document.querySelectorAll('.landing-notification-close-btn');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const widget = this.closest('.landing-notification-widget');
                    if (widget) {
                        widget.style.animation = 'slideOutToRight 0.3s ease-in forwards';
                        setTimeout(() => {
                            widget.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });

        // Add slide out animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideOutToRight {
                0% {
                    transform: translateX(0);
                    opacity: 1;
                }
                100% {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

    </script>


    <!-- Demo Request Modal -->
    <div id="demoModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-900 rounded-2xl p-8 max-w-md w-full glass-effect">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold gradient-text">Demo Talebi Oluştur</h3>
                    <button onclick="closeDemoModal()" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-gray-300 mb-4 text-center">Yapay zekanın gücüyle gelen ziyaretçi trafiğini müşteriye dönüştürün.</p>
                <form id="demoForm" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Ad</label>
                            <input type="text" name="first_name" required class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:border-purple-glow focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Soyad</label>
                            <input type="text" name="last_name" required class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:border-purple-glow focus:outline-none transition-colors">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">E-posta</label>
                        <input type="email" name="email" required class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:border-purple-glow focus:outline-none transition-colors">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Şifre</label>
                        <input type="password" name="password" required minlength="8" class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:border-purple-glow focus:outline-none transition-colors">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Telefon Numarası</label>
                        <input type="tel" name="phone" class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:border-purple-glow focus:outline-none transition-colors">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Site Ziyaretçi Sayısı (Aylık)</label>
                        <select name="site_visitor_count" class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:border-purple-glow focus:outline-none transition-colors">
                            <option value="">Seçiniz</option>
                            <option value="1000">1.000'den az</option>
                            <option value="5000">1.000 - 5.000</option>
                            <option value="10000">5.000 - 10.000</option>
                            <option value="25000">10.000 - 25.000</option>
                            <option value="50000">25.000 - 50.000</option>
                            <option value="100000">50.000 - 100.000</option>
                            <option value="250000">100.000'den fazla</option>
                        </select>
                    </div>
                    
                    <div id="demoFormMessage" class="hidden p-4 rounded-lg mb-4"></div>
                    
                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg font-semibold text-white hover:from-purple-dark hover:to-purple-glow transition-all duration-300 transform hover:scale-105">
                        Demo Talebi Gönder
                    </button>
                </form>
            </div>
        </div>
    </div>

<script>
    // Demo Modal Functions
    function openDemoModal() {
        document.getElementById('demoModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeDemoModal() {
        document.getElementById('demoModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        resetDemoForm();
    }
    
    function resetDemoForm() {
        document.getElementById('demoForm').reset();
        document.getElementById('demoFormMessage').classList.add('hidden');
    }
    
    // Demo Form Submission
    document.getElementById('demoForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const messageDiv = document.getElementById('demoFormMessage');
        const submitBtn = this.querySelector('button[type="submit"]');
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.textContent = 'Gönderiliyor...';
        
        try {
            const response = await fetch('{{ route("demo-request.storee") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                messageDiv.className = 'p-4 rounded-lg mb-4 bg-green-900/20 border border-green-500/30 text-green-300';
                messageDiv.textContent = data.message;
                messageDiv.classList.remove('hidden');
                
                // Reset form after success
                setTimeout(() => {
                    resetDemoForm();
                    closeDemoModal();
                }, 2000);
            } else {
                messageDiv.className = 'p-4 rounded-lg mb-4 bg-red-900/20 border border-red-500/30 text-red-300';
                messageDiv.textContent = data.message;
                messageDiv.classList.remove('hidden');
            }
        } catch (error) {
            messageDiv.className = 'p-4 rounded-lg mb-4 bg-red-900/20 border border-red-500/30 text-red-300';
            messageDiv.textContent = 'Bir hata oluştu. Lütfen tekrar deneyin.';
            messageDiv.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Demo Talebi Gönder';
        }
    });
    
    // Close modal when clicking outside
    document.getElementById('demoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDemoModal();
        }
    });

 
</script>
<script src="https://convstateai.com/embed/convstateai.min.js"></script>
<script>
window.convstateaiConfig = {
 projectId: "1",
 customizationToken: "7ad7f50cafe54fa113a60425fd7abeef257b6194ef18e65d71db0e65d40d2dea"
};
</script>
</body>
</html>
