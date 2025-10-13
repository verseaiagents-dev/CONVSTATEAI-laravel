@extends('layouts.admin')

@section('title', 'Admin Dashboard - ConvStateAI')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-bold mb-4">
                Hoş geldin, <span class="gradient-text">{{ $admin->getDisplayName() }}</span>! 👋
            </h1>
            <p class="text-xl text-gray-300 mb-6">
                Admin panelinde sistem yönetimi ve kullanıcı takibi yapabilirsiniz.
            </p>
            
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105">
                    Kullanıcı Yönetimi
                </a>
                <a href="{{ route('admin.analytics') }}" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg text-white font-semibold hover:from-blue-600 hover:to-cyan-600 transition-all duration-300 transform hover:scale-105">
                    Sistem Analytics
                </a>
                <a href="{{ route('admin.payment-settings.index') }}" class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg text-white font-semibold hover:from-green-600 hover:to-emerald-600 transition-all duration-300 transform hover:scale-105">
                    Ödeme Ayarları
                </a>
             
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6">
        <!-- Total Users -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-purple-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Toplam Kullanıcı</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_users'] }}</p>
                </div>
                <div class="p-3 bg-purple-500/20 rounded-full">
                    <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-400">↗️ +12% from last week</span>
            </div>
        </div>

        <!-- Total Campaigns -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-green-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-green-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Toplam Kampanya</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_campaigns'] }}</p>
                </div>
                <div class="p-3 bg-green-500/20 rounded-full">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-400">↗️ +8% from last week</span>
            </div>
        </div>

        <!-- Total FAQs -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-blue-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Toplam SSS</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_faqs'] }}</p>
                </div>
                <div class="p-3 bg-blue-500/20 rounded-full">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-400">↗️ +15% from last week</span>
            </div>
        </div>

        <!-- Total Chat Sessions -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-yellow-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-yellow-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Chat Oturumu</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_chat_sessions'] }}</p>
                </div>
                <div class="p-3 bg-yellow-500/20 rounded-full">
                    <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-400">↗️ +22% from last week</span>
            </div>
        </div>

        <!-- Demo Requests -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-orange-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-orange-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Demo Talepleri</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_demo_requests'] }}</p>
                    @if($stats['pending_demo_requests'] > 0)
                        <p class="text-sm text-orange-400 font-medium">{{ $stats['pending_demo_requests'] }} bekleyen</p>
                    @endif
                </div>
                <div class="p-3 bg-orange-500/20 rounded-full">
                    <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <a href="{{ route('admin.demo-requests.index') }}" class="text-orange-400 hover:text-orange-300 transition-colors">
                    Demo taleplerini görüntüle →
                </a>
            </div>
        </div>

        <!-- Giftbox Users -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-pink-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-pink-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Giftbox Leadleri</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_giftbox_users'] ?? 0 }}</p>
                    @if(isset($stats['recent_giftbox_users']) && $stats['recent_giftbox_users'] > 0)
                        <p class="text-sm text-pink-400 font-medium">{{ $stats['recent_giftbox_users'] }} bu hafta</p>
                    @endif
                </div>
                <div class="p-3 bg-pink-500/20 rounded-full">
                    <svg class="w-8 h-8 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <a href="{{ route('admin.giftbox-data.index') }}" class="text-pink-400 hover:text-pink-300 transition-colors">
                    Lead verilerini görüntüle →
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Users -->
        <div class="glass-effect rounded-xl p-6">
            <h3 class="text-xl font-semibold text-white mb-4">Son Kayıt Olan Kullanıcılar</h3>
            <div class="space-y-3">
                @forelse($stats['recent_users'] as $user)
                <div class="flex items-center space-x-3 p-3 bg-gray-800/30 rounded-lg">
                    <div class="w-10 h-10 bg-purple-500/20 rounded-full flex items-center justify-center">
                        <span class="text-purple-400 font-medium">{{ substr($user->name, 0, 2) }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-medium">{{ $user->name }}</p>
                        <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                    </div>
                    <span class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <p class="text-gray-400 text-center py-4">Henüz kullanıcı yok</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Campaigns -->
        <div class="glass-effect rounded-xl p-6">
            <h3 class="text-xl font-semibold text-white mb-4">Son Kampanyalar</h3>
            <div class="space-y-3">
                @forelse($stats['recent_campaigns'] as $campaign)
                <div class="flex items-center space-x-3 p-3 bg-gray-800/30 rounded-lg">
                    <div class="w-10 h-10 bg-green-500/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-medium">{{ $campaign->title }}</p>
                        <p class="text-gray-400 text-sm">{{ $campaign->description }}</p>
                    </div>
                    <span class="text-xs text-gray-500">{{ $campaign->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <p class="text-gray-400 text-center py-4">Henüz kampanya yok</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Demo Requests -->
        <div class="glass-effect rounded-xl p-6">
            <h3 class="text-xl font-semibold text-white mb-4">Son Demo Talepleri</h3>
            <div class="space-y-3">
                @forelse($stats['recent_demo_requests'] as $demoRequest)
                <div class="flex items-center space-x-3 p-3 bg-gray-800/30 rounded-lg">
                    <div class="w-10 h-10 bg-orange-500/20 rounded-full flex items-center justify-center">
                        <span class="text-orange-400 font-medium">{{ substr($demoRequest->first_name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-medium">{{ $demoRequest->full_name }}</p>
                        <p class="text-gray-400 text-sm">{{ $demoRequest->email }}</p>
                        <div class="flex items-center space-x-2 mt-1">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-500/20 text-yellow-400',
                                    'contacted' => 'bg-blue-500/20 text-blue-400',
                                    'completed' => 'bg-green-500/20 text-green-400',
                                    'cancelled' => 'bg-red-500/20 text-red-400'
                                ];
                                $statusLabels = [
                                    'pending' => 'Bekleyen',
                                    'contacted' => 'İletişim Kuruldu',
                                    'completed' => 'Tamamlandı',
                                    'cancelled' => 'İptal Edildi'
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$demoRequest->status] }}">
                                {{ $statusLabels[$demoRequest->status] }}
                            </span>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500">{{ $demoRequest->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <p class="text-gray-400 text-center py-4">Henüz demo talebi yok</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="glass-effect rounded-2xl p-6">
        <h2 class="text-2xl font-bold mb-6 text-white">Hızlı İşlemler</h2>
        <div class="grid md:grid-cols-3 gap-4">
            <a href="{{ route('admin.users.index') }}" class="p-4 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-purple-glow/20 rounded-lg flex items-center justify-center group-hover:bg-purple-glow/30 transition-all duration-200">
                        <svg class="w-5 h-5 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-medium">Kullanıcı Yönetimi</p>
                        <p class="text-gray-400 text-sm">Kullanıcıları görüntüle ve yönet</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.analytics') }}" class="p-4 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-neon-purple/20 rounded-lg flex items-center justify-center group-hover:bg-neon-purple/30 transition-all duration-200">
                        <svg class="w-5 h-5 text-neon-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-medium">Sistem Analytics</p>
                        <p class="text-gray-400 text-sm">Detaylı sistem istatistikleri</p>
                    </div>
                </div>
            </a>

          
        </div>
    </div>
</div>
@endsection
