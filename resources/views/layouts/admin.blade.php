<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - ConvStateAI')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('imgs/favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('imgs/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('imgs/favicon.ico') }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <style>
        .glass-effect {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(75, 85, 99, 0.3);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #8B5CF6, #EC4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .purple-glow {
            color: #8B5CF6;
        }
        
        .neon-purple {
            color: #A855F7;
        }
        
        .purple-dark {
            color: #7C3AED;
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .form-input {
            background: rgba(31, 41, 55, 0.5);
            border: 1px solid rgba(75, 85, 99, 0.5);
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
        
        .form-checkbox {
            background: rgba(31, 41, 55, 0.5);
            border: 1px solid rgba(75, 85, 99, 0.5);
            border-radius: 0.375rem;
        }
        
        .form-checkbox:checked {
            background: #8B5CF6;
            border-color: #8B5CF6;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-black text-white h-full">
    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900/95 backdrop-blur-xl border-r border-gray-800 transform transition-transform duration-300 ease-in-out md:translate-x-0 closed -translate-x-full">
        <div class="flex flex-col h-full">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-800">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('imgs/ai-conversion-logo.svg') }}" alt="ConvStateAI Logo" class="w-8 h-8">
                    <span class="text-xl font-bold gradient-text">Admin Panel</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-purple-glow/10 hover:text-purple-glow transition-all duration-200 {{ request()->routeIs('admin.dashboard*') ? 'bg-purple-glow/20 text-purple-glow' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.analytics') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-purple-glow/10 hover:text-purple-glow transition-all duration-200 {{ request()->routeIs('admin.analytics*') ? 'bg-purple-glow/20 text-purple-glow' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Analytics</span>
                </a>

                <a href="{{ route('admin.plans.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-purple-glow/10 hover:text-purple-glow transition-all duration-200 {{ request()->routeIs('admin.plans*') ? 'bg-purple-glow/20 text-purple-glow' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Planlar</span>
                </a>


                <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-purple-glow/10 hover:text-purple-glow transition-all duration-200 {{ request()->routeIs('admin.users*') ? 'bg-purple-glow/20 text-purple-glow' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <span>Kullanıcı Yönetimi</span>
                </a>

                <a href="{{ route('admin.mail-templates.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-purple-glow/10 hover:text-purple-glow transition-all duration-200 {{ request()->routeIs('admin.mail-templates*') ? 'bg-purple-glow/20 text-purple-glow' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>Mail Template'ler</span>
                </a>

                <a href="{{ route('admin.api-settings.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-purple-glow/10 hover:text-purple-glow transition-all duration-200 {{ request()->routeIs('admin.api-settings*') ? 'bg-purple-glow/20 text-purple-glow' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>API Ayarları</span>
                </a>

                <a href="{{ route('admin.payment-settings.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-purple-glow/10 hover:text-purple-glow transition-all duration-200 {{ request()->routeIs('admin.payment-settings*') ? 'bg-purple-glow/20 text-purple-glow' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span>Ödeme Ayarları</span>
                </a>

                <a href="{{ route('admin.knowledge-base-settings') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-purple-glow/10 hover:text-purple-glow transition-all duration-200 {{ request()->routeIs('admin.knowledge-base-settings*') ? 'bg-purple-glow/20 text-purple-glow' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span>Bilgi Tabanı Ayarları</span>
                </a>

                <a href="{{ route('dashboard.chat-sessions') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-purple-glow/10 hover:text-purple-glow transition-all duration-200 {{ request()->routeIs('dashboard.chat-sessions*') ? 'bg-purple-glow/20 text-purple-glow' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span>Chat Oturumları</span>
                </a>

                <a href="{{ route('dashboard.widget-design') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-purple-glow/10 hover:text-purple-glow transition-all duration-200 {{ request()->routeIs('dashboard.widget-design*') ? 'bg-purple-glow/20 text-purple-glow' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                    </svg>
                    <span>Widget Tasarımı</span>
                </a>

                <a href="{{ route('admin.demo-requests.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-purple-glow/10 hover:text-purple-glow transition-all duration-200 {{ request()->routeIs('admin.demo-requests*') ? 'bg-purple-glow/20 text-purple-glow' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span>Demo Talepleri</span>
                </a>

                <a href="{{ route('admin.giftbox-data.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-purple-glow/10 hover:text-purple-glow transition-all duration-200 {{ request()->routeIs('admin.giftbox-data*') ? 'bg-purple-glow/20 text-purple-glow' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                    <span>🎁 Giftbox Leadleri</span>
                </a>

                <!-- Kullanıcı Paneline Geç -->
                <div class="pt-4 border-t border-gray-800">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-blue-500/10 hover:text-blue-400 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Kullanıcı Paneline Geç</span>
                    </a>
                </div>
            </nav>

            <!-- Close Button for Mobile -->
            <button id="close-sidebar" class="md:hidden absolute top-4 right-4 text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Logout -->
        <div class="p-6 border-t border-gray-800 flex-shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-red-500/10 hover:text-red-400 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Çıkış Yap</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="md:ml-64 min-h-screen">
        <!-- Top Bar -->
        <div class="bg-gray-900/95 backdrop-blur-xl border-b border-gray-800 p-4">
            <div class="flex items-center justify-between">
                <button id="open-sidebar" class="md:hidden text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-300 hover:text-purple-glow transition-colors">Admin Panel</a>
                    <a href="{{ route('dashboard') }}" class="text-blue-400 hover:text-blue-300 transition-colors">Kullanıcı Paneline Geç</a>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <main class="p-6 overflow-y-auto">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-40 md:hidden hidden"></div>

    <script>
        // Sidebar toggle for mobile
        const sidebar = document.getElementById('sidebar');
        const openSidebarBtn = document.getElementById('open-sidebar');
        const closeSidebarBtn = document.getElementById('close-sidebar');
        const mobileOverlay = document.getElementById('mobile-overlay');

        function openSidebar() {
            sidebar.classList.remove('closed');
            mobileOverlay.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('closed');
            mobileOverlay.classList.add('hidden');
        }

        openSidebarBtn.addEventListener('click', openSidebar);
        closeSidebarBtn.addEventListener('click', closeSidebar);
        mobileOverlay.addEventListener('click', closeSidebar);

        // Close sidebar on window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('closed');
                mobileOverlay.classList.add('hidden');
            } else {
                sidebar.classList.add('closed');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
