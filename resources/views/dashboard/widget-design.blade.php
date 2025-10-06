@extends('layouts.dashboard')

@section('title', 'Widget Tasarımı')

@section('content')
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

/* Custom Purple Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(31, 41, 55, 0.3);
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #8B5CF6, #A855F7);
    border-radius: 4px;
    transition: all 0.3s ease;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #7C3AED, #9333EA);
    box-shadow: 0 0 10px rgba(139, 92, 246, 0.5);
}

/* Firefox scrollbar */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #8B5CF6 rgba(31, 41, 55, 0.3);
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
</style>

<div class="space-y-6">
    <!-- Widget Design Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10 slide-in-up">
            <h1 class="text-4xl font-bold mb-4">
                <span class="gradient-text">Widget Tasarımı</span>
            </h1>
            <p class="text-xl text-gray-300">
                Widget tasarımı ve API ayarlarını buradan yönetebilirsiniz
            </p>
        </div>
    </div>


    <!-- Usage Instructions Modal -->
    <div id="usageModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="glass-effect rounded-2xl p-8 max-w-2xl w-full max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-white">
                    <span class="gradient-text">Nasıl Kullanılır?</span>
                </h3>
                <button id="closeUsageModal" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-6">
                <!-- Step by Step Instructions -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-white mb-3">Adım Adım Kurulum</h4>
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3 p-4 bg-gray-800/50 rounded-lg border border-gray-700">
                            <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-r from-purple-glow to-neon-purple rounded-full flex items-center justify-center text-white font-bold text-sm">1</div>
                            <div class="text-gray-300">
                                <p class="font-medium mb-1">HTML dosyanızı açın</p>
                                <p class="text-sm">Web sitenizin ana HTML dosyasını düzenleyici ile açın</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3 p-4 bg-gray-800/50 rounded-lg border border-gray-700">
                            <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-r from-purple-glow to-neon-purple rounded-full flex items-center justify-center text-white font-bold text-sm">2</div>
                            <div class="text-gray-300">
                                <p class="font-medium mb-1">Kodu yerleştirin</p>
                                <p class="text-sm">Yukarıdaki embed kodunu <code class="bg-gray-700 px-2 py-1 rounded text-purple-300">&lt;/head&gt;</code> etiketinden önce veya <code class="bg-gray-700 px-2 py-1 rounded text-purple-300">&lt;/body&gt;</code> etiketinden önce yapıştırın</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3 p-4 bg-gray-800/50 rounded-lg border border-gray-700">
                            <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-r from-purple-glow to-neon-purple rounded-full flex items-center justify-center text-white font-bold text-sm">3</div>
                            <div class="text-gray-300">
                                <p class="font-medium mb-1">Dosyayı kaydedin</p>
                                <p class="text-sm">HTML dosyanızı kaydedin ve web sitenizi yenileyin</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3 p-4 bg-gray-800/50 rounded-lg border border-gray-700">
                            <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-r from-purple-glow to-neon-purple rounded-full flex items-center justify-center text-white font-bold text-sm">4</div>
                            <div class="text-gray-300">
                                <p class="font-medium mb-1">Widget'ı kontrol edin</p>
                                <p class="text-sm">Widget otomatik olarak sayfanızın sağ alt köşesinde görünecektir</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Important Notes -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-white mb-3">Önemli Bilgiler</h4>
                    
                    <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-yellow-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-yellow-400 font-medium mb-2">⚠️ Dikkat!</h5>
                                <p class="text-yellow-300 text-sm">Bu kodu kopyaladıktan sonra, web sitenizin HTML dosyasına yapıştırın. Widget'ın çalışması için internet bağlantısı gereklidir.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-blue-400 font-medium mb-2">ℹ️ Önemli Not</h5>
                                <p class="text-blue-300 text-sm">Bu embed kodu localhost gibi yerel alanlarda çalışmayabilir. Canlı web sitenizde test edin.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-green-400 font-medium mb-2">✅ İpucu</h5>
                                <p class="text-green-300 text-sm">Kodu her sayfada tekrarlamanıza gerek yok, sadece ana sayfaya eklemeniz yeterlidir. Widget tüm sayfalarda çalışacaktır.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end mt-8">
                <button id="closeUsageModalBtn" class="px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-300">
                    Anladım
                </button>
            </div>
        </div>
    </div>

    <!-- API Example Modal -->
    <div id="apiExampleModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="glass-effect rounded-2xl p-8 max-w-4xl w-full max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-white">
                    <span class="gradient-text" id="apiModalTitle">API JSON Örneği</span>
                </h3>
                <button id="closeApiModal" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-6">
                <!-- API Info -->
                <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-blue-400 font-medium mb-2">API Kullanım Bilgisi</h4>
                            <p class="text-blue-300 text-sm" id="apiUsageInfo">
                                Bu API endpoint'i için örnek JSON yapısı aşağıda gösterilmektedir.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- JSON Example -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-white">JSON Örneği</h4>
                    <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                        <pre id="apiJsonExample" class="text-green-400 font-mono text-sm overflow-x-auto whitespace-pre-wrap"></pre>
                    </div>
                </div>
                
                <!-- Copy Button -->
                <div class="flex justify-end">
                    <button id="copyApiJsonBtn" 
                            class="px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-300 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <span>JSON'u Kopyala</span>
                    </button>
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
                <h3 class="text-xl font-semibold text-white mb-2">Widget Ayarları Yükleniyor</h3>
                <p class="text-gray-400">Lütfen bekleyin...</p>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-64 bg-gray-700 rounded-full h-2 overflow-hidden">
                <div id="progressBar" class="bg-gradient-to-r from-purple-glow to-neon-purple h-2 rounded-full progress-animation" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <!-- Loading Skeleton (Hidden initially) -->
    <div id="skeletonState" class="hidden space-y-6">
        <!-- API Ayarları Skeleton -->
        <div class="glass-effect rounded-2xl p-8">
            <div class="animate-pulse">
                <div class="h-8 bg-gray-700 rounded-lg w-48 mb-6"></div>
                
                <!-- Sipariş Durumu Skeleton -->
                <div class="space-y-4 mb-6">
                    <div class="h-5 bg-gray-700 rounded w-64"></div>
                    <div class="flex space-x-3">
                        <div class="flex-1 h-12 bg-gray-700 rounded-lg"></div>
                        <div class="w-24 h-12 bg-gray-700 rounded-lg"></div>
                    </div>
                    <div class="h-4 bg-gray-700 rounded w-80"></div>
                </div>
                
                <!-- Kargo Durumu Skeleton -->
                <div class="space-y-4 mb-6">
                    <div class="h-5 bg-gray-700 rounded w-64"></div>
                    <div class="flex space-x-3">
                        <div class="flex-1 h-12 bg-gray-700 rounded-lg"></div>
                        <div class="w-24 h-12 bg-gray-700 rounded-lg"></div>
                    </div>
                    <div class="h-4 bg-gray-700 rounded w-80"></div>
                </div>
                
                <!-- Info Box Skeleton -->
                <div class="h-16 bg-gray-700 rounded-lg mb-6"></div>
                
                <!-- Button Skeleton -->
                <div class="flex justify-end">
                    <div class="w-32 h-12 bg-gray-700 rounded-lg"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Container (Hidden initially) -->
    <div id="contentContainer" class="hidden space-y-6 slide-in-up">
        <!-- API Ayarları Container -->
        <div class="glass-effect rounded-2xl p-8">
            <h2 class="text-2xl font-bold mb-6 text-white">API Ayarları</h2>
            
            <!-- Success Message -->
            <div id="successMessage" class="hidden mb-4 p-3 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400 text-sm">
                Ayarlar başarıyla kaydedildi!
            </div>
            
            <!-- Error Message -->
            <div id="errorMessage" class="hidden mb-4 p-3 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 text-sm">
                Hata oluştu!
            </div>
            
            <!-- Test Result Modal -->
            <div id="testResultModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-200">
                        <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">API Test Sonucu</h3>
                        <button onclick="closeTestModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Modal Content -->
                    <div id="modalContent" class="p-6">
                        <!-- Content will be dynamically inserted here -->
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="flex justify-end p-6 border-t border-gray-200">
                        <button onclick="closeTestModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Kapat
                        </button>
                    </div>
                </div>
            </div>
            
            <form id="widgetApiForm" class="space-y-6">
                @csrf
                
                <!-- AI Asistan Özelleştirme -->
                <div class="glass-effect rounded-2xl p-6 space-y-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">AI Asistan Özelleştirme</h3>
                            <p class="text-gray-400 text-sm">Asistanınızın kişiliğini ve mesajlarını özelleştirin</p>
                        </div>
                    </div>
                    
                    <!-- AI Adı -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="ai_name" class="block text-sm font-medium text-gray-300 mb-2">
                                AI Asistan Adı
                            </label>
                            <input 
                                type="text" 
                                id="ai_name" 
                                name="ai_name"
                                placeholder="Örn: Müşteri Hizmetleri Asistanı"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                            >
                        </div>
                        
                        
                        <!-- Bildirim Mesajı -->
                        <div>
                            <label for="notification_message" class="block text-sm font-medium text-gray-300 mb-2">
                                Bildirim Mesajı
                            </label>
                            <input 
                                type="text" 
                                id="notification_message" 
                                name="notification_message"
                                placeholder="Sizin için kampanyamız var!"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                            >
                        </div>
                    </div>
                    
                    <!-- Hoş Geldin Mesajı -->
                    <div>
                        <label for="welcome_message" class="block text-sm font-medium text-gray-300 mb-2">
                            Hoş Geldin Mesajı
                        </label>
                        <textarea 
                            id="welcome_message" 
                            name="welcome_message"
                            rows="3"
                            placeholder="Merhaba! Size nasıl yardımcı olabilirim?"
                            class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 resize-none"
                        ></textarea>
                    </div>
                </div>
                
                <!-- Mesaj Özelleştirme -->
                <div class="glass-effect rounded-2xl p-6 space-y-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Mesaj Özelleştirme</h3>
                            <p class="text-gray-400 text-sm">Hata ve durum mesajlarını özelleştirin</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kargo Bulunamadı Mesajı -->
                        <div>
                            <label for="cargo_not_found_message" class="block text-sm font-medium text-gray-300 mb-2">
                                Kargo Bulunamadı Mesajı
                            </label>
                            <textarea 
                                id="cargo_not_found_message" 
                                name="cargo_not_found_message"
                                rows="2"
                                placeholder="Kargo numarası ile kargo bulunamadı"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 resize-none"
                            ></textarea>
                        </div>
                        
                        <!-- Özellik Kapalı Mesajı -->
                        <div>
                            <label for="feature_disabled_message" class="block text-sm font-medium text-gray-300 mb-2">
                                Özellik Kapalı Mesajı
                            </label>
                            <textarea 
                                id="feature_disabled_message" 
                                name="feature_disabled_message"
                                rows="2"
                                placeholder="Bu özellik yakında açılacak"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 resize-none"
                            ></textarea>
                        </div>
                        
                        <!-- Hata Mesajı -->
                        <div>
                            <label for="error_message_template" class="block text-sm font-medium text-gray-300 mb-2">
                                Hata Mesajı
                            </label>
                            <textarea 
                                id="error_message_template" 
                                name="error_message_template"
                                rows="2"
                                placeholder="Bir sorun oluştu, lütfen daha sonra tekrar deneyin"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 resize-none"
                            ></textarea>
                        </div>
                        
                        <!-- Sipariş Bulunamadı Mesajı -->
                        <div>
                            <label for="order_not_found_message" class="block text-sm font-medium text-gray-300 mb-2">
                                Sipariş Bulunamadı Mesajı
                            </label>
                            <textarea 
                                id="order_not_found_message" 
                                name="order_not_found_message"
                                rows="2"
                                placeholder="Sipariş numarası ile sipariş bulunamadı"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 resize-none"
                            ></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Tema ve Görünüm -->
                <div class="glass-effect rounded-2xl p-6 space-y-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Tema ve Görünüm</h3>
                            <p class="text-gray-400 text-sm">Widget'ınızın görünümünü özelleştirin</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Ana Renk -->
                        <div>
                            <label for="primary_color" class="block text-sm font-medium text-gray-300 mb-2">
                                Ana Renk
                            </label>
                            <div class="flex space-x-2">
                                <input 
                                    type="color" 
                                    id="primary_color" 
                                    name="primary_color"
                                    value="#3B82F6"
                                    class="w-12 h-12 bg-gray-800 border border-gray-600 rounded-lg cursor-pointer"
                                >
                                <input 
                                    type="text" 
                                    id="primary_color_text" 
                                    value="#3B82F6"
                                    placeholder="#3B82F6"
                                    class="flex-1 px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                >
                            </div>
                        </div>
                        
                        <!-- İkincil Renk -->
                        <div>
                            <label for="secondary_color" class="block text-sm font-medium text-gray-300 mb-2">
                                İkincil Renk
                            </label>
                            <div class="flex space-x-2">
                                <input 
                                    type="color" 
                                    id="secondary_color" 
                                    name="secondary_color"
                                    value="#6B7280"
                                    class="w-12 h-12 bg-gray-800 border border-gray-600 rounded-lg cursor-pointer"
                                >
                                <input 
                                    type="text" 
                                    id="secondary_color_text" 
                                    value="#6B7280"
                                    placeholder="#6B7280"
                                    class="flex-1 px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                >
                            </div>
                        </div>
                        
                        <!-- Tema Rengi -->
                        <div>
                            <label for="theme_color" class="block text-sm font-medium text-gray-300 mb-2">
                                Tema Rengi
                            </label>
                            <div class="flex space-x-2">
                                <input 
                                    type="color" 
                                    id="theme_color" 
                                    name="theme_color"
                                    value="#3B82F6"
                                    class="w-12 h-12 bg-gray-800 border border-gray-600 rounded-lg cursor-pointer"
                                >
                                <input 
                                    type="text" 
                                    id="theme_color_text" 
                                    value="#3B82F6"
                                    placeholder="#3B82F6"
                                    class="flex-1 px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                >
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Font Ailesi -->
                        <div>
                            <label for="font_family" class="block text-sm font-medium text-gray-300 mb-2">
                                Font Ailesi
                            </label>
                            <select 
                                id="font_family" 
                                name="font_family"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                            >
                                <option value="Inter">Inter</option>
                                <option value="Arial, sans-serif">Arial</option>
                                <option value="Helvetica, sans-serif">Helvetica</option>
                                <option value="Georgia, serif">Georgia</option>
                                <option value="Times New Roman, serif">Times New Roman</option>
                                <option value="Roboto, sans-serif">Roboto</option>
                            </select>
                        </div>
                        
                        <!-- Logo URL -->
                        <div>
                            <label for="logo_url" class="block text-sm font-medium text-gray-300 mb-2">
                                Logo URL
                            </label>
                            <input 
                                type="url" 
                                id="logo_url" 
                                name="logo_url"
                                placeholder="https://example.com/logo.png"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                            >
                        </div>
                    </div>
                </div>
                
                <!-- Bildirim Widget'ı -->
                <div class="glass-effect rounded-2xl p-6 space-y-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-pink-500 to-rose-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v2H4a2 2 0 01-2-2V5a2 2 0 012-2h6v2H4v14zM12 3h8a2 2 0 012 2v8M12 3L8 7l4 4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Bildirim Widget'ı</h3>
                            <p class="text-gray-400 text-sm">Kullanıcılara özel bildirimler gönderin</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Bildirim Aktif/Pasif -->
                        <div class="flex items-center space-x-3">
                            <input 
                                type="checkbox" 
                                id="notification_active" 
                                name="notification_active"
                                class="w-5 h-5 text-purple-600 bg-gray-800 border-gray-600 rounded focus:ring-purple-500 focus:ring-2"
                            >
                            <label for="notification_active" class="text-sm font-medium text-gray-300">
                                Bildirim Widget'ı Aktif
                            </label>
                        </div>
                        
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Mesaj Metni -->
                        <div>
                            <label for="notification_message_text" class="block text-sm font-medium text-gray-300 mb-2">
                                Bildirim Mesajı
                            </label>
                            <input 
                                type="text" 
                                id="notification_message_text" 
                                name="notification_message_text"
                                placeholder="Sizin için kampanyamız var!"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                            >
                        </div>
                        
                        <!-- Görünürlük Süresi -->
                        <div>
                            <label for="notification_display_duration" class="block text-sm font-medium text-gray-300 mb-2">
                                Görünürlük Süresi (saniye)
                            </label>
                            <input 
                                type="number" 
                                id="notification_display_duration" 
                                name="notification_display_duration"
                                min="1"
                                max="30"
                                value="5"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                            >
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Animasyon Türü -->
                        <div>
                            <label for="notification_animation_type" class="block text-sm font-medium text-gray-300 mb-2">
                                Animasyon Türü
                            </label>
                            <select 
                                id="notification_animation_type" 
                                name="notification_animation_type"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                            >
                                <option value="fade-in">Fade In</option>
                                <option value="slide-in">Slide In</option>
                                <option value="bounce">Bounce</option>
                            </select>
                        </div>
                        
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <input 
                            type="checkbox" 
                            id="notification_show_close_button" 
                            name="notification_show_close_button"
                            checked
                            class="w-5 h-5 text-purple-600 bg-gray-800 border-gray-600 rounded focus:ring-purple-500 focus:ring-2"
                        >
                        <label for="notification_show_close_button" class="text-sm font-medium text-gray-300">
                            Kapatma Butonu Göster
                        </label>
                    </div>
                </div>

                <!-- Gelişmiş Ayarlar -->
                <div class="glass-effect rounded-2xl p-6 space-y-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Gelişmiş Ayarlar</h3>
                            <p class="text-gray-400 text-sm">API ve performans ayarlarını yapılandırın</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Rate Limit -->
                        <div>
                            <label for="rate_limit_per_minute" class="block text-sm font-medium text-gray-300 mb-2">
                                Dakikada İstek Limiti
                            </label>
                            <input 
                                type="number" 
                                id="rate_limit_per_minute" 
                                name="rate_limit_per_minute"
                                min="1"
                                max="100"
                                value="10"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                            >
                        </div>
                        
                        <!-- API Timeout -->
                        <div>
                            <label for="api_timeout_seconds" class="block text-sm font-medium text-gray-300 mb-2">
                                API Timeout (Saniye)
                            </label>
                            <input 
                                type="number" 
                                id="api_timeout_seconds" 
                                name="api_timeout_seconds"
                                min="1"
                                max="60"
                                value="10"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                            >
                        </div>
                        
                        <!-- Retry Attempts -->
                        <div>
                            <label for="max_retry_attempts" class="block text-sm font-medium text-gray-300 mb-2">
                                Maksimum Tekrar Deneme
                            </label>
                            <input 
                                type="number" 
                                id="max_retry_attempts" 
                                name="max_retry_attempts"
                                min="0"
                                max="5"
                                value="2"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                            >
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Typing Indicator -->
                        <div class="flex items-center space-x-3">
                            <input 
                                type="checkbox" 
                                id="enable_typing_indicator" 
                                name="enable_typing_indicator"
                                checked
                                class="w-5 h-5 text-purple-600 bg-gray-800 border-gray-600 rounded focus:ring-purple-500 focus:ring-2"
                            >
                            <label for="enable_typing_indicator" class="text-sm font-medium text-gray-300">
                                Yazıyor Göstergesi Aktif
                            </label>
                        </div>
                        
                        <!-- Sound Notifications -->
                        <div class="flex items-center space-x-3">
                            <input 
                                type="checkbox" 
                                id="enable_sound_notifications" 
                                name="enable_sound_notifications"
                                class="w-5 h-5 text-purple-600 bg-gray-800 border-gray-600 rounded focus:ring-purple-500 focus:ring-2"
                            >
                            <label for="enable_sound_notifications" class="text-sm font-medium text-gray-300">
                                Ses Bildirimleri Aktif
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Sipariş Durumu API -->
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <label for="siparis_durumu_endpoint" class="block text-sm font-medium text-gray-300">
                                    Sipariş Durumu API Endpoint
                                </label>
                                <button type="button" 
                                        onclick="console.log('Siparis button clicked'); showApiExampleModal('siparis')"
                                        class="text-blue-400 hover:text-blue-300 transition-colors"
                                        title="JSON Örneği Göster">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                            </div>
                            <!-- Aktif/Pasif Toggle -->
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-400">Aktif:</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        id="siparis_active_toggle" 
                                        name="siparis_active_toggle"
                                        class="sr-only peer"
                                    >
                                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <!-- Test Order Number Input -->
                            <input 
                                type="text" 
                                id="siparis_test_order" 
                                name="siparis_test_order"
                                placeholder="ORD123456789"
                                class="w-48 px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                title="Test için kullanılacak sipariş numarası"
                            >
                            
                            <!-- Endpoint URL Input -->
                            <input 
                                type="url" 
                                id="siparis_durumu_endpoint" 
                                name="siparis_durumu_endpoint"
                                placeholder="https://example.com/api/order-tracking"
                                class="flex-1 px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                            >
                            
                            <!-- Test Button -->
                            <button 
                                type="button"
                                id="testSiparisButton"
                                onclick="testEndpoint('siparis')"
                                class="px-6 py-3 bg-blue-600 hover:bg-blue-500 rounded-lg text-white font-semibold transition-all duration-200 flex items-center space-x-2"
                            >
                                <span id="testSiparisText">Test Et</span>
                                <div id="testSiparisSpinner" class="hidden">
                                    <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                </div>
                            </button>
                        </div>
                        <p class="text-sm text-gray-400 mt-1">
                            Sipariş durumu sorgulama için API endpoint'i. Sol taraftaki alana test sipariş numarası girebilirsiniz.
                        </p>
                    </div>
                </div>
                
                <!-- Kargo Durumu API -->
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <label for="kargo_durumu_endpoint" class="block text-sm font-medium text-gray-300">
                                    Kargo Durumu API Endpoint
                                </label>
                                <button type="button" 
                                        onclick="console.log('Kargo button clicked'); showApiExampleModal('kargo')"
                                        class="text-blue-400 hover:text-blue-300 transition-colors"
                                        title="JSON Örneği Göster">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                            </div>
                            <!-- Aktif/Pasif Toggle -->
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-400">Aktif:</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        id="kargo_active_toggle" 
                                        name="kargo_active_toggle"
                                        class="sr-only peer"
                                    >
                                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <!-- Test Tracking Number Input -->
                            <input 
                                type="text" 
                                id="kargo_test_tracking" 
                                name="kargo_test_tracking"
                                placeholder="TRK789456123"
                                class="w-48 px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                title="Test için kullanılacak tracking number"
                            >
                            
                            <!-- Endpoint URL Input -->
                            <input 
                                type="url" 
                                id="kargo_durumu_endpoint" 
                                name="kargo_durumu_endpoint"
                                placeholder="https://example.com/api/cargo-tracking"
                                class="flex-1 px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                            >
                            
                            <!-- Test Button -->
                            <button 
                                type="button"
                                id="testKargoButton"
                                onclick="testEndpoint('kargo')"
                                class="px-6 py-3 bg-blue-600 hover:bg-blue-500 rounded-lg text-white font-semibold transition-all duration-200 flex items-center space-x-2"
                            >
                                <span id="testKargoText">Test Et</span>
                                <div id="testKargoSpinner" class="hidden">
                                    <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                </div>
                            </button>
                        </div>
                        <p class="text-sm text-gray-400 mt-1">
                            Kargo durumu sorgulama için API endpoint'i. Sol taraftaki alana test tracking number girebilirsiniz.
                        </p>
                    </div>
                </div>
                
                <!-- HTTP Action Info -->
                <div class="p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-blue-400 text-sm">
                            Şu anda sadece GET işlemleri desteklenmektedir
                        </span>
                    </div>
                </div>
                
                            <!-- Save Button -->
            <div class="flex justify-end">
                <button 
                    type="submit"
                    id="saveButton"
                    class="px-8 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105 inline-flex items-center space-x-2"
                >
                    <span id="saveButtonText">Ayarları Kaydet</span>
                    <div id="saveButtonSpinner" class="hidden">
                        <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    </div>
                </button>
            </div>
            </form>
        </div>
    </div>

    <!-- Error State -->
    <div id="errorState" class="hidden glass-effect rounded-2xl p-8 fade-in-scale">
        <div class="flex flex-col items-center justify-center space-y-4">
            <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-white">İçerik Yüklenemedi</h3>
            <p class="text-gray-400 text-center">Widget ayarları yüklenirken bir hata oluştu. Lütfen sayfayı yenileyin.</p>
            <button 
                onclick="retryLoading()"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-500 rounded-lg text-white font-semibold transition-all duration-200"
            >
                Tekrar Dene
            </button>
        </div>
    </div>
</div>

<script>
// Global variables
let loadingProgress = 0;
let loadingInterval;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    startLoading();
    loadContent();
    
    // Sayfa yüklendiğinde tüm endpoint section'larını kapalı yap
    setTimeout(() => {
        toggleEndpointSection('siparis', false);
        toggleEndpointSection('kargo', false);
    }, 1000);
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
            }, 100);
        }, 800); // Show skeleton for 800ms
    }, 500);
}

// Load content from server
async function loadContent() {
    try {
        const projectId = '{{ $projectId }}';
        const url = '{{ route("dashboard.widget-design.load-content") }}' + (projectId ? `?project_id=${projectId}` : '');
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Populate form fields with loaded data
            populateFormFields(result.data);
            completeLoading();
        } else {
            throw new Error(result.message || 'İçerik yüklenemedi');
        }
        
    } catch (error) {
        console.error('Loading error:', error);
        showErrorState();
    }
}

// Populate form fields with loaded data
function populateFormFields(data) {
    // Widget Customization verilerini populate et
    if (data.widgetCustomization) {
        const customization = data.widgetCustomization;
        
        // AI Asistan Özelleştirme
        if (customization.ai_name) {
            document.getElementById('ai_name').value = customization.ai_name;
        }
        if (customization.welcome_message) {
            document.getElementById('welcome_message').value = customization.welcome_message;
        }
        if (customization.notification_message) {
            document.getElementById('notification_message').value = customization.notification_message;
        }
        
        // Mesaj Özelleştirme
        if (customization.cargo_not_found_message) {
            document.getElementById('cargo_not_found_message').value = customization.cargo_not_found_message;
        }
        if (customization.feature_disabled_message) {
            document.getElementById('feature_disabled_message').value = customization.feature_disabled_message;
        }
        if (customization.error_message_template) {
            document.getElementById('error_message_template').value = customization.error_message_template;
        }
        if (customization.order_not_found_message) {
            document.getElementById('order_not_found_message').value = customization.order_not_found_message;
        }
        
        // Tema ve Görünüm
        if (customization.primary_color) {
            document.getElementById('primary_color').value = customization.primary_color;
            document.getElementById('primary_color_text').value = customization.primary_color;
        }
        if (customization.secondary_color) {
            document.getElementById('secondary_color').value = customization.secondary_color;
            document.getElementById('secondary_color_text').value = customization.secondary_color;
        }
        if (customization.theme_color) {
            document.getElementById('theme_color').value = customization.theme_color;
            document.getElementById('theme_color_text').value = customization.theme_color;
        }
        if (customization.font_family) {
            document.getElementById('font_family').value = customization.font_family;
        }
        if (customization.logo_url) {
            document.getElementById('logo_url').value = customization.logo_url;
        }
        
        // Gelişmiş Ayarlar
        if (customization.rate_limit_per_minute) {
            document.getElementById('rate_limit_per_minute').value = customization.rate_limit_per_minute;
        }
        if (customization.api_timeout_seconds) {
            document.getElementById('api_timeout_seconds').value = customization.api_timeout_seconds;
        }
        if (customization.max_retry_attempts) {
            document.getElementById('max_retry_attempts').value = customization.max_retry_attempts;
        }
        if (customization.enable_typing_indicator !== undefined) {
            document.getElementById('enable_typing_indicator').checked = customization.enable_typing_indicator;
        }
        if (customization.enable_sound_notifications !== undefined) {
            document.getElementById('enable_sound_notifications').checked = customization.enable_sound_notifications;
        }
    }
    
    // Notification Widget verilerini populate et
    if (data.notificationWidget) {
        const notification = data.notificationWidget;
        
        if (notification.is_active !== undefined) {
            document.getElementById('notification_active').checked = notification.is_active;
        }
        if (notification.message_text) {
            document.getElementById('notification_message_text').value = notification.message_text;
        }
        if (notification.display_duration) {
            document.getElementById('notification_display_duration').value = Math.round(notification.display_duration / 1000);
        }
        if (notification.animation_type) {
            document.getElementById('notification_animation_type').value = notification.animation_type;
        }
        if (notification.show_close_button !== undefined) {
            document.getElementById('notification_show_close_button').checked = notification.show_close_button;
        }
    }
    
    // Widget Actions verilerini populate et
    if (data.widgetActions) {
        // Yeni yapıda widgetActions bir array olabilir
        const actions = Array.isArray(data.widgetActions) ? data.widgetActions : [data.widgetActions];
        
        actions.forEach(action => {
            if (action.type === 'siparis_durumu_endpoint') {
                if (action.endpoint) {
                    document.getElementById('siparis_durumu_endpoint').value = action.endpoint;
                }
                // Varsayılan olarak kapalı, sadece hem endpoint var hem de aktif ise aç
                const isActive = action.endpoint && action.is_active;
                document.getElementById('siparis_active_toggle').checked = isActive;
                toggleEndpointSection('siparis', isActive);
            }
            if (action.type === 'kargo_durumu_endpoint') {
                if (action.endpoint) {
                    document.getElementById('kargo_durumu_endpoint').value = action.endpoint;
                }
                // Varsayılan olarak kapalı, sadece hem endpoint var hem de aktif ise aç
                const isActive = action.endpoint && action.is_active;
                document.getElementById('kargo_active_toggle').checked = isActive;
                toggleEndpointSection('kargo', isActive);
            }
        });
    }
    
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
    loadContent();
}

// Form submission
document.getElementById('widgetApiForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Show loading state
    const saveButton = document.getElementById('saveButton');
    const saveButtonText = document.getElementById('saveButtonText');
    const saveButtonSpinner = document.getElementById('saveButtonSpinner');
    
    saveButton.disabled = true;
    saveButtonText.textContent = 'Kaydediliyor...';
    saveButtonSpinner.classList.remove('hidden');
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    // Add project_id from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const projectId = urlParams.get('project_id');
    if (projectId) {
        data.project_id = projectId;
    }
    
    // Debug: Form verilerini console'a yazdır
    console.log('📝 Form Data:', data);
    
    try {
        const response = await fetch('{{ route("dashboard.widget-design.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage('successMessage', result.message);
        } else {
            showMessage('errorMessage', result.message);
        }
    } catch (error) {
        showMessage('errorMessage', 'Bir hata oluştu: ' + error.message);
    } finally {
        // Reset button state
        saveButton.disabled = false;
        saveButtonText.textContent = 'Ayarları Kaydet';
        saveButtonSpinner.classList.add('hidden');
    }
});

// Renk picker'ları senkronize et
document.addEventListener('DOMContentLoaded', function() {
    // Primary color sync
    const primaryColorPicker = document.getElementById('primary_color');
    const primaryColorText = document.getElementById('primary_color_text');
    
    primaryColorPicker.addEventListener('input', function() {
        primaryColorText.value = this.value;
    });
    
    primaryColorText.addEventListener('input', function() {
        if (this.value.match(/^#[0-9A-F]{6}$/i)) {
            primaryColorPicker.value = this.value;
        }
    });
    
    // Secondary color sync
    const secondaryColorPicker = document.getElementById('secondary_color');
    const secondaryColorText = document.getElementById('secondary_color_text');
    
    secondaryColorPicker.addEventListener('input', function() {
        secondaryColorText.value = this.value;
    });
    
    secondaryColorText.addEventListener('input', function() {
        if (this.value.match(/^#[0-9A-F]{6}$/i)) {
            secondaryColorPicker.value = this.value;
        }
    });
    
    // Theme color sync
    const themeColorPicker = document.getElementById('theme_color');
    const themeColorText = document.getElementById('theme_color_text');
    
    themeColorPicker.addEventListener('input', function() {
        themeColorText.value = this.value;
    });
    
    themeColorText.addEventListener('input', function() {
        if (this.value.match(/^#[0-9A-F]{6}$/i)) {
            themeColorPicker.value = this.value;
        }
    });
});

// Test endpoint function
async function testEndpoint(type) {
    const endpointInput = document.getElementById(type === 'siparis' ? 'siparis_durumu_endpoint' : 'kargo_durumu_endpoint');
    const endpoint = endpointInput.value.trim();
    
    if (!endpoint) {
        showMessage('errorMessage', 'Lütfen önce endpoint URL\'ini girin');
        return;
    }
    
    // Get test number for both cargo and order tests
    let testNumber = '';
    if (type === 'kargo') {
        const trackingInput = document.getElementById('kargo_test_tracking');
        testNumber = trackingInput ? trackingInput.value.trim() : '';
    } else if (type === 'siparis') {
        const orderInput = document.getElementById('siparis_test_order');
        testNumber = orderInput ? orderInput.value.trim() : '';
    }
    
    // Show loading state
    const button = document.getElementById(type === 'siparis' ? 'testSiparisButton' : 'testKargoButton');
    const buttonText = document.getElementById(type === 'siparis' ? 'testSiparisText' : 'testKargoText');
    const buttonSpinner = document.getElementById(type === 'siparis' ? 'testSiparisSpinner' : 'testKargoSpinner');
    
    button.disabled = true;
    buttonText.textContent = 'Test Ediliyor...';
    buttonSpinner.classList.remove('hidden');
    
    try {
        const response = await fetch('{{ route("dashboard.widget-design.test-endpoint") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                endpoint: endpoint,
                type: type,
                tracking_number: testNumber
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // HTTP 200 başarılı olduğunda switch'i otomatik olarak aktif et
            const toggle = document.getElementById(type === 'siparis' ? 'siparis_active_toggle' : 'kargo_active_toggle');
            if (toggle) {
                toggle.checked = true;
                // Endpoint section'ı aktif hale getir
                toggleEndpointSection(type, true);
            }
            showTestModal('success', result, type);
        } else {
            showTestModal('error', result, type);
        }
    } catch (error) {
        showMessage('errorMessage', 'Test sırasında hata oluştu: ' + error.message);
    } finally {
        // Reset button state
        button.disabled = false;
        buttonText.textContent = 'Test Et';
        buttonSpinner.classList.add('hidden');
    }
}

// Show message function
function showMessage(elementId, message) {
    const element = document.getElementById(elementId);
    element.textContent = message;
    element.classList.remove('hidden');
    
    setTimeout(() => {
        element.classList.add('hidden');
    }, 5000);
}

// Show test result modal
function showTestModal(type, result, apiType) {
    const modal = document.getElementById('testResultModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    
    if (type === 'success') {
        modalTitle.textContent = `${apiType === 'siparis' ? 'Sipariş' : 'Kargo'} API Test Başarılı`;
        modalContent.innerHTML = generateSuccessModalContent(result, apiType);
    } else {
        modalTitle.textContent = `${apiType === 'siparis' ? 'Sipariş' : 'Kargo'} API Test Hatası`;
        modalContent.innerHTML = generateErrorModalContent(result, apiType);
    }
    
    modal.classList.remove('hidden');
}

// Close test modal
function closeTestModal() {
    const modal = document.getElementById('testResultModal');
    modal.classList.add('hidden');
}

// Generate success modal content (React UI kopyası)
function generateSuccessModalContent(result, apiType) {
    const data = result.data || {};
    const testNumber = data.tracking_number || 'N/A';
    const endpoint = data.endpoint || 'N/A';
    const status = data.status || 'N/A';
    
    return `
        <div class="space-y-6">
            <!-- Test Info -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center space-x-2 mb-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <h4 class="text-green-800 font-semibold">Test Başarılı</h4>
                </div>
                <div class="space-y-2 text-sm text-green-700">
                    <div><strong>Endpoint:</strong> ${endpoint}</div>
                    <div><strong>Test ${apiType === 'siparis' ? 'Sipariş' : 'Tracking'} No:</strong> ${testNumber}</div>
                    <div><strong>HTTP Status:</strong> ${status}</div>
                </div>
            </div>
            
            <!-- Auto Enable Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center space-x-2 mb-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h4 class="text-blue-800 font-semibold">Otomatik Aktivasyon</h4>
                </div>
                <p class="text-sm text-blue-700">
                    ✅ ${apiType === 'siparis' ? 'Sipariş' : 'Kargo'} API'si başarıyla test edildi ve otomatik olarak aktif edildi.
                </p>
            </div>
            
            
            <!-- Cargo Tracking UI (React'ten kopyalanan) -->
            ${apiType === 'kargo' ? generateCargoTrackingUI(data) : generateOrderTrackingUI(data)}
        </div>
    `;
}

// Generate error modal content
function generateErrorModalContent(result, apiType) {
    const data = result.data || {};
    const testNumber = data.tracking_number || 'N/A';
    const endpoint = data.endpoint || 'N/A';
    const status = data.status || 'N/A';
    
    return `
        <div class="space-y-6">
            <!-- Error Info -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center space-x-2 mb-3">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <h4 class="text-red-800 font-semibold">Test Başarısız</h4>
                </div>
                <div class="space-y-2 text-sm text-red-700">
                    <div><strong>Hata:</strong> ${result.message}</div>
                    <div><strong>Endpoint:</strong> ${endpoint}</div>
                    <div><strong>Test ${apiType === 'siparis' ? 'Sipariş' : 'Tracking'} No:</strong> ${testNumber}</div>
                    <div><strong>HTTP Status:</strong> ${status}</div>
                </div>
            </div>
            
        </div>
    `;
}

// Generate cargo tracking UI (React OrderMessage'dan kopyalanan)
function generateCargoTrackingUI(data) {
    return `
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h5 class="font-semibold text-blue-800 mb-4">Kargo Takip UI Önizlemesi</h5>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Sipariş No:</span>
                    <span class="text-sm text-gray-900">${data.tracking_number || 'TEST123456789'}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Durum:</span>
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded">Yolda</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Kargo Firması:</span>
                    <span class="text-sm text-gray-900">Test Kargo</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Takip Numarası:</span>
                    <span class="text-sm font-mono text-gray-900">${data.tracking_number || 'TRK789456123'}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Son Güncelleme:</span>
                    <span class="text-sm text-gray-900">${new Date().toLocaleString('tr-TR')}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Tahmini Teslim:</span>
                    <span class="text-sm text-gray-900">${new Date(Date.now() + 2 * 24 * 60 * 60 * 1000).toLocaleDateString('tr-TR')}</span>
                </div>
            </div>
        </div>
    `;
}

// Generate order tracking UI (React OrderMessage'dan kopyalanan)
function generateOrderTrackingUI(data) {
    return `
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <h5 class="font-semibold text-purple-800 mb-4">Sipariş Takip UI Önizlemesi</h5>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Sipariş No:</span>
                    <span class="text-sm text-gray-900">${data.tracking_number || 'ORD123456789'}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Durum:</span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded">Hazırlanıyor</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Sipariş Tarihi:</span>
                    <span class="text-sm text-gray-900">${new Date().toLocaleDateString('tr-TR')}</span>
                </div>
            </div>
        </div>
    `;
}

// Toggle endpoint section based on state
function toggleEndpointSection(type, isActive) {
    const endpointInput = document.getElementById(type === 'siparis' ? 'siparis_durumu_endpoint' : 'kargo_durumu_endpoint');
    const testButton = document.getElementById(type === 'siparis' ? 'testSiparisButton' : 'testKargoButton');
    const testInput = document.getElementById(type === 'siparis' ? 'siparis_test_order' : 'kargo_test_tracking');
    const toggle = document.getElementById(type === 'siparis' ? 'siparis_active_toggle' : 'kargo_active_toggle');
    const section = endpointInput.closest('.space-y-4');
    
    if (isActive) {
        // Aktif durumda - input'ları etkinleştir
        endpointInput.disabled = false;
        testButton.disabled = false;
        testInput.disabled = false;
        toggle.checked = true;
        
        // Input'ları normal renge çevir
        endpointInput.classList.remove('bg-gray-600', 'text-gray-400');
        endpointInput.classList.add('bg-gray-800', 'text-white');
        testInput.classList.remove('bg-gray-600', 'text-gray-400');
        testInput.classList.add('bg-gray-800', 'text-white');
        
        // Test butonunu normal renge çevir
        testButton.classList.remove('bg-gray-600', 'cursor-not-allowed');
        testButton.classList.add('bg-blue-600', 'hover:bg-blue-500');
        
        // Açıklama metnini güncelle
        const description = section.querySelector('p.text-sm.text-gray-400');
        if (description) {
            if (type === 'siparis') {
                description.textContent = 'Sipariş durumu sorgulama için API endpoint\'i. Sol taraftaki alana test sipariş numarası girebilirsiniz.';
            } else {
                description.textContent = 'Kargo durumu sorgulama için API endpoint\'i. Sol taraftaki alana test tracking number girebilirsiniz.';
            }
        }
    } else {
        // Pasif durumda - input'ları devre dışı bırak
        endpointInput.disabled = true;
        testButton.disabled = true;
        testInput.disabled = true;
        toggle.checked = false;
        
        // Input'ları gri renge çevir
        endpointInput.classList.remove('bg-gray-800', 'text-white');
        endpointInput.classList.add('bg-gray-600', 'text-gray-400');
        testInput.classList.remove('bg-gray-800', 'text-white');
        testInput.classList.add('bg-gray-600', 'text-gray-400');
        
        // Test butonunu gri renge çevir
        testButton.classList.remove('bg-blue-600', 'hover:bg-blue-500');
        testButton.classList.add('bg-gray-600', 'cursor-not-allowed');
        
        // Açıklama metnini güncelle
        const description = section.querySelector('p.text-sm.text-gray-400');
        if (description) {
            if (type === 'siparis') {
                description.textContent = 'Sipariş durumu API\'si henüz yapılandırılmamış. Endpoint URL\'i girerek özelliği aktifleştirin.';
            } else {
                description.textContent = 'Kargo durumu API\'si henüz yapılandırılmamış. Endpoint URL\'i girerek özelliği aktifleştirin.';
            }
        }
    }
}

// Add event listeners for toggle changes
document.addEventListener('DOMContentLoaded', function() {
    // Inputları başlangıçta aktif hale getir
    toggleEndpointSection('siparis', true);
    toggleEndpointSection('kargo', true);
    
    // Toggle'ları da başlangıçta aktif yap
    const siparisToggle = document.getElementById('siparis_active_toggle');
    const kargoToggle = document.getElementById('kargo_active_toggle');
    if (siparisToggle) siparisToggle.checked = true;
    if (kargoToggle) kargoToggle.checked = true;
    
    // Sipariş durumu toggle
    if (siparisToggle) {
        siparisToggle.addEventListener('change', function() {
            toggleEndpointSection('siparis', this.checked);
        });
    }
    
    // Kargo durumu toggle
    if (kargoToggle) {
        kargoToggle.addEventListener('change', function() {
            toggleEndpointSection('kargo', this.checked);
        });
    }
    
    // Endpoint input change listeners
    const siparisEndpoint = document.getElementById('siparis_durumu_endpoint');
    if (siparisEndpoint) {
        siparisEndpoint.addEventListener('input', function() {
            // Sadece toggle durumunu kontrol et, endpoint varlığını kontrol etme
            toggleEndpointSection('siparis', siparisToggle.checked);
        });
    }
    
    const kargoEndpoint = document.getElementById('kargo_durumu_endpoint');
    if (kargoEndpoint) {
        kargoEndpoint.addEventListener('input', function() {
            // Sadece toggle durumunu kontrol et, endpoint varlığını kontrol etme
            toggleEndpointSection('kargo', kargoToggle.checked);
        });
    }
    
    
    
    // Close modal functions
    function closeModal() {
        usageModal.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restore scrolling
    }
    
    if (closeUsageModal) {
        closeUsageModal.addEventListener('click', closeModal);
    }
    
    if (closeUsageModalBtn) {
        closeUsageModalBtn.addEventListener('click', closeModal);
    }
    
    // Close modal when clicking outside
    if (usageModal) {
        usageModal.addEventListener('click', function(e) {
            if (e.target === usageModal) {
                closeModal();
            }
        });
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !usageModal.classList.contains('hidden')) {
            closeModal();
        }
    });
    
    // API Example Modal Functions
    const apiExampleModal = document.getElementById('apiExampleModal');
    const closeApiModal = document.getElementById('closeApiModal');
    const copyApiJsonBtn = document.getElementById('copyApiJsonBtn');
    
    // Close API modal functions
    function closeApiModalFunc() {
        if (apiExampleModal) {
            apiExampleModal.style.display = 'none';
        }
        document.body.style.overflow = 'auto';
    }
    
    if (closeApiModal) {
        closeApiModal.addEventListener('click', closeApiModalFunc);
    }
    
    if (copyApiJsonBtn) {
        copyApiJsonBtn.addEventListener('click', function() {
            const jsonContent = document.getElementById('apiJsonExample').textContent;
            navigator.clipboard.writeText(jsonContent).then(function() {
                const originalText = copyApiJsonBtn.querySelector('span').textContent;
                copyApiJsonBtn.querySelector('span').textContent = 'Kopyalandı!';
                copyApiJsonBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                copyApiJsonBtn.classList.remove('from-purple-glow', 'to-neon-purple', 'hover:from-purple-600', 'hover:to-purple-700');
                
                setTimeout(function() {
                    copyApiJsonBtn.querySelector('span').textContent = originalText;
                    copyApiJsonBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                    copyApiJsonBtn.classList.add('from-purple-glow', 'to-neon-purple', 'hover:from-purple-600', 'hover:to-purple-700');
                }, 2000);
            }).catch(function(err) {
                console.error('JSON kopyalanamadı: ', err);
                alert('JSON kopyalanamadı. Lütfen manuel olarak kopyalayın.');
            });
        });
    }
    
    // Close API modal when clicking outside
    if (apiExampleModal) {
        apiExampleModal.addEventListener('click', function(e) {
            if (e.target === apiExampleModal) {
                closeApiModalFunc();
            }
        });
    }
    
    // Close API modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && apiExampleModal && apiExampleModal.style.display !== 'none') {
            closeApiModalFunc();
        }
    });
    
});

// Show API Example Modal - Global function
window.showApiExampleModal = function(type) {
    console.log('showApiExampleModal called with type:', type);
    
    const modal = document.getElementById('apiExampleModal');
    const modalTitle = document.getElementById('apiModalTitle');
    const apiUsageInfo = document.getElementById('apiUsageInfo');
    const apiJsonExample = document.getElementById('apiJsonExample');
    
    console.log('Modal elements found:', {
        modal: !!modal,
        modalTitle: !!modalTitle,
        apiUsageInfo: !!apiUsageInfo,
        apiJsonExample: !!apiJsonExample
    });
    
    if (!modal) {
        console.error('API Example Modal not found!');
        alert('Modal bulunamadı. Sayfayı yenileyin.');
        return;
    }
    
    if (type === 'siparis') {
        modalTitle.textContent = 'Sipariş Durumu API JSON Örneği';
        apiUsageInfo.textContent = 'Sipariş durumu sorgulama API endpoint\'i için örnek JSON yapısı. Bu yapıyı kullanarak API\'nizi test edebilirsiniz.';
        apiJsonExample.textContent = window.getSiparisApiExample();
    } else if (type === 'kargo') {
        modalTitle.textContent = 'Kargo Durumu API JSON Örneği';
        apiUsageInfo.textContent = 'Kargo durumu sorgulama API endpoint\'i için örnek JSON yapısı. Bu yapıyı kullanarak API\'nizi test edebilirsiniz.';
        apiJsonExample.textContent = window.getKargoApiExample();
    }
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

// Get Sipariş API Example - Global function
window.getSiparisApiExample = function() {
    return `{
  "success": true,
  "message": "Sipariş durumu başarıyla getirildi",
  "data": {
    "order_id": "ORD123456789",
    "order_number": "2024-001234",
    "status": "shipped",
    "status_text": "Kargoya Verildi",
    "status_code": "SHIPPED",
    "order_date": "2024-01-15T10:30:00Z",
    "estimated_delivery": "2024-01-18T18:00:00Z",
    "customer": {
      "name": "Ahmet Yılmaz",
      "email": "ahmet@example.com",
      "phone": "+90 555 123 4567"
    },
    "shipping_address": {
      "full_name": "Ahmet Yılmaz",
      "address_line_1": "Atatürk Caddesi No: 123",
      "address_line_2": "Daire 4",
      "city": "İstanbul",
      "district": "Kadıköy",
      "postal_code": "34710",
      "country": "Türkiye"
    },
    "items": [
      {
        "product_id": "PROD001",
        "product_name": "Samsung Galaxy S24",
        "quantity": 1,
        "unit_price": 25000.00,
        "total_price": 25000.00,
        "image_url": "https://example.com/images/samsung-s24.jpg"
      }
    ],
    "pricing": {
      "subtotal": 28500.00,
      "shipping_cost": 25.00,
      "tax_amount": 5130.00,
      "discount_amount": 1000.00,
      "total_amount": 32655.00,
      "currency": "TRY"
    },
    "payment": {
      "method": "credit_card",
      "method_text": "Kredi Kartı",
      "status": "paid",
      "status_text": "Ödendi",
      "transaction_id": "TXN789456123",
      "payment_date": "2024-01-15T10:35:00Z"
    },
    "shipping": {
      "method": "standard",
      "method_text": "Standart Kargo",
      "carrier": "Aras Kargo",
      "tracking_number": "AR123456789",
      "shipped_date": "2024-01-16T14:20:00Z",
      "estimated_delivery": "2024-01-18T18:00:00Z"
    },
    "timeline": [
      {
        "date": "2024-01-15T10:30:00Z",
        "status": "order_placed",
        "status_text": "Sipariş Verildi",
        "description": "Siparişiniz başarıyla alındı ve onaylandı."
      },
      {
        "date": "2024-01-16T14:20:00Z",
        "status": "shipped",
        "status_text": "Kargoya Verildi",
        "description": "Siparişiniz kargoya verildi. Kargo takip numarası: AR123456789"
      }
    ],
    "support_contact": {
      "phone": "+90 212 555 0123",
      "email": "destek@example.com",
      "whatsapp": "+90 555 123 4567"
    }
  }
}`;
}

// Get Kargo API Example - Global function
window.getKargoApiExample = function() {
    return `{
  "success": true,
  "message": "Kargo durumu başarıyla getirildi",
  "data": {
    "tracking_number": "AR123456789",
    "carrier": "Aras Kargo",
    "carrier_code": "ARAS",
    "status": "in_transit",
    "status_text": "Yolda",
    "status_code": "IN_TRANSIT",
    "origin": {
      "name": "İstanbul Merkez Depo",
      "address": "Atatürk Mahallesi, Depo Sokak No: 15, Sancaktepe/İstanbul",
      "city": "İstanbul",
      "postal_code": "34785"
    },
    "destination": {
      "name": "Ahmet Yılmaz",
      "address": "Atatürk Caddesi No: 123, Daire 4, Kadıköy/İstanbul",
      "city": "İstanbul",
      "postal_code": "34710"
    },
    "package_info": {
      "weight": "0.8 kg",
      "dimensions": "25x15x10 cm",
      "package_type": "Box",
      "contents": "Electronics",
      "value": 28500.00,
      "currency": "TRY"
    },
    "shipping_info": {
      "shipped_date": "2024-01-16T14:20:00Z",
      "estimated_delivery": "2024-01-18T18:00:00Z",
      "delivery_method": "Standard",
      "signature_required": true,
      "insurance": true
    },
    "current_location": {
      "facility": "İstanbul Transfer Merkezi",
      "address": "Kurtköy Mahallesi, Transfer Sokak No: 8, Pendik/İstanbul",
      "city": "İstanbul",
      "postal_code": "34912",
      "last_scan": "2024-01-17T08:30:00Z"
    },
    "timeline": [
      {
        "date": "2024-01-16T14:20:00Z",
        "status": "picked_up",
        "status_text": "Kargoya Alındı",
        "location": "İstanbul Merkez Depo",
        "description": "Kargonuz kargo firması tarafından alındı."
      },
      {
        "date": "2024-01-17T08:30:00Z",
        "status": "in_transit",
        "status_text": "Yolda",
        "location": "İstanbul Transfer Merkezi",
        "description": "Kargonuz hedef şehre doğru yola çıktı."
      }
    ],
    "delivery_info": {
      "attempted_deliveries": 0,
      "delivery_attempts_left": 3,
      "delivery_time_slot": "09:00-18:00",
      "delivery_notes": "Kapı zili çalınmadığında lütfen telefon ediniz."
    },
    "contact_info": {
      "carrier_phone": "+90 212 444 0 247",
      "carrier_website": "https://www.araskargo.com.tr",
      "tracking_url": "https://www.araskargo.com.tr/takip?kod=AR123456789"
    },
    "last_updated": "2024-01-17T08:30:00Z"
  }
}`;
}
</script>
@endsection
