@extends('layouts.admin')

@section('title', 'Sistem Analytics - Admin Panel')

@section('content')
<style>
@keyframes pulse-glow {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

@keyframes slide-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in-scale {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes shimmer {
    0% { background-position: -200px 0; }
    100% { background-position: calc(200px + 100%) 0; }
}

.slide-in-up {
    animation: slide-in-up 0.4s ease-out;
}

.fade-in-scale {
    animation: fade-in-scale 0.3s ease-out;
}

.shimmer {
    background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
    background-size: 200px 100%;
    animation: shimmer 1.5s infinite;
}
</style>
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden slide-in-up">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-bold mb-4">
                <span class="gradient-text">Sistem Analytics</span> 📊
            </h1>
            <p class="text-xl text-gray-300 mb-6">
                Sistem performansı ve kullanıcı aktivitelerini detaylı olarak takip edin.
            </p>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="glass-effect rounded-2xl p-12 text-center fade-in-scale">
        <div class="space-y-6">
            <!-- Multi-colored spinner -->
            <div class="flex justify-center">
                <div class="relative">
                    <div class="w-16 h-16 border-4 border-purple-glow/30 rounded-full animate-spin"></div>
                    <div class="absolute top-0 left-0 w-16 h-16 border-4 border-transparent border-t-purple-glow rounded-full animate-spin"></div>
                    <div class="absolute top-0 left-0 w-16 h-16 border-4 border-transparent border-r-neon-purple rounded-full animate-spin" style="animation-delay: -0.3s;"></div>
                    <div class="absolute top-0 left-0 w-16 h-16 border-4 border-transparent border-b-blue-400 rounded-full animate-spin" style="animation-delay: -0.6s;"></div>
                </div>
            </div>
            
            <div>
                <h3 class="text-xl font-semibold text-white mb-2">Analytics Verileri Yükleniyor</h3>
                <p class="text-gray-400">Sistem istatistikleri ve performans verileri hazırlanıyor...</p>
            </div>
            
            <!-- Progress bar -->
            <div class="w-full max-w-md mx-auto">
                <div class="w-full bg-gray-700 rounded-full h-2">
                    <div id="progress-bar" class="bg-gradient-to-r from-purple-glow via-neon-purple to-blue-400 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skeleton State -->
    <div id="skeletonState" class="hidden space-y-6">
        <!-- Stats Grid Skeleton -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @for($i = 0; $i < 4; $i++)
            <div class="glass-effect rounded-xl p-6 border border-gray-700 animate-pulse">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 space-y-3">
                            <div class="h-4 bg-gray-700 rounded w-24"></div>
                            <div class="h-8 bg-gray-700 rounded w-16"></div>
                        </div>
                        <div class="w-14 h-14 bg-gray-700 rounded-full"></div>
                    </div>
                    <div class="h-4 bg-gray-700 rounded w-32"></div>
                </div>
            </div>
            @endfor
        </div>

        <!-- Additional Stats Skeleton -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @for($i = 0; $i < 3; $i++)
            <div class="glass-effect rounded-xl p-6 border border-gray-700 animate-pulse">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 space-y-3">
                            <div class="h-4 bg-gray-700 rounded w-20"></div>
                            <div class="h-8 bg-gray-700 rounded w-12"></div>
                        </div>
                        <div class="w-14 h-14 bg-gray-700 rounded-full"></div>
                    </div>
                </div>
            </div>
            @endfor
        </div>

        <!-- Chat Sessions Skeleton -->
        <div class="glass-effect rounded-xl p-6 animate-pulse">
            <div class="h-6 bg-gray-700 rounded w-48 mb-4"></div>
            <div class="space-y-3">
                @for($i = 0; $i < 5; $i++)
                <div class="flex items-center space-x-3 p-3 bg-gray-800/30 rounded-lg">
                    <div class="w-10 h-10 bg-gray-700 rounded-full"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-4 bg-gray-700 rounded w-32"></div>
                        <div class="h-3 bg-gray-700 rounded w-24"></div>
                    </div>
                    <div class="space-y-2">
                        <div class="h-3 bg-gray-700 rounded w-16"></div>
                        <div class="h-3 bg-gray-700 rounded w-12"></div>
                    </div>
                </div>
                @endfor
            </div>
        </div>

        <!-- System Health Skeleton -->
        <div class="glass-effect rounded-xl p-6 animate-pulse">
            <div class="h-6 bg-gray-700 rounded w-32 mb-4"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @for($i = 0; $i < 4; $i++)
                <div class="p-4 bg-gray-800/30 rounded-lg">
                    <div class="space-y-2">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-gray-700 rounded-full"></div>
                            <div class="h-4 bg-gray-700 rounded w-32"></div>
                        </div>
                        <div class="h-3 bg-gray-700 rounded w-24"></div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Content Container (Hidden initially) -->
    <div id="contentContainer" class="hidden space-y-6 slide-in-up">
        <!-- Stats Grid will be populated here -->
        <div id="stats-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Stats will be populated via JavaScript -->
        </div>

        <!-- Additional Stats will be populated here -->
        <div id="additional-stats" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Additional stats will be populated via JavaScript -->
        </div>

        <!-- Recent Chat Sessions will be populated here -->
        <div id="chat-sessions-container" class="glass-effect rounded-xl p-6">
            <!-- Chat sessions will be populated via JavaScript -->
        </div>

        <!-- System Health will be populated here -->
        <div id="system-health-container" class="glass-effect rounded-xl p-6">
            <!-- System health will be populated via JavaScript -->
        </div>
    </div>

    <!-- Error State -->
    <div id="errorState" class="hidden glass-effect rounded-2xl p-8 fade-in-scale mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">Analytics Verileri Yüklenemedi</h3>
                    <p class="text-gray-400 text-sm">Sistem istatistikleri yüklenirken bir hata oluştu.</p>
                </div>
            </div>
            <button 
                onclick="retryLoading()"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded-lg text-white text-sm transition-all duration-200"
            >
                Tekrar Dene
            </button>
        </div>
    </div>

</div>

<script>
// Global variables
let analyticsData = {};
let loadingInterval;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    startLoading();
    loadAnalyticsContent();
});

// Start loading animation
function startLoading() {
    const progressBar = document.getElementById('progress-bar');
    let progress = 0;
    
    loadingInterval = setInterval(() => {
        progress += Math.random() * 20;
        if (progress > 90) progress = 90;
        progressBar.style.width = progress + '%';
    }, 150);
}

// Complete loading
function completeLoading() {
    clearInterval(loadingInterval);
    
    setTimeout(() => {
        document.getElementById('loadingState').classList.add('hidden');
        document.getElementById('skeletonState').classList.remove('hidden');
        
        // Show skeleton for a moment to simulate content loading
        setTimeout(() => {
            document.getElementById('skeletonState').classList.add('hidden');
            document.getElementById('contentContainer').classList.remove('hidden');
            
            // Add fade-in animation
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.style.opacity = '0';
            contentContainer.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                contentContainer.style.transition = 'all 0.3s ease-out';
                contentContainer.style.opacity = '1';
                contentContainer.style.transform = 'translateY(0)';
                
                // Populate content
                populateContent();
            }, 50);
        }, 400); // Show skeleton for 400ms
    }, 500);
}

// Load content from server
async function loadAnalyticsContent() {
    try {
        const url = '{{ route("admin.analytics.load-content") }}';
        
        console.log('Loading analytics from:', url);
        
        // CSRF token'ı al
        let csrfToken = '{{ csrf_token() }}';
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('Response data:', result);
        
        if (result.success) {
            analyticsData = result.data.stats || {};
            console.log('Analytics data loaded successfully');
            completeLoading();
        } else {
            throw new Error(result.message || 'Analytics verileri yüklenemedi');
        }
        
    } catch (error) {
        console.error('Loading error:', error);
        showErrorState();
    }
}

// Populate content with loaded data
function populateContent() {
    populateStatsGrid();
    populateAdditionalStats();
    populateChatSessions();
    populateSystemHealth();
    
    // Add staggered animations to all elements
    const allElements = document.querySelectorAll('#contentContainer > div');
    allElements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            element.style.transition = 'all 0.3s ease-out';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, 100 + (index * 50));
    });
}

// Populate stats grid
function populateStatsGrid() {
    const statsGrid = document.getElementById('stats-grid');
    
    const stats = [
        {
            title: 'Toplam Kullanıcı',
            value: analyticsData.total_users || 0,
            icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
            color: 'purple',
            trend: '+12% from last week'
        },
        {
            title: 'Aktif Kullanıcı',
            value: analyticsData.active_users || 0,
            icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            color: 'green',
            trend: '+8% from last week'
        },
        {
            title: 'Toplam Kampanya',
            value: analyticsData.total_campaigns || 0,
            icon: 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z',
            color: 'blue',
            trend: '+15% from last week'
        },
        {
            title: 'Aktif Kampanya',
            value: analyticsData.active_campaigns || 0,
            icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            color: 'yellow',
            trend: '+22% from last week'
        }
    ];
    
    statsGrid.innerHTML = stats.map(stat => `
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-${stat.color}-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-${stat.color}-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">${stat.title}</p>
                    <p class="mt-2 text-3xl font-bold text-white">${stat.value}</p>
                </div>
                <div class="p-3 bg-${stat.color}-500/20 rounded-full">
                    <svg class="w-8 h-8 text-${stat.color}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${stat.icon}"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-400">↗️ ${stat.trend}</span>
            </div>
        </div>
    `).join('');
}

// Populate additional stats
function populateAdditionalStats() {
    const additionalStats = document.getElementById('additional-stats');
    
    const stats = [
        {
            title: 'Toplam SSS',
            value: analyticsData.total_faqs || 0,
            icon: 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            color: 'indigo'
        },
        {
            title: 'Bilgi Tabanı',
            value: analyticsData.total_knowledge_bases || 0,
            icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            color: 'pink'
        },
        {
            title: 'Chat Oturumu',
            value: analyticsData.total_chat_sessions || 0,
            icon: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
            color: 'teal'
        }
    ];
    
    additionalStats.innerHTML = stats.map(stat => `
        <div class="glass-effect rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">${stat.title}</p>
                    <p class="mt-2 text-3xl font-bold text-white">${stat.value}</p>
                </div>
                <div class="p-3 bg-${stat.color}-500/20 rounded-full">
                    <svg class="w-8 h-8 text-${stat.color}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${stat.icon}"></path>
                    </svg>
                </div>
            </div>
        </div>
    `).join('');
}

// Populate chat sessions
function populateChatSessions() {
    const container = document.getElementById('chat-sessions-container');
    
    if (!analyticsData.recent_chat_sessions || analyticsData.recent_chat_sessions.length === 0) {
        container.innerHTML = `
            <h3 class="text-xl font-semibold text-white mb-4">Son Chat Oturumları</h3>
            <p class="text-gray-400 text-center py-4">Henüz chat oturumu yok</p>
        `;
        return;
    }
    
    container.innerHTML = `
        <h3 class="text-xl font-semibold text-white mb-4">Son Chat Oturumları</h3>
        <div class="space-y-3">
            ${analyticsData.recent_chat_sessions.map(session => `
                <div class="flex items-center space-x-3 p-3 bg-gray-800/30 rounded-lg">
                    <div class="w-10 h-10 bg-teal-500/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-medium">${session.user ? session.user.name : 'Anonim'}</p>
                        <p class="text-gray-400 text-sm">Session ID: ${session.session_id}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">${getTimeAgo(session.created_at)}</p>
                        <p class="text-xs text-gray-400">${new Date(session.created_at).toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'})}</p>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

// Populate system health
function populateSystemHealth() {
    const container = document.getElementById('system-health-container');
    
    const healthItems = [
        { name: 'Veritabanı Bağlantısı', status: 'Aktif ve stabil' },
        { name: 'API Servisleri', status: 'Çalışıyor' },
        { name: 'Cache Sistemi', status: 'Optimize edilmiş' },
        { name: 'Güvenlik', status: 'Güncel ve güvenli' }
    ];
    
    container.innerHTML = `
        <h3 class="text-xl font-semibold text-white mb-4">Sistem Durumu</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            ${healthItems.map(item => `
                <div class="p-4 bg-green-500/10 border border-green-500/30 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                        <span class="text-green-400 font-medium">${item.name}</span>
                    </div>
                    <p class="text-green-300 text-sm mt-2">${item.status}</p>
                </div>
            `).join('')}
        </div>
    `;
}

// Helper function for time ago
function getTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return 'Az önce';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} dakika önce`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} saat önce`;
    return `${Math.floor(diffInSeconds / 86400)} gün önce`;
}

// Show error state
function showErrorState() {
    clearInterval(loadingInterval);
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('errorState').classList.remove('hidden');
}

// Retry loading
function retryLoading() {
    document.getElementById('errorState').classList.add('hidden');
    document.getElementById('loadingState').classList.remove('hidden');
    startLoading();
    loadAnalyticsContent();
}
</script>
@endsection
