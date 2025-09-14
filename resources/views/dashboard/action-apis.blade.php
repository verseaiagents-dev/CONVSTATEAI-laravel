@extends('layouts.dashboard')

@section('title', 'Eylem API\'leri')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-4">
                        <span class="gradient-text">Eylem API'leri</span> ⚡
                    </h1>
                    <p class="text-xl text-gray-300 mb-6">
                        Chatbot'unuzda kullanılabilecek eylem API'lerini yönetin
                    </p>
                </div>
                <div>
            
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Sessions -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-purple-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Toplam Session</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ number_format($stats['total_sessions']) }}</p>
                </div>
                <div class="p-3 bg-blue-500/20 rounded-full">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Intents -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-purple-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Aktif Intent</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_intents'] }}</p>
                </div>
                <div class="p-3 bg-green-500/20 rounded-full">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- API Endpoints -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-purple-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">API Endpoint</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $userEndpoints->count() }}</p>
                </div>
                <div class="p-3 bg-purple-500/20 rounded-full">
                    <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Last 7 Days -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-purple-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Son 7 Gün</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $recentUsage->sum('count') }}</p>
                </div>
                <div class="p-3 bg-yellow-500/20 rounded-full">
                    <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- User Endpoints Management -->
    <div class="glass-effect rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-white">Endpoint Yönetimi</h3>
            <button class="px-4 py-2 bg-purple-glow hover:bg-purple-dark text-white font-medium rounded-lg transition-all duration-200" onclick="openEndpointModal()">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Yeni Endpoint Ekle
            </button>
        </div>
        
        <div class="space-y-4">
            @foreach($intentTypes as $intentKey => $intentName)
                @php
                    $endpoint = $userEndpoints->get($intentKey);
                @endphp
                <div class="p-4 bg-gray-800/30 rounded-lg border border-gray-700 hover:border-purple-500/50 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="p-2 rounded-lg bg-gray-700/50">
                                @if($intentKey === 'order-tracking')
                                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                @elseif($intentKey === 'cargo-tracking')
                                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                @elseif($intentKey === 'add-to-cart')
                                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h4 class="text-white font-medium">{{ $intentName }}</h4>
                                @if($endpoint)
                                    <p class="text-gray-400 text-sm">{{ $endpoint->description ?: 'Endpoint tanımlanmış' }}</p>
                                    <div class="flex items-center space-x-4 mt-2">
                                        <code class="text-xs bg-gray-700 px-2 py-1 rounded text-purple-300">{{ $endpoint->endpoint_url }}</code>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $endpoint->method === 'POST' ? 'bg-green-500/20 text-green-400' : 'bg-blue-500/20 text-blue-400' }}">
                                            {{ $endpoint->method }}
                                        </span>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $endpoint->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                            {{ $endpoint->is_active ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    </div>
                                @else
                                    <p class="text-gray-400 text-sm">Henüz endpoint tanımlanmamış</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($endpoint)
                                <button class="p-2 text-gray-400 hover:text-blue-400 transition-colors" 
                                        onclick="editEndpoint({{ $endpoint->id }})"
                                        title="Düzenle">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button class="p-2 text-gray-400 hover:text-green-400 transition-colors" 
                                        onclick="testUserEndpoint({{ $endpoint->id }})"
                                        title="Test Et">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <button class="p-2 text-gray-400 hover:text-red-400 transition-colors" 
                                        onclick="deleteEndpoint({{ $endpoint->id }})"
                                        title="Sil">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            @else
                                <button class="p-2 text-gray-400 hover:text-purple-400 transition-colors" 
                                        onclick="addEndpoint('{{ $intentKey }}')"
                                        title="Endpoint Ekle">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Usage -->
    <div class="glass-effect rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-white">Son Kullanılan API'ler</h3>
            <div class="text-sm text-gray-400">
                Son 7 gün
            </div>
        </div>
        
        @if($recentUsage->count() > 0)
            <div class="space-y-3">
                @foreach($recentUsage as $intent => $usage)
                <div class="flex items-center justify-between p-3 bg-gray-800/30 rounded-lg border border-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 rounded-lg bg-purple-500/20">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-purple-500/20 text-purple-400">
                                {{ ucfirst(str_replace('_', ' ', $intent)) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 text-sm text-gray-400">
                        <span>{{ $usage['count'] }} kullanım</span>
                        <span>{{ $usage['last_used']->diffForHumans() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="p-4 rounded-full bg-gray-700/50 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h5 class="text-xl font-semibold text-gray-300 mb-2">Henüz API kullanımı yok</h5>
                <p class="text-gray-400 mb-6">API'lerinizi test etmeye başlayın!</p>

            </div>
        @endif
    </div>
</div>

<!-- Endpoint Modal -->
<div id="endpointModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="endpointModalLabel" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        
        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-700">
            <!-- Modal header -->
            <div class="bg-gray-800 px-6 py-4 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-white flex items-center" id="endpointModalLabel">
                        <svg class="w-6 h-6 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span id="modalTitle">Endpoint Ekle</span>
                    </h3>
                    <button type="button" class="text-gray-400 hover:text-white focus:outline-none focus:text-white" onclick="closeModal()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal body -->
            <div class="bg-gray-800 px-6 py-4">
                <form id="endpointForm">
                    <input type="hidden" id="endpointId" name="id">
                    <input type="hidden" id="endpointIntentType" name="intent_type">
                    
                    <div class="mb-4">
                        <label for="endpointName" class="block text-sm font-medium text-gray-300 mb-2">Endpoint Adı</label>
                        <input type="text" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500" id="endpointName" name="name" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="endpointDescription" class="block text-sm font-medium text-gray-300 mb-2">Açıklama</label>
                        <textarea class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500" id="endpointDescription" name="description" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="endpointMethod" class="block text-sm font-medium text-gray-300 mb-2">HTTP Method</label>
                        <select class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500" id="endpointMethod" name="method" required>
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="endpointUrl" class="block text-sm font-medium text-gray-300 mb-2">Endpoint URL</label>
                        <input type="url" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500" id="endpointUrl" name="endpoint_url" placeholder="https://example.com/api/endpoint" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="endpointTimeout" class="block text-sm font-medium text-gray-300 mb-2">Timeout (saniye)</label>
                        <input type="number" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500" id="endpointTimeout" name="timeout" value="30" min="1" max="300">
                    </div>
                    
                    <div class="mb-4">
                        <label for="endpointHeaders" class="block text-sm font-medium text-gray-300 mb-2">Custom Headers (JSON)</label>
                        <textarea class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 font-mono text-sm" id="endpointHeaders" name="headers" rows="3" placeholder='{"Authorization": "Bearer token", "Content-Type": "application/json"}'></textarea>
                    </div>
                    

                    
                    <div class="mb-4" id="activeField" style="display: none;">
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-600 bg-gray-700 text-purple-500 focus:ring-purple-500" id="endpointActive" name="is_active" checked>
                            <span class="ml-2 text-sm text-gray-300">Aktif</span>
                        </label>
                    </div>
                </form>
                
                <div id="testResult" class="mt-4" style="display: none;">
                    <h6 class="text-white font-medium mb-2">Test Sonucu:</h6>
                    <pre id="testResultContent" class="bg-gray-900 p-4 rounded-lg text-green-400 text-sm overflow-auto max-h-64"></pre>
                </div>
            </div>
            
            <!-- Modal footer -->
            <div class="bg-gray-800 px-6 py-4 border-t border-gray-700 flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 bg-gray-600 hover:bg-gray-500 text-white font-medium rounded-lg transition-colors" onclick="closeModal()">Kapat</button>
                <button type="button" class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white font-medium rounded-lg transition-colors" id="testButton" onclick="testEndpoint()" style="display: none;">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Test Et
                </button>
                <button type="button" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white font-medium rounded-lg transition-colors" onclick="saveEndpoint()">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Kaydet
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@verbatim
<script>
// Global variables
let currentEndpointId = null;
let isEditMode = false;

// Intent types mapping
const intentTypes = {
    'order-tracking': 'Sipariş Takibi',
    'cargo-tracking': 'Kargo Takibi', 
    'add-to-cart': 'Sepete Ekle'
};

// Open endpoint modal for adding new endpoint
function openEndpointModal() {
    resetForm();
    const modalTitle = document.getElementById('modalTitle');
    if (modalTitle) modalTitle.textContent = 'Yeni Endpoint Ekle';
    
    const testButton = document.getElementById('testButton');
    if (testButton) testButton.style.display = 'none';
    
    const activeField = document.getElementById('activeField');
    if (activeField) activeField.style.display = 'none';
    
    isEditMode = false;
    currentEndpointId = null;
    
    showModal();
}

// Add endpoint for specific intent
function addEndpoint(intentType) {
    resetForm();
    
    const endpointIntentType = document.getElementById('endpointIntentType');
    if (endpointIntentType) endpointIntentType.value = intentType;
    
    const modalTitle = document.getElementById('modalTitle');
    if (modalTitle) modalTitle.textContent = `${intentTypes[intentType]} - Endpoint Ekle`;
    
    const testButton = document.getElementById('testButton');
    if (testButton) testButton.style.display = 'none';
    
    const activeField = document.getElementById('activeField');
    if (activeField) activeField.style.display = 'none';
    
    isEditMode = false;
    currentEndpointId = null;
    
    // Set default values based on intent type
    setDefaultValues(intentType);
    
    showModal();
}

// Edit existing endpoint
function editEndpoint(endpointId) {
    // For now, we'll use a simple approach since we don't have a show endpoint route
    // In a real implementation, you'd fetch the endpoint data
    alert('Düzenleme özelliği geliştiriliyor...');
}

// Set default values based on intent type
function setDefaultValues(intentType) {
    const defaults = {
        'order-tracking': {
            name: 'Sipariş Takibi API',
            description: 'Sipariş durumu sorgulama endpoint\'i',
            method: 'POST'
        },
        'cargo-tracking': {
            name: 'Kargo Takibi API',
            description: 'Kargo durumu sorgulama endpoint\'i',
            method: 'POST'
        },
        'add-to-cart': {
            name: 'Sepete Ekle API',
            description: 'Ürün sepete ekleme endpoint\'i',
            method: 'POST'
        }
    };
    
    const defaultData = defaults[intentType] || {};
    if (defaultData.name) {
        const nameField = document.getElementById('endpointName');
        if (nameField) nameField.value = defaultData.name;
    }
    if (defaultData.description) {
        const descField = document.getElementById('endpointDescription');
        if (descField) descField.value = defaultData.description;
    }
    if (defaultData.method) {
        const methodField = document.getElementById('endpointMethod');
        if (methodField) methodField.value = defaultData.method;
    }
}

// Reset form
function resetForm() {
    const form = document.getElementById('endpointForm');
    if (form) form.reset();
    
    const endpointId = document.getElementById('endpointId');
    if (endpointId) endpointId.value = '';
    
    const endpointIntentType = document.getElementById('endpointIntentType');
    if (endpointIntentType) endpointIntentType.value = '';
    
    const endpointTimeout = document.getElementById('endpointTimeout');
    if (endpointTimeout) endpointTimeout.value = 30;
    
    const testResult = document.getElementById('testResult');
    if (testResult) testResult.style.display = 'none';
}

// Save endpoint
function saveEndpoint() {
    console.log('saveEndpoint fonksiyonu çağrıldı');
    
    const form = document.getElementById('endpointForm');
    if (!form) {
        alert('Form bulunamadı!');
        return;
    }
    
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    console.log('Form data:', data);
    
    // Parse JSON fields
    try {
        if (data.headers) {
            data.headers = JSON.parse(data.headers);
        }
    } catch (e) {
        alert('JSON formatında hata var. Lütfen kontrol edin.');
        return;
    }
    
    const url = '/dashboard/action-apis/endpoints';
    const method = 'POST';
    
    console.log('API çağrısı yapılıyor:', url, data);
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Endpoint kaydedilirken hata oluştu: ' + error.message);
    });
}

// Test endpoint
function testEndpoint() {
    alert('Test özelliği geliştiriliyor...');
}

// Test user endpoint (from list)
function testUserEndpoint(endpointId) {
    const testData = {
        order_number: 'ORD-123456',
        tracking_number: 'YT123456789TR',
        product_id: 1,
        quantity: 1,
        session_id: 'test_session_' + Date.now()
    };
    
    fetch(`/dashboard/action-apis/endpoints/${endpointId}/test`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ test_data: testData })
    })
    .then(response => response.json())
    .then(data => {
        alert('Test sonucu: ' + JSON.stringify(data, null, 2));
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Test sırasında hata oluştu.');
    });
}

// Delete endpoint
function deleteEndpoint(endpointId) {
    if (!confirm('Bu endpoint\'i silmek istediğinizden emin misiniz?')) {
        return;
    }
    
    fetch(`/dashboard/action-apis/endpoints/${endpointId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Endpoint silinirken hata oluştu.');
    });
}

// Copy endpoint URL
function copyEndpoint(endpoint) {
    navigator.clipboard.writeText(endpoint).then(function() {
        // Show success message
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        setTimeout(() => {
            btn.innerHTML = originalHTML;
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy: ', err);
    });
}

// Show modal
function showModal() {
    const modal = document.getElementById('endpointModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

// Close modal
function closeModal() {
    const modal = document.getElementById('endpointModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto'; // Restore scrolling
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('endpointModal');
    if (event.target === modal) {
        closeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('endpointModal');
        if (!modal.classList.contains('hidden')) {
            closeModal();
        }
    }
});
</script>
@endverbatim