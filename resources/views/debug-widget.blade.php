<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ConvState AI Widget - Debug Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .debug-info {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .widget-container {
            height: 600px;
            border: 2px solid #ddd;
            border-radius: 10px;
            position: relative;
        }
        .status {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .debug-log {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
            font-family: monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ConvState AI Widget - Debug Test</h1>
        
        <div class="debug-info">
            <h3>Debug Bilgileri:</h3>
            <ul>
                <li><strong>API URL:</strong> <span id="api-url">Yükleniyor...</span></li>
                <li><strong>Project ID:</strong> 1</li>
                <li><strong>Environment:</strong> development</li>
                <li><strong>Debug Mode:</strong> Aktif</li>
            </ul>
        </div>
        
        <div id="status" class="status">
            ✅ Widget yükleniyor...
        </div>
        
        <div class="widget-container">
            <div id="convstateai-widget"></div>
        </div>
        
        <div class="debug-log" id="debug-log">
            <strong>Debug Log:</strong><br>
            <div id="log-content"></div>
        </div>
    </div>

    <script>
        // Debug logging
        function debugLog(message) {
            const logContent = document.getElementById('log-content');
            const timestamp = new Date().toLocaleTimeString();
            logContent.innerHTML += `[${timestamp}] ${message}<br>`;
            logContent.scrollTop = logContent.scrollHeight;
        }

        // Widget yapılandırması - Development modu
        window.convstateaiConfig = {
            apiKey: 'test-api-key',
            projectId: '1',
            environment: 'development',
            apiBaseUrl: '{{ url('/') }}',
            debug: true
        };
        
        debugLog('Widget configuration loaded');
        debugLog('API Base URL: ' + window.convstateaiConfig.apiBaseUrl);
        
        // API URL'i göster
        document.getElementById('api-url').textContent = window.convstateaiConfig.apiBaseUrl;
        
        // Status güncelleme
        function updateStatus(message, isError = false) {
            const statusEl = document.getElementById('status');
            statusEl.textContent = message;
            statusEl.className = isError ? 'error' : 'status';
            debugLog('Status: ' + message);
        }
        
        // API test
        async function testAPI() {
            try {
                debugLog('Testing API endpoint...');
                const response = await fetch(`${window.convstateaiConfig.apiBaseUrl}/api/check-availability?project_id=1`);
                const data = await response.json();
                debugLog('API Response: ' + JSON.stringify(data, null, 2));
                
                if (data.success) {
                    updateStatus('✅ API test başarılı! Widget yükleniyor...');
                } else {
                    updateStatus('❌ API test başarısız: ' + data.message, true);
                }
            } catch (error) {
                debugLog('API Error: ' + error.message);
                updateStatus('❌ API test hatası: ' + error.message, true);
            }
        }
        
        // Widget yükleme kontrolü
        window.addEventListener('load', function() {
            debugLog('Page loaded, testing API...');
            testAPI();
            
            setTimeout(() => {
                if (window.convstateai && window.convstateai.isLoaded()) {
                    updateStatus('✅ Widget yüklendi ve hazır!');
                    debugLog('Widget successfully loaded and ready');
                } else {
                    updateStatus('⚠️ Widget yüklendi ama aktif değil', true);
                    debugLog('Widget loaded but not active');
                }
            }, 3000);
        });
        
        // Global error handler
        window.addEventListener('error', function(e) {
            debugLog('Global Error: ' + e.message + ' at ' + e.filename + ':' + e.lineno);
        });
        
        // Unhandled promise rejection handler
        window.addEventListener('unhandledrejection', function(e) {
            debugLog('Unhandled Promise Rejection: ' + e.reason);
        });
    </script>
    
    <!-- Widget script'i yükle -->
    <script src="{{ asset('dist/main.min.js') }}"></script>
</body>
</html>
