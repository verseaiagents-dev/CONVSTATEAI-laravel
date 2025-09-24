<!-- Embed Script Container -->
<div class="max-w-4xl mx-auto">
    <!-- Success Message -->
    <div id="successMessage" class="hidden mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-green-400"></div>
    
    <!-- Error Message -->
    <div id="errorMessage" class="hidden mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400"></div>

    <div class="space-y-8">
        <!-- Embed Script Section -->
        <div class="glass-effect p-6 rounded-xl">
            <h2 class="text-xl font-semibold text-white mb-6 flex items-center">
                <svg class="w-6 h-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                </svg>
                Embed Script
            </h2>
            
            <div class="space-y-6">
                <p class="text-gray-400 text-sm mb-4">
                    Widget'ınızı web sitenize entegre etmek için aşağıdaki kodu kullanın.
                </p>
                
                <!-- Embed Script Display -->
                <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-white">Embed Kodu</h3>
                        <div class="flex space-x-2">
                            <button 
                                onclick="copyEmbedScript()"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded-lg text-white font-semibold transition-all duration-200 flex items-center space-x-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span>Kopyala</span>
                            </button>
                            <button 
                                onclick="testEmbedScript()"
                                class="px-4 py-2 bg-green-600 hover:bg-green-500 rounded-lg text-white font-semibold transition-all duration-200 flex items-center space-x-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Test Et</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="bg-gray-900/50 rounded-lg p-4 border border-gray-600">
                        <pre id="embedScriptDisplay" class="text-green-400 font-mono text-sm break-all whitespace-pre-wrap overflow-x-auto">
<!-- ConvStateAI Widget -->
<script src="{{ url('/embed/convstateai.min.js') }}"></script>
<script>
    window.convstateaiConfig = {
        projectId: "{{ request('project_id') ?? '1' }}",
        customizationToken: "{{ Auth::user()->personal_token ?? 'dcf91b8e63c9552b724a4523261318e565ef33992e454dbc0cff1064aae19246' }}",
        apiUrl: "{{ config('app.url') }}/api",
        debug: false
    };
</script>
                        </pre>
                    </div>
                </div>

                <!-- Usage Instructions -->
                <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-400 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Kullanım Talimatları
                    </h3>
                    <div class="space-y-3 text-sm text-gray-300">
                        <div class="flex items-start space-x-3">
                            <span class="text-blue-400 font-semibold">1.</span>
                            <span>Yukarıdaki kodu kopyalayın</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <span class="text-blue-400 font-semibold">2.</span>
                            <span>Web sitenizin <code class="bg-gray-700 px-2 py-1 rounded">&lt;/body&gt;</code> etiketinden önce yapıştırın</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <span class="text-blue-400 font-semibold">3.</span>
                            <span>Sayfayı yenileyin ve widget'ın göründüğünü kontrol edin</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <span class="text-blue-400 font-semibold">4.</span>
                            <span>Widget ayarlarınızı bu panelden yönetebilirsiniz</span>
                        </div>
                    </div>
                </div>

                <!-- Widget Preview -->
                <div class="bg-gray-800/50 rounded-lg p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4">Widget Önizleme</h3>
                    <div class="bg-gray-900 rounded-lg p-4 border border-gray-600">
                        <div class="flex items-center justify-center h-32 bg-gray-800 rounded-lg border-2 border-dashed border-gray-600">
                            <div class="text-center">
                                <svg class="w-12 h-12 text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <p class="text-gray-400 text-sm">Widget burada görünecek</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings -->
                <div class="bg-gray-800/50 rounded-lg p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4">Gelişmiş Ayarlar</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="widget_position" class="block text-sm font-medium text-gray-300 mb-2">
                                Widget Pozisyonu
                            </label>
                            <select 
                                id="widget_position" 
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                            >
                                <option value="bottom-right">Sağ Alt</option>
                                <option value="bottom-left">Sol Alt</option>
                                <option value="center">Orta</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="widget_theme" class="block text-sm font-medium text-gray-300 mb-2">
                                Widget Teması
                            </label>
                            <select 
                                id="widget_theme" 
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                            >
                                <option value="default">Varsayılan</option>
                                <option value="dark">Koyu</option>
                                <option value="light">Açık</option>
                            </select>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <input 
                                type="checkbox" 
                                id="enable_debug" 
                                class="form-checkbox"
                            >
                            <label for="enable_debug" class="text-gray-300">
                                Debug modunu etkinleştir
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load embed script data
document.addEventListener('DOMContentLoaded', function() {
    loadEmbedScript();
});

// Load embed script data
async function loadEmbedScript() {
    try {
        const response = await fetch('/dashboard/widget-customization/get-embed-script', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update embed script display
            updateEmbedScriptDisplay(result.data);
        }
    } catch (error) {
        console.error('Embed script loading error:', error);
    }
}

// Update embed script display
function updateEmbedScriptDisplay(data) {
    const scriptDisplay = document.getElementById('embedScriptDisplay');
    if (scriptDisplay && data.script) {
        scriptDisplay.textContent = data.script;
    }
    
    // Update form fields
    if (data.position) {
        document.getElementById('widget_position').value = data.position;
    }
    if (data.theme) {
        document.getElementById('widget_theme').value = data.theme;
    }
    if (data.debug !== undefined) {
        document.getElementById('enable_debug').checked = data.debug;
    }
}

// Copy embed script
function copyEmbedScript() {
    const scriptText = document.getElementById('embedScriptDisplay').textContent;
    
    navigator.clipboard.writeText(scriptText).then(() => {
        showMessage('successMessage', 'Embed kodu kopyalandı!');
    }).catch(err => {
        console.error('Copy failed:', err);
        showMessage('errorMessage', 'Kopyalama başarısız oldu');
    });
}

// Test embed script
function testEmbedScript() {
    // Open test page in new tab
    const testUrl = '/dashboard/widget-customization/test-widget';
    window.open(testUrl, '_blank');
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
