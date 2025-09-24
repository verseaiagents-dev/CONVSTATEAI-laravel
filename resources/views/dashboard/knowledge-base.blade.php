@extends('layouts.dashboard')

@section('title', 'Bilgi Tabanı')

@section('content')
<!-- Progress Tracking Script -->
<script src="{{ asset('js/knowledge-base-progress.js') }}"></script>
<style>
@keyframes pulse-glow {
    0%, 100% { 
        opacity: 0.3; 
        transform: scale(1);
    }
    50% { 
        opacity: 0.8; 
        transform: scale(1.05);
    }
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
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes shimmer {
    0% {
        background-position: -200px 0;
    }
    100% {
        background-position: calc(200px + 100%) 0;
    }
}

.loading-pulse {
    animation: pulse-glow 2s ease-in-out infinite;
}

.slide-in-up {
    animation: slide-in-up 0.6s ease-out forwards;
}

.fade-in-scale {
    animation: fade-in-scale 0.5s ease-out forwards;
}

.progress-animation {
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.shimmer {
    background: linear-gradient(90deg, #374151 25%, #4B5563 50%, #374151 75%);
    background-size: 200px 100%;
    animation: shimmer 1.5s infinite;
}


/* Chunks Modal Styles */
#chunks-modal {
    backdrop-filter: blur(8px);
    z-index: 9999 !important;
    /* Use normal display instead of important to allow proper control */
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Hide modal by default */
#chunks-modal.hidden {
    display: none !important;
}

#chunks-modal .fixed {
    position: fixed !important;
    top: 50% !important;
    left: 50% !important;
    transform: translate(-50%, -50%) !important;
    z-index: 10000 !important;
}

/* Loading message styles */
.loading-message {
    backdrop-filter: blur(8px);
    border: 1px solid rgba(59, 130, 246, 0.3);
}

/* Modal animation */
@keyframes modal-fade-in {
    from {
        opacity: 0;
        transform: scale(0.95) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@keyframes chunks-modal-fade-in {
    from {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}

#kb-detail-modal .bg-gray-800 {
    animation: modal-fade-in 0.3s ease-out forwards;
}

/* Only apply animation when modal is actually visible */
#chunks-modal:not(.hidden) .fixed {
    animation: chunks-modal-fade-in 0.3s ease-out forwards;
}

/* Chunk preview styling */
.chunk-scroll::-webkit-scrollbar {
    width: 8px;
}

.chunk-scroll::-webkit-scrollbar-track {
    background: #374151;
    border-radius: 4px;
}

.chunk-scroll::-webkit-scrollbar-thumb {
    background: #8B5CF6;
    border-radius: 4px;
}

.chunk-scroll::-webkit-scrollbar-thumb:hover {
    background: #A855F7;
}

.chunk-item {
    background-color: rgba(75, 85, 99, 0.5);
    padding: 12px;
    border-radius: 4px;
    border-left: 4px solid #8B5CF6;
    transition: all 0.2s ease;
    cursor: pointer;
}

.chunk-item:hover {
    background-color: rgba(139, 92, 246, 0.2);
    transform: translateX(2px);
    box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
}
</style>

<div class="space-y-6">
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden slide-in-up">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-4xl font-bold">
                        <span class="gradient-text">Bilgi Tabanı</span>
                    </h1>
                    <p class="text-xl text-gray-300">
                        AI destekli bilgi tabanı sistemi ile dosyalarınızı yükleyin ve akıllı arama yapın
                    </p>
                </div>
           
                <!-- Yönetim Butonları -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard.campaigns.index') }}?project_id={{ $project->id ?? 1 }}" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg text-white font-semibold hover:from-blue-600 hover:to-cyan-600 transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span>Kampanya Yönetimi</span>
                    
                    </a>
                    
                    <a href="{{ route('dashboard.faqs.index') }}?project_id={{ $project->id ?? 1 }}" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg text-white font-semibold hover:from-purple-600 hover:to-pink-600 transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>SSS Yönetimi</span>
   
                    </a>
                </div>


               
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="glass-effect rounded-2xl p-8">
        <div class="flex flex-col items-center justify-center space-y-6">
            <!-- Animated Loading Spinner -->
            <div class="relative loading-pulse">
                <div class="w-16 h-16 border-4 border-gray-700 rounded-full"></div>
                <div class="absolute top-0 left-0 w-16 h-16 border-4 border-transparent border-t-purple-500 rounded-full animate-spin"></div>
                <div class="absolute top-0 left-0 w-16 h-16 border-4 border-transparent border-t-neon-purple rounded-full animate-spin" style="animation-delay: -0.5s;"></div>
                <div class="absolute top-0 left-0 w-16 h-16 border-4 border-transparent border-t-blue-500 rounded-full animate-spin" style="animation-delay: -1s;"></div>
            </div>
            
            <!-- Loading Text -->
            <div class="text-center">
                <h3 class="text-xl font-semibold text-white mb-2">Bilgi Tabanı Yükleniyor</h3>
                <p class="text-gray-400">AI sistemleri hazırlanıyor, lütfen bekleyin...</p>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-64 bg-gray-700 rounded-full h-2 overflow-hidden">
                <div id="progressBar" class="bg-gradient-to-r from-purple-glow to-neon-purple h-2 rounded-full progress-animation" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <!-- Loading Skeleton -->
    <div id="skeletonState" class="hidden space-y-6">
        <!-- New Knowledge Base Creation Skeleton -->
        <div class="glass-effect rounded-2xl p-8">
            <div class="animate-pulse">
                <div class="h-8 bg-gray-700 rounded-lg w-64 mb-6 shimmer"></div>
                
                <!-- Method Selection Skeleton -->
                <div class="mb-8 p-6 bg-gray-800/30 rounded-lg border border-gray-700">
                    <div class="text-center mb-4">
                        <div class="h-6 bg-gray-700 rounded w-80 mx-auto mb-2 shimmer"></div>
                        <div class="h-4 bg-gray-700 rounded w-96 mx-auto shimmer"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="p-4 bg-gray-700 rounded-lg h-32 shimmer"></div>
                        <div class="p-4 bg-gray-700 rounded-lg h-32 shimmer"></div>
                    </div>
                    
                    <div class="mt-6 text-center">
                        <div class="h-4 bg-gray-700 rounded w-48 mx-auto mb-2 shimmer"></div>
                        <div class="h-10 bg-gray-700 rounded w-64 mx-auto shimmer"></div>
                        <div class="h-3 bg-gray-700 rounded w-80 mx-auto mt-2 shimmer"></div>
                    </div>
                </div>
                
                <!-- Form Sections Skeleton -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="space-y-4">
                        <div class="h-6 bg-gray-700 rounded w-32 shimmer"></div>
                        <div class="h-32 bg-gray-700 rounded-lg shimmer"></div>
                    </div>
                    <div class="lg:col-span-2 space-y-4">
                        <div class="h-6 bg-gray-700 rounded w-40 shimmer"></div>
                        <div class="space-y-3">
                            <div class="h-12 bg-gray-700 rounded-lg shimmer"></div>
                            <div class="h-12 bg-gray-700 rounded-lg shimmer"></div>
                            <div class="h-12 bg-gray-700 rounded-lg shimmer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Container (Hidden initially) -->
    <div id="contentContainer" class="hidden space-y-6 slide-in-up">
        <!-- New Bilgi Tabanı Creation Container -->
        <div class="glass-effect rounded-2xl p-8">
            <h2 class="text-2xl font-bold mb-6 text-white">Yeni Bilgi Tabanı Oluştur</h2>
            
            <!-- Method Selection Guide -->
            <div class="mb-8 p-6 bg-gray-800/30 rounded-lg border border-gray-700">
                <div class="text-center mb-4">
                    <h3 class="text-lg font-semibold text-white mb-2">📋 Bilgi Tabanı Oluşturma Yöntemi Seçin</h3>
                    <p class="text-gray-300">Aşağıdaki iki yöntemden birini kullanarak yeni bilgi tabanı oluşturabilirsiniz:</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="p-4 bg-purple-500/10 border border-purple-500/30 rounded-lg text-center hover:bg-purple-500/20 transition-colors duration-300 cursor-pointer" onclick="scrollToSection('file-upload')">
                        <div class="text-2xl mb-2">📁</div>
                        <h4 class="font-semibold text-white mb-2">Dosya Yükleme</h4>
                        <p class="text-sm text-gray-300">Bilgisayarınızdan dosya seçin ve yükleyin</p>
                        <div class="mt-3 text-xs text-purple-300">→ Tıklayın</div>
                    </div>
                    
                    <div class="p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg text-center hover:bg-blue-500/20 transition-colors duration-300 cursor-pointer" onclick="scrollToSection('url-fetch')">
                        <div class="text-2xl mb-2">🌐</div>
                        <h4 class="font-semibold text-white mb-2">URL ile İçerik Çekme</h4>
                        <p class="text-sm text-gray-300">Web'den dosya URL'si ile içerik çekin</p>
                        <div class="mt-3 text-xs text-blue-300">→ Tıklayın</div>
                    </div>
                </div>
                
              
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- File Upload Section -->
                <div id="file-upload" class="lg:col-span-1">
                    <h3 class="text-lg font-semibold mb-4 text-white">Dosya Yükleme</h3>

                    <!-- Upload Area -->
                    <div id="upload-area" class="border-2 border-dashed border-gray-600 rounded-2xl p-8 text-center hover:border-purple-glow transition-colors duration-300 cursor-pointer">
                        <div class="space-y-4">
                            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <div>
                                <p class="text-lg font-semibold text-white mb-2">Dosya seçin veya sürükleyin</p>
                                <p class="text-gray-400 mb-4">Desteklenen formatlar: CSV, TXT, XML, JSON, Excel</p>
                                <p class="text-sm text-gray-500">Maksimum dosya boyutu: 10MB</p>
                            </div>
                            <button id="select-file-btn" class="px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105">
                                Dosya Seç
                            </button>
                        </div>
                    </div>

                    <!-- Hidden File Input -->
                    <input type="file" id="file-input" accept=".csv,.txt,.xml,.json,.xlsx,.xls" class="hidden">
                    
                    <!-- Upload Progress -->
                    <div id="upload-progress" class="hidden mt-6">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="w-4 h-4 border-2 border-purple-glow border-t-transparent rounded-full animate-spin"></div>
                            <span class="text-purple-glow">Dosya yükleniyor ve işleniyor...</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div id="progress-bar" class="bg-gradient-to-r from-purple-glow to-neon-purple h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <!-- URL Fetch Section -->
                <div id="url-fetch" class="lg:col-span-2 flex items-center justify-center">
                    <div class="w-full max-w-2xl">
                        <h3 class="text-lg font-semibold mb-4 text-white text-center">URL ile İçerik Çekme</h3>
                        
                        <div class="space-y-4">
                            <div class="flex flex-col space-y-3">
                                <input type="url" id="url-input" placeholder="https://example.com/data.csv" class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20 text-center">
                                <button id="fetch-url-btn" class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg text-white font-semibold hover:from-blue-600 hover:to-cyan-600 transition-all duration-300 transform hover:scale-105" onclick="openUrlContentTypeModal()">
                                    İçerik Tipini Seç
                                </button>
                            </div>
                            
                            <p class="text-sm text-gray-400 text-center">
                                CSV, TXT, XML, JSON veya Excel dosyalarının URL'lerini girin. İçerik otomatik olarak chunk'lara bölünecek ve AI ile işlenecektir.
                            </p>
                        </div>
                        
                        <!-- URL Fetch Progress -->
                        <div id="url-fetch-progress" class="hidden mt-6">
                            <div class="flex items-center justify-center space-x-3 mb-2">
                                <div class="w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                                <span class="text-blue-400">URL'den içerik çekiliyor ve işleniyor...</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div id="url-progress-bar" class="bg-gradient-to-r from-blue-500 to-cyan-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bilgi Tabanı Listesi -->
        <div id="knowledge-bases-container" class="glass-effect rounded-2xl p-8">
            <h2 class="text-2xl font-bold mb-6 text-white">Mevcut Bilgi Tabanları</h2>
            
            <!-- Search Container -->
            <div class="mb-8 p-6 bg-gray-800/30 rounded-lg border border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-white">Bilgi Tabanı Arama</h3>
                
                <div class="space-y-4">
                    <div class="flex space-x-4">
                        <input type="text" id="search-query" placeholder="Ürün arama, kategori bilgisi, yardım..." class="flex-1 px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        <button id="search-btn" class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg text-white font-semibold hover:from-green-600 hover:to-emerald-600 transition-all duration-300 transform hover:scale-105">
                            AI ile Ara
                        </button>
                    </div>
                    
                    <p class="text-sm text-gray-400">
                        AI destekli intent detection ile bilgi tabanında arama yapın. Sistem otomatik olarak en uygun yanıtı üretecektir.
                    </p>
                </div>
                
                <!-- Search Results -->
                <div id="search-results" class="hidden mt-6">
                    <div id="search-content" class="space-y-4">
                        <!-- Search results will be populated here -->
                    </div>
                </div>
            </div>
            
            <!-- Knowledge Bases List -->
            <div id="knowledge-bases-list" class="space-y-4">
                <!-- Knowledge bases will be populated here -->
            </div>
        </div>
        
        <!-- Results Container -->
        <div id="results-container" class="glass-effect rounded-2xl p-8 hidden">
            <h2 class="text-2xl font-bold mb-6 text-white">İşlem Sonuçları</h2>
            
            <div id="results-content" class="space-y-6">
                <!-- Results will be populated here -->
            </div>
        </div>


        <!-- Content Type Selection Modal -->
        <div id="contentTypeModal" class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden flex items-center justify-center">
            <div class="relative p-6 border border-gray-700 w-full max-w-2xl shadow-2xl rounded-xl glass-effect">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">İçerik Tipini Seçin</h3>
                    <button onclick="closeContentTypeModal()" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-6">
                    <p class="text-gray-300 text-center mb-6">
                        Yüklediğiniz dosyanın içerik tipini seçin. Bu seçim, AI sisteminin içeriği nasıl işleyeceğini belirler.
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- FAQ Content Type -->
                        <div class="content-type-option p-6 bg-gray-800/30 rounded-lg border border-gray-700 hover:border-purple-500/50 transition-all duration-300 cursor-pointer" data-type="faq">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-white mb-2">FAQ / SSS</h4>
                                <p class="text-sm text-gray-400">Sık sorulan sorular ve cevapları içeren içerik</p>
                            </div>
                        </div>
                        
                        <!-- Product Content Type -->
                        <div class="content-type-option p-6 bg-gray-800/30 rounded-lg border border-gray-700 hover:border-blue-500/50 transition-all duration-300 cursor-pointer" data-type="product">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-white mb-2">Ürün Kataloğu</h4>
                                <p class="text-sm text-gray-400">Ürün bilgileri, fiyatlar ve kategoriler içeren içerik</p>
                            </div>
                        </div>
                        
                        <!-- Blog Content Type -->
                        <div class="content-type-option p-6 bg-gray-800/30 rounded-lg border border-gray-700 hover:border-green-500/50 transition-all duration-300 cursor-pointer" data-type="blog">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-white mb-2">Blog / Makale</h4>
                                <p class="text-sm text-gray-400">Blog yazıları, makaleler ve bilgilendirici içerik</p>
                            </div>
                        </div>
                        
                        <!-- Campaign Content Type -->
                        <div class="content-type-option p-6 bg-gray-800/30 rounded-lg border border-gray-700 hover:border-orange-500/50 transition-all duration-300 cursor-pointer" data-type="campaign">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-orange-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-white mb-2">Kampanya</h4>
                                <p class="text-sm text-gray-400">Kampanya bilgileri, promosyonlar ve özel teklifler</p>
                            </div>
                        </div>
                        
                        <!-- General Content Type -->
                        <div class="content-type-option p-6 bg-gray-800/30 rounded-lg border border-gray-700 hover:border-gray-500/50 transition-all duration-300 cursor-pointer" data-type="general">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-white mb-2">Genel İçerik</h4>
                                <p class="text-sm text-gray-400">Belirli bir kategoriye girmeyen genel içerik</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 pt-4 border-t border-gray-700 flex space-x-3">
                    <button onclick="closeContentTypeModal()" class="flex-1 px-4 py-3 bg-gray-600 hover:bg-gray-500 rounded-lg text-white font-semibold transition-colors">
                        Geri
                    </button>
                    <button onclick="proceedToFieldMapping()" class="flex-1 px-4 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold transition-all duration-300 transform hover:scale-105">
                        Field Mapping'e Geç
                    </button>
                </div>
    </div>
</div>

<!-- URL Content Type Selection Modal -->
<div id="urlContentTypeModal" class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden flex items-center justify-center">
    <div class="relative p-6 border border-gray-700 w-full max-w-2xl shadow-2xl rounded-xl glass-effect">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-white">İçerik Tipini Seçin</h3>
            <button onclick="closeUrlContentTypeModal()" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="space-y-6">
            <p class="text-gray-300 text-center mb-6">
                URL'den çekilecek içeriğin tipini seçin. Bu seçim, AI sisteminin içeriği nasıl işleyeceğini belirler.
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- FAQ Content Type -->
                <div class="content-type-option p-6 bg-gray-800/30 rounded-lg border border-gray-700 hover:border-purple-500/50 transition-all duration-300 cursor-pointer" data-type="faq">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-white mb-2">FAQ / SSS</h4>
                        <p class="text-sm text-gray-400">Sık sorulan sorular ve cevapları içeren içerik</p>
                    </div>
                </div>
                
                <!-- Product Content Type -->
                <div class="content-type-option p-6 bg-gray-800/30 rounded-lg border border-gray-700 hover:border-blue-500/50 transition-all duration-300 cursor-pointer" data-type="product">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-white mb-2">Ürün Kataloğu</h4>
                        <p class="text-sm text-gray-400">Ürün bilgileri, fiyatlar ve kategoriler içeren içerik</p>
                    </div>
                </div>
                
                <!-- Blog Content Type -->
                <div class="content-type-option p-6 bg-gray-800/30 rounded-lg border border-gray-700 hover:border-green-500/50 transition-all duration-300 cursor-pointer" data-type="blog">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-white mb-2">Blog / Makale</h4>
                        <p class="text-sm text-gray-400">Blog yazıları, makaleler ve bilgilendirici içerik</p>
                    </div>
                </div>
                
                <!-- Campaign Content Type -->
                <div class="content-type-option p-6 bg-gray-800/30 rounded-lg border border-gray-700 hover:border-orange-500/50 transition-all duration-300 cursor-pointer" data-type="campaign">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-orange-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-white mb-2">Kampanya</h4>
                        <p class="text-sm text-gray-400">Kampanya bilgileri, promosyonlar ve özel teklifler</p>
                    </div>
                </div>
                
                <!-- General Content Type -->
                <div class="content-type-option p-6 bg-gray-800/30 rounded-lg border border-gray-700 hover:border-gray-500/50 transition-all duration-300 cursor-pointer" data-type="general">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gray-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-white mb-2">Genel İçerik</h4>
                        <p class="text-sm text-gray-400">Belirli bir kategoriye girmeyen genel içerik</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 pt-4 border-t border-gray-700 flex space-x-3">
            <button onclick="closeUrlContentTypeModal()" class="flex-1 px-4 py-3 bg-gray-600 hover:bg-gray-500 rounded-lg text-white font-semibold transition-colors">
                Geri
            </button>
            <button onclick="proceedToUrlFieldMapping()" class="flex-1 px-4 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold transition-all duration-300 transform hover:scale-105">
                Field Mapping'e Geç
            </button>
        </div>
    </div>
</div>

<!-- Field Mapping Modal -->
        <div id="fieldMappingModal" class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden flex items-center justify-center">
            <div class="relative p-6 border border-gray-700 w-full max-w-6xl max-h-[90vh] shadow-2xl rounded-xl glass-effect">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Field Mapping</h3>
                    <button onclick="closeFieldMappingModal()" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-6">
                    <p class="text-gray-300 text-center mb-6">
                        Dosyanızdaki alanları sistemimizdeki standart alanlarla eşleştirin. Bu sayede AI sisteminiz doğru şekilde çalışacaktır.
                    </p>
                    
                    <!-- Field Mapping Container -->
                    <div id="field-mapping-container" class="space-y-4 max-h-96 overflow-y-auto">
                        <!-- Field mappings will be populated here -->
                    </div>
                    
                    
                    <!-- Sample Data Preview -->
                    <div id="sample-data-preview" class="hidden">
                        <h4 class="text-lg font-semibold text-white mb-4">Örnek Veri Önizleme</h4>
                        <div class="bg-gray-800/50 rounded-lg p-4 max-h-80 overflow-auto border border-gray-600">
                            <div id="sample-data-content" class="text-sm text-gray-300 min-w-full">
                                <!-- Sample data will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 pt-4 border-t border-gray-700 flex space-x-3">
                    <button onclick="closeFieldMappingModal()" class="px-4 py-3 bg-gray-600 hover:bg-gray-500 rounded-lg text-white font-semibold transition-colors">
                        İptal
                    </button>
                    <button onclick="previewFieldMapping()" class="px-4 py-3 bg-blue-600 hover:bg-blue-500 rounded-lg text-white font-semibold transition-colors">
                        Önizle
                    </button>
                    <button onclick="confirmFieldMapping()" class="px-4 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold transition-all duration-300 transform hover:scale-105">
                        Kaydet ve Yükle
                    </button>
                </div>
            </div>
        </div>

        <!-- Chunks Modal -->
        <div id="chunks-modal" class="fixed inset-0 z-50 hidden">
            <!-- Modal Backdrop -->
            <div class="absolute inset-0 bg-black/85 backdrop-blur-sm"></div>
            
            <!-- Modal Content -->
            <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-6xl max-h-[90vh] bg-gray-900 rounded-2xl border border-gray-700 shadow-2xl overflow-hidden">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-700 bg-gray-800/50">
                    <h2 class="text-2xl font-bold text-white">Bilgi Tabanı Chunk'ları</h2>
                    <button id="close-chunks-modal" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <div id="chunks-content" class="space-y-6">
                        <!-- Chunks will be populated here -->
                    </div>
                </div>
                
                <!-- Modal Footer with Pagination -->
                <div id="chunks-pagination" class="flex items-center justify-between p-6 border-t border-gray-700 bg-gray-800/50">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-400">Sayfa:</span>
                        <span id="current-page" class="text-white font-medium">1</span>
                        <span class="text-gray-400">/</span>
                        <span id="total-pages" class="text-white font-medium">1</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button id="prev-page-btn" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-white text-sm font-medium transition-colors duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button id="next-page-btn" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-white text-sm font-medium transition-colors duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
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
                    <h3 class="text-lg font-semibold text-white">İçerik Yüklenemedi</h3>
                    <p class="text-gray-400 text-sm">Bilgi tabanı verileri yüklenirken bir hata oluştu, ancak form alanları kullanılabilir.</p>
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
                    

    <!-- Results Container -->
    <div id="results-container" class="glass-effect rounded-2xl p-8 hidden">
        <h2 class="text-2xl font-bold mb-6 text-white">İşlem Sonuçları</h2>
        
        <div id="results-content" class="space-y-6">
            <!-- Results will be populated here -->
    </div>
    </div>
</div>

<script>
// Global variables
let loadingProgress = 0;
let loadingInterval;
let knowledgeBases = [];
let projects = [];

// Define all functions first to ensure they're available globally

window.deleteKB = async function(kbId) {
    try {
        // Confirm deletion
        if (!confirm('Bu bilgi tabanını silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
            return;
        }
        
        // Show loading state
        showLoadingMessage('Siliniyor...');
        
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token bulunamadı');
        }
        
        const requestOptions = {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        };
        
        const response = await fetch(`/dashboard/knowledge-base/${kbId}`, requestOptions);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            showSuccessMessage('Bilgi tabanı başarıyla silindi!');
            // Reload content
            loadContent();
        } else {
            showErrorMessage(result.message || 'Bilgi tabanı silinemedi');
        }
        
    } catch (error) {
        showErrorMessage('Bilgi tabanı silinirken hata oluştu: ' + error.message);
    }
};



// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    startLoading();
    loadContent();
});

// Start loading animation
function startLoading() {
    loadingProgress = 0;
    const progressBar = document.getElementById('progressBar');
    
    loadingInterval = setInterval(() => {
        loadingProgress += Math.random() * 15;
        if (loadingProgress > 90) loadingProgress = 90;
        
        progressBar.style.width = loadingProgress + '%';
    }, 200);
}


// Complete loading animation
function completeLoading() {
    clearInterval(loadingInterval);
    const progressBar = document.getElementById('progressBar');
    progressBar.style.width = '100%';
    
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
                contentContainer.style.transition = 'all 0.5s ease-out';
                contentContainer.style.opacity = '1';
                contentContainer.style.transform = 'translateY(0)';
                
                // Populate content
                populateContent();
            }, 100);
        }, 800); // Show skeleton for 800ms
    }, 500);
}

// Load content from server
async function loadContent() {
    try {
        const projectId = '{{ $projectId }}';
        const url = '{{ route("dashboard.knowledge-base.load-content") }}' + (projectId ? `?project_id=${projectId}` : '');
        
        // CSRF token'ı al - önce meta tag'den, yoksa input'tan
        let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            csrfToken = document.querySelector('input[name="_token"]')?.value;
        }
        if (!csrfToken) {
            // Fallback olarak Laravel'in session'dan al
            csrfToken = '{{ csrf_token() }}';
        }
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            // Store data globally
            knowledgeBases = result.data.knowledgeBases || [];
            projects = result.data.projects || [];
            
            completeLoading();
        } else {
            // Hata mesajı varsa göster ama form alanlarını da yükle
            knowledgeBases = [];
            projects = [];
            completeLoading();
        }
        
    } catch (error) {
        // Hata durumunda bile form alanlarını göster
        knowledgeBases = [];
        projects = [];
        showErrorState();
    }
}

// Populate content with loaded data
function populateContent() {
    // Populate projects dropdown
        const projectSelect = document.getElementById('global-project');
    if (projectSelect) {
        projectSelect.innerHTML = '<option value="">Proje Seçin (Opsiyonel)</option>';
        
        if (projects && projects.length > 0) {
            const currentProjectId = '{{ $projectId }}';
            projects.forEach(project => {
                const option = document.createElement('option');
                option.value = project.id;
                option.textContent = project.name;
                if (project.id == currentProjectId) {
                    option.selected = true;
                }
                projectSelect.appendChild(option);
            });
        }
        
        // Add event listener for project selection
        projectSelect.addEventListener('change', function() {
            const selectedProjectId = this.value;
            if (selectedProjectId) {
                // Redirect to knowledge base with selected project ID
                window.location.href = `{{ route('dashboard.knowledge-base') }}?project_id=${selectedProjectId}`;
            }
        });
    }
    
    // Populate knowledge bases list
    populateKnowledgeBasesList();
    
    // Add staggered animations to form elements
    const formElements = document.querySelectorAll('#contentContainer .glass-effect, #contentContainer form > div');
    formElements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            element.style.transition = 'all 0.5s ease-out';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, 200 + (index * 100));
    });
    
}

// Populate content with default values (when loading fails)
function populateContentWithDefaults() {
    // Projects dropdown'ı boş bırak
        const projectSelect = document.getElementById('global-project');
    if (projectSelect) {
        projectSelect.innerHTML = '<option value="">Proje Seçin (Opsiyonel)</option>';
        
        // Add event listener for project selection
        projectSelect.addEventListener('change', function() {
            const selectedProjectId = this.value;
            if (selectedProjectId) {
                // Redirect to knowledge base with selected project ID
                window.location.href = `{{ route('dashboard.knowledge-base') }}?project_id=${selectedProjectId}`;
            }
        });
    }
    
    // Knowledge bases listesini boş göster
    populateKnowledgeBasesList();
    
    // Form alanlarını görünür yap
    const formElements = document.querySelectorAll('#contentContainer .glass-effect, #contentContainer form > div');
    formElements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
            element.style.transition = 'all 0.5s ease-out';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, 200 + (index * 100));
    });
    
    
    // Error state'i gizle çünkü form alanları yüklendi
    setTimeout(() => {
        const errorState = document.getElementById('errorState');
        if (errorState) {
            errorState.classList.add('hidden');
        }
    }, 5000); // 5 saniye sonra gizle
}

// Populate knowledge bases list
function populateKnowledgeBasesList() {
    const container = document.getElementById('knowledge-bases-list');
    
    if (!knowledgeBases || knowledgeBases.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📚</div>
                <h3 class="text-xl font-semibold text-white mb-2">Henüz Bilgi Tabanı Yok</h3>
                <p class="text-gray-400">İlk bilgi tabanınızı oluşturmak için yukarıdaki formları kullanın</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = knowledgeBases.map(kb => `
        <div class="p-6 bg-gray-800/30 rounded-lg border border-gray-700 hover:border-purple-glow transition-colors duration-300 cursor-pointer" onclick="viewKnowledgeBaseDetail(${kb.id})">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-white mb-2">${kb.name}</h3>
                    ${kb.description ? `<p class="text-gray-300 text-sm mb-3">${kb.description}</p>` : ''}
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-gray-400">Kaynak:</span>
                            <span class="text-white ml-2">${kb.source_type === 'file' ? 'Dosya' : 'URL'}</span>
                        </div>
                        <div>
                            <span class="text-gray-400">Format:</span>
                            <span class="text-white ml-2">${kb.file_type ? kb.file_type.toUpperCase() : 'N/A'}</span>
                        </div>
                        <div>
                            <span class="text-gray-400">Chunk Sayısı:</span>
                            <span class="text-white ml-2">${kb.chunk_count || 0}</span>
                        </div>
                        <div>
                            <span class="text-gray-400">Durum:</span>
                            <span class="ml-2 px-2 py-1 rounded-full text-xs font-medium
                                ${kb.processing_status === 'completed' ? 'bg-green-500/20 text-green-300' :
                                  kb.processing_status === 'processing' ? 'bg-yellow-500/20 text-yellow-300' :
                                  kb.processing_status === 'failed' ? 'bg-red-500/20 text-red-300' :
                                  'bg-gray-500/20 text-gray-300'}">
                                ${kb.processing_status ? kb.processing_status.charAt(0).toUpperCase() + kb.processing_status.slice(1) : 'N/A'}
                            </span>
                        </div>
                    </div>
                    
                    ${kb.processing_status === 'completed' ? `
                      
                    ` : ''}
                    
                    ${kb.processing_status === 'pending' || kb.processing_status === 'processing' ? `
                        <div class="mt-4" id="progress-${kb.id}" data-knowledge-base-id="${kb.id}">
                            <div class="progress-container">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-300">İşleme Durumu</span>
                                    <span class="text-sm text-gray-400">
                                        <span class="chunk-count">0</span> chunk
                                    </span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: 5%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                        <span class="processing-indicator"></span>
                                    </div>
                                </div>
                                <div class="status-text text-center mt-2">İşleme bekleniyor...</div>
                            </div>
                        </div>
                    ` : ''}
                    
                    ${kb.processing_status === 'failed' ? `
                        <div class="mt-4">
                            <div class="bg-red-500/20 border border-red-500/30 rounded-lg p-3">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-red-300 text-sm">İşleme başarısız oldu</span>
                                </div>
                                <div class="mt-2 text-xs text-red-400">${kb.error_message || 'Bilinmeyen hata'}</div>
                                <button onclick="event.stopPropagation(); retryProcessing(${kb.id})" class="mt-2 px-3 py-1 bg-red-600 hover:bg-red-700 rounded text-white text-xs transition-colors duration-300">
                                    Yeniden Dene
                                </button>
                            </div>
                        </div>
                    ` : ''}
                </div>
                
                <div class="flex flex-col items-end space-y-2">
                    <span class="text-xs text-gray-500">${new Date(kb.created_at).toLocaleDateString('tr-TR')}</span>
                    <button onclick="event.stopPropagation(); deleteKnowledgeBase(${kb.id})" class="text-red-400 hover:text-red-300 transition-colors duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// Get status color
function getStatusColor(status) {
    switch (status) {
        case 'completed': return 'bg-green-500/20 text-green-400';
        case 'processing': return 'bg-yellow-500/20 text-yellow-400';
        case 'failed': return 'bg-red-500/20 text-red-400';
        default: return 'bg-gray-500/20 text-gray-400';
    }
}

// Functions are now defined at the top of the script

// Close function is now defined at the top of the script

// Show loading message
function showLoadingMessage(message) {
    // Remove existing loading message
    const existingMessage = document.querySelector('.loading-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Create loading message
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'loading-message fixed top-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 slide-in-up';
    loadingDiv.textContent = message;
    
    document.body.appendChild(loadingDiv);
    
    // Remove after 3 seconds
    setTimeout(() => {
        if (loadingDiv.parentNode) {
            loadingDiv.remove();
        }
    }, 3000);
}

// Show error state
function showErrorState() {
    clearInterval(loadingInterval);
    document.getElementById('loadingState').classList.add('hidden');
    
    // Hata durumunda bile form alanlarını göster
    document.getElementById('contentContainer').classList.remove('hidden');
    
    // Error state'i de göster ama form alanları da görünsün
    document.getElementById('errorState').classList.remove('hidden');
    
    // Form alanlarını varsayılan değerlerle doldur
    populateContentWithDefaults();
    
    // Form alanlarını animasyonla göster
    const formElements = document.querySelectorAll('#contentContainer .glass-effect, #contentContainer form > div');
    formElements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            element.style.transition = 'all 0.5s ease-out';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, 200 + (index * 100));
    });
    
    
    // Error state'i 5 saniye sonra gizle
    setTimeout(() => {
        const errorState = document.getElementById('errorState');
        if (errorState) {
            errorState.classList.add('hidden');
        }
    }, 5000);
}

// Retry loading
function retryLoading() {
    document.getElementById('errorState').classList.add('hidden');
    document.getElementById('loadingState').classList.remove('hidden');
    startLoading();
    loadContent();
}

// Utility functions
function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
        
        // Add highlight effect
        element.style.transition = 'all 0.3s ease';
        element.style.boxShadow = '0 0 20px rgba(139, 92, 246, 0.5)';
        element.style.borderColor = '#8B5CF6';
        
        setTimeout(() => {
            element.style.boxShadow = '';
            element.style.borderColor = '';
        }, 2000);
    }
}



function deleteKB(id) {
    // Implementation for deleting knowledge base
    if (confirm('Bu bilgi tabanını silmek istediğinizden emin misiniz?')) {
        // Delete implementation
    }
}

// Global functions for bilgi tabanı operations
function searchKnowledgeBase(kbId) {
    const query = prompt('Bu bilgi tabanında arama yapmak için sorgu girin:');
    if (query) {
        const searchInput = document.getElementById('search-query');
        const searchButton = document.getElementById('search-btn');
        if (searchInput && searchButton) {
            searchInput.value = query;
            searchButton.click();
        }
    }
}

function viewChunks(kbId) {
    // Get elements
    const chunksModal = document.getElementById('chunks-modal');
    const chunksContent = document.getElementById('chunks-content');
    
    // Show loading
    if (chunksModal) {
        chunksModal.classList.remove('hidden');
        
        // Ensure modal is properly positioned and visible
        chunksModal.style.position = 'fixed';
        chunksModal.style.top = '0';
        chunksModal.style.left = '0';
        chunksModal.style.width = '100vw';
        chunksModal.style.height = '100vh';
        chunksModal.style.zIndex = '9999';
    } else {
        alert('Chunks modal bulunamadı. Sayfayı yenileyin.');
        return;
    }
    
    if (chunksContent) {
        chunksContent.innerHTML = `
            <div class="flex items-center justify-center py-12">
                <div class="flex items-center space-x-3">
                    <div class="w-6 h-6 border-2 border-purple-glow border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-purple-glow">Chunk'lar yükleniyor...</span>
                </div>
            </div>
        `;
    } else {
        alert('Chunks content bulunamadı. Sayfayı yenileyin.');
        return;
    }
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('Güvenlik token\'ı bulunamadı. Sayfayı yenileyin.');
        return;
    }
    
    // Fetch chunks from API
    fetch(`/api/knowledge-base/${kbId}/chunks`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            displayChunks(data.chunks, data.knowledge_base, 1);
        } else {
            if (chunksContent) {
                chunksContent.innerHTML = `
                    <div class="p-6 bg-red-500/10 border border-red-500/30 rounded-lg">
                        <p class="text-red-400">Hata: ${data.message}</p>
                    </div>
                `;
            }
        }
    })
    .catch(error => {
        if (chunksContent) {
            chunksContent.innerHTML = `
                <div class="p-6 bg-red-500/10 border border-red-500/30 rounded-lg">
                    <p class="text-red-400">Chunk'lar yüklenirken hata oluştu: ${error.message}</p>
                </div>
            `;
        }
    });
}

function deleteKnowledgeBase(kbId) {
    if (confirm('Bu bilgi tabanını silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
        fetch(`/api/knowledge-base/${kbId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Hata: ' + data.message);
            }
        })
        .catch(error => {
            alert('Silme işlemi sırasında hata oluştu: ' + error.message);
        });
    }
}

function retryProcessing(kbId) {
    // Show loading state
    const retryButton = event.target;
    const originalText = retryButton.textContent;
    retryButton.textContent = 'Yeniden deneniyor...';
    retryButton.disabled = true;
    
    fetch(`/api/knowledge-base/${kbId}/retry`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            if (window.KnowledgeBaseProgressTracker) {
                const tracker = new window.KnowledgeBaseProgressTracker();
                tracker.showNotification('İşleme yeniden başlatıldı!', 'success');
            }
            
            // Reload the page to show updated status
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            alert('Hata: ' + data.message);
            retryButton.textContent = originalText;
            retryButton.disabled = false;
        }
    })
    .catch(error => {
        alert('Yeniden deneme sırasında hata oluştu: ' + error.message);
        retryButton.textContent = originalText;
        retryButton.disabled = false;
    });
}

function viewKnowledgeBaseDetail(kbId) {
    // Show modal
    const modal = document.getElementById('kbDetailModal');
    const content = document.getElementById('kbDetailContent');
    const title = document.getElementById('modalTitle');
    
    if (modal && content) {
        modal.classList.remove('hidden');
        
        // Show loading
        content.innerHTML = `
            <div class="flex items-center justify-center py-12">
                <div class="flex items-center space-x-3">
                    <div class="w-6 h-6 border-2 border-purple-glow border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-purple-glow">Bilgi tabanı detayları yükleniyor...</span>
                </div>
            </div>
        `;
        
        // Fetch bilgi tabanı details
        fetch(`/api/knowledge-base/${kbId}/detail`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayKnowledgeBaseDetail(data.knowledge_base, data.chunks, data.stats);
                title.textContent = data.knowledge_base.name;
            } else {
                content.innerHTML = `
                    <div class="p-6 bg-red-500/10 border border-red-500/30 rounded-lg">
                        <p class="text-red-400">Hata: ${data.message}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            content.innerHTML = `
                <div class="p-6 bg-red-500/10 border border-red-500/30 rounded-lg">
                    <p class="text-red-400">Detaylar yüklenirken hata oluştu: ${error.message}</p>
                </div>
            `;
        });
    }
}


function displayKnowledgeBaseDetail(kb, chunks, stats) {
    const content = document.getElementById('kbDetailContent');
    
    content.innerHTML = `
        <!-- Bilgi Tabanı Overview -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700">
            <h4 class="text-xl font-semibold text-white mb-4">Genel Bilgiler</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">ID:</span>
                        <span class="text-white font-mono">#${kb.id}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Oluşturulma:</span>
                        <span class="text-white">${new Date(kb.created_at).toLocaleDateString('tr-TR')}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Son Güncelleme:</span>
                        <span class="text-white">${new Date(kb.updated_at).toLocaleDateString('tr-TR')}</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Kaynak Tipi:</span>
                        <span class="text-white">${kb.source_type}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Dosya Formatı:</span>
                        <span class="text-white">${kb.file_type || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Site ID:</span>
                        <span class="text-white">${kb.site_id}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass-effect rounded-xl p-6 border border-gray-700 text-center">
                <div class="text-3xl font-bold text-purple-glow mb-2">${stats.total_chunks}</div>
                <div class="text-gray-400">Toplam Chunk</div>
            </div>
            <div class="glass-effect rounded-xl p-6 border border-gray-700 text-center">
                <div class="text-3xl font-bold text-green-400 mb-2">${stats.avg_chunk_size}</div>
                <div class="text-gray-400">Ortalama Chunk Boyutu</div>
            </div>
            <div class="glass-effect rounded-xl p-6 border border-gray-700 text-center">
                <div class="text-3xl font-bold text-blue-400 mb-2">${stats.total_tokens}</div>
                <div class="text-gray-400">Toplam Token</div>
            </div>
        </div>
        
        <!-- Chunks Preview -->
        <div class="glass-effect rounded-xl border border-gray-700">
            <div class="p-6 border-b border-gray-700">
                <h4 class="text-xl font-semibold text-white">Chunk Önizlemesi (İlk 10)</h4>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    ${chunks.slice(0, 10).map((chunk, index) => `
                        <div class="p-4 bg-gray-800/30 rounded-lg border border-gray-700">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-sm text-purple-glow font-medium">Chunk #${index + 1}</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-gray-400">${chunk.content_type}</span>
                                </div>
                            </div>
                            <p class="text-gray-300 text-sm mb-2">${chunk.content.substring(0, 200)}${chunk.content.length > 200 ? '...' : ''}</p>
                            <div class="flex justify-between text-xs text-gray-400">
                                <span>Boyut: ${chunk.content.length} karakter</span>
                                <span>Oluşturulma: ${new Date(chunk.created_at).toLocaleDateString('tr-TR')}</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
                ${chunks.length > 10 ? `
                    <div class="mt-4 text-center">
                        <p class="text-gray-400">Ve ${chunks.length - 10} chunk daha...</p>
                    </div>
                ` : ''}
            </div>
        </div>

        <!-- Actions -->
        <div class="flex space-x-4">
            <button onclick="searchKnowledgeBase(${kb.id})" class="px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300">
                Arama Yap
            </button>
            <button onclick="viewChunks(${kb.id})" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 rounded-lg text-white font-semibold transition-all duration-300">
                Tüm Chunk'ları Gör
            </button>
            <button onclick="closeKBDetailModal()" class="px-6 py-3 bg-gray-600 hover:bg-gray-500 rounded-lg text-white font-semibold transition-all duration-300">
                Kapat
            </button>
        </div>
    `;
}

// Global displayChunks function
function displayChunks(chunks, knowledgeBase, currentPage = 1) {
    // Get elements
    const chunksContent = document.getElementById('chunks-content');
    const currentPageSpan = document.getElementById('current-page');
    const totalPagesSpan = document.getElementById('total-pages');
    const prevPageBtn = document.getElementById('prev-page-btn');
    const nextPageBtn = document.getElementById('next-page-btn');
    
    if (!chunksContent) {
        return;
    }
    
    const chunksPerPage = 5;
    const totalPages = Math.ceil(chunks.length / chunksPerPage);
    const startIndex = (currentPage - 1) * chunksPerPage;
    const endIndex = startIndex + chunksPerPage;
    const pageChunks = chunks.slice(startIndex, endIndex);
    
    // Update pagination info
    if (currentPageSpan) currentPageSpan.textContent = currentPage;
    if (totalPagesSpan) totalPagesSpan.textContent = totalPages;
    
    // Update pagination buttons
    if (prevPageBtn) {
        prevPageBtn.disabled = currentPage <= 1;
        prevPageBtn.classList.toggle('opacity-50', currentPage <= 1);
        prevPageBtn.classList.toggle('cursor-not-allowed', currentPage <= 1);
    }
    if (nextPageBtn) {
        nextPageBtn.disabled = currentPage >= totalPages;
        nextPageBtn.classList.toggle('opacity-50', currentPage >= totalPages);
        nextPageBtn.classList.toggle('cursor-not-allowed', currentPage >= totalPages);
    }
    
    // Add pagination event listeners
    if (prevPageBtn) {
        prevPageBtn.onclick = () => {
            if (currentPage > 1) {
                displayChunks(chunks, knowledgeBase, currentPage - 1);
            }
        };
    }
    if (nextPageBtn) {
        nextPageBtn.onclick = () => {
            if (currentPage < totalPages) {
                displayChunks(chunks, knowledgeBase, currentPage + 1);
            }
        };
    }
    
    // Display chunks
    let html = '';
    
    // Bilgi Tabanı info header
    html += `
        <div class="p-6 bg-blue-500/10 border border-blue-500/30 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-blue-400 mb-3">${knowledgeBase.name}</h3>
            <div class="grid md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-gray-400">Toplam Chunk:</span>
                    <span class="text-white ml-2">${chunks.length}</span>
                </div>
                <div>
                    <span class="text-gray-400">Dosya Tipi:</span>
                    <span class="text-white ml-2">${knowledgeBase.file_type?.toUpperCase() || 'N/A'}</span>
                </div>
                <div>
                    <span class="text-gray-400">Kaynak:</span>
                    <span class="text-white ml-2">${knowledgeBase.source_type === 'file' ? 'Dosya' : 'URL'}</span>
                </div>
                <div>
                    <span class="text-gray-400">Oluşturulma:</span>
                    <span class="text-white ml-2">${new Date(knowledgeBase.created_at).toLocaleDateString('tr-TR')}</span>
                </div>
            </div>
        </div>
    `;
    
    // Chunks list
    if (pageChunks.length > 0) {
        html += `
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-lg font-semibold text-white">Chunk'lar (Sayfa ${currentPage}/${totalPages})</h4>
                    <span class="text-sm text-gray-400">${startIndex + 1}-${Math.min(endIndex, chunks.length)} / ${chunks.length}</span>
                </div>
        `;
        
        pageChunks.forEach((chunk, index) => {
            const globalIndex = startIndex + index;
            html += `
                <div class="p-6 bg-gray-800/30 rounded-lg border border-gray-700 hover:border-purple-glow transition-colors duration-300">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <span class="px-3 py-1 bg-purple-500/20 text-purple-300 rounded-full text-sm font-medium">
                                Chunk ${chunk.chunk_index || globalIndex + 1}
                            </span>
                            <span class="px-2 py-1 bg-gray-600/50 text-gray-300 rounded text-xs">
                                ${chunk.content_type || 'text'}
                            </span>
                        </div>
                        <div class="flex items-center space-x-2 text-xs text-gray-400">
                            <span>${chunk.word_count || 'N/A'} kelime</span>
                            <span>•</span>
                            <span>${chunk.chunk_size || 'N/A'} karakter</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-sm text-gray-400 mb-2">İçerik:</div>
                        <div class="p-4 bg-gray-900/50 rounded-lg border border-gray-700">
                            <pre class="text-white text-sm whitespace-pre-wrap break-words">${chunk.content || 'İçerik bulunamadı'}</pre>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>Hash: ${chunk.content_hash || 'N/A'}</span>
                        <span>Oluşturulma: ${new Date(chunk.created_at).toLocaleString('tr-TR')}</span>
                    </div>
                </div>
            `;
        });
        
        html += `</div>`;
    } else {
        html += `
            <div class="p-6 bg-yellow-500/10 border border-yellow-500/30 rounded-lg text-center">
                <p class="text-yellow-400">Bu sayfada chunk bulunamadı.</p>
            </div>
        `;
    }
    
    chunksContent.innerHTML = html;
}

// Chunks modal close button
document.addEventListener('DOMContentLoaded', function() {
    const closeChunksModal = document.getElementById('close-chunks-modal');
    if (closeChunksModal) {
        closeChunksModal.addEventListener('click', () => {
            const chunksModal = document.getElementById('chunks-modal');
            if (chunksModal) {
                chunksModal.classList.add('hidden');
            }
        });
    }
    
    // Close modal when clicking on backdrop
    const chunksModal = document.getElementById('chunks-modal');
    if (chunksModal) {
        chunksModal.addEventListener('click', (e) => {
            if (e.target === chunksModal) {
                chunksModal.classList.add('hidden');
            }
        });
    }
    
    
    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const chunksModal = document.getElementById('chunks-modal');
            
            if (chunksModal && !chunksModal.classList.contains('hidden')) {
                chunksModal.classList.add('hidden');
            }
        }
    });
});

// File upload functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectFileBtn = document.getElementById('select-file-btn');
    const fileInput = document.getElementById('file-input');
    const uploadArea = document.getElementById('upload-area');
    
    if (selectFileBtn && fileInput) {
        selectFileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            fileInput.click();
        });
    }
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                handleFileUpload(e.target.files[0]);
            }
        });
    }
    
    // Drag and drop functionality
    if (uploadArea) {
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('border-purple-glow', 'bg-purple-500/10');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('border-purple-glow', 'bg-purple-500/10');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('border-purple-glow', 'bg-purple-500/10');
            
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                handleFileUpload(e.dataTransfer.files[0]);
            }
        });
    }
});

// Global variable to store selected file
let selectedFile = null;

function handleFileUpload(file) {
    // Validate file type
    const allowedTypes = ['csv', 'txt', 'xml', 'json', 'xlsx', 'xls'];
    const fileExtension = file.name.split('.').pop().toLowerCase();
    
    if (!allowedTypes.includes(fileExtension)) {
        alert('Desteklenmeyen dosya formatı. Lütfen CSV, TXT, XML, JSON veya Excel dosyası seçin.');
        return;
    }

    // Validate file size (10MB)
    if (file.size > 10 * 1024 * 1024) {
        alert('Dosya boyutu çok büyük. Maksimum 10MB olmalıdır.');
        return;
    }

    // Store file globally and generate KB name from file name
    selectedFile = file;
    
    // Generate knowledge base name from file name (remove extension)
    const fileName = file.name;
    const nameWithoutExtension = fileName.replace(/\.[^/.]+$/, "");
    selectedKbName = nameWithoutExtension;
    
    // Go directly to content type selection
    openContentTypeModal();
}

// Modal functions

function resetFileSelection() {
    selectedFile = null;
    // Also reset file input
    const fileInput = document.getElementById('file-input');
    if (fileInput) {
        fileInput.value = '';
    }
}


function uploadFileWithName(file, kbName) {
    
    // Show progress
    const uploadProgress = document.getElementById('upload-progress');
    if (uploadProgress) {
        uploadProgress.classList.remove('hidden');
    }
    
    // Show real progress
    let progress = 0;
    const progressBar = document.getElementById('progress-bar');
    const progressInterval = setInterval(() => {
        progress += Math.random() * 20;
        if (progress > 95) progress = 95;
        if (progressBar) {
            progressBar.style.width = progress + '%';
        }
    }, 100);

    // Create FormData
    const formData = new FormData();
    formData.append('file', file);
    formData.append('name', kbName);
    
    // Add project_id if available
    const projectId = '{{ $projectId }}';
    if (projectId) {
        formData.append('project_id', projectId);
    }
    
    // CSRF token'ı güvenli şekilde al
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        formData.append('_token', csrfToken.getAttribute('content'));
    } else {
        alert('Güvenlik token\'ı bulunamadı. Sayfayı yenileyin.');
        return;
    }
    

    // Upload file
    fetch('/dashboard/knowledge-base/upload', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        return response.json();
    })
    .then(data => {
        clearInterval(progressInterval);
        if (progressBar) {
            progressBar.style.width = '100%';
        }
        
        setTimeout(() => {
            if (uploadProgress) {
                uploadProgress.classList.add('hidden');
            }
            if (data.success) {
                showResults(data);
                // Reset file selection
                resetFileSelection();
                // Reload page to show new knowledge base
                setTimeout(() => location.reload(), 2000);
            } else {
                let errorMessage = 'Hata: ' + data.message;
                if (data.errors) {
                    errorMessage += '\nDetaylar: ' + JSON.stringify(data.errors, null, 2);
                }
                alert(errorMessage);
            }
        }, 500);
    })
    .catch(error => {
        clearInterval(progressInterval);
        if (uploadProgress) {
            uploadProgress.classList.add('hidden');
        }
        // Reset file selection on error
        resetFileSelection();
        alert('Dosya yüklenirken hata oluştu: ' + error.message);
    });
}

// URL fetch functionality - now handled by modal system
document.addEventListener('DOMContentLoaded', function() {
    const urlInput = document.getElementById('url-input');
    const kbNameInput = document.getElementById('kb-name');
    
    if (urlInput) {
        urlInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                openUrlContentTypeModal();
            }
        });
    }
    
    if (kbNameInput) {
        kbNameInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                openUrlContentTypeModal();
            }
        });
    }
});


function isValidUrl(string) {
    try {
        new URL(string);
        return true;
    } catch (_) {
        return false;
    }
}

function showResults(data) {
    const resultsContent = document.getElementById('results-content');
    const resultsContainer = document.getElementById('results-container');
    
    if (!resultsContent || !resultsContainer) {
        return;
    }
    
    if (data.success) {
        let html = '';

        // Success info
        html += `
            <div class="p-6 bg-green-500/10 border border-green-500/30 rounded-lg mb-6">
                <h3 class="text-lg font-semibold text-green-400 mb-2">✓ ${data.message || 'İşlem başarılı'}</h3>
                <div class="grid md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-400">Bilgi Tabanı ID:</span>
                        <span class="text-white ml-2">${data.knowledge_base_id || 'N/A'}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Chunk Sayısı:</span>
                        <span class="text-white ml-2">${data.chunk_count || 'N/A'}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Dosya Adı:</span>
                        <span class="text-white ml-2">${data.file_name || 'N/A'}</span>
                    </div>
                </div>
            </div>
        `;

        resultsContent.innerHTML = html;
        resultsContainer.classList.remove('hidden');
    } else {
        alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
    }
}

// Search functionality
document.getElementById('search-btn').addEventListener('click', function() {
    const query = document.getElementById('search-query').value.trim();
    if (query) {
        handleSearch();
    }
});

document.getElementById('search-query').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const query = e.target.value.trim();
        if (query) {
            handleSearch();
        }
    }
});

function handleSearch() {
    const query = document.getElementById('search-query').value.trim();
    if (!query) {
        alert('Lütfen arama sorgusu girin');
        return;
    }

    // Show loading
    const searchResults = document.getElementById('search-results');
    const searchContent = document.getElementById('search-content');
    if (searchResults) {
        searchResults.classList.remove('hidden');
    }
    if (searchContent) {
        searchContent.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="w-4 h-4 border-2 border-green-500 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-green-400">AI ile arama yapılıyor...</span>
            </div>
        `;
    }

    // Create form data
    const formData = new FormData();
    formData.append('query', query);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    // Search
    fetch('/api/knowledge-base/search', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSearchResults(data);
        } else {
            if (searchContent) {
                searchContent.innerHTML = `
                    <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                        <p class="text-red-400">Hata: ${data.message}</p>
                    </div>
                `;
            }
        }
    })
    .catch(error => {
        if (searchContent) {
            searchContent.innerHTML = `
                <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                    <p class="text-red-400">Arama yapılırken hata oluştu: ${error.message}</p>
                </div>
            `;
        }
    });
}

function showSearchResults(data) {
    const searchContent = document.getElementById('search-content');
    if (!searchContent) return;
    
    let html = '';
    
    // Intent info
    if (data.intent) {
        html += `
            <div class="p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg mb-4">
                <h4 class="text-lg font-semibold text-blue-400 mb-2">Tespit Edilen Intent</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-400">Intent:</span>
                        <span class="text-white ml-2">${data.intent.intent || 'N/A'}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Güven:</span>
                        <span class="text-white ml-2">${data.intent.confidence ? (data.intent.confidence * 100).toFixed(1) + '%' : 'N/A'}</span>
                    </div>
                </div>
            </div>
        `;
    }
    
    // AI Response
    if (data.response) {
        html += `
            <div class="p-4 bg-green-500/10 border border-green-500/30 rounded-lg mb-4">
                <h4 class="text-lg font-semibold text-green-400 mb-2">AI Yanıtı</h4>
                <p class="text-white">${data.response}</p>
            </div>
        `;
    }
    
    // Suggestions
    if (data.suggestions && data.suggestions.length > 0) {
        html += `
            <div class="p-4 bg-purple-500/10 border border-purple-500/30 rounded-lg mb-4">
                <h4 class="text-lg font-semibold text-purple-400 mb-2">Öneriler</h4>
                <ul class="space-y-2">
                    ${data.suggestions.map(suggestion => `<li class="text-white">• ${suggestion}</li>`).join('')}
                </ul>
            </div>
        `;
    }
    
    // Used chunks
    if (data.chunks && data.chunks.length > 0) {
        html += `
            <div class="p-4 bg-gray-800/30 border border-gray-700 rounded-lg">
                <h4 class="text-lg font-semibold text-white mb-2">Kullanılan Bilgi Parçaları</h4>
                <div class="space-y-3">
                    ${data.chunks.map(chunk => `
                        <div class="p-3 bg-gray-900/50 rounded-lg">
                            <div class="text-sm text-gray-400 mb-1">Chunk ${chunk.chunk_index || 'N/A'} (${chunk.content_type || 'N/A'})</div>
                            <div class="text-white text-sm">${chunk.content ? (chunk.content.substring(0, 200) + (chunk.content.length > 200 ? '...' : '')) : 'İçerik bulunamadı'}</div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }
    
    if (html === '') {
        html = `
            <div class="p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                <p class="text-yellow-400">Arama sonucu bulunamadı veya veri eksik.</p>
            </div>
        `;
    }
    
    searchContent.innerHTML = html;
}

// Message functions
function showSuccessMessage(message) {
    // Create success message element
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 slide-in-up';
    successDiv.textContent = message;
    
    document.body.appendChild(successDiv);
    
    // Remove after 3 seconds
    setTimeout(() => {
        successDiv.remove();
    }, 3000);
}

function showErrorMessage(message) {
    // Create error message element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 slide-in-up';
    errorDiv.textContent = message;
    
    document.body.appendChild(errorDiv);
    
    // Remove after 5 seconds
    setTimeout(() => {
        errorDiv.remove();
    }, 5000);
}

// Global variables for modal state
let selectedContentType = null;
let detectedFields = [];
let fieldMappings = [];
let selectedUrl = null;
let selectedUrlContentType = null;
let selectedKbName = null;
let selectedKbDescription = null;

// Content Type Modal Functions
function openContentTypeModal() {
    const modal = document.getElementById('contentTypeModal');
    if (modal) {
        modal.classList.remove('hidden');
        // Add click handlers for content type options
        addContentTypeClickHandlers();
    }
}

function closeContentTypeModal() {
    const modal = document.getElementById('contentTypeModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// URL Content Type Modal Functions
function openUrlContentTypeModal() {
    const urlInput = document.getElementById('url-input');
    
    if (!urlInput) {
        alert('URL işlemi için gerekli elementler bulunamadı');
        return;
    }
    
    const url = urlInput.value.trim();
    
    if (!url) {
        alert('Lütfen geçerli bir URL girin');
        return;
    }
    
    if (!isValidUrl(url)) {
        alert('Lütfen geçerli bir URL formatı girin (örn: https://example.com/data.csv)');
        return;
    }
    
    // Generate knowledge base name from URL (extract filename)
    const urlParts = url.split('/');
    const fileName = urlParts[urlParts.length - 1];
    const nameWithoutExtension = fileName.replace(/\.[^/.]+$/, "");
    selectedKbName = nameWithoutExtension || 'url-content';
    
    // Store values in global variables
    selectedUrl = url;
    const modal = document.getElementById('urlContentTypeModal');
    if (modal) {
        modal.classList.remove('hidden');
        addUrlContentTypeClickHandlers();
    }
}

function closeUrlContentTypeModal() {
    const modal = document.getElementById('urlContentTypeModal');
    if (modal) {
        modal.classList.add('hidden');
        selectedUrlContentType = null;
        // Don't reset selectedUrl here - it should persist until field mapping is complete
    }
}

function addContentTypeClickHandlers() {
    const options = document.querySelectorAll('.content-type-option');
    options.forEach(option => {
        option.addEventListener('click', function() {
            // Remove previous selection
            options.forEach(opt => {
                opt.classList.remove('ring-2', 'ring-purple-500', 'bg-purple-500/10');
            });
            
            // Add selection to clicked option
            this.classList.add('ring-2', 'ring-purple-500', 'bg-purple-500/10');
            
            // Store selected content type
            selectedContentType = this.dataset.type;
        });
    });
}

function addUrlContentTypeClickHandlers() {
    const options = document.querySelectorAll('#urlContentTypeModal .content-type-option');
    options.forEach(option => {
        option.addEventListener('click', function() {
            // Remove previous selection
            options.forEach(opt => {
                opt.classList.remove('ring-2', 'ring-purple-500', 'bg-purple-500/10');
            });
            
            // Add selection to clicked option
            this.classList.add('ring-2', 'ring-purple-500', 'bg-purple-500/10');
            
            // Store selected content type
            selectedUrlContentType = this.dataset.type;
        });
    });
}

function proceedToFieldMapping() {
    if (!selectedContentType) {
        alert('Lütfen bir içerik tipi seçin.');
        return;
    }
    
    closeContentTypeModal();
    
    // Detect fields from the uploaded file
    detectFieldsFromFile();
}

function proceedToUrlFieldMapping() {
    if (!selectedUrlContentType) {
        alert('Lütfen bir içerik tipi seçin.');
        return;
    }
    
    if (!selectedUrl) {
        alert('URL bulunamadı. Lütfen tekrar URL girin.');
        return;
    }
    
    closeUrlContentTypeModal();
    detectFieldsFromUrl();
}

// Field Detection and Mapping Functions
function detectFieldsFromFile() {
    if (!selectedFile) {
        alert('Dosya bulunamadı.');
        return;
    }
    
    // Show loading state
    const modal = document.getElementById('fieldMappingModal');
    if (modal) {
        modal.classList.remove('hidden');
        
        // Show loading in field mapping container
        const container = document.getElementById('field-mapping-container');
        container.innerHTML = `
            <div class="flex items-center justify-center p-8">
                <div class="text-center">
                    <div class="w-8 h-8 border-4 border-purple-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-gray-300">Dosya analiz ediliyor ve alanlar tespit ediliyor...</p>
                </div>
            </div>
        `;
    }
    
    // Call real API for field detection
    const formData = new FormData();
    formData.append('file', selectedFile);
    
    // CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        formData.append('_token', csrfToken.getAttribute('content'));
    }
    
    fetch('/dashboard/knowledge-base/detect-fields-from-file', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            detectedFields = data.detected_fields || [];
            renderFieldMapping();
        } else {
            // Fallback to simulation
            detectedFields = simulateFieldDetection(selectedFile);
            renderFieldMapping();
        }
    })
    .catch(error => {
        // Fallback to simulation
        try {
            detectedFields = simulateFieldDetection(selectedFile);
        } catch (simError) {
            detectedFields = [
                { name: 'content', type: 'text', sample: 'Dosya içeriği' }
            ];
        }
        renderFieldMapping();
    });
}

function detectFieldsFromUrl() {
    if (!selectedUrl) {
        alert('URL bulunamadı. Lütfen tekrar URL girin.');
        return;
    }
    
    // Show field mapping modal with loading state
    const modal = document.getElementById('fieldMappingModal');
    if (modal) {
        modal.classList.remove('hidden');
        
        // Show loading state
        const container = document.getElementById('field-mapping-container');
        if (container) {
            container.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <div class="w-6 h-6 border-2 border-purple-500 border-t-transparent rounded-full animate-spin mr-3"></div>
                    <span class="text-purple-400">URL'den field'lar tespit ediliyor...</span>
                </div>
            `;
        }
    }
    
    // Call real API for URL field detection
    const formData = new FormData();
    formData.append('url', selectedUrl);
    
    // CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        formData.append('_token', csrfToken.getAttribute('content'));
    }
    
    fetch('/dashboard/knowledge-base/detect-fields-from-url', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            detectedFields = data.detected_fields || [];
            renderFieldMapping();
        } else {
            // Fallback to simulation
            detectedFields = simulateUrlFieldDetection(selectedUrl);
            renderFieldMapping();
        }
    })
    .catch(error => {
        // Fallback to simulation
        try {
            detectedFields = simulateUrlFieldDetection(selectedUrl);
        } catch (simError) {
            detectedFields = [
                { name: 'content', type: 'text', sample: 'URL içeriği' }
            ];
        }
        renderFieldMapping();
    });
}

function simulateFieldDetection(file) {
    const extension = file.name.split('.').pop().toLowerCase();
    const fields = [];
    
    // Simulate different field detection based on file type
    switch (extension) {
        case 'csv':
            fields.push(
                { name: 'id', type: 'number', sample: '1' },
                { name: 'name', type: 'text', sample: 'Ürün Adı' },
                { name: 'price', type: 'number', sample: '99.99' },
                { name: 'category', type: 'text', sample: 'Elektronik' },
                { name: 'description', type: 'text', sample: 'Ürün açıklaması' }
            );
            break;
        case 'json':
            fields.push(
                { name: 'product_id', type: 'number', sample: '123' },
                { name: 'title', type: 'text', sample: 'Ürün Başlığı' },
                { name: 'cost', type: 'number', sample: '150.00' },
                { name: 'type', type: 'text', sample: 'Kategori' },
                { name: 'details', type: 'text', sample: 'Detaylı açıklama' }
            );
            break;
        case 'xml':
            fields.push(
                { name: 'item_id', type: 'number', sample: '456' },
                { name: 'item_name', type: 'text', sample: 'XML Ürün' },
                { name: 'amount', type: 'number', sample: '75.50' },
                { name: 'cat', type: 'text', sample: 'Kategori' },
                { name: 'desc', type: 'text', sample: 'XML açıklama' }
            );
            break;
        default:
            fields.push(
                { name: 'field1', type: 'text', sample: 'Değer 1' },
                { name: 'field2', type: 'text', sample: 'Değer 2' },
                { name: 'field3', type: 'text', sample: 'Değer 3' }
            );
    }
    
    return fields;
}

function simulateUrlFieldDetection(url) {
    const extension = url.split('.').pop().toLowerCase().split('?')[0]; // Remove query params
    const fields = [];
    
    // Simulate different field detection based on URL file type
    switch (extension) {
        case 'csv':
            fields.push(
                { name: 'id', type: 'number', sample: '1' },
                { name: 'name', type: 'text', sample: 'Ürün Adı' },
                { name: 'price', type: 'number', sample: '99.99' },
                { name: 'category', type: 'text', sample: 'Elektronik' },
                { name: 'description', type: 'text', sample: 'Ürün açıklaması' }
            );
            break;
        case 'json':
            fields.push(
                { name: 'product_id', type: 'number', sample: '123' },
                { name: 'title', type: 'text', sample: 'Ürün Başlığı' },
                { name: 'cost', type: 'number', sample: '150.00' },
                { name: 'type', type: 'text', sample: 'Kategori' },
                { name: 'details', type: 'text', sample: 'Detaylı açıklama' }
            );
            break;
        case 'xml':
            fields.push(
                { name: 'item_id', type: 'number', sample: '456' },
                { name: 'item_name', type: 'text', sample: 'XML Ürün' },
                { name: 'amount', type: 'number', sample: '75.50' },
                { name: 'cat', type: 'text', sample: 'Kategori' },
                { name: 'desc', type: 'text', sample: 'XML açıklama' }
            );
            break;
        default:
            fields.push(
                { name: 'field1', type: 'text', sample: 'Değer 1' },
                { name: 'field2', type: 'text', sample: 'Değer 2' },
                { name: 'field3', type: 'text', sample: 'Değer 3' }
            );
    }
    
    detectedFields = fields;
    renderFieldMapping();
}

function renderFieldMapping() {
    const container = document.getElementById('field-mapping-container');
    const standardFields = getStandardFields();
    
    if (!container) {
        return;
    }
    
    if (!detectedFields || detectedFields.length === 0) {
        // Show fallback fields for testing
        detectedFields = [
            { name: 'id', type: 'number', sample: '1' },
            { name: 'name', type: 'text', sample: 'Örnek İsim' },
            { name: 'description', type: 'text', sample: 'Örnek açıklama' },
            { name: 'price', type: 'number', sample: '99.99' }
        ];
    }
    
    let html = '';
    
    detectedFields.forEach((field, index) => {
        // Show additional info for JSON fields
        const additionalInfo = field.total_occurrences ? 
            `<div class="text-xs text-blue-400 mb-1">📊 ${field.total_occurrences} kayıt bulundu</div>` : '';
        
        html += `
            <div class="p-4 bg-gray-800/30 rounded-lg border border-gray-700">
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-medium text-gray-300">
                                ${field.name} <span class="text-xs text-gray-500">(${field.type})</span>
                            </label>
                            ${field.index !== undefined ? `<span class="text-xs text-gray-500">#${field.index + 1}</span>` : ''}
                        </div>
                        ${additionalInfo}
                        <div class="text-xs text-gray-400 mb-2 break-words">
                            <span class="text-gray-500">Örnek:</span> ${field.sample || 'Değer yok'}
                        </div>
                        <select class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:border-purple-500 focus:outline-none" 
                                onchange="updateFieldMapping(${index}, this.value)">
                            <option value="" selected>Alan seçin...</option>
                            ${standardFields.map(sf => `
                                <option value="${sf.value}">
                                    ${sf.label}
                                </option>
                            `).join('')}
                        </select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-600 text-purple-500 focus:ring-purple-500" 
                                   onchange="toggleFieldRequired(${index}, this.checked)">
                            <span class="ml-2 text-sm text-gray-300">Zorunlu</span>
                        </label>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    // Initialize field mappings array
    fieldMappings = detectedFields.map(field => ({
        source_field: field.name,
        target_field: '',
        field_type: field.type,
        is_required: false
    }));
}


function getStandardFields() {
    return [
        { value: 'product_name', label: 'Ürün Adı' },
        { value: 'product_description', label: 'Ürün Açıklaması' },
        { value: 'product_price', label: 'Ürün Fiyatı' },
        { value: 'product_category', label: 'Ürün Kategorisi' },
        { value: 'product_brand', label: 'Ürün Markası' },
        { value: 'product_sku', label: 'Ürün SKU' },
        { value: 'product_stock', label: 'Stok Miktarı' },
        { value: 'product_image', label: 'Ürün Resmi' },
        { value: 'product_tags', label: 'Ürün Etiketleri' },
        { value: 'product_rating', label: 'Ürün Puanı' },
        { value: 'product_reviews', label: 'Ürün Yorumları' }
    ];
}

function getSuggestedMapping(sourceField, targetField) {
    const suggestions = {
        'product_name': ['name', 'title', 'product_name', 'item_name'],
        'product_description': ['description', 'desc', 'details'],
        'product_price': ['price', 'cost', 'amount'],
        'product_category': ['category', 'cat', 'type'],
        'product_brand': ['brand', 'manufacturer'],
        'product_sku': ['sku', 'code', 'id'],
        'product_stock': ['stock', 'quantity', 'inventory'],
        'product_image': ['image', 'photo', 'picture'],
        'product_tags': ['tags', 'keywords', 'labels'],
        'product_rating': ['rating', 'score', 'stars'],
        'product_reviews': ['reviews', 'review_count']
    };
    
    const targetSuggestions = suggestions[targetField] || [];
    return targetSuggestions.some(suggestion => 
        sourceField.toLowerCase().includes(suggestion.toLowerCase()) ||
        suggestion.toLowerCase().includes(sourceField.toLowerCase())
    );
}

function updateFieldMapping(index, targetField) {
    if (fieldMappings[index]) {
        fieldMappings[index].target_field = targetField;
    }
}

function toggleFieldRequired(index, isRequired) {
    if (fieldMappings[index]) {
        fieldMappings[index].is_required = isRequired;
    }
}

function previewFieldMapping() {
    // Show sample data preview
    const preview = document.getElementById('sample-data-preview');
    const content = document.getElementById('sample-data-content');
    
    if (preview && content) {
        preview.classList.remove('hidden');
        
        let html = '<div class="overflow-x-auto"><table class="w-full text-sm table-auto">';
        html += '<thead><tr class="border-b border-gray-600 bg-gray-700/50">';
        html += '<th class="text-left p-3 text-gray-200 font-semibold min-w-32">Kaynak Alan</th>';
        html += '<th class="text-left p-3 text-gray-200 font-semibold min-w-32">Hedef Alan</th>';
        html += '<th class="text-left p-3 text-gray-200 font-semibold min-w-40">Örnek Değer</th>';
        html += '<th class="text-left p-3 text-gray-200 font-semibold min-w-20">Zorunlu</th>';
        html += '</tr></thead><tbody>';
        
        fieldMappings.forEach(mapping => {
            if (mapping.target_field) {
                const sourceField = detectedFields.find(f => f.name === mapping.source_field);
                html += `<tr class="border-b border-gray-700 hover:bg-gray-700/30">`;
                html += `<td class="p-3 text-gray-300 font-medium">${mapping.source_field}</td>`;
                html += `<td class="p-3 text-green-400 font-medium">${mapping.target_field}</td>`;
                html += `<td class="p-3 text-gray-400 break-words">${sourceField ? sourceField.sample : 'N/A'}</td>`;
                html += `<td class="p-3 text-center">${mapping.is_required ? '<span class="text-red-400">✓</span>' : '<span class="text-gray-500">-</span>'}</td>`;
                html += `</tr>`;
            }
        });
        
        if (fieldMappings.filter(m => m.target_field).length === 0) {
            html += `<tr><td colspan="4" class="p-4 text-center text-gray-500">Henüz alan eşleştirmesi yapılmadı</td></tr>`;
        }
        
        html += '</tbody></table></div>';
        content.innerHTML = html;
    }
}


function confirmFieldMapping() {
    // Validate mappings - check for non-empty target_field values
    const validMappings = fieldMappings.filter(mapping => mapping.target_field && mapping.target_field.trim() !== '');
    
    if (validMappings.length === 0) {
        alert('Lütfen en az bir alan eşleştirmesi yapın. Dropdown menülerden alan seçimi yapmalısınız.');
        return;
    }
    
    // Close field mapping modal
    closeFieldMappingModal();
    
    // Check if this is URL or file upload
    if (selectedUrl) {
        uploadUrlWithMappings();
    } else {
        uploadFileWithMappings();
    }
}

function uploadFileWithMappings() {
    
    if (!selectedKbName) {
        alert('Bilgi tabanı adı gerekli.');
        return;
    }
    
    if (!selectedFile) {
        alert('Dosya seçilmedi. Lütfen tekrar dosya seçin.');
        return;
    }
    
    // Show progress
    const uploadProgress = document.getElementById('upload-progress');
    if (uploadProgress) {
        uploadProgress.classList.remove('hidden');
    }
    
    // Show real progress
    let progress = 0;
    const progressBar = document.getElementById('progress-bar');
    const progressInterval = setInterval(() => {
        progress += Math.random() * 20;
        if (progress > 95) progress = 95;
        if (progressBar) {
            progressBar.style.width = progress + '%';
        }
    }, 100);

    // Create FormData
    const formData = new FormData();
    formData.append('file', selectedFile);
    formData.append('name', selectedKbName);
    if (selectedContentType) {
        formData.append('content_type', selectedContentType);
    }
    formData.append('field_mappings', JSON.stringify(fieldMappings.filter(m => m.target_field)));
    
    // Add project_id if available
    const projectId = '{{ $projectId }}';
    if (projectId) {
        formData.append('project_id', projectId);
    }
    
    // CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        formData.append('_token', csrfToken.getAttribute('content'));
    } else {
        alert('Güvenlik token\'ı bulunamadı. Sayfayı yenileyin.');
        return;
    }

    // Upload file
    fetch('/dashboard/knowledge-base/upload', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        clearInterval(progressInterval);
        if (progressBar) {
            progressBar.style.width = '100%';
        }
        
        setTimeout(() => {
            if (uploadProgress) {
                uploadProgress.classList.add('hidden');
            }
            if (data.success) {
                showResults(data);
                // Clear all global variables
                selectedFile = null;
                selectedContentType = null;
                selectedKbName = null;
                selectedKbDescription = null;
                fieldMappings = [];
                detectedFields = [];
                // Reset file input
                const fileInput = document.getElementById('file-input');
                if (fileInput) {
                    fileInput.value = '';
                }
                setTimeout(() => location.reload(), 2000);
            } else {
                let errorMessage = 'Hata: ' + data.message;
                if (data.errors) {
                    errorMessage += '\nDetaylar: ' + JSON.stringify(data.errors, null, 2);
                }
                alert(errorMessage);
            }
        }, 500);
    })
    .catch(error => {
        clearInterval(progressInterval);
        // Clear all global variables on error
        selectedFile = null;
        selectedContentType = null;
        selectedKbName = null;
        selectedKbDescription = null;
        fieldMappings = [];
        detectedFields = [];
        // Reset file input
        const fileInput = document.getElementById('file-input');
        if (fileInput) {
            fileInput.value = '';
        }
        alert('Dosya yüklenirken hata oluştu: ' + error.message);
    });
}

function uploadUrlWithMappings() {
    
    if (!selectedKbName) {
        alert('Bilgi tabanı adı gerekli.');
        return;
    }
    
    if (!selectedUrl) {
        alert('URL bulunamadı. Lütfen tekrar URL girin.');
        return;
    }
    
    // Show progress
    const urlFetchProgress = document.getElementById('url-fetch-progress');
    if (urlFetchProgress) {
        urlFetchProgress.classList.remove('hidden');
    }
    
    // Show real progress
    let progress = 0;
    const urlProgressBar = document.getElementById('url-progress-bar');
    const progressInterval = setInterval(() => {
        progress += Math.random() * 20;
        if (progress > 95) progress = 95;
        if (urlProgressBar) {
            urlProgressBar.style.width = progress + '%';
        }
    }, 100);

    // Create FormData
    const formData = new FormData();
    formData.append('url', selectedUrl);
    formData.append('name', selectedKbName);
    if (selectedUrlContentType) {
        formData.append('content_type', selectedUrlContentType);
    }
    formData.append('field_mappings', JSON.stringify(fieldMappings.filter(m => m.target_field)));
    
    // Add project_id if available
    const projectId = '{{ $projectId }}';
    if (projectId) {
        formData.append('project_id', projectId);
    }
    
    // CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        formData.append('_token', csrfToken.getAttribute('content'));
    }

    // Upload URL
    fetch('/dashboard/knowledge-base/fetch-url', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        clearInterval(progressInterval);
        if (urlProgressBar) {
            urlProgressBar.style.width = '100%';
        }
        
        setTimeout(() => {
            if (urlFetchProgress) {
                urlFetchProgress.classList.add('hidden');
            }
            if (data.success) {
                showResults(data);
                // Clear form
                document.getElementById('kb-name').value = '';
                document.getElementById('url-input').value = '';
                selectedUrl = null;
                selectedUrlContentType = null;
                selectedKbName = null;
                selectedKbDescription = null;
                fieldMappings = [];
                detectedFields = [];
                // Reload page after success
                setTimeout(() => location.reload(), 2000);
            } else {
                let errorMessage = 'Hata: ' + data.message;
                if (data.errors) {
                    errorMessage += '\nDetaylar: ' + JSON.stringify(data.errors, null, 2);
                }
                alert(errorMessage);
            }
        }, 500);
    })
    .catch(error => {
        clearInterval(progressInterval);
        if (urlFetchProgress) {
            urlFetchProgress.classList.add('hidden');
        }
        alert('URL\'den içerik çekilirken hata oluştu: ' + error.message);
        
        // Clear URL variables on error
        selectedUrl = null;
        selectedUrlContentType = null;
        selectedKbName = null;
        selectedKbDescription = null;
        fieldMappings = [];
        detectedFields = [];
    });
}

// Field Mapping Modal Functions
function closeFieldMappingModal() {
    const modal = document.getElementById('fieldMappingModal');
    if (modal) {
        modal.classList.add('hidden');
    }
    
}

</script>
@endsection

