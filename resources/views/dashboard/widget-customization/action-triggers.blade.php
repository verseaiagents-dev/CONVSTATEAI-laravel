<!-- Eylem Tetikleyicileri Container -->
<div class="max-w-6xl mx-auto">
    <!-- Success Message -->
    <div id="successMessage" class="hidden mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-green-400"></div>
    
    <!-- Error Message -->
    <div id="errorMessage" class="hidden mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400"></div>

    <!-- Error Modal -->
    <div id="errorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-red-600/20 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white">Hata</h3>
            </div>
            <p id="errorModalMessage" class="text-gray-300 mb-6"></p>
            <div class="flex justify-end">
                <button 
                    onclick="closeErrorModal()"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200"
                >
                    Tamam
                </button>
            </div>
        </div>
    </div>

    <!-- Add Custom Button Modal -->
    <div id="addButtonModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-white">Custom Button Ekle</h3>
                <button 
                    onclick="closeAddButtonModal()"
                    class="text-gray-400 hover:text-white transition-colors duration-200"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="customButtonForms" class="space-y-6">
                <!-- Custom button forms will be added here -->
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-700">
                <button 
                    onclick="closeAddButtonModal()"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200"
                >
                    İptal
                </button>
                <button 
                    onclick="addNewButtonForm()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Yeni Form Ekle</span>
                </button>
                <button 
                    onclick="saveAllCustomButtons()"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Tümünü Kaydet</span>
                </button>
            </div>
        </div>
    </div>

    <div class="space-y-8">
        <!-- Default Action Buttons Section -->
        <div class="glass-effect p-6 rounded-xl">
            <h2 class="text-xl font-semibold text-white mb-6 flex items-center">
                <svg class="w-6 h-6 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Default Eylem Butonları
            </h2>
            
            <div class="space-y-4">
                <p class="text-gray-400 text-sm mb-4">
                    Bu butonlar varsayılan olarak widget'ta görünür. Sadece aktif/pasif durumlarını değiştirebilirsiniz.
                </p>
                
                <!-- Default Buttons List -->
                <div id="defaultButtonsList" class="space-y-3">
                    <!-- Default buttons will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Custom Action Buttons Section -->
        <div class="glass-effect p-6 rounded-xl">
            <h2 class="text-xl font-semibold text-white mb-6 flex items-center">
                <svg class="w-6 h-6 text-purple-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Custom Eylem Butonları
            </h2>
            
            <div class="space-y-4">
                <p class="text-gray-400 text-sm mb-4">
                    Kendi özel eylem butonlarınızı oluşturun ve React widget'a entegre edin.
                </p>
                
                <!-- Add Custom Button Button -->
                <div class="bg-gray-800/50 p-4 rounded-lg border border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-white">Custom Button Yönetimi</h3>
                        <button 
                            type="button"
                            onclick="openAddButtonModal()"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>Yeni Button Ekle</span>
                        </button>
                    </div>
                    
                    <p class="text-gray-400 text-sm">
                        Custom button'larınızı yönetin ve React widget'a entegre edin.
                    </p>
                </div>
                
                <!-- Custom Buttons List -->
                <div id="customButtonsList" class="space-y-3">
                    <!-- Custom buttons will be loaded here -->
                </div>
            </div>
        </div>

        <!-- API Endpoints Section -->
        <div class="glass-effect p-6 rounded-xl">
            <h2 class="text-xl font-semibold text-white mb-6 flex items-center">
                <svg class="w-6 h-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                API Endpoint Ayarları
            </h2>
            
            <div class="space-y-6">
                <p class="text-gray-400 text-sm mb-4">
                    Default action button'lar için API endpoint'lerini yapılandırın.
                </p>
                
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
                            placeholder="https://your-api.com/order-status"
                            class="flex-1 px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                        >
                        <button 
                            type="button"
                            onclick="testEndpoint('siparis_durumu_endpoint')"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition-colors duration-200"
                        >
                            Test
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Sipariş durumu sorgulama için API endpoint</p>
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
                            placeholder="https://your-api.com/cargo-status"
                            class="flex-1 px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                        >
                        <button 
                            type="button"
                            onclick="testEndpoint('kargo_durumu_endpoint')"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition-colors duration-200"
                        >
                            Test
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Kargo durumu sorgulama için API endpoint</p>
                </div>
                
                <!-- Save API Settings Button -->
                <div class="flex justify-end">
                    <button 
                        type="button"
                        onclick="saveAPISettings()"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>API Ayarlarını Kaydet</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load action triggers data
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
            loadDefaultButtons(data.defaultButtons || []);
            
            // Load custom buttons
            loadCustomButtons(data.customButtons || []);
            
            // Load API settings
            loadAPISettings(data.apiSettings || {});
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
        document.getElementById('siparis_durumu_endpoint').value = settings.siparis_durumu_endpoint;
    }
    if (settings.kargo_durumu_endpoint) {
        document.getElementById('kargo_durumu_endpoint').value = settings.kargo_durumu_endpoint;
    }
}

// Toggle default button
async function toggleDefaultButton(buttonId) {
    try {
        const response = await fetch('/dashboard/widget-customization/toggle-default-button', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ button_id: buttonId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess(data.message);
            loadActionTriggers(); // Reload data
        } else {
            showError(data.message);
        }
        
    } catch (error) {
        showError('Buton durumu değiştirilirken hata oluştu');
    }
}

// Toggle custom button
async function toggleCustomButton(buttonId) {
    try {
        const response = await fetch('/dashboard/widget-customization/toggle-custom-button', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ button_id: buttonId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess(data.message);
            loadActionTriggers(); // Reload data
        } else {
            showError(data.message);
        }
        
    } catch (error) {
        showError('Buton durumu değiştirilirken hata oluştu');
    }
}

// Modal functions are now in global scope

// Delete custom button
async function deleteCustomButton(buttonId) {
    if (!confirm('Bu custom button\'ı silmek istediğinizden emin misiniz?')) {
        return;
    }
    
    try {
        const response = await fetch('/dashboard/widget-customization/delete-custom-button', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ button_id: buttonId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess(data.message);
            loadActionTriggers(); // Reload data
        } else {
            showError(data.message);
        }
        
    } catch (error) {
        showError('Custom button silinirken hata oluştu');
    }
}

// Save API settings
async function saveAPISettings() {
    const data = {
        siparis_durumu_endpoint: document.getElementById('siparis_durumu_endpoint').value,
        kargo_durumu_endpoint: document.getElementById('kargo_durumu_endpoint').value
    };
    
    try {
        const response = await fetch('/dashboard/widget-customization/save-api-settings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess(result.message);
        } else {
            showError(result.message);
        }
        
    } catch (error) {
        showError('API ayarları kaydedilirken hata oluştu');
    }
}

// Test endpoint
async function testEndpoint(endpointId) {
    const endpoint = document.getElementById(endpointId).value;
    
    if (!endpoint) {
        showError('Lütfen endpoint URL\'sini girin');
        return;
    }
    
    try {
        const response = await fetch('/dashboard/widget-customization/test-endpoint', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ endpoint: endpoint })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess('Endpoint test edildi: ' + data.message);
        } else {
            showError('Endpoint test hatası: ' + data.message);
        }
        
    } catch (error) {
        showError('Endpoint test edilirken hata oluştu');
    }
}

// Utility functions are now in global scope

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadActionTriggers();
});
</script>
