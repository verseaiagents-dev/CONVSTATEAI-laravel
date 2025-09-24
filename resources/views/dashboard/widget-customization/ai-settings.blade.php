<!-- AI Asistan Ayarları Container -->
<div class="max-w-4xl mx-auto">
    <!-- Success Message -->
    <div id="successMessage" class="hidden mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-green-400"></div>
    
    <!-- Error Message -->
    <div id="errorMessage" class="hidden mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400"></div>

    <!-- AI Settings Form -->
    <form id="aiSettingsForm" class="space-y-8">
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

<script>
// Load AI settings data
document.addEventListener('DOMContentLoaded', function() {
    loadAISettings();
});

// Load AI settings
async function loadAISettings() {
    try {
        const response = await fetch('/dashboard/widget-customization/get-ai-settings', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success && result.data) {
            if (result.data.ai_name) {
                document.getElementById('ai_name').value = result.data.ai_name;
            }
            if (result.data.welcome_message) {
                document.getElementById('welcome_message').value = result.data.welcome_message;
            }
        }
    } catch (error) {
        console.error('AI settings loading error:', error);
    }
}

// Form submission
document.getElementById('aiSettingsForm').addEventListener('submit', async function(e) {
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
        const response = await fetch('/dashboard/widget-customization/save-ai-settings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
