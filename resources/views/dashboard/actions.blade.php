@extends('layouts.dashboard')

@section('title', 'Eylemler')

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
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden slide-in-up">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-4xl font-bold">
                        <span class="gradient-text">Eylemler</span>
                    </h1>
                    <p class="text-xl text-gray-300">
                        API endpoint'lerini yönetin ve test edin
                    </p>
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
                <h3 class="text-xl font-semibold text-white mb-2">Eylemler Yükleniyor</h3>
                <p class="text-gray-400">API endpoint'leri hazırlanıyor, lütfen bekleyin...</p>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-64 bg-gray-700 rounded-full h-2 overflow-hidden">
                <div id="progressBar" class="bg-gradient-to-r from-purple-glow to-neon-purple h-2 rounded-full progress-animation" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <!-- Content Container (Hidden initially) -->
    <div id="contentContainer" class="hidden space-y-6 slide-in-up">
        <!-- API Endpoints Management -->
        <div class="glass-effect rounded-2xl p-8">
            <h2 class="text-2xl font-bold mb-6 text-white">API Endpoint Yönetimi</h2>
            
            <form id="actionsForm" class="space-y-8">
                @csrf
                
                <!-- Sipariş Durumu API -->
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <label for="siparis_durumu_endpoint" class="block text-sm font-medium text-gray-300">
                                    Sipariş Durumu API Endpoint
                                </label>
                                <button type="button" 
                                        onclick="showApiExampleModal('siparis')"
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
                                        onclick="showApiExampleModal('kargo')"
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
            <p class="text-gray-400 text-center">Eylem ayarları yüklenirken bir hata oluştu. Lütfen sayfayı yenileyin.</p>
            <button 
                onclick="retryLoading()"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-500 rounded-lg text-white font-semibold transition-all duration-200"
            >
                Tekrar Dene
            </button>
        </div>
    </div>
</div>

<!-- Test Result Modal -->
<div id="testResultModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/85 backdrop-blur-sm"></div>
    <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl max-h-[90vh] bg-gray-900 rounded-2xl border border-gray-700 shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-gray-700 bg-gray-800/50">
            <h2 id="modalTitle" class="text-2xl font-bold text-white">Test Sonucu</h2>
            <button onclick="closeTestModal()" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
            <div id="modalContent"></div>
        </div>
    </div>
</div>

<!-- API Example Modal -->
<div id="apiExampleModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/85 backdrop-blur-sm"></div>
    <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl max-h-[90vh] bg-gray-900 rounded-2xl border border-gray-700 shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-gray-700 bg-gray-800/50">
            <h2 id="apiModalTitle" class="text-2xl font-bold text-white">API JSON Örneği</h2>
            <button onclick="closeApiExampleModal()" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
            <div class="space-y-4">
                <p id="apiUsageInfo" class="text-gray-300"></p>
                <div class="bg-gray-800 rounded-lg p-4">
                    <pre id="apiJsonExample" class="text-green-400 text-sm whitespace-pre-wrap"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let loadingProgress = 0;
let loadingInterval;
let widgetActions = [];
let widgetCustomizations = [];
let stats = {};

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
    }, 500);
}

// Load content from server
async function loadContent() {
    try {
        const projectId = {!! json_encode($projectId ?? null) !!};
        const url = '{{ route("dashboard.actions.load-content") }}' + (projectId ? `?project_id=${projectId}` : '');
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Store data globally
            widgetActions = result.data.widgetActions || [];
            widgetCustomizations = result.data.widgetCustomizations || [];
            stats = result.data.stats || {};
            
            completeLoading();
        } else {
            showErrorState();
        }
        
    } catch (error) {
        console.error('Loading error:', error);
        showErrorState();
    }
}

// Populate content with loaded data
function populateContent() {
    // Populate form fields with existing data
    populateFormFields();
    
    // Add form submission handler
    document.getElementById('actionsForm').addEventListener('submit', handleFormSubmit);
    
    console.log('Content başarıyla yüklendi');
}

// Populate form fields
function populateFormFields() {
    // Find siparis and kargo actions
    const siparisAction = widgetActions.find(action => action.type === 'siparis_durumu_endpoint');
    const kargoAction = widgetActions.find(action => action.type === 'kargo_durumu_endpoint');
    
    // Populate siparis fields
    if (siparisAction) {
        document.getElementById('siparis_durumu_endpoint').value = siparisAction.endpoint || '';
        document.getElementById('siparis_active_toggle').checked = siparisAction.is_active || false;
    }
    
    // Populate kargo fields
    if (kargoAction) {
        document.getElementById('kargo_durumu_endpoint').value = kargoAction.endpoint || '';
        document.getElementById('kargo_active_toggle').checked = kargoAction.is_active || false;
    }
}

// Handle form submission
async function handleFormSubmit(e) {
    e.preventDefault();
    
    const saveButton = document.getElementById('saveButton');
    const saveButtonText = document.getElementById('saveButtonText');
    const saveButtonSpinner = document.getElementById('saveButtonSpinner');
    
    // Show loading state
    saveButton.disabled = true;
    saveButtonText.textContent = 'Kaydediliyor...';
    saveButtonSpinner.classList.remove('hidden');
    
    try {
        const formData = new FormData(e.target);
        
        const response = await fetch('{{ route("dashboard.actions.save") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage('successMessage', 'Ayarlar başarıyla kaydedildi!');
            // Update stats after successful save
            updateStatsDisplay();
        } else {
            showMessage('errorMessage', result.message);
        }
        
    } catch (error) {
        showMessage('errorMessage', 'Kaydetme sırasında hata oluştu: ' + error.message);
    } finally {
        // Reset button state
        saveButton.disabled = false;
        saveButtonText.textContent = 'Ayarları Kaydet';
        saveButtonSpinner.classList.add('hidden');
    }
}

// Update stats display after changes
function updateStatsDisplay() {
    // Reload stats from server
    loadStats();
}

// Load current stats
async function loadStats() {
    try {
        const response = await fetch('{{ route("dashboard.actions.load-content") }}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            stats = result.data.stats || {};
            console.log('Stats updated:', stats);
        }
        
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

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
        const response = await fetch('{{ route("dashboard.actions.test-endpoint") }}', {
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
            }
            showTestModal('success', result, type);
            // Update stats after successful test
            updateStatsDisplay();
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

// Generate success modal content
function generateSuccessModalContent(result, apiType) {
    return `
        <div class="space-y-4">
            <div class="p-4 bg-green-500/10 border border-green-500/30 rounded-lg">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-green-400 font-semibold">API Endpoint Başarıyla Test Edildi</span>
                </div>
                <p class="text-gray-300 text-sm">${result.message}</p>
            </div>
            
            <div class="space-y-3">
                <div>
                    <span class="text-gray-400 text-sm">HTTP Status:</span>
                    <span class="text-white ml-2">${result.data.status_code}</span>
                </div>
                <div>
                    <span class="text-gray-400 text-sm">Endpoint:</span>
                    <span class="text-white ml-2">${result.data.endpoint}</span>
                </div>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-2">API Yanıtı:</h4>
                <div class="bg-gray-800 rounded-lg p-4">
                    <pre class="text-green-400 text-sm whitespace-pre-wrap">${JSON.stringify(result.data.response, null, 2)}</pre>
                </div>
            </div>
        </div>
    `;
}

// Generate error modal content
function generateErrorModalContent(result, apiType) {
    return `
        <div class="space-y-4">
            <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="text-red-400 font-semibold">API Test Hatası</span>
                </div>
                <p class="text-gray-300 text-sm">${result.message}</p>
            </div>
            
            ${result.data.status_code ? `
                <div>
                    <span class="text-gray-400 text-sm">HTTP Status:</span>
                    <span class="text-white ml-2">${result.data.status_code}</span>
                </div>
            ` : ''}
            
            ${result.data.response ? `
                <div>
                    <h4 class="text-white font-semibold mb-2">API Yanıtı:</h4>
                    <div class="bg-gray-800 rounded-lg p-4">
                        <pre class="text-red-400 text-sm whitespace-pre-wrap">${JSON.stringify(result.data.response, null, 2)}</pre>
                    </div>
                </div>
            ` : ''}
            
            ${result.data.error ? `
                <div>
                    <h4 class="text-white font-semibold mb-2">Hata Detayı:</h4>
                    <div class="bg-gray-800 rounded-lg p-4">
                        <pre class="text-red-400 text-sm whitespace-pre-wrap">${result.data.error}</pre>
                    </div>
                </div>
            ` : ''}
        </div>
    `;
}

// Show API Example Modal
function showApiExampleModal(type) {
    const modal = document.getElementById('apiExampleModal');
    const modalTitle = document.getElementById('apiModalTitle');
    const apiUsageInfo = document.getElementById('apiUsageInfo');
    const apiJsonExample = document.getElementById('apiJsonExample');
    
    if (type === 'siparis') {
        modalTitle.textContent = 'Sipariş Durumu API JSON Örneği';
        apiUsageInfo.textContent = 'Sipariş durumu sorgulama API endpoint\'i için örnek JSON yapısı. Bu yapıyı kullanarak API\'nizi test edebilirsiniz.';
        apiJsonExample.textContent = getSiparisApiExample();
    } else if (type === 'kargo') {
        modalTitle.textContent = 'Kargo Durumu API JSON Örneği';
        apiUsageInfo.textContent = 'Kargo durumu sorgulama API endpoint\'i için örnek JSON yapısı. Bu yapıyı kullanarak API\'nizi test edebilirsiniz.';
        apiJsonExample.textContent = getKargoApiExample();
    }
    
    modal.classList.remove('hidden');
}

// Close API Example Modal
function closeApiExampleModal() {
    const modal = document.getElementById('apiExampleModal');
    modal.classList.add('hidden');
}

// Get Sipariş API Example
function getSiparisApiExample() {
    return `{
  "success": true,
  "message": "Sipariş durumu başarıyla getirildi",
  "data": {
    "order_number": "ORD123456789",
    "status": "shipped",
    "status_text": "Kargoya Verildi",
    "order_date": "2024-01-15T10:30:00Z",
    "estimated_delivery": "2024-01-20T18:00:00Z",
    "tracking_number": "TRK789456123",
    "items": [
      {
        "name": "Ürün Adı",
        "quantity": 2,
        "price": 99.99
      }
    ],
    "total_amount": 199.98,
    "shipping_address": {
      "name": "Ahmet Yılmaz",
      "address": "Örnek Mahallesi, Örnek Sokak No:123",
      "city": "İstanbul",
      "postal_code": "34000"
    }
  }
}`;
}

// Get Kargo API Example
function getKargoApiExample() {
    return `{
  "success": true,
  "message": "Kargo durumu başarıyla getirildi",
  "data": {
    "tracking_number": "TRK789456123",
    "status": "in_transit",
    "status_text": "Yolda",
    "shipped_date": "2024-01-15T14:30:00Z",
    "estimated_delivery": "2024-01-20T18:00:00Z",
    "current_location": "İstanbul Dağıtım Merkezi",
    "events": [
      {
        "date": "2024-01-15T14:30:00Z",
        "status": "shipped",
        "description": "Kargoya verildi",
        "location": "İstanbul"
      },
      {
        "date": "2024-01-16T08:15:00Z",
        "status": "in_transit",
        "description": "Dağıtım merkezine ulaştı",
        "location": "İstanbul Dağıtım Merkezi"
      }
    ],
    "carrier": {
      "name": "Örnek Kargo",
      "phone": "+90 212 123 45 67"
    }
  }
}`;
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

// Show message function
function showMessage(elementId, message) {
    // Create temporary message element
    const messageDiv = document.createElement('div');
    messageDiv.className = 'fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 slide-in-up';
    
    if (elementId === 'successMessage') {
        messageDiv.className += ' bg-green-500 text-white';
    } else {
        messageDiv.className += ' bg-red-500 text-white';
    }
    
    messageDiv.textContent = message;
    document.body.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.id === 'testResultModal') {
        closeTestModal();
    }
    if (e.target.id === 'apiExampleModal') {
        closeApiExampleModal();
    }
});

// Close modals with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTestModal();
        closeApiExampleModal();
    }
});
</script>
@endsection
