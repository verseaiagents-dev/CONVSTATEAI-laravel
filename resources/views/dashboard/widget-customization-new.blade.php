@extends('layouts.app')

@section('title', 'Widget Özelleştirme')

@section('content')
<style>
/* Hide footer for widget customization page */
footer {
    display: none !important;
}

/* Hide normal sidebar on mobile and tablet for widget customization page */
@media (max-width: 1023px) {
    #sidebar {
        display: none !important;
    }
    
    .md\:ml-64 {
        margin-left: 0 !important;
    }
}

/* Show sidebar on desktop for widget customization page */
@media (min-width: 1024px) {
    #sidebar {
        display: flex !important;
    }
    
    .md\:ml-64 {
        margin-left: 16rem !important;
    }
}

/* Custom scrollbar for widget customization page */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(31, 41, 55, 0.3);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #8B5CF6, #A855F7);
    border-radius: 4px;
    transition: background-color 0.2s ease;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #7C3AED, #9333EA);
}

/* Firefox scrollbar */
* {
    scrollbar-width: thin;
    scrollbar-color: #8B5CF6 rgba(31, 41, 55, 0.3);
}

/* Hide header on mobile for widget customization page */
@media (max-width: 1023px) {
    .bg-gray-800.border-b.border-gray-700 {
        display: none !important;
    }
    
    .min-h-screen.bg-gray-900 {
        padding-top: 0 !important;
    }
    
    /* Ensure mobile dock is visible */
    .lg\:hidden {
        display: block !important;
    }
}
</style>
<div class="min-h-screen bg-gray-900">
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-bold mb-4">
                <span class="gradient-text">Widget Özelleştirme</span>
            </h1>
            <p class="text-xl text-gray-300 mb-6">
                AI asistanınızı ve API ayarlarınızı özelleştirin
            </p>
        </div>
    </div>

    <!-- Main Content with Dock System -->
    <div class="flex h-screen">
        <!-- Desktop Dock Sidebar -->
        <div class="hidden lg:flex flex-col w-20">
            <!-- Dock Icons -->
            <div class="flex flex-col items-center justify-center space-y-4 p-4 h-full">
                <!-- AI Asistan Ayarları -->
                <button 
                    onclick="loadContainer('ai-settings')" 
                    class="dock-icon group relative w-12 h-12 bg-purple-600/20 hover:bg-purple-600/40 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110"
                    data-container="ai-settings"
                    title="AI Asistan Ayarları"
                >
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    <!-- Active Indicator -->
                    <div class="absolute -right-1 top-1/2 transform -translate-y-1/2 w-1 h-8 bg-purple-500 rounded-l-full opacity-0 transition-opacity duration-300"></div>
                </button>

                <!-- Eylem Tetikleyicileri -->
                <button 
                    onclick="loadContainer('action-triggers')" 
                    class="dock-icon group relative w-12 h-12 bg-purple-600/20 hover:bg-purple-600/40 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110"
                    data-container="action-triggers"
                    title="Eylem Tetikleyicileri"
                >
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <!-- Active Indicator -->
                    <div class="absolute -right-1 top-1/2 transform -translate-y-1/2 w-1 h-8 bg-purple-500 rounded-l-full opacity-0 transition-opacity duration-300"></div>
                </button>


                <!-- Embed Script -->
                <button 
                    onclick="loadContainer('embed-script')" 
                    class="dock-icon group relative w-12 h-12 bg-purple-600/20 hover:bg-purple-600/40 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110"
                    data-container="embed-script"
                    title="Embed Script"
                >
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    <!-- Active Indicator -->
                    <div class="absolute -right-1 top-1/2 transform -translate-y-1/2 w-1 h-8 bg-purple-500 rounded-l-full opacity-0 transition-opacity duration-300"></div>
                </button>
            </div>
        </div>

        <!-- Mobile Dock Top Bar -->
        <div class="lg:hidden w-full bg-gray-800/95 backdrop-blur-xl border-b border-gray-700 fixed top-0 left-0 right-0 z-40">
            <div class="flex items-center justify-center space-x-4 py-3 px-4">
                <!-- AI Asistan Ayarları -->
                <button 
                    onclick="loadContainer('ai-settings')" 
                    class="dock-icon-mobile group relative w-12 h-12 bg-purple-600/20 hover:bg-purple-600/40 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110"
                    data-container="ai-settings"
                    title="AI Asistan Ayarları"
                >
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    <!-- Active Indicator -->
                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-8 h-1 bg-purple-500 rounded-t-full opacity-0 transition-opacity duration-300"></div>
                </button>

                <!-- Eylem Tetikleyicileri -->
                <button 
                    onclick="loadContainer('action-triggers')" 
                    class="dock-icon-mobile group relative w-12 h-12 bg-purple-600/20 hover:bg-purple-600/40 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110"
                    data-container="action-triggers"
                    title="Eylem Tetikleyicileri"
                >
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <!-- Active Indicator -->
                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-8 h-1 bg-purple-500 rounded-t-full opacity-0 transition-opacity duration-300"></div>
                </button>


                <!-- Embed Script -->
                <button 
                    onclick="loadContainer('embed-script')" 
                    class="dock-icon-mobile group relative w-12 h-12 bg-purple-600/20 hover:bg-purple-600/40 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110"
                    data-container="embed-script"
                    title="Embed Script"
                >
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    <!-- Active Indicator -->
                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-8 h-1 bg-purple-500 rounded-t-full opacity-0 transition-opacity duration-300"></div>
                </button>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">
            <!-- Content Container -->
            <div class="flex-1 overflow-y-auto pt-16 lg:pt-0">
                <!-- Loading State -->
                <div id="loadingState" class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500"></div>
                        <p class="text-gray-400 mt-4">İçerik yükleniyor...</p>
                    </div>
                </div>

                <!-- Error State -->
                <div id="errorState" class="hidden items-center justify-center h-full">
                    <div class="text-center">
                        <div class="text-red-500 text-6xl mb-4">⚠️</div>
                        <h3 class="text-xl font-semibold text-white mb-2">Bir hata oluştu</h3>
                        <p class="text-gray-400 mb-6">İçerik yüklenirken bir sorun yaşandı</p>
                        <button onclick="retryLoading()" class="px-6 py-3 bg-purple-600 hover:bg-purple-500 rounded-lg text-white font-semibold transition-all duration-200">
                            Tekrar Dene
                        </button>
                    </div>
                </div>

                <!-- Dynamic Content Container -->
                <div id="contentContainer" class="hidden p-6">
                    <!-- Content will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Dock Icon Styles */
.dock-icon {
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.dock-icon:hover {
    backdrop-filter: blur(15px);
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(139, 92, 246, 0.3);
}

.dock-icon.active {
    background: rgba(139, 92, 246, 0.4);
    border-color: rgba(139, 92, 246, 0.6);
}

.dock-icon.active .absolute {
    opacity: 1;
}

.dock-icon-mobile {
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.dock-icon-mobile:hover {
    backdrop-filter: blur(15px);
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(139, 92, 246, 0.3);
}

.dock-icon-mobile.active {
    background: rgba(139, 92, 246, 0.4);
    border-color: rgba(139, 92, 246, 0.6);
}

.dock-icon-mobile.active .absolute {
    opacity: 1;
}

/* Content Animation */
.content-slide-in {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
let currentContainer = null;
let loadingInterval;


// Load container content via AJAX
async function loadContainer(containerName) {
    // Update active dock icon
    updateActiveDockIcon(containerName);
    
    // Show loading state
    showLoadingState();
    
    try {
        const response = await fetch(`/dashboard/widget-customization/container/${containerName}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            hideLoadingState();
            displayContent(result.html);
            currentContainer = containerName;
        } else {
            throw new Error(result.message || 'İçerik yüklenemedi');
        }
        
    } catch (error) {
        showErrorState();
    }
}

// Update active dock icon
function updateActiveDockIcon(containerName) {
    // Remove active class from all icons
    document.querySelectorAll('.dock-icon, .dock-icon-mobile').forEach(icon => {
        icon.classList.remove('active');
    });
    
    // Add active class to current icon
    const activeIcon = document.querySelector(`[data-container="${containerName}"]`);
    if (activeIcon) {
        activeIcon.classList.add('active');
    }
}

// Show loading state
function showLoadingState() {
    document.getElementById('loadingState').classList.remove('hidden');
    document.getElementById('errorState').classList.add('hidden');
    document.getElementById('contentContainer').classList.add('hidden');
    
    // Start loading animation
    startLoadingAnimation();
}

// Hide loading state
function hideLoadingState() {
    clearInterval(loadingInterval);
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('contentContainer').classList.remove('hidden');
}

// Show error state
function showErrorState() {
    clearInterval(loadingInterval);
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('errorState').classList.remove('hidden');
    document.getElementById('contentContainer').classList.add('hidden');
}

// Display content with animation
function displayContent(html) {
    const container = document.getElementById('contentContainer');
    container.innerHTML = html;
    container.classList.add('content-slide-in');
    
    // Remove animation class after animation completes
    setTimeout(() => {
        container.classList.remove('content-slide-in');
    }, 300);
}

// Start loading animation
function startLoadingAnimation() {
    const dots = ['', '.', '..', '...'];
    let dotIndex = 0;
    loadingInterval = setInterval(() => {
        const loadingText = document.querySelector('#loadingState p');
        if (loadingText) {
            loadingText.textContent = 'İçerik yükleniyor' + dots[dotIndex];
            dotIndex = (dotIndex + 1) % dots.length;
        }
    }, 500);
}

// Retry loading
function retryLoading() {
    if (currentContainer) {
        loadContainer(currentContainer);
    } else {
        loadContainer('ai-settings');
    }
}

// Clean URL parameters on page load
function cleanURL() {
    if (window.location.search) {
        const url = new URL(window.location);
        url.search = '';
        window.history.replaceState({}, document.title, url.pathname);
    }
}

// Global functions for action triggers
let formCounter = 0;

// Modal functions
function openAddButtonModal() {
    document.getElementById('addButtonModal').classList.remove('hidden');
    formCounter = 0; // Reset counter
    document.getElementById('customButtonForms').innerHTML = ''; // Clear forms
    
    // Add initial form
    addNewButtonForm();
}

function closeAddButtonModal() {
    document.getElementById('addButtonModal').classList.add('hidden');
    formCounter = 0; // Reset counter
    document.getElementById('customButtonForms').innerHTML = ''; // Clear forms
}

// Add new button form
function addNewButtonForm() {
    formCounter++;
    const formsContainer = document.getElementById('customButtonForms');
    
    const formElement = document.createElement('div');
    formElement.className = 'bg-gray-700/50 p-4 rounded-lg border border-gray-600';
    formElement.innerHTML = `
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-md font-medium text-white">Custom Button ${formCounter}</h4>
            <button 
                type="button"
                onclick="removeButtonForm(this)"
                class="text-red-400 hover:text-red-300 p-1 rounded-lg hover:bg-red-600/20 transition-colors duration-200"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Button Adı *
                </label>
                <input 
                    type="text" 
                    name="custom_button_name_${formCounter}"
                    placeholder="Örn: Sepete Ekle"
                    class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                    required
                >
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Intent (Niyet) *
                </label>
                <select 
                    name="custom_button_intent_${formCounter}"
                    class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                    required
                >
                    <option value="">Intent seçin...</option>
                    <option value="add_to_cart">Sepete Ekle</option>
                    <option value="make_payment">Ödeme Yap</option>
                    <option value="check_order_status">Sipariş Durumu Sorgula</option>
                    <option value="check_cargo_status">Kargo Durumu Sorgula</option>
                    <option value="contact_support">Destek İletişim</option>
                    <option value="get_promotion">Promosyon Sorgula</option>
                    <option value="custom_action">Özel Eylem</option>
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    API Endpoint *
                </label>
                <input 
                    type="url" 
                    name="custom_button_endpoint_${formCounter}"
                    placeholder="https://your-api.com/endpoint"
                    class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                    required
                >
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    HTTP Method
                </label>
                <select 
                    name="custom_button_method_${formCounter}"
                    class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                >
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                    <option value="PUT">PUT</option>
                    <option value="DELETE">DELETE</option>
                </select>
            </div>
        </div>
        
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Açıklama
            </label>
            <textarea 
                name="custom_button_description_${formCounter}"
                rows="2"
                placeholder="Bu butonun ne işe yaradığını açıklayın..."
                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 resize-none"
            ></textarea>
        </div>
    `;
    
    formsContainer.appendChild(formElement);
}

// Remove button form
function removeButtonForm(button) {
    button.closest('.bg-gray-700\\/50').remove();
}

// Save all custom buttons
async function saveAllCustomButtons() {
    const forms = document.querySelectorAll('#customButtonForms .bg-gray-700\\/50');
    const buttons = [];
    
    forms.forEach((formDiv, index) => {
        // Get form inputs directly from the div
        const nameInput = formDiv.querySelector(`input[name="custom_button_name_${index + 1}"]`);
        const intentSelect = formDiv.querySelector(`select[name="custom_button_intent_${index + 1}"]`);
        const endpointInput = formDiv.querySelector(`input[name="custom_button_endpoint_${index + 1}"]`);
        const methodSelect = formDiv.querySelector(`select[name="custom_button_method_${index + 1}"]`);
        const descriptionTextarea = formDiv.querySelector(`textarea[name="custom_button_description_${index + 1}"]`);
        
        if (nameInput && intentSelect && endpointInput) {
            const name = nameInput.value.trim();
            const intent = intentSelect.value;
            const endpoint = endpointInput.value.trim();
            const method = methodSelect ? methodSelect.value : 'GET';
            const description = descriptionTextarea ? descriptionTextarea.value.trim() : '';
            
            // Validate endpoint (no convstateai.com domain)
            if (endpoint && endpoint.includes('convstateai.com')) {
                showErrorModal('Bu URL ile işlem yapılamaz. Lütfen farklı bir endpoint kullanın.');
                return;
            }
            
            if (name && intent && endpoint) {
                buttons.push({
                    name: name,
                    intent: intent,
                    endpoint: endpoint,
                    method: method,
                    description: description
                });
            }
        }
    });
    
    if (buttons.length === 0) {
        showError('Lütfen en az bir custom button ekleyin');
        return;
    }
    
    try {
        const response = await fetch('/dashboard/widget-customization/add-multiple-custom-buttons', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ buttons: buttons })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess(result.message);
            closeAddButtonModal(); // Close modal
            loadActionTriggers(); // Reload data
        } else {
            showError(result.message);
        }
        
    } catch (error) {
        showError('Custom button\'lar eklenirken hata oluştu');
    }
}

// Show success message
function showSuccess(message) {
    const successDiv = document.getElementById('successMessage');
    if (successDiv) {
        successDiv.textContent = message;
        successDiv.classList.remove('hidden');
        
        setTimeout(() => {
            successDiv.classList.add('hidden');
        }, 5000);
    }
}

// Show error message
function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
        
        setTimeout(() => {
            errorDiv.classList.add('hidden');
        }, 5000);
    }
}

// Show error modal
function showErrorModal(message) {
    const modal = document.getElementById('errorModal');
    const messageElement = document.getElementById('errorModalMessage');
    if (modal && messageElement) {
        messageElement.textContent = message;
        modal.classList.remove('hidden');
    }
}

// Close error modal
function closeErrorModal() {
    const modal = document.getElementById('errorModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Load action triggers data (needed for reloading after save)
async function loadActionTriggers() {
    try {
        const response = await fetch('/dashboard/widget-customization/get-action-triggers', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Load default buttons
            loadDefaultButtons(data.data.defaultButtons || []);
            
            // Load custom buttons
            loadCustomButtons(data.data.customButtons || []);
            
            // Load API settings
            loadAPISettings(data.data.apiSettings || {});
        } else {
            showError(data.message || 'Veri yüklenirken hata oluştu');
        }
        
    } catch (error) {
        showError('Veri yüklenirken hata oluştu: ' + error.message);
    }
}

// Load default buttons
function loadDefaultButtons(buttons) {
    const container = document.getElementById('defaultButtonsList');
    if (!container) return;
    
    container.innerHTML = '';
    
    buttons.forEach(button => {
        const buttonElement = document.createElement('div');
        buttonElement.className = 'flex items-center justify-between p-4 bg-gray-800/50 rounded-lg border border-gray-700';
        buttonElement.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-medium">${button.name}</h3>
                    <p class="text-gray-400 text-sm">${button.description}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-sm text-gray-400">${button.is_active ? 'Aktif' : 'Pasif'}</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input 
                        type="checkbox" 
                        class="sr-only peer" 
                        ${button.is_active ? 'checked' : ''}
                        onchange="toggleDefaultButton('${button.id}')"
                    >
                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                </label>
            </div>
        `;
        container.appendChild(buttonElement);
    });
}

// Load custom buttons
function loadCustomButtons(buttons) {
    const container = document.getElementById('customButtonsList');
    if (!container) return;
    
    container.innerHTML = '';
    
    if (buttons.length === 0) {
        container.innerHTML = '<p class="text-gray-400 text-center py-8">Henüz custom button eklenmemiş</p>';
        return;
    }
    
    buttons.forEach(button => {
        const buttonElement = document.createElement('div');
        buttonElement.className = 'flex items-center justify-between p-4 bg-gray-800/50 rounded-lg border border-gray-700';
        buttonElement.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-medium">${button.name}</h3>
                    <p class="text-gray-400 text-sm">Intent: ${button.intent} | Method: ${button.method}</p>
                    <p class="text-gray-500 text-xs">${button.endpoint}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-sm text-gray-400">${button.is_active ? 'Aktif' : 'Pasif'}</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input 
                        type="checkbox" 
                        class="sr-only peer" 
                        ${button.is_active ? 'checked' : ''}
                        onchange="toggleCustomButton('${button.id}')"
                    >
                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                </label>
                <button 
                    onclick="deleteCustomButton('${button.id}')"
                    class="text-red-400 hover:text-red-300 p-2 rounded-lg hover:bg-red-600/20 transition-colors duration-200"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        `;
        container.appendChild(buttonElement);
    });
}

// Load API settings
function loadAPISettings(settings) {
    if (settings.siparis_durumu_endpoint) {
        const siparisInput = document.getElementById('siparis_durumu_endpoint');
        if (siparisInput) {
            siparisInput.value = settings.siparis_durumu_endpoint;
        }
    }
    if (settings.kargo_durumu_endpoint) {
        const kargoInput = document.getElementById('kargo_durumu_endpoint');
        if (kargoInput) {
            kargoInput.value = settings.kargo_durumu_endpoint;
        }
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    cleanURL();
    loadContainer('ai-settings');
});
</script>
@endsection
