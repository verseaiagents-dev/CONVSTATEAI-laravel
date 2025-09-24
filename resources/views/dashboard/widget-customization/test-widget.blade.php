<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Widget Test - ConvStateAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard.widget-customization') }}" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <h1 class="text-xl font-semibold text-gray-900">Widget Test Sayfası</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">Test Modu</span>
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Test Information -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Test Bilgileri</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Project ID</label>
                        <input type="text" value="{{ request('project_id') ?? '1' }}" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">API URL</label>
                        <input type="text" value="{{ config('app.url') }}/api" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customization Token</label>
                        <input type="text" value="{{ Auth::user()->personal_token ?? 'Not set' }}" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
                    </div>
                </div>
            </div>

            <!-- Widget Status -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Widget Durumu</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Script Yüklendi</span>
                        <div id="scriptStatus" class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                            <span class="text-sm text-gray-500">Bekleniyor...</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Widget Aktif</span>
                        <div id="widgetStatus" class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                            <span class="text-sm text-gray-500">Bekleniyor...</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">API Bağlantısı</span>
                        <div id="apiStatus" class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                            <span class="text-sm text-gray-500">Bekleniyor...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Instructions -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">Test Talimatları</h3>
            <div class="space-y-2 text-sm text-blue-800">
                <p>1. Widget'ın sağ alt köşede görünmesini bekleyin</p>
                <p>2. Widget'a tıklayarak açılmasını test edin</p>
                <p>3. Eylem butonlarının çalışıp çalışmadığını kontrol edin</p>
                <p>4. AI asistan ile konuşmayı test edin</p>
                <p>5. Herhangi bir hata durumunda konsol loglarını kontrol edin</p>
            </div>
        </div>

        <!-- Console Logs -->
        <div class="mt-8 bg-gray-900 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Konsol Logları</h3>
            <div id="consoleLogs" class="bg-black rounded p-4 h-64 overflow-y-auto text-green-400 font-mono text-sm">
                <div>Widget test sayfası yüklendi...</div>
                <div>Script yükleniyor...</div>
            </div>
        </div>
    </div>

    <!-- Widget Script -->
    <script src="{{ url('/embed/convstateai.min.js') }}"></script>
    <script>
        // Widget configuration
        window.convstateaiConfig = {
            projectId: "{{ request('project_id') ?? '1' }}",
            customizationToken: "{{ Auth::user()->personal_token ?? 'dcf91b8e63c9552b724a4523261318e565ef33992e454dbc0cff1064aae19246' }}",
            apiUrl: "{{ config('app.url') }}/api",
            debug: true
        };

        // Console logging
        const consoleLogs = document.getElementById('consoleLogs');
        const originalLog = console.log;
        const originalError = console.error;

        function addLog(message, type = 'log') {
            const timestamp = new Date().toLocaleTimeString();
            const logElement = document.createElement('div');
            logElement.className = type === 'error' ? 'text-red-400' : 'text-green-400';
            logElement.textContent = `[${timestamp}] ${message}`;
            consoleLogs.appendChild(logElement);
            consoleLogs.scrollTop = consoleLogs.scrollHeight;
        }

        console.log = function(...args) {
            originalLog.apply(console, args);
            addLog(args.join(' '), 'log');
        };

        console.error = function(...args) {
            originalError.apply(console, args);
            addLog(args.join(' '), 'error');
        };

        // Widget status monitoring
        let scriptLoaded = false;
        let widgetLoaded = false;
        let apiConnected = false;

        // Monitor script loading
        const script = document.querySelector('script[src*="convstateai.min.js"]');
        if (script) {
            script.onload = function() {
                scriptLoaded = true;
                updateStatus('scriptStatus', true, 'Yüklendi');
                addLog('Widget script başarıyla yüklendi');
                
                // Check if widget is available
                setTimeout(() => {
                    if (window.convstateai) {
                        widgetLoaded = true;
                        updateStatus('widgetStatus', true, 'Aktif');
                        addLog('Widget başarıyla başlatıldı');
                        
                        // Test API connection
                        testAPIConnection();
                    } else {
                        updateStatus('widgetStatus', false, 'Hata');
                        addLog('Widget başlatılamadı', 'error');
                    }
                }, 2000);
            };
            
            script.onerror = function() {
                updateStatus('scriptStatus', false, 'Hata');
                addLog('Widget script yüklenemedi', 'error');
            };
        }

        // Update status indicator
        function updateStatus(elementId, success, text) {
            const element = document.getElementById(elementId);
            const indicator = element.querySelector('.w-3');
            const textElement = element.querySelector('span:last-child');
            
            if (success) {
                indicator.className = 'w-3 h-3 bg-green-500 rounded-full';
                textElement.className = 'text-sm text-green-600';
            } else {
                indicator.className = 'w-3 h-3 bg-red-500 rounded-full';
                textElement.className = 'text-sm text-red-600';
            }
            
            textElement.textContent = text;
        }

        // Test API connection
        async function testAPIConnection() {
            try {
                const response = await fetch('{{ config('app.url') }}/api/widget-customization', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                if (response.ok) {
                    apiConnected = true;
                    updateStatus('apiStatus', true, 'Bağlı');
                    addLog('API bağlantısı başarılı');
                } else {
                    updateStatus('apiStatus', false, 'Hata');
                    addLog('API bağlantısı başarısız', 'error');
                }
            } catch (error) {
                updateStatus('apiStatus', false, 'Hata');
                addLog('API bağlantı hatası: ' + error.message, 'error');
            }
        }

        // Monitor widget events
        document.addEventListener('DOMContentLoaded', function() {
            addLog('Test sayfası yüklendi');
            
            // Listen for widget events
            window.addEventListener('convstateai:loaded', function() {
                addLog('Widget loaded event received');
            });
            
            window.addEventListener('convstateai:error', function(event) {
                addLog('Widget error event: ' + event.detail, 'error');
            });
        });
    </script>
</body>
</html>
