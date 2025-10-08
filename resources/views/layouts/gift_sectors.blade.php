<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ConvStateAI - Satış odaklı Müşteri Asistanı')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('imgs/ai-conversion-logo.svg') }}">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('imgs/ai-conversion-logo.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-PYZ8FQ8JV2"></script>
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

<link rel="icon" type="image/x-icon" href="{{ asset('imgs/favicon.ico') }}">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('imgs/favicon.ico') }}">
<link rel="apple-touch-icon" href="{{ asset('imgs/favicon.ico') }}">
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
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .fashion-carousel-container {
                height: 80vh;
                max-height: 400px;
            }
        }
        
        @media (max-width: 640px) {
            .fashion-carousel-container {
                height: 70vh;
                max-height: 350px;
            }
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
                    @yield('hero_title')
                    @yield('hero_subtitle')
                    @yield('hero_buttons')
                </div>
                
                <!-- Right Content - Widget Preview -->
                <div class="relative">
                    @hasSection('hero_widget')
                        @yield('hero_widget')
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Additional Custom Content Section -->
    @yield('custom_content')

    <!-- CTA Section (Action Stage) -->
    <section class="py-20 relative">
        @yield('cta_section')
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


    <!-- Sector-Aware Demo Request Modal -->
    <div id="demoModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-900 rounded-2xl p-8 max-w-2xl w-full glass-effect">
                <div class="flex justify-between items-center mb-6">
                    <p class="text-gray-300 text-center flex-1" id="modalDescription">Yapay zekanın gücüyle gelen ziyaretçi trafiğini müşteriye dönüştürün.</p>
                    <button onclick="closeDemoModal()" class="text-gray-400 hover:text-white transition-colors ml-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="flex gap-6">
                    <!-- Sol taraf - Resim alanı -->
                    <div class="flex-1">
                        <div class="border-2 border-dashed border-gray-500 p-5 rounded-lg bg-gray-800/50 h-full">
                            <img id="sectorImage" src="/imgs/giftpage/fashion/2.png" alt="Sector Image" class="w-full h-full object-cover rounded-lg">
                        </div>
                    </div>
                    
                    <!-- Sağ taraf - Form alanı -->
                    <div class="flex-1">
                        <form id="giftboxform" class="space-y-4">
                            @csrf
                            <input type="hidden" name="sector" id="sectorInput" value="">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Ad</label>
                                    <input type="text" name="name" required class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:border-purple-glow focus:outline-none transition-colors">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Soyad</label>
                                    <input type="text" name="surname" required class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:border-purple-glow focus:outline-none transition-colors">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">E-posta</label>
                                <input type="email" name="mail" required class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:border-purple-glow focus:outline-none transition-colors">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Telefon Numarası</label>
                                <input type="tel" name="phone" class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:border-purple-glow focus:outline-none transition-colors">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Site Ziyaretçi Sayısı (Aylık)</label>
                                <select name="visitors" class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:border-purple-glow focus:outline-none transition-colors">
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
                                Ücretsiz kitapçığı edin
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    // Sector-aware Demo Modal Functions
    let currentSector = '';
    
    function openDemoModal(sector = '') {
        currentSector = sector;
        
        // Set sector-specific content
        const modalDescription = document.getElementById('modalDescription');
        const sectorInput = document.getElementById('sectorInput');
        const sectorImage = document.getElementById('sectorImage');
        
        if (sector) {
            const sectorNames = {
                'fashion': 'Moda',
                'furniture': 'Mobilya', 
                'home-appliances': 'Ev Aletleri',
                'health-beauty': 'Sağlık & Güzellik',
                'electronics': 'Elektronik'
            };
            
            const sectorImages = {
                'fashion': '/imgs/giftpage/fashion/2.png',
                'furniture': '/imgs/giftpage/furniture/2.png',
                'home-appliances': '/imgs/giftpage/homeappliances/2.png',
                'health-beauty': '/imgs/giftpage/healthbeauty/2.png',
                'electronics': '/imgs/giftpage/electronics/2.png'
            };
            
            const sectorName = sectorNames[sector] || sector;
            modalDescription.textContent = `${sectorName} sektöründe dijital mağazalarınızı büyütme sırlarını keşfedin`;
            sectorInput.value = sector;
            sectorImage.src = sectorImages[sector] || '/imgs/giftpage/fashion/2.png';
        } else {
            modalDescription.textContent = 'Yapay zekanın gücüyle gelen ziyaretçi trafiğini müşteriye dönüştürün.';
            sectorInput.value = '';
            sectorImage.src = '/imgs/giftpage/fashion/2.png';
        }
        
        document.getElementById('demoModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeDemoModal() {
        document.getElementById('demoModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        resetDemoForm();
        currentSector = '';
    }
    
    function resetDemoForm() {
        document.getElementById('giftboxform').reset();
        document.getElementById('demoFormMessage').classList.add('hidden');
    }
    
    // Demo Form Submission
    document.getElementById('giftboxform').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const messageDiv = document.getElementById('demoFormMessage');
        const submitBtn = this.querySelector('button[type="submit"]');
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.textContent = 'Gönderiliyor...';
        
        try {
            // Determine the correct route based on sector
            let routeUrl = ''; // Default fallback
            
            if (currentSector) {
                const routeNames = {
                    'fashion': '{{ route("gift-data.fashion-sector.store") }}',
                    'furniture': '{{ route("gift-data.furniture-sector.store") }}',
                    'home-appliances': '{{ route("gift-data.home-appliances-sector.store") }}',
                    'health-beauty': '{{ route("gift-data.health-beauty-sector.store") }}',
                    'electronics': '{{ route("gift-data.electronics-sector.store") }}'
                };
                routeUrl = routeNames[currentSector] || routeUrl;
            }
            
            const response = await fetch(routeUrl, {
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
            submitBtn.textContent = 'Ücretsiz kitapçığı edin';
        }
    });
    
    // Close modal when clicking outside
    document.getElementById('demoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDemoModal();
        }
    });

</script>

<script src="http://127.0.0.1:8000/embed/convstateai.min.js">

</script>
<script>
    window.convstateaiConfig = {
        projectId: "1",
        customizationToken: "52f701bdbf9d376d508c7e2ea92f2a72e1d5d907bc4b90041d57a57a8a3a3887"
    };
</script>
</body>
</html>
