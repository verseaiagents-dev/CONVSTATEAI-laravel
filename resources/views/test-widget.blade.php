<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ConvState AI Widget - Development Test</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>ConvState AI Widget - Development Test</h1>
        
        <div id="status" class="status">
            ✅ Widget yükleniyor...
        </div>
        
        <div class="widget-container">
            <!-- ConvState AI Widget buraya yüklenecek -->
            <div id="convstateai-widget"></div>
        </div>
        
        <!-- Test Butonları -->
        <div style="margin-top: 20px; text-align: center;">
            <h3>🧪 Limit Exceeded Widget Test</h3>
            <button onclick="simulateLimitExceeded()" style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 5px;">
                🚨 Limit Aşımı Simüle Et
            </button>
            <button onclick="resetWidget()" style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 5px;">
                🔄 Widget'ı Sıfırla
            </button>
            <button onclick="testChat()" style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 5px;">
                💬 Chat Test Et
            </button>
        </div>
    </div>

    <!-- ConvState AI Widget Script -->
    <script>
        // Widget yapılandırması - Development modu
        window.convstateaiConfig = {
            apiKey: 'test-api-key',
            projectId: '1',
            environment: 'development',
            apiBaseUrl: '{{ url('/') }}',
            debug: true
        };
        
        // Status güncelleme
        function updateStatus(message, isError = false) {
            const statusEl = document.getElementById('status');
            statusEl.textContent = message;
            statusEl.className = isError ? 'error' : 'status';
        }
        
        // Widget yükleme kontrolü
        window.addEventListener('load', function() {
            setTimeout(() => {
                if (window.convstateai && window.convstateai.isLoaded()) {
                    updateStatus('✅ Widget yüklendi ve hazır!');
                } else {
                    updateStatus('⚠️ Widget yüklendi ama aktif değil', true);
                }
            }, 2000);
        });
        
        // Test fonksiyonları
        function simulateLimitExceeded() {
            console.log('🚨 Simulating daily limit exceeded...');
            
            // Limit exceeded event'ini tetikle
            const event = new CustomEvent('convstateai:limitExceeded', {
                detail: {
                    error: 'DAILY_LIMIT_EXCEEDED',
                    message: 'Kullanım limiti aşıldı',
                    data: {
                        session_id: 'test-session-' + Date.now(),
                        daily_view_count: 10,
                        daily_view_limit: 10
                    }
                }
            });
            window.dispatchEvent(event);
            
            updateStatus('🚨 Limit aşımı simüle edildi! Widget görünmeli...');
        }
        
        function resetWidget() {
            console.log('🔄 Resetting widget...');
            
            // Widget'ı yeniden yükle
            if (window.convstateai && window.convstateai.destroy) {
                window.convstateai.destroy();
            }
            
            // Sayfayı yenile
            setTimeout(() => {
                location.reload();
            }, 500);
        }
        
        function testChat() {
            console.log('💬 Testing chat functionality...');
            
            // Chat'i açmaya çalış
            if (window.convstateai && window.convstateai.openChat) {
                window.convstateai.openChat();
                updateStatus('💬 Chat açılmaya çalışılıyor...');
            } else {
                updateStatus('⚠️ Chat fonksiyonu bulunamadı', true);
            }
        }
    </script>
    
    <!-- Widget script'i yükle - Laravel public klasöründen -->
    <script src="{{ asset('embed/convstateai.min.js') }}"></script>
</body>
</html>
