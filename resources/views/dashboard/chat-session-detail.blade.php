@extends('layouts.dashboard')

@section('title', 'Chat Session Detail')

@section('content')
<div class="space-y-6">
    <!-- Chat Session Detail Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-4xl font-bold mb-4">
                        <span class="gradient-text">Tekil Chat Oturumu Detayı</span>
                    </h1>
                    <p class="text-xl text-gray-300">
                        Session ID: {{ Str::limit($session->session_id, 30) }}
                    </p>
                </div>
                
                <a href="{{ route('dashboard.chat-sessions') }}" class="px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 rounded-lg text-white font-semibold hover:from-gray-700 hover:to-gray-800 transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to Sessions</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Session Overview -->
    <div class="glass-effect rounded-xl border border-gray-700">
        <div class="p-6 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">Session Overview</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-700">
                        <span class="text-gray-400 font-medium">Session ID:</span>
                        <code class="text-sm bg-gray-800 px-2 py-1 rounded text-purple-glow">{{ $session->session_id }}</code>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-700">
                        <span class="text-gray-400 font-medium">Status:</span>
                        @if($session->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400 border border-green-500/30">
                                Active
                            </span>
                        @elseif($session->status === 'expired')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">
                                Expired
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400 border border-gray-500/30">
                                {{ ucfirst($session->status) }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-700">
                        <span class="text-gray-400 font-medium">Created:</span>
                        <span class="text-white">{{ $session->created_at->format('M d, Y H:i:s') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-700">
                        <span class="text-gray-400 font-medium">Last Activity:</span>
                        <div class="text-right">
                            @if($session->last_activity)
                                <div class="text-white">{{ $session->last_activity->format('M d, Y H:i:s') }}</div>
                                <div class="text-sm text-gray-400">{{ $session->last_activity->diffForHumans() }}</div>
                            @else
                                <span class="text-gray-500">Never</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-700">
                        <span class="text-gray-400 font-medium">User:</span>
                        <div class="text-right">
                            @if($session->user)
                                <div class="text-white">{{ $session->user->name }}</div>
                                <div class="text-sm text-gray-400">{{ $session->user->email }}</div>
                            @else
                                <span class="text-gray-500">Guest User</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-700">
                        <span class="text-gray-400 font-medium">Daily Usage:</span>
                        <div class="text-right">
                            <div class="w-32 bg-gray-700 rounded-full h-2 mb-1">
                                @php
                                    $usagePercent = ($session->daily_view_count / $session->daily_view_limit) * 100;
                                    $progressClass = $usagePercent > 80 ? 'bg-red-500' : ($usagePercent > 60 ? 'bg-yellow-500' : 'bg-green-500');
                                @endphp
                                <div class="h-2 rounded-full transition-all duration-300 {{ $progressClass }}" 
                                     style="width: {{ $usagePercent }}%"></div>
                            </div>
                            <div class="text-xs text-gray-400">{{ $session->daily_view_count }}/{{ $session->daily_view_limit }}</div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-700">
                        <span class="text-gray-400 font-medium">Session Duration:</span>
                        <span class="text-white">{{ $analytics['session_stats']['session_duration_minutes'] ?? 0 }} minutes</span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-700">
                        <span class="text-gray-400 font-medium">Total Interactions:</span>
                        <span class="text-white">{{ $analytics['session_stats']['total_interactions'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Intent Count -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-purple-glow/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-glow/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Intent Count</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $analytics['session_stats']['intent_count'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-purple-glow/20 rounded-full">
                    <svg class="w-8 h-8 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Product Views -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-green-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-green-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Product Views</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $analytics['session_stats']['product_views'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-green-500/20 rounded-full">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Cart Additions -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-blue-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Cart Additions</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $analytics['session_stats']['cart_additions'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-blue-500/20 rounded-full">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Conversion Rate -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-yellow-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-yellow-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Conversion Rate</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $analytics['session_stats']['conversion_rate'] ?? 0 }}%</p>
                </div>
                <div class="p-3 bg-yellow-500/20 rounded-full">
                    <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Intent Timeline -->
    <div class="glass-effect rounded-xl border border-gray-700">
        <div class="p-6 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">Intent Timeline</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($analytics['intent_analysis']['intent_distribution'] ?? [] as $intentName => $count)
                <div class="flex items-start space-x-4 p-4 bg-gray-800/30 rounded-lg">
                    <div class="w-10 h-10 bg-purple-glow/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-white font-medium">{{ ucfirst($intentName) }}</h4>
                            <span class="text-sm text-gray-400">Count: {{ $count }}</span>
                        </div>
                        <p class="text-gray-300 text-sm mt-1">Intent type: {{ $intentName }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Product Interactions -->
    <div class="glass-effect rounded-xl border border-gray-700">
        <div class="p-6 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">Product Interactions</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Timestamp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Duration</th>
                        </tr>
                    </thead>
                    <tbody class="bg-transparent divide-y divide-gray-700">
                        @forelse($interactions as $interaction)
                        <tr class="hover:bg-gray-800/30 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($interaction['product'] && $interaction['product']['image'])
                                        <img class="w-10 h-10 rounded-lg object-cover" src="{{ $interaction['product']['image'] }}" alt="{{ $interaction['product']['name'] }}">
                                    @else
                                        <div class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-white">
                                            {{ $interaction['product'] ? $interaction['product']['name'] : 'Unknown Product' }}
                                        </div>
                                        <div class="text-sm text-gray-400">
                                            {{ $interaction['product'] && $interaction['product']['category'] ? $interaction['product']['category']['name'] : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($interaction['action'] === 'view')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                        View
                                    </span>
                                @elseif($interaction['action'] === 'add_to_cart')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400 border border-green-500/30">
                                        Add to Cart
                                    </span>
                                @elseif($interaction['action'] === 'purchase')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-500/20 text-purple-400 border border-purple-500/30">
                                        Purchase
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400 border border-gray-500/30">
                                        {{ ucfirst($interaction['action']) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $interaction['timestamp'] ? $interaction['timestamp']->format('M d, H:i:s') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $interaction['duration_seconds'] ?? 0 }}s
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                <div class="flex flex-col items-center space-y-2">
                                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No Product Interactions</p>
                                    <p class="text-sm">This session doesn't have any product interactions yet.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chat History Section -->
    <div class="glass-effect rounded-xl border border-gray-700">
        <div class="p-6 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">Chat History</h3>
        </div>
        <div class="p-6">
            <div id="chatHistoryContainer" class="space-y-4">
                <div class="text-center text-gray-500 py-8">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <p>Chat history yükleniyor...</p>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadChatHistoryForDetail();
});

function loadChatHistoryForDetail() {
    const sessionId = '{{ $session->session_id }}';
    const container = document.getElementById('chatHistoryContainer');
    
    console.log('Loading chat history for session:', sessionId);
    
    fetch(`/api/chat-history/${sessionId}`)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            if (data.success) {
                displayChatHistoryForDetail(data);
            } else {
                container.innerHTML = `
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
            container.innerHTML = `
                <div class="text-center text-red-500 py-8">
                    <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>Bağlantı hatası: ${error.message}</p>
                </div>
            `;
        });
}

function displayChatHistoryForDetail(data) {
    console.log('Displaying chat history:', data);
    
    const container = document.getElementById('chatHistoryContainer');
    if (!container) {
        console.error('chatHistoryContainer element not found');
        return;
    }
    
    const chatHistory = data.chat_history || [];
    console.log('Chat history array:', chatHistory);
    
    // Display messages
    if (chatHistory && chatHistory.length > 0) {
        try {
            container.innerHTML = chatHistory.map(message => {
                console.log('Processing message:', message);
                
                // Mesaj içeriğini belirle
                let messageContent = '';
                if (message.role === 'user') {
                    messageContent = message.content || 'Mesaj içeriği bulunamadı';
                } else if (message.role === 'assistant') {
                    // Asistan mesajları için response_data.message kullan
                    messageContent = message.response_data && message.response_data.message 
                        ? message.response_data.message 
                        : message.content || 'Asistan cevabı bulunamadı';
                } else {
                    messageContent = message.content || 'Bilinmeyen mesaj türü';
                }
                
                // Tarih formatını düzenle
                let messageDate = 'Bilinmiyor';
                if (message.timestamp) {
                    try {
                        messageDate = new Date(message.timestamp).toLocaleString('tr-TR', {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        });
                    } catch (e) {
                        console.error('Date parsing error:', e);
                        messageDate = message.timestamp;
                    }
                }
                
                return `
                    <div class="flex ${message.role === 'user' ? 'justify-end' : 'justify-start'} mb-4">
                        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${
                            message.role === 'user' 
                                ? 'bg-purple-glow text-white' 
                                : 'bg-gray-700 text-gray-100'
                        }">
                            <div class="text-sm">${messageContent}</div>
                            <div class="text-xs mt-1 opacity-70">
                                ${messageDate}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            console.log('Chat history displayed successfully');
        } catch (error) {
            console.error('Error processing chat history:', error);
            container.innerHTML = `
                <div class="text-center text-red-500 py-8">
                    <p>Mesajlar işlenirken hata oluştu: ${error.message}</p>
                </div>
            `;
        }
    } else {
        console.log('No chat history found');
        container.innerHTML = `
            <div class="text-center text-gray-500 py-8">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p>Bu session'da henüz chat mesajı bulunmuyor.</p>
            </div>
        `;
    }
}
</script>

@endsection
