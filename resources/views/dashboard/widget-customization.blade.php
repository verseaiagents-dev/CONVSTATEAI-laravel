@extends('layouts.app')

@section('title', 'Widget Özelleştirme')

@section('content')
<div class="min-h-screen bg-gray-900">
    <!-- Header -->
    <div class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-2xl font-bold text-white">Widget Özelleştirme</h1>
                    <p class="text-gray-400 mt-1">AI asistanınızı ve API ayarlarınızı özelleştirin</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Loading State -->
        <div id="loadingState" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500"></div>
            <p class="text-gray-400 mt-4">Ayarlar yükleniyor...</p>
        </div>

        <!-- Error State -->
        <div id="errorState" class="hidden text-center py-12">
            <div class="text-red-500 text-6xl mb-4">⚠️</div>
            <h3 class="text-xl font-semibold text-white mb-2">Bir hata oluştu</h3>
            <p class="text-gray-400 mb-6">Ayarlar yüklenirken bir sorun yaşandı</p>
            <button onclick="retryLoading()" class="px-6 py-3 bg-purple-600 hover:bg-purple-500 rounded-lg text-white font-semibold transition-all duration-200">
                Tekrar Dene
            </button>
        </div>

        <!-- Content Container -->
        <div id="contentContainer" class="hidden">
            <!-- Success Message -->
            <div id="successMessage" class="hidden mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-green-400"></div>
            
            <!-- Error Message -->
            <div id="errorMessage" class="hidden mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400"></div>

            <!-- Widget Customization Form -->
            <form id="widgetCustomizationForm" class="space-y-8">
                @csrf
                
                <!-- AI Settings Section -->
                <div class="glass-effect p-6 rounded-xl">
                    <h2 class="text-xl font-semibold text-white mb-6 flex items-center">
                        <svg class="w-6 h-6 text-purple-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        AI Asistan Ayarları
                    </h2>
                    
                    <div class="space-y-6">
                        <!-- AI Name -->
                        <div>
                            <label for="ai_name" class="block text-sm font-medium text-gray-300 mb-2">
                                AI Asistan Adı
                            </label>
                            <input 
                                type="text" 
                                id="ai_name" 
                                name="ai_name"
                                placeholder="Örn: Convstate AI"
                                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                            >
                            <p class="text-sm text-gray-400 mt-1">
                                Widget'ta görünecek AI asistan adı
                            </p>
                        </div>

                        <!-- Welcome Message -->
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
                            <p class="text-sm text-gray-400 mt-1">
                                Kullanıcılar widget'ı açtığında görecekleri ilk mesaj
                            </p>
                        </div>
                    </div>
                </div>

                <!-- API Settings Section -->
                <div class="glass-effect p-6 rounded-xl">
                    <h2 class="text-xl font-semibold text-white mb-6 flex items-center">
                        <svg class="w-6 h-6 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        API Endpoint Ayarları
                    </h2>
                    
                    <div class="space-y-6">
                        <!-- Sipariş Durumu API -->
                        <div>
                            <label for="siparis_durumu_endpoint" class="block text-sm font-medium text-gray-300 mb-2">
                                Sipariş Durumu API Endpoint
                            </label>
                            <div class="flex space-x-3">
                                <input 
                                    type="url" 
                                    id="siparis_durumu_endpoint" 
                                    name="siparis_durumu_endpoint"
                                    placeholder="https://example.com/api/order-tracking"
                                    class="flex-1 px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                >
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
                                Sipariş durumu sorgulama için API endpoint'i
                            </p>
                        </div>
                        
                        <!-- Kargo Durumu API -->
                        <div>
                            <label for="kargo_durumu_endpoint" class="block text-sm font-medium text-gray-300 mb-2">
                                Kargo Durumu API Endpoint
                            </label>
                            <div class="flex space-x-3">
                                <input 
                                    type="url" 
                                    id="kargo_durumu_endpoint" 
                                    name="kargo_durumu_endpoint"
                                    placeholder="https://example.com/api/cargo-tracking"
                                    class="flex-1 px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                >
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
                                Kargo durumu sorgulama için API endpoint'i
                            </p>
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
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end">
                    <button 
                        type="submit"
                        id="saveButton"
                        class="px-8 py-3 bg-purple-600 hover:bg-purple-500 rounded-lg text-white font-semibold transition-all duration-200 flex items-center space-x-2"
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
</div>

<script>
// Loading animation
let loadingInterval;
function startLoading() {
    const dots = ['', '.', '..', '...'];
    let dotIndex = 0;
    loadingInterval = setInterval(() => {
        document.querySelector('#loadingState p').textContent = 'Ayarlar yükleniyor' + dots[dotIndex];
        dotIndex = (dotIndex + 1) % dots.length;
    }, 500);
}

// Load content on page load
document.addEventListener('DOMContentLoaded', function() {
    startLoading();
    loadContent();
});

// Load content function
async function loadContent() {
    try {
        const response = await fetch('{{ route("dashboard.widget-customization.get") }}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            clearInterval(loadingInterval);
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('contentContainer').classList.remove('hidden');
            populateFormFields(result.data);
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
    if (data.widgetCustomization) {
        if (data.widgetCustomization.ai_name) {
            document.getElementById('ai_name').value = data.widgetCustomization.ai_name;
        }
        if (data.widgetCustomization.welcome_message) {
            document.getElementById('welcome_message').value = data.widgetCustomization.welcome_message;
        }
    }
    
    if (data.widgetActions) {
        // Yeni yapıda widgetActions bir array olabilir
        const actions = Array.isArray(data.widgetActions) ? data.widgetActions : [data.widgetActions];
        
        actions.forEach(action => {
            if (action.type === 'siparis_durumu_endpoint' && action.endpoint) {
                document.getElementById('siparis_durumu_endpoint').value = action.endpoint;
            }
            if (action.type === 'kargo_durumu_endpoint' && action.endpoint) {
                document.getElementById('kargo_durumu_endpoint').value = action.endpoint;
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
document.getElementById('widgetCustomizationForm').addEventListener('submit', async function(e) {
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
    
    try {
        const response = await fetch('{{ route("dashboard.widget-customization.store") }}', {
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

// Test endpoint function
async function testEndpoint(type) {
    const endpointInput = document.getElementById(type === 'siparis' ? 'siparis_durumu_endpoint' : 'kargo_durumu_endpoint');
    const endpoint = endpointInput.value.trim();
    
    if (!endpoint) {
        showMessage('errorMessage', 'Lütfen önce endpoint URL\'ini girin');
        return;
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
                type: type
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage('successMessage', `${type === 'siparis' ? 'Sipariş' : 'Kargo'} API endpoint başarıyla test edildi!`);
        } else {
            showMessage('errorMessage', result.message);
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
</script>
@endsection
