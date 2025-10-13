@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-bold mb-4">
                Hoş geldin, <span class="gradient-text">{{ $user->getDisplayName() }}</span>! 👋
            </h1>
            <p class="text-xl text-gray-300 mb-6">
                ConvStateAI dashboard'una hoş geldin. Buradan tüm işlemlerini yönetebilirsin.
            </p>

            @if($user->hasActiveSubscription())
                @php
                    $subscription = $user->activeSubscription;
                    $daysRemaining = $subscription->days_remaining;
                @endphp 
                
                @if($daysRemaining <= 7)
                    <div class="bg-red-500/20 border border-red-500/50 rounded-lg p-4 mb-4">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div>
                                <h3 class="text-lg font-semibold text-red-400">Plan Süresi Dolmak Üzere</h3>
                                <p class="text-red-300">Planınızın süresi {{ $daysRemaining }} gün sonra dolacak.</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('dashboard.subscription.index') }}" class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-200">
                                Planı Yenile
                            </a>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Project Overview -->
        <div class="glass-effect rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-white">Proje Genel Bakış</h3>
                <a href="{{ route('dashboard.projects') }}" class="px-4 py-2 bg-purple-glow hover:bg-purple-dark text-white font-medium rounded-lg transition-all duration-200">
                    Tüm Projeleri Gör
                </a>
            </div>
            
            <!-- Horizontal Scroll Container -->
            <div class="overflow-x-auto pb-4">
                <div class="flex space-x-4 min-w-max">
                    <!-- Proje Ekle Butonu -->
                    <div class="flex-shrink-0">
                        <button onclick="openCreateProjectModal()" class="w-64 h-32 bg-gradient-to-br from-purple-glow/20 to-neon-purple/20 rounded-lg border-2 border-dashed border-purple-500/50 hover:border-purple-500 hover:from-purple-glow/30 hover:to-neon-purple/30 transition-all duration-300 flex flex-col items-center justify-center group">
                            <svg class="w-8 h-8 text-purple-400 group-hover:text-purple-300 transition-colors mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span class="text-purple-400 group-hover:text-purple-300 font-medium">Yeni Proje Ekle</span>
                        </button>
                    </div>
                    
                    <!-- Proje Kartları -->
                    @forelse($projectStats as $project)
                        <div class="flex-shrink-0 w-64 h-32 p-4 bg-gray-800/30 rounded-lg border border-gray-700 hover:border-purple-500/50 transition-all duration-200 cursor-pointer" onclick="window.location.href='{{ route('dashboard.knowledge-base', ['project_id' => $project->id]) }}'">
                            <div class="flex flex-col justify-between h-full">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-white font-medium text-sm truncate">{{ $project->name ?? 'İsimsiz Proje' }}</h4>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ ($project->status ?? '') === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }}">
                                        {{ ($project->status ?? '') === 'active' ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </div>
                                <div class="space-y-1 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">KB:</span>
                                        <span class="text-white">{{ $project->knowledge_bases_count ?? 0 }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Chat:</span>
                                        <span class="text-white">{{ $project->chat_sessions_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex-shrink-0 w-64 h-32 flex items-center justify-center text-center">
                            <div>
                                <p class="text-gray-400 text-sm mb-2">Henüz proje yok</p>
                                <p class="text-gray-500 text-xs">Yeni proje eklemek için sol butona tıklayın</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Total Projects -->
            <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-purple-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Toplam Proje</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_projects'] }}</p>
                    </div>
                    <div class="p-3 bg-purple-500/20 rounded-full">
                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Knowledge Bases -->
            <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-blue-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Knowledge Base</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_knowledge_bases'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-500/20 rounded-full">
                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Chat Sessions -->
            <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-green-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-green-500/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Chat Oturumu</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_chat_sessions'] }}</p>
                    </div>
                    <div class="p-3 bg-green-500/20 rounded-full">
                        <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Intents -->
            <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-yellow-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-yellow-500/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Toplam Intent</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_intents'] }}</p>
                    </div>
                    <div class="p-3 bg-yellow-500/20 rounded-full">
                        <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-pink-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-pink-500/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Toplam Ürün</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_products'] }}</p>
                    </div>
                    <div class="p-3 bg-pink-500/20 rounded-full">
                        <svg class="w-8 h-8 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Interactions -->
            <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Toplam Etkileşim</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_interactions'] }}</p>
                    </div>
                    <div class="p-3 bg-teal-500/20 rounded-full">
                        <svg class="w-8 h-8 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chat Session Trend Chart -->
            <div class="glass-effect rounded-xl p-6">
                <h3 class="text-xl font-semibold text-white mb-4">Son 7 Gün Chat Trendi</h3>
                <div class="h-64 flex items-end justify-between space-x-2">
                    @php
                        $maxCount = !empty($chatTrend) ? max(array_values($chatTrend)) : 1;
                    @endphp
                    @foreach(range(6, 0) as $daysAgo)
                        @php
                            $date = now()->subDays($daysAgo)->format('Y-m-d');
                            $count = $chatTrend[$date] ?? 0;
                            $height = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                        @endphp
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-gradient-to-t from-purple-500/50 to-purple-500/20 rounded-t transition-all duration-300 hover:from-purple-500/70 hover:to-purple-500/40" style="height: {{ $height }}%"></div>
                            <div class="text-xs text-gray-400 mt-2 text-center">
                                <div>{{ $count }}</div>
                                <div class="text-xs">{{ now()->subDays($daysAgo)->format('d/m') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Knowledge Base Status -->
            <div class="glass-effect rounded-xl p-6">
                <h3 class="text-xl font-semibold text-white mb-4">Knowledge Base Durumları</h3>
                <div class="space-y-3">
                    @foreach($kbStatuses as $status => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-300 capitalize">
                                @switch($status)
                                    @case('pending')
                                        Beklemede
                                        @break
                                    @case('processing')
                                        İşleniyor
                                        @break
                                    @case('completed')
                                        Tamamlandı
                                        @break
                                    @case('failed')
                                        Başarısız
                                        @break
                                    @case('mapped')
                                        Eşleştirildi
                                        @break
                                    @default
                                        {{ $status ?: 'Bilinmiyor' }}
                                @endswitch
                            </span>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-gray-700 rounded-full h-2">
                                    @php
                                        $total = !empty($kbStatuses) ? array_sum($kbStatuses) : 0;
                                        $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                    @endphp
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-white font-medium">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                    @if(empty($kbStatuses))
                        <p class="text-gray-400 text-center py-4">Henüz knowledge base yok</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Intents -->
            <div class="glass-effect rounded-xl p-6">
                <h3 class="text-xl font-semibold text-white mb-4">En Çok Kullanılan Intents</h3>
                <div class="space-y-3">
                    @forelse($topIntents as $intent)
                        <div class="flex items-center justify-between p-3 bg-gray-800/30 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-500/20 rounded-full flex items-center justify-center">
                                    <span class="text-blue-400 text-sm font-medium">{{ $loop->iteration }}</span>
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $intent->name ?? 'İsimsiz Intent' }}</p>
                                    <p class="text-gray-400 text-sm">{{ $intent->keywords_count ?? 0 }} anahtar kelime</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">{{ $intent->created_at ? $intent->created_at->diffForHumans() : 'Bilinmiyor' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-center py-4">Henüz intent yok</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Chat Sessions -->
            <div class="glass-effect rounded-xl p-6">
                <h3 class="text-xl font-semibold text-white mb-4">Son Chat Oturumları</h3>
                <div class="space-y-3">
                    @forelse($recentChatSessions as $session)
                        <div class="flex items-center space-x-3 p-3 bg-gray-800/30 rounded-lg">
                            <div class="w-10 h-10 bg-teal-500/20 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-white font-medium">Session ID: {{ $session->session_id ?? 'Bilinmiyor' }}</p>
                                <p class="text-gray-400 text-sm">{{ $session->created_at ? $session->created_at->diffForHumans() : 'Bilinmiyor' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ ($session->status ?? '') === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }}">
                                    {{ ($session->status ?? '') === 'active' ? 'Aktif' : 'Pasif' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-center py-4">Henüz chat oturumu yok</p>
                    @endforelse
                </div>
            </div>
        </div>



        <!-- Quick Actions -->
        <div class="glass-effect rounded-2xl p-6">
            <h2 class="text-2xl font-bold mb-6 text-white">Hızlı İşlemler</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('dashboard.projects') }}" class="p-4 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-glow/20 rounded-lg flex items-center justify-center group-hover:bg-purple-glow/30 transition-all duration-200">
                            <svg class="w-5 h-5 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-medium">Projeler</p>
                            <p class="text-gray-400 text-sm">Projeleri yönet</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('dashboard.knowledge-base') }}" class="p-4 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center group-hover:bg-blue-500/30 transition-all duration-200">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-medium">Knowledge Base</p>
                            <p class="text-gray-400 text-sm">Bilgi tabanını yönet</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('dashboard.chat-sessions') }}" class="p-4 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center group-hover:bg-green-500/30 transition-all duration-200">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-medium">Chat Oturumları</p>
                            <p class="text-gray-400 text-sm">Konuşmaları takip et</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('dashboard.settings') }}" class="p-4 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-neon-purple/20 rounded-lg flex items-center justify-center group-hover:bg-neon-purple/30 transition-all duration-200">
                            <svg class="w-5 h-5 text-neon-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-medium">Ayarlar</p>
                            <p class="text-gray-400 text-sm">Hesap ayarlarını yönet</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
</div>

<!-- Create Project Modal -->
<div id="createProjectModal" class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
    <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 p-6 border border-gray-700 w-96 shadow-2xl rounded-xl glass-effect">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-white">Yeni Proje Oluştur</h3>
            <button onclick="closeCreateProjectModal()" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="createProjectForm" class="space-y-4">
            @csrf
            <div>
                <label for="project_name" class="block text-sm font-medium text-gray-300 mb-2">Proje Adı</label>
                <input type="text" id="project_name" name="name" required class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700">
            </div>
            
            <div>
                <label for="project_description" class="block text-sm font-medium text-gray-300 mb-2">Açıklama</label>
                <textarea id="project_description" name="description" rows="3" class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700 resize-none"></textarea>
            </div>
            
            <div>
                <label for="project_url" class="block text-sm font-medium text-gray-300 mb-2">URL <span class="text-red-400">*</span></label>
                <div class="flex space-x-2">
                    <input type="url" id="project_url" name="url" required placeholder="https://example.com" class="form-input flex-1 px-4 py-3 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700">
                    <button type="button" id="test_url_btn" class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="test_url_text">Test Et</span>
                        <span id="test_url_loading" class="hidden">Test Ediliyor...</span>
                    </button>
                </div>
                <div id="url_test_result" class="mt-2 text-sm hidden"></div>
            </div>
            
            <div>
                <label for="project_status" class="block text-sm font-medium text-gray-300 mb-2">Durum</label>
                <select id="project_status" name="status" required class="form-select w-full px-4 py-3 rounded-lg text-white bg-gray-800/50 border border-gray-700">
                    <option value="active">Aktif</option>
                    <option value="inactive">Pasif</option>
                    <option value="completed">Tamamlandı</option>
                    <option value="archived">Arşivlendi</option>
                </select>
            </div>
            
            <div class="flex items-center space-x-3 pt-4">
                <button type="submit" class="flex-1 px-4 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300">
                    Proje Oluştur
                </button>
                <button type="button" onclick="closeCreateProjectModal()" class="px-4 py-3 bg-gray-600 hover:bg-gray-500 rounded-lg text-white font-semibold transition-colors">
                    İptal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Create Project Modal Functions
function openCreateProjectModal() {
    document.getElementById('createProjectModal').classList.remove('hidden');
}

function closeCreateProjectModal() {
    document.getElementById('createProjectModal').classList.add('hidden');
    document.getElementById('createProjectForm').reset();
}

// Form submission
document.getElementById('createProjectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/dashboard/projects', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCreateProjectModal();
            // Başarılı ekleme sonrası knowledge base sayfasına yönlendir
            window.location.href = `/dashboard/knowledge-base?project_id=${data.project.id}`;
        } else {
            alert('Proje oluşturulurken hata oluştu: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Proje oluşturulurken hata oluştu');
    });
});

// URL Test functionality
function testUrl(url, testBtn, textSpan, loadingSpan, resultDiv) {
    if (!url || url.trim() === '') {
        showUrlTestResult(resultDiv, 'Lütfen bir URL girin.', 'error');
        return;
    }

    // Show loading state
    testBtn.disabled = true;
    textSpan.classList.add('hidden');
    loadingSpan.classList.remove('hidden');

    fetch('/api/test-url', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ url: url })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showUrlTestResult(resultDiv, data.message, 'success');
        } else {
            showUrlTestResult(resultDiv, data.message, 'error');
        }
    })
    .catch(error => {
        showUrlTestResult(resultDiv, 'Test sırasında hata oluştu: ' + error.message, 'error');
    })
    .finally(() => {
        // Reset button state
        testBtn.disabled = false;
        textSpan.classList.remove('hidden');
        loadingSpan.classList.add('hidden');
    });
}

function showUrlTestResult(resultDiv, message, type) {
    resultDiv.textContent = message;
    resultDiv.classList.remove('hidden', 'text-green-400', 'text-red-400');
    
    if (type === 'success') {
        resultDiv.classList.add('text-green-400');
    } else {
        resultDiv.classList.add('text-red-400');
    }
}

// Add event listener for URL test button
document.getElementById('test_url_btn').addEventListener('click', function() {
    const url = document.getElementById('project_url').value;
    const testBtn = this;
    const textSpan = document.getElementById('test_url_text');
    const loadingSpan = document.getElementById('test_url_loading');
    const resultDiv = document.getElementById('url_test_result');
    
    testUrl(url, testBtn, textSpan, loadingSpan, resultDiv);
});

// Close modal when clicking outside
document.getElementById('createProjectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCreateProjectModal();
    }
});
</script>

@endsection
