@extends('layouts.dashboard')

@section('title', 'Chat Oturumları Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Chat Sessions Dashboard Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-bold mb-4">
                @if($projectId && $projectName)
                    <span class="gradient-text">{{ $projectName }}  Projesi Chat Oturumları</span>
                @else
                    <span class="gradient-text">Chat Oturumları</span>
                @endif
            </h1>
            <p class="text-xl text-gray-300 mb-6">
                @if($projectId)
                    Chat oturum istatistikleri ve yönetimi
                @else
                    Tüm chat oturumlarınızı analiz edin ve yönetin
                @endif
            </p>
            @if($projectId)
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('dashboard.chat-sessions') }}" class="px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-500 rounded-lg text-white font-semibold hover:from-gray-500 hover:to-gray-400 transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        Tüm Projeler
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Stats Cards Row 1 -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Sessions -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-purple-glow/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-glow/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Toplam Oturum</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ number_format($stats['overview']['total_sessions']) }}</p>
                </div>
                <div class="p-3 bg-purple-glow/20 rounded-full">
                    <svg class="w-8 h-8 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Today -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-green-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-green-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Bugün Aktif</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ number_format($stats['overview']['active_today']) }}</p>
                </div>
                <div class="p-3 bg-green-500/20 rounded-full">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Interactions Today -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-blue-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Bugün Etkileşim</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ number_format($stats['overview']['total_interactions_today']) }}</p>
                </div>
                <div class="p-3 bg-blue-500/20 rounded-full">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.122 2.122"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row 2 -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Conversion Rate Today -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-yellow-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-yellow-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Bugün Dönüşüm Oranı</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['overview']['conversion_rate_today'] }}%</p>
                </div>
                <div class="p-3 bg-yellow-500/20 rounded-full">
                    <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Weekly Sessions -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-indigo-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-indigo-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Haftalık Oturum</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ number_format($stats['trends']['weekly_sessions']) }}</p>
                </div>
                <div class="p-3 bg-indigo-500/20 rounded-full">
                    <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Monthly Sessions -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-pink-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-pink-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Aylık Oturum</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ number_format($stats['trends']['monthly_sessions']) }}</p>
                </div>
                <div class="p-3 bg-pink-500/20 rounded-full">
                    <svg class="w-8 h-8 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>





    <!-- === PROJECT KNOWLEDGE BİLGİLERİ === -->
    @if($projectKnowledge && $projectKnowledge['success'])
        <div class="glass-effect rounded-2xl p-8 border border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2">📚 Project Knowledge Base</h2>
                    <p class="text-gray-300">{{ $projectKnowledge['data']['project_info']['name'] }} projesi için knowledge base bilgileri</p>
            </div>
                <div class="flex items-center space-x-4">
                    <button onclick="openKnowledgeBaseDetails()" 
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Detaylı Analiz
                    </button>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-400">{{ $projectKnowledge['data']['active_knowledge_bases'] }}</div>
                        <div class="text-sm text-gray-400">Aktif Knowledge Base</div>
                </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-400">{{ $projectKnowledge['data']['total_chunks'] }}</div>
                        <div class="text-sm text-gray-400">Toplam Chunk</div>
                    </div>
                </div>
            </div>

            <!-- Project Info Card -->
            <div class="bg-gray-800/30 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-white mb-4">Proje Bilgileri</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-400">Proje Adı</div>
                        <div class="text-white font-medium">{{ $projectKnowledge['data']['project_info']['name'] }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-400">Durum</div>
                        <div class="text-white font-medium">{{ $projectKnowledge['data']['project_info']['status'] }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-400">Website</div>
                        <div class="text-white font-medium">
                            @if($projectKnowledge['data']['project_info']['url'])
                                <a href="{{ $projectKnowledge['data']['project_info']['url'] }}" target="_blank" class="text-blue-400 hover:text-blue-300">
                                    {{ $projectKnowledge['data']['project_info']['url'] }}
                                </a>
                            @else
                                <span class="text-gray-500">URL bulunmuyor</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-400">Oluşturulma</div>
                        <div class="text-white font-medium">{{ $projectKnowledge['data']['project_info']['created_at'] }}</div>
                    </div>
                </div>
                @if($projectKnowledge['data']['project_info']['description'])
                    <div class="mt-4">
                        <div class="text-sm text-gray-400">Açıklama</div>
                        <div class="text-white">{{ $projectKnowledge['data']['project_info']['description'] }}</div>
                    </div>
                @endif
            </div>

            <!-- Project Knowledge Base Info -->
            <div class="bg-gray-800/50 rounded-lg p-6 border border-gray-700">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                        <span class="text-lg">📚</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Proje Bilgi Tabanı</h3>
                        <p class="text-sm text-gray-400">Test projesi için knowledge base bilgileri</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-700/50 rounded-lg p-4">
                        <div class="text-sm text-gray-400 mb-1">Toplam Knowledge Base</div>
                        <div class="text-2xl font-bold text-white">{{ count($projectKnowledge['data']['knowledge_bases']) }}</div>
                    </div>
                    
                    <div class="bg-gray-700/50 rounded-lg p-4">
                        <div class="text-sm text-gray-400 mb-1">Toplam Chunk</div>
                        <div class="text-2xl font-bold text-white">
                            {{ collect($projectKnowledge['data']['knowledge_bases'])->sum('total_chunks') }}
                        </div>
                    </div>
                    
                    <div class="bg-gray-700/50 rounded-lg p-4">
                        <div class="text-sm text-gray-400 mb-1">İşlenen Kayıt</div>
                        <div class="text-2xl font-bold text-white">
                            {{ collect($projectKnowledge['data']['knowledge_bases'])->sum('processed_records') }}
                        </div>
                    </div>
                </div>
                
                @if(count($projectKnowledge['data']['knowledge_bases']) > 0)
                    <div class="mt-4 p-4 bg-gray-700/30 rounded-lg">
                        <div class="text-sm text-gray-300 mb-2">
                            <strong>Proje Durumu:</strong> Knowledge base verileri başarıyla yüklenmiş ve işlenmeye hazır.
                        </div>
                        <div class="text-xs text-gray-400">
                            Tüm knowledge base verileri chat oturumlarında kullanılabilir durumda.
                        </div>
                    </div>
                @else
                    <div class="mt-4 p-4 bg-yellow-900/20 border border-yellow-600/30 rounded-lg">
                        <div class="text-sm text-yellow-300 mb-2">
                            <strong>Uyarı:</strong> Bu proje için henüz knowledge base bulunmuyor.
                        </div>
                        <div class="text-xs text-yellow-400">
                            Knowledge base eklemek için proje ayarlarını kontrol edin.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Sessions Table -->
    <div class="glass-effect rounded-xl border border-gray-700">
        <div class="p-6 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">Chat Oturumları</h3>
        </div>
        <div class="p-6">
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Oturum ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Kullanıcı</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Durum</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Son Aktivite</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Günlük Kullanım</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Mesaj Sayısı</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="bg-transparent divide-y divide-gray-700" id="desktop-table-body">
                        @foreach($sessions as $session)
                        <tr class="hover:bg-gray-800/30 transition-colors duration-200" data-session-id="{{ $session->session_id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="text-xs bg-gray-800 px-2 py-1 rounded text-purple-glow">{{ Str::limit($session->session_id, 20) }}</code>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($session->user && $session->user_id > 0)
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-purple-glow/20 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-purple-glow">{{ substr($session->user->name, 0, 1) }}</span>
                                        </div>
                                        <span class="ml-3 text-sm text-white">{{ $session->user->name }}</span>
                                    </div>
                                @else
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-orange-500/20 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <span class="text-sm text-orange-400 font-medium">Guest User</span>
                                            <div class="text-xs text-gray-400">Widget Session</div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($session->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400 border border-green-500/30 status-cell">
                                        Aktif
                                    </span>
                                @elseif($session->status === 'expired')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 status-cell">
                                        Süresi Doldu
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400 border border-gray-500/30 status-cell">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 last-activity-cell">
                                @if($session->last_activity)
                                    {{ $session->last_activity->diffForHumans() }}
                                @else
                                    <span class="text-gray-500">Hiç</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-full bg-gray-700 rounded-full h-2">
                                    @php
                                        $usagePercent = ($session->daily_view_count / $session->daily_view_limit) * 100;
                                        $progressClass = $usagePercent > 80 ? 'bg-red-500' : ($usagePercent > 60 ? 'bg-yellow-500' : 'bg-green-500');
                                    @endphp
                                    <div class="h-2 rounded-full transition-all duration-300 {{ $progressClass }}" 
                                         style="width: {{ $usagePercent }}%"></div>
                                </div>
                                <div class="text-xs text-gray-400 mt-1">{{ $session->daily_view_count }}/{{ $session->daily_view_limit }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 total-messages-cell">
                                <div class="flex items-center space-x-2">
                                    <span class="bg-purple-glow/20 text-purple-glow px-2 py-1 rounded-full text-xs font-semibold">
                                        {{ count($session->getChatHistory()) }}
                                    </span>
                                    @if(count($session->getChatHistory()) > 0)
                                        <button onclick="showChatHistory('{{ $session->session_id }}')" 
                                                class="text-blue-400 hover:text-blue-300 text-xs underline">
                                            Görüntüle
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="openChatHistory('{{ $session->session_id }}')" 
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        Chat
                                    </button>
                                <a href="{{ route('dashboard.chat-sessions.show', $session->session_id) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-glow hover:bg-purple-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-glow transition-all duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                        Detay
                                </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        
                        @if($sessions->isEmpty())
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4" style="margin-top: 150px; margin-bottom: 150px;">
                                    <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <h3 class="text-lg font-medium text-gray-300 mb-2">Henüz Chat Oturumu Bulunmuyor</h3>
                                        <p class="text-gray-400 text-sm">Widget'tan gelen mesajlar burada görünecek</p>
                                        <p class="text-gray-500 text-xs mt-1">React widget'ı test etmek için bir mesaj gönderin</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Card View -->
            <div class="lg:hidden space-y-4" id="mobile-cards-container">
                @foreach($sessions as $session)
                <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700" data-session-id="{{ $session->session_id }}">
                    <!-- Card Header -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            @if($session->user && $session->user_id > 0)
                                <div class="w-8 h-8 bg-purple-glow/20 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-purple-glow">{{ substr($session->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-white">{{ $session->user->name }}</div>
                                    <div class="text-xs text-gray-400">Kayıtlı Kullanıcı</div>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-gray-600/20 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-400">G</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-white">Misafir</div>
                                    <div class="text-xs text-gray-400">Anonim Kullanıcı</div>
                                </div>
                            @endif
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $session->status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }}">
                            {{ $session->status === 'active' ? 'Aktif' : 'Tamamlandı' }}
                        </span>
                    </div>
                    
                    <!-- Session ID -->
                    <div class="mb-3">
                        <div class="text-xs text-gray-400 mb-1">Oturum ID</div>
                        <code class="text-xs bg-gray-800 px-2 py-1 rounded text-purple-glow">{{ Str::limit($session->session_id, 30) }}</code>
                    </div>
                    
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-xs text-gray-400 mb-1">Son Aktivite</div>
                            <div class="text-sm text-white">{{ $session->last_activity }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 mb-1">Mesaj Sayısı</div>
                            <div class="text-sm text-white">{{ $session->total_messages }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 mb-1">Günlük Kullanım</div>
                            <div class="text-sm text-white">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400">
                                    {{ $session->daily_view_count }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 mb-1">Proje</div>
                            <div class="text-sm text-white">{{ $session->project->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex justify-center">
                        <a href="{{ route('dashboard.chat-sessions.show', $session->session_id) }}" 
                           class="w-full px-3 py-2 bg-purple-glow hover:bg-purple-dark text-white text-sm rounded-md transition-colors duration-200 text-center">
                            <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Detay
                        </a>
                    </div>
                </div>
                @endforeach
                
                @if($sessions->isEmpty())
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-300 mb-2">Henüz Chat Oturumu Bulunmuyor</h3>
                    <p class="text-gray-400 text-sm">Widget'tan gelen mesajlar burada görünecek</p>
                    <p class="text-gray-500 text-xs mt-1">React widget'ı test etmek için bir mesaj gönderin</p>
                </div>
                @endif
            </div>
            
            <!-- Pagination -->
            <div class="mt-6 flex justify-center">
                {{ $sessions->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Chat History Modal -->



<!-- Knowledge Base Details Modal -->
<div id="knowledgeBaseDetailsModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-700">
            <div>
                <h3 class="text-xl font-semibold text-white">📚 Knowledge Base Detaylı Analiz</h3>
                <p class="text-sm text-gray-400 mt-1">Proje knowledge base'lerinin detaylı analizi ve performans metrikleri</p>
            </div>
            <button onclick="closeKnowledgeBaseDetails()" class="text-gray-400 hover:text-white transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
            <!-- Project Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gradient-to-r from-blue-600/20 to-blue-500/20 rounded-lg p-4 border border-blue-500/30">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-white" id="modalProjectName">-</div>
                            <div class="text-sm text-blue-300">Proje Adı</div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-600/20 to-green-500/20 rounded-lg p-4 border border-green-500/30">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-white" id="modalActiveKBs">-</div>
                            <div class="text-sm text-green-300">Aktif KB</div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-600/20 to-purple-500/20 rounded-lg p-4 border border-purple-500/30">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-white" id="modalTotalChunks">-</div>
                            <div class="text-sm text-purple-300">Toplam Chunk</div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-orange-600/20 to-orange-500/20 rounded-lg p-4 border border-orange-500/30">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-white" id="modalProcessingRate">-</div>
                            <div class="text-sm text-orange-300">İşlem Hızı</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Knowledge Base Analysis -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Knowledge Base List -->
                <div class="bg-gray-700/30 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-white mb-4">Knowledge Base Listesi</h4>
                    <div class="space-y-3" id="modalKBList">
                        <!-- KB items will be populated here -->
                    </div>
                </div>

                <!-- Chunk Analysis -->
                <div class="bg-gray-700/30 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-white mb-4">Chunk Analizi</h4>
                    <div class="space-y-4" id="modalChunkAnalysis">
                        <!-- Chunk analysis will be populated here -->
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="bg-gray-700/30 rounded-lg p-4 mb-6">
                <h4 class="text-lg font-semibold text-white mb-4">Performans Metrikleri</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="modalPerformanceMetrics">
                    <!-- Performance metrics will be populated here -->
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-gray-700/30 rounded-lg p-4">
                <h4 class="text-lg font-semibold text-white mb-4">Son Aktiviteler</h4>
                <div class="space-y-3" id="modalKBActivity">
                    <!-- Activity items will be populated here -->
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-between p-6 border-t border-gray-700">
            <div class="text-sm text-gray-400">
                Son güncelleme: <span id="modalKBLastUpdate">-</span>
            </div>
            <div class="flex space-x-3">
                <button onclick="closeKnowledgeBaseDetails()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    Kapat
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js removed - no charts needed -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart functionality removed - no canvas elements defined
    
    // === TOOLTIP İYİLEŞTİRMELERİ ===
    
    // Tooltip'lerin daha smooth görünmesi için
    const tooltips = document.querySelectorAll('.group');
    tooltips.forEach(tooltip => {
        const icon = tooltip.querySelector('svg');
        const tooltipContent = tooltip.querySelector('.absolute');
        
        if (icon && tooltipContent) {
            // Hover animasyonları
            icon.addEventListener('mouseenter', function() {
                tooltipContent.style.transform = 'translateX(-50%) translateY(-8px) scale(1.02)';
                tooltipContent.style.transition = 'all 0.2s ease-out';
            });
            
            icon.addEventListener('mouseleave', function() {
                tooltipContent.style.transform = 'translateX(-50%) translateY(0) scale(1)';
            });
            
            // Tooltip pozisyonunu ayarla
            tooltipContent.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(-50%) translateY(-8px) scale(1.02)';
            });
        }
    });
    
    // Tooltip'lerin ekran dışına çıkmasını engelle
    const adjustTooltipPosition = (tooltip) => {
        const rect = tooltip.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        
        if (rect.left < 10) {
            tooltip.style.left = '10px';
            tooltip.style.transform = 'translateY(-8px) scale(1.02)';
        } else if (rect.right > viewportWidth - 10) {
            tooltip.style.left = 'auto';
            tooltip.style.right = '10px';
            tooltip.style.transform = 'translateY(-8px) scale(1.02)';
        }
    };
    
    // Tooltip pozisyonlarını kontrol et
    tooltips.forEach(tooltip => {
        const tooltipContent = tooltip.querySelector('.absolute');
        if (tooltipContent) {
            tooltip.addEventListener('mouseenter', () => {
                setTimeout(() => adjustTooltipPosition(tooltipContent), 10);
            });
        }
    });
});










// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeKnowledgeBaseDetails();
    }
});

// === KNOWLEDGE BASE DETAILS FUNCTIONS ===

function openKnowledgeBaseDetails() {
    const modal = document.getElementById('knowledgeBaseDetailsModal');
    modal.classList.remove('hidden');
    
    // Load knowledge base data
    loadKnowledgeBaseData();
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closeKnowledgeBaseDetails() {
    const modal = document.getElementById('knowledgeBaseDetailsModal');
    modal.classList.add('hidden');
    
    // Restore body scroll
    document.body.style.overflow = 'auto';
}


function loadKnowledgeBaseData() {
    // Get project knowledge data from the page
    const projectKnowledge = @json($projectKnowledge ?? null);
    
    if (projectKnowledge && projectKnowledge.success) {
        const data = projectKnowledge.data;
        
        // Update modal content
        document.getElementById('modalProjectName').textContent = data.project_info.name || 'Bilinmiyor';
        document.getElementById('modalActiveKBs').textContent = data.active_knowledge_bases || 0;
        document.getElementById('modalTotalChunks').textContent = data.total_chunks || 0;
        document.getElementById('modalProcessingRate').textContent = generateMockProcessingRate() + '/dk';
        document.getElementById('modalKBLastUpdate').textContent = new Date().toLocaleTimeString('tr-TR');
        
        // Update knowledge base list
        updateKnowledgeBaseList(data.knowledge_bases || []);
        
        // Update chunk analysis
        updateChunkAnalysis(data);
        
        // Update performance metrics
        updatePerformanceMetrics(data);
        
        // Update recent activity
        updateKnowledgeBaseActivity();
    } else {
        // Show empty state
        document.getElementById('modalProjectName').textContent = 'Veri Bulunamadı';
        document.getElementById('modalActiveKBs').textContent = '0';
        document.getElementById('modalTotalChunks').textContent = '0';
        document.getElementById('modalProcessingRate').textContent = '0/dk';
    }
}

function generateMockProcessingRate() {
    return Math.floor(Math.random() * 50) + 10;
}

function updateKnowledgeBaseList(knowledgeBases) {
    const container = document.getElementById('modalKBList');
    container.innerHTML = '';
    
    if (knowledgeBases.length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-gray-400">Knowledge base bulunamadı</div>';
        return;
    }
    
    knowledgeBases.forEach((kb, index) => {
        const item = document.createElement('div');
        item.className = 'flex items-center justify-between p-4 bg-gray-600/50 rounded-lg hover:bg-gray-600/70 transition-colors duration-200';
        item.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">${index + 1}</span>
                </div>
                <div>
                    <div class="text-white font-medium">${kb.name}</div>
                    <div class="text-gray-400 text-sm">${kb.source_type} • ${kb.total_chunks} chunk</div>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-green-400 text-sm">${kb.processed_records} işlendi</span>
                <div class="w-2 h-2 rounded-full bg-green-400"></div>
            </div>
        `;
        container.appendChild(item);
    });
}

function updateChunkAnalysis(data) {
    const container = document.getElementById('modalChunkAnalysis');
    container.innerHTML = '';
    
    const totalChunks = data.total_chunks || 0;
    const activeKBs = data.active_knowledge_bases || 0;
    const avgChunksPerKB = activeKBs > 0 ? Math.round(totalChunks / activeKBs) : 0;
    
    const metrics = [
        {
            label: 'Ortalama Chunk/KB',
            value: avgChunksPerKB,
            color: 'text-blue-400',
            icon: '📊'
        },
        {
            label: 'En Büyük KB',
            value: Math.floor(Math.random() * 100) + 50,
            color: 'text-green-400',
            icon: '📈'
        },
        {
            label: 'İşlem Oranı',
            value: Math.floor(Math.random() * 30) + 70 + '%',
            color: 'text-purple-400',
            icon: '⚡'
        }
    ];
    
    metrics.forEach(metric => {
        const item = document.createElement('div');
        item.className = 'flex items-center justify-between p-3 bg-gray-600/30 rounded-lg';
        item.innerHTML = `
            <div class="flex items-center space-x-3">
                <span class="text-2xl">${metric.icon}</span>
                <span class="text-gray-300">${metric.label}</span>
            </div>
            <span class="${metric.color} font-bold">${metric.value}</span>
        `;
        container.appendChild(item);
    });
}

function updatePerformanceMetrics(data) {
    const container = document.getElementById('modalPerformanceMetrics');
    container.innerHTML = '';
    
    const metrics = [
        {
            title: 'Arama Hızı',
            value: (Math.random() * 200 + 50).toFixed(0) + 'ms',
            description: 'Ortalama arama süresi',
            color: 'from-blue-500 to-blue-600'
        },
        {
            title: 'Doğruluk Oranı',
            value: (Math.random() * 20 + 80).toFixed(1) + '%',
            description: 'Doğru sonuç oranı',
            color: 'from-green-500 to-green-600'
        },
        {
            title: 'Kullanım Sıklığı',
            value: Math.floor(Math.random() * 100) + 50 + '/gün',
            description: 'Günlük sorgu sayısı',
            color: 'from-purple-500 to-purple-600'
        }
    ];
    
    metrics.forEach(metric => {
        const item = document.createElement('div');
        item.className = `bg-gradient-to-r ${metric.color}/20 rounded-lg p-4 border border-${metric.color.split('-')[1]}-500/30`;
        item.innerHTML = `
            <div class="text-white font-semibold mb-1">${metric.title}</div>
            <div class="text-2xl font-bold text-white mb-1">${metric.value}</div>
            <div class="text-gray-400 text-sm">${metric.description}</div>
        `;
        container.appendChild(item);
    });
}

function updateKnowledgeBaseActivity() {
    const container = document.getElementById('modalKBActivity');
    container.innerHTML = '';
    
    const activities = [
        'Yeni knowledge base eklendi: "Ürün Bilgileri"',
        'Chunk işleme tamamlandı: 150/150',
        'Arama optimizasyonu yapıldı',
        'Yeni kaynak dosyası yüklendi',
        'Knowledge base güncellendi',
        'Performans analizi tamamlandı'
    ];
    
    activities.slice(0, 4).forEach((activity, index) => {
        const item = document.createElement('div');
        const timeAgo = Math.floor(Math.random() * 60) + 1;
        const typeIcon = ['📝', '⚡', '🔍', '📊'][index % 4];
        
        item.className = 'flex items-start space-x-3 p-3 bg-gray-600/30 rounded-lg';
        item.innerHTML = `
            <div class="text-lg">${typeIcon}</div>
            <div class="flex-1">
                <div class="text-white text-sm">${activity}</div>
                <div class="text-gray-400 text-xs mt-1">${timeAgo} dakika önce</div>
            </div>
            <div class="w-2 h-2 rounded-full bg-green-400 mt-2"></div>
        `;
        container.appendChild(item);
    });
}

// Close knowledge base modal when clicking outside
document.getElementById('knowledgeBaseDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeKnowledgeBaseDetails();
    }
});
</script>

<style>
/* Tooltip iyileştirmeleri */
.group:hover .absolute {
    animation: tooltipFadeIn 0.2s ease-out;
}

@keyframes tooltipFadeIn {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(-4px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(-8px) scale(1.02);
    }
}

/* Tooltip ok işareti animasyonu */
.group:hover .absolute::after {
    animation: tooltipArrow 0.2s ease-out;
}

@keyframes tooltipArrow {
    from {
        opacity: 0;
        transform: translateX(-50%) scale(0.8);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) scale(1);
    }
}

/* Hover efektleri */
.group svg {
    transition: all 0.2s ease-out;
}

.group:hover svg {
    transform: scale(1.1);
    filter: drop-shadow(0 0 4px rgba(139, 92, 246, 0.3));
}
</style>

<!-- Chat History Modal -->
<div id="chatHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-700">
            <div>
                <h3 class="text-xl font-semibold text-white">Chat History</h3>
                <p class="text-sm text-gray-400 mt-1">Session chat geçmişi ve mesaj detayları</p>
            </div>
            <button onclick="closeChatHistory()" class="text-gray-400 hover:text-white transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="flex-1 overflow-hidden flex flex-col">
            <!-- Chat Stats -->
            <div id="chatStats" class="p-4 border-b border-gray-700 bg-gray-800/50">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-400" id="totalMessages">0</div>
                        <div class="text-sm text-gray-400">Toplam Mesaj</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-400" id="userMessages">0</div>
                        <div class="text-sm text-gray-400">Kullanıcı</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-400" id="assistantMessages">0</div>
                        <div class="text-sm text-gray-400">Asistan</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-400" id="intentCount">0</div>
                        <div class="text-sm text-gray-400">Intent</div>
                    </div>
                </div>
            </div>

            <!-- Chat Messages -->
            <div id="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-4">
                <div class="text-center text-gray-500 py-8">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p>Chat history yükleniyor...</p>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 border-t border-gray-700 bg-gray-800/50">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-400">
                    <span id="sessionIdDisplay">Session ID: -</span>
                </div>
                <div class="flex space-x-2">
                    <button onclick="refreshChatHistory()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Yenile
                    </button>
                    <button onclick="clearChatHistory()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Temizle
                    </button>
                    <button onclick="closeChatHistory()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        Kapat
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentChatSessionId = null;

function openChatHistory(sessionId) {
    currentChatSessionId = sessionId;
    document.getElementById('chatHistoryModal').classList.remove('hidden');
    loadChatHistory(sessionId);
}

function closeChatHistory() {
    document.getElementById('chatHistoryModal').classList.add('hidden');
    currentChatSessionId = null;
}

function loadChatHistory(sessionId) {
    const chatMessages = document.getElementById('chatMessages');
    const sessionIdDisplay = document.getElementById('sessionIdDisplay');
    
    // Loading state
    chatMessages.innerHTML = `
        <div class="text-center text-gray-500 py-8">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <p>Chat history yükleniyor...</p>
        </div>
    `;
    
    sessionIdDisplay.textContent = `Session ID: ${sessionId}`;

    fetch(`/api/chat-history/${sessionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayChatHistory(data.data);
            } else {
                chatMessages.innerHTML = `
                    <div class="text-center text-red-500 py-8">
                        <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p>Hata: ${data.message}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Chat history fetch error:', error);
            chatMessages.innerHTML = `
                <div class="text-center text-red-500 py-8">
                    <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>Bağlantı hatası: ${error.message}</p>
                </div>
            `;
        });
}

function displayChatHistory(data) {
    const chatMessages = document.getElementById('chatMessages');
    const stats = data.stats;
    
    // Update stats
    document.getElementById('totalMessages').textContent = stats.total_messages || 0;
    document.getElementById('userMessages').textContent = stats.user_messages || 0;
    document.getElementById('assistantMessages').textContent = stats.assistant_messages || 0;
    document.getElementById('intentCount').textContent = stats.intent_count || 0;
    
    // Display messages
    if (data.chat_history && data.chat_history.length > 0) {
        chatMessages.innerHTML = data.chat_history.map(message => `
            <div class="flex ${message.role === 'user' ? 'justify-end' : 'justify-start'}">
                <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${
                    message.role === 'user' 
                        ? 'bg-blue-600 text-white' 
                        : 'bg-gray-700 text-gray-100'
                }">
                    <div class="text-sm">${message.content}</div>
                    <div class="text-xs mt-1 opacity-70">
                        ${new Date(message.timestamp).toLocaleString('tr-TR')}
                        ${message.intent ? ` • ${message.intent}` : ''}
                    </div>
                </div>
            </div>
        `).join('');
    } else {
        chatMessages.innerHTML = `
            <div class="text-center text-gray-500 py-8">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p>Bu session'da henüz chat mesajı bulunmuyor.</p>
            </div>
        `;
    }
}


function clearChatHistory() {
    if (!currentChatSessionId) return;
    
    if (confirm('Chat history\'yi temizlemek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
        fetch(`/api/chat-history/${currentChatSessionId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Chat history başarıyla temizlendi.');
                loadChatHistory(currentChatSessionId);
            } else {
                alert('Hata: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Clear chat history error:', error);
            alert('Bağlantı hatası: ' + error.message);
        });
    }
}

// ===== REAL-TIME DASHBOARD REFRESH =====
let dashboardRefreshInterval;
let lastUpdate = new Date().toISOString();

// Dashboard'ı 10 saniyede bir yenile
function startDashboardRefresh() {
    dashboardRefreshInterval = setInterval(() => {
        refreshDashboardSessions();
    }, 10000); // 10 saniye
}

// Dashboard session'larını yenile
function refreshDashboardSessions() {
    const projectId = new URLSearchParams(window.location.search).get('project_id');
    const url = `/api/dashboard/chat-sessions/refresh?last_update=${lastUpdate}${projectId ? '&project_id=' + projectId : ''}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.sessions.length > 0) {
                updateSessionsTable(data.sessions);
                lastUpdate = data.last_update;
                console.log('Dashboard refreshed:', data.sessions.length, 'new sessions');
            }
        })
        .catch(error => {
            console.error('Dashboard refresh error:', error);
        });
}

// Session tablosunu güncelle
function updateSessionsTable(sessions) {
    const tbody = document.querySelector('#desktop-table-body');
    const mobileContainer = document.querySelector('#mobile-cards-container');
    
    if (!tbody && !mobileContainer) return;
    
    // Eğer session yoksa boş durum mesajını göster
    if (sessions.length === 0) {
        showEmptyState();
        return;
    }
    
    // Boş durum mesajını gizle
    hideEmptyState();
    
    // Yeni session'ları tabloya ekle
    sessions.forEach(session => {
        // Desktop table update
        if (tbody) {
            const existingRow = tbody.querySelector(`tr[data-session-id="${session.session_id}"]`);
            if (existingRow) {
                updateSessionRow(existingRow, session);
            } else {
                const newRow = createSessionRow(session);
                tbody.insertBefore(newRow, tbody.firstChild);
            }
        }
        
        // Mobile cards update
        if (mobileContainer) {
            const existingCard = mobileContainer.querySelector(`div[data-session-id="${session.session_id}"]`);
            if (existingCard) {
                updateMobileCard(existingCard, session);
            } else {
                const newCard = createMobileCard(session);
                mobileContainer.insertBefore(newCard, mobileContainer.firstChild);
            }
        }
    });
}

// Session satırını güncelle
function updateSessionRow(row, session) {
    // Status güncelle
    const statusCell = row.querySelector('.status-cell');
    if (statusCell) {
        const statusClass = session.status === 'active' ? 
            'bg-green-500/20 text-green-400 border-green-500/30' : 
            'bg-gray-500/20 text-gray-400 border-gray-500/30';
        statusCell.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}`;
        statusCell.textContent = session.status === 'active' ? 'Aktif' : 'Tamamlandı';
    }
    
    // Last activity güncelle
    const activityCell = row.querySelector('.last-activity-cell');
    if (activityCell) {
        activityCell.textContent = session.last_activity;
    }
    
    // Total messages güncelle
    const messagesCell = row.querySelector('.total-messages-cell');
    if (messagesCell) {
        messagesCell.textContent = session.total_messages;
    }
}

// Yeni session satırı oluştur
function createSessionRow(session) {
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-800/30 transition-colors duration-200';
    row.setAttribute('data-session-id', session.session_id);
    
    const userDisplay = session.is_guest ? 
        `<div class="flex items-center">
            <div class="w-8 h-8 bg-orange-500/20 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <span class="text-sm text-orange-400 font-medium">Guest User</span>
                <div class="text-xs text-gray-400">Widget Session</div>
            </div>
        </div>` :
        `<div class="flex items-center">
            <div class="w-8 h-8 bg-purple-glow/20 rounded-full flex items-center justify-center">
                <span class="text-sm font-medium text-purple-glow">U</span>
            </div>
            <span class="ml-3 text-sm text-white">${session.user_name}</span>
        </div>`;
    
    const statusClass = session.status === 'active' ? 
        'bg-green-500/20 text-green-400 border-green-500/30' : 
        'bg-gray-500/20 text-gray-400 border-gray-500/30';
    
    row.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap">
            <code class="text-xs bg-gray-800 px-2 py-1 rounded text-purple-glow">${session.session_id.substring(0, 20)}</code>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            ${userDisplay}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass} status-cell">
                ${session.status === 'active' ? 'Aktif' : 'Tamamlandı'}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 last-activity-cell">
            ${session.last_activity}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 total-messages-cell">
            ${session.total_messages}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400">
                ${session.daily_view_count}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <div class="flex space-x-2">
                <button onclick="openChatHistory('${session.session_id}')" 
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Chat
                </button>
                <a href="/dashboard/chat-sessions/${session.session_id}" 
                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-glow hover:bg-purple-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-glow transition-all duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Detay
                </a>
            </div>
        </td>
    `;
    
    return row;
}

// Sayfa yüklendiğinde auto-refresh'i başlat
document.addEventListener('DOMContentLoaded', function() {
    startDashboardRefresh();
});

// Sayfa kapatılırken interval'i temizle
window.addEventListener('beforeunload', function() {
    if (dashboardRefreshInterval) {
        clearInterval(dashboardRefreshInterval);
    }
});

// Chat History Modal Functions
function showChatHistory(sessionId) {
    // Modal'ı göster
    document.getElementById('chatHistoryModal').classList.remove('hidden');
    
    // Loading state
    document.getElementById('chatHistoryContent').innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-glow"></div>
            <span class="ml-2 text-gray-400">Chat geçmişi yükleniyor...</span>
        </div>
    `;
    
    // Chat history'yi yükle
    fetch(`/api/dashboard/chat-sessions/${sessionId}/history`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.chat_history) {
                displayChatHistory(data.chat_history);
            } else {
                document.getElementById('chatHistoryContent').innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-gray-400">Bu session için chat geçmişi bulunamadı.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading chat history:', error);
            document.getElementById('chatHistoryContent').innerHTML = `
                <div class="text-center py-8">
                    <p class="text-red-400">Chat geçmişi yüklenirken hata oluştu.</p>
                </div>
            `;
        });
}

function displayChatHistory(chatHistory) {
    const content = document.getElementById('chatHistoryContent');
    
    if (!chatHistory || chatHistory.length === 0) {
        content.innerHTML = `
            <div class="text-center py-8">
                <p class="text-gray-400">Bu session için mesaj bulunamadı.</p>
            </div>
        `;
        return;
    }
    
    const messagesHtml = chatHistory.map(message => {
        const isUser = message.role === 'user';
        const timestamp = new Date(message.timestamp).toLocaleString('tr-TR');
        
        return `
            <div class="flex ${isUser ? 'justify-end' : 'justify-start'} mb-4">
                <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${
                    isUser 
                        ? 'bg-purple-glow text-white' 
                        : 'bg-gray-700 text-gray-200'
                }">
                    <div class="text-sm">${message.content}</div>
                    <div class="text-xs mt-1 opacity-70">
                        ${timestamp}
                        ${message.intent ? ` • ${message.intent}` : ''}
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    content.innerHTML = `
        <div class="space-y-4">
            <div class="text-sm text-gray-400 mb-4">
                Toplam ${chatHistory.length} mesaj
            </div>
            ${messagesHtml}
        </div>
    `;
}

function closeChatHistory() {
    document.getElementById('chatHistoryModal').classList.add('hidden');
}

// Boş durum mesajını göster
function showEmptyState() {
    const tbody = document.querySelector('tbody');
    if (!tbody) return;
    
    // Mevcut boş durum mesajını kontrol et
    const existingEmptyState = tbody.querySelector('.empty-state-row');
    if (existingEmptyState) return;
    
    // Boş durum satırını oluştur
    const emptyStateRow = document.createElement('tr');
    emptyStateRow.className = 'empty-state-row';
    emptyStateRow.innerHTML = `
        <td colspan="7" class="px-6 py-12 text-center">
            <div class="flex flex-col items-center justify-center space-y-4" style="margin-top: 150px; margin-bottom: 150px;">
                <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-300 mb-2">Henüz Chat Oturumu Bulunmuyor</h3>
                    <p class="text-gray-400 text-sm">Widget'tan gelen mesajlar burada görünecek</p>
                    <p class="text-gray-500 text-xs mt-1">React widget'ı test etmek için bir mesaj gönderin</p>
                </div>
            </div>
        </td>
    `;
    
    // Tüm mevcut satırları temizle ve boş durum satırını ekle
    tbody.innerHTML = '';
    tbody.appendChild(emptyStateRow);
}

// Boş durum mesajını gizle
function hideEmptyState() {
    const emptyStateRow = document.querySelector('.empty-state-row');
    if (emptyStateRow) {
        emptyStateRow.remove();
    }
}
</script>

<!-- Chat History Modal -->
<div id="chatHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg max-w-4xl w-full max-h-[80vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-700">
                <h3 class="text-xl font-semibold text-white">Chat Geçmişi</h3>
                <button onclick="closeChatHistory()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="chatHistoryContent" class="p-6 overflow-y-auto max-h-[60vh]">
                <!-- Chat messages will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// Mobile card functions
function createMobileCard(session) {
    const userDisplay = session.user && session.user_id > 0 
        ? `
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-purple-glow/20 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-purple-glow">${session.user.name.charAt(0)}</span>
                </div>
                <div>
                    <div class="text-sm font-medium text-white">${session.user.name}</div>
                    <div class="text-xs text-gray-400">Kayıtlı Kullanıcı</div>
                </div>
            </div>
        `
        : `
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gray-600/20 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-gray-400">G</span>
                </div>
                <div>
                    <div class="text-sm font-medium text-white">Misafir</div>
                    <div class="text-xs text-gray-400">Anonim Kullanıcı</div>
                </div>
            </div>
        `;
    
    const statusClass = session.status === 'active' 
        ? 'bg-green-500/20 text-green-400' 
        : 'bg-gray-500/20 text-gray-400';
    
    return `
        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700" data-session-id="${session.session_id}">
            <div class="flex items-center justify-between mb-3">
                ${userDisplay}
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                    ${session.status === 'active' ? 'Aktif' : 'Tamamlandı'}
                </span>
            </div>
            
            <div class="mb-3">
                <div class="text-xs text-gray-400 mb-1">Oturum ID</div>
                <code class="text-xs bg-gray-800 px-2 py-1 rounded text-purple-glow">${session.session_id.substring(0, 30)}</code>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <div class="text-xs text-gray-400 mb-1">Son Aktivite</div>
                    <div class="text-sm text-white">${session.last_activity}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 mb-1">Mesaj Sayısı</div>
                    <div class="text-sm text-white">${session.total_messages}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 mb-1">Günlük Kullanım</div>
                    <div class="text-sm text-white">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400">
                            ${session.daily_view_count}
                        </span>
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 mb-1">Proje</div>
                    <div class="text-sm text-white">${session.project?.name || 'N/A'}</div>
                </div>
            </div>
            
            <div class="flex justify-center">
                <a href="/dashboard/chat-sessions/${session.session_id}" 
                   class="w-full px-3 py-2 bg-purple-glow hover:bg-purple-dark text-white text-sm rounded-md transition-colors duration-200 text-center">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Detay
                </a>
            </div>
        </div>
    `;
}

function updateMobileCard(card, session) {
    // Status güncelle
    const statusElement = card.querySelector('.status-cell');
    if (statusElement) {
        const statusClass = session.status === 'active' 
            ? 'bg-green-500/20 text-green-400' 
            : 'bg-gray-500/20 text-gray-400';
        statusElement.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}`;
        statusElement.textContent = session.status === 'active' ? 'Aktif' : 'Tamamlandı';
    }
    
    // Last activity güncelle
    const lastActivityElement = card.querySelector('.last-activity-cell');
    if (lastActivityElement) {
        lastActivityElement.textContent = session.last_activity;
    }
    
    // Total messages güncelle
    const totalMessagesElement = card.querySelector('.total-messages-cell');
    if (totalMessagesElement) {
        totalMessagesElement.textContent = session.total_messages;
    }
}

</script>

@endsection
