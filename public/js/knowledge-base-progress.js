/**
 * Knowledge Base Background Processing Progress Tracker
 * Bu dosya knowledge base işleme durumunu takip eder ve kullanıcıya gerçek zamanlı güncellemeler sağlar.
 */

class KnowledgeBaseProgressTracker {
    constructor() {
        this.pollingInterval = 1000; // 1 saniye - daha hızlı güncelleme
        this.maxPollingTime = 300000; // 5 dakika
        this.pollingStartTime = null;
        this.currentPollingInterval = null;
        this.processingElements = new Map();
    }

    /**
     * Knowledge base işleme durumunu takip etmeye başla
     */
    startTracking(knowledgeBaseId, options = {}) {
        const {
            onProgress = () => {},
            onComplete = () => {},
            onError = () => {},
            onTimeout = () => {}
        } = options;

        this.pollingStartTime = Date.now();
        
        // Mevcut polling'i durdur
        this.stopTracking();

        // İlk kontrolü hemen yap
        this.checkStatus(knowledgeBaseId, {
            onProgress,
            onComplete,
            onError,
            onTimeout
        });

        // Periyodik kontrolü başlat
        this.currentPollingInterval = setInterval(() => {
            this.checkStatus(knowledgeBaseId, {
                onProgress,
                onComplete,
                onError,
                onTimeout
            });
        }, this.pollingInterval);
    }

    /**
     * Takibi durdur
     */
    stopTracking() {
        if (this.currentPollingInterval) {
            clearInterval(this.currentPollingInterval);
            this.currentPollingInterval = null;
        }
    }

    /**
     * İşleme durumunu kontrol et
     */
    async checkStatus(knowledgeBaseId, callbacks) {
        try {
            // Timeout kontrolü
            if (Date.now() - this.pollingStartTime > this.maxPollingTime) {
                this.stopTracking();
                callbacks.onTimeout();
                return;
            }

            const response = await fetch(`/api/knowledge-base/${knowledgeBaseId}/processing-status`);
            const data = await response.json();

            if (data.success) {
                const status = data.processing_status;
                const isProcessing = data.is_processing;
                const progressPercentage = data.progress_percentage || 0;
                const chunkCount = data.chunk_count || 0;
                const totalRecords = data.total_records || 0;
                const processedRecords = data.processed_records || 0;
                const errorMessage = data.error_message;

                // Progress callback
                callbacks.onProgress({
                    status,
                    isProcessing,
                    progressPercentage,
                    chunkCount,
                    totalRecords,
                    processedRecords,
                    errorMessage
                });

                // Tamamlandı veya başarısız oldu
                if (status === 'completed' || status === 'failed') {
                    this.stopTracking();
                    
                    if (status === 'completed') {
                        callbacks.onComplete({
                            chunkCount,
                            totalRecords,
                            processedRecords
                        });
                    } else {
                        callbacks.onError({
                            errorMessage,
                            status
                        });
                    }
                }
            } else {
                console.error('Status check failed:', data.message);
            }
        } catch (error) {
            console.error('Error checking status:', error);
        }
    }

    /**
     * Tüm işleme durumlarını getir
     */
    async getProcessingList() {
        try {
            const response = await fetch('/api/knowledge-base/processing/list');
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error getting processing list:', error);
            return { success: false, knowledge_bases: [] };
        }
    }

    /**
     * Başarısız işlemi yeniden dene
     */
    async retryProcessing(knowledgeBaseId) {
        try {
            const response = await fetch(`/api/knowledge-base/${knowledgeBaseId}/retry`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error retrying processing:', error);
            return { success: false, message: 'Retry failed' };
        }
    }

    /**
     * Progress bar güncelle
     */
    updateProgressBar(elementId, progressData) {
        const element = document.getElementById(elementId);
        if (!element) {
            console.warn(`Progress element not found: ${elementId}`);
            return;
        }

        const { progressPercentage = 0, status, isProcessing, chunkCount = 0, totalRecords = 0, processedRecords = 0 } = progressData;

        console.log(`Updating progress for ${elementId}:`, progressData);

        // Progress bar
        const progressBar = element.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = `${Math.max(progressPercentage, 5)}%`; // Minimum %5 göster
            progressBar.setAttribute('aria-valuenow', progressPercentage);
            
            // Progress yüzdesini göster
            const percentageText = progressBar.querySelector('.percentage-text');
            if (percentageText) {
                percentageText.textContent = `${Math.round(progressPercentage)}%`;
            } else {
                // Eğer yüzde text yoksa oluştur
                const text = document.createElement('span');
                text.className = 'percentage-text';
                text.textContent = `${Math.round(progressPercentage)}%`;
                progressBar.appendChild(text);
            }
        }

        // Status text
        const statusText = element.querySelector('.status-text');
        if (statusText) {
            let statusMessage = '';
            switch (status) {
                case 'pending':
                    statusMessage = 'İşleme bekleniyor...';
                    break;
                case 'processing':
                    statusMessage = `İşleniyor... (${processedRecords}/${totalRecords})`;
                    break;
                case 'completed':
                    statusMessage = `✅ Tamamlandı! ${chunkCount} chunk oluşturuldu.`;
                    break;
                case 'failed':
                    statusMessage = '❌ İşleme başarısız oldu.';
                    break;
                default:
                    statusMessage = 'Bilinmeyen durum';
            }
            statusText.textContent = statusMessage;
        }

        // Chunk count
        const chunkCountElement = element.querySelector('.chunk-count');
        if (chunkCountElement) {
            chunkCountElement.textContent = chunkCount;
        }

        // Processing indicator
        const processingIndicator = element.querySelector('.processing-indicator');
        if (processingIndicator) {
            if (isProcessing || status === 'processing') {
                processingIndicator.classList.add('spinning');
                processingIndicator.style.display = 'inline-block';
            } else {
                processingIndicator.classList.remove('spinning');
                processingIndicator.style.display = 'none';
            }
        }

        // Progress bar rengini duruma göre değiştir
        if (progressBar) {
            progressBar.classList.remove('bg-blue-500', 'bg-green-500', 'bg-red-500', 'bg-yellow-500');
            switch (status) {
                case 'pending':
                    progressBar.classList.add('bg-yellow-500');
                    break;
                case 'processing':
                    progressBar.classList.add('bg-blue-500');
                    break;
                case 'completed':
                    progressBar.classList.add('bg-green-500');
                    break;
                case 'failed':
                    progressBar.classList.add('bg-red-500');
                    break;
            }
        }
    }

    /**
     * Notification göster
     */
    showNotification(message, type = 'info') {
        // Toast notification sistemi
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // 5 saniye sonra otomatik kapat
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }
}

// Global instance
window.KnowledgeBaseProgressTracker = KnowledgeBaseProgressTracker;

// Auto-initialize if data attributes are present
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Knowledge Base Progress Tracker initialized');
    
    const progressElements = document.querySelectorAll('[data-knowledge-base-id]');
    console.log(`📊 Found ${progressElements.length} progress elements`);
    
    progressElements.forEach(element => {
        const knowledgeBaseId = element.getAttribute('data-knowledge-base-id');
        console.log(`🔄 Starting tracking for Knowledge Base ID: ${knowledgeBaseId}`);
        
        const tracker = new KnowledgeBaseProgressTracker();
        
        tracker.startTracking(knowledgeBaseId, {
            onProgress: (data) => {
                console.log(`📈 Progress update for KB ${knowledgeBaseId}:`, data);
                tracker.updateProgressBar(element.id, data);
            },
            onComplete: (data) => {
                console.log(`✅ Completed for KB ${knowledgeBaseId}:`, data);
                tracker.updateProgressBar(element.id, { ...data, status: 'completed', isProcessing: false, progressPercentage: 100 });
                tracker.showNotification(`Knowledge base işleme tamamlandı! ${data.chunkCount} chunk oluşturuldu.`, 'success');
                
                // 2 saniye sonra sayfayı yenile
                setTimeout(() => {
                    console.log('🔄 Refreshing page...');
                    location.reload();
                }, 2000);
            },
            onError: (data) => {
                console.log(`❌ Error for KB ${knowledgeBaseId}:`, data);
                tracker.updateProgressBar(element.id, { ...data, status: 'failed', isProcessing: false });
                tracker.showNotification(`Knowledge base işleme başarısız: ${data.errorMessage}`, 'danger');
            },
            onTimeout: () => {
                console.log(`⏰ Timeout for KB ${knowledgeBaseId}`);
                tracker.updateProgressBar(element.id, { status: 'timeout', isProcessing: false });
                tracker.showNotification('İşleme zaman aşımına uğradı. Lütfen sayfayı yenileyin.', 'warning');
            }
        });
    });
});

// Global debug function
window.debugKnowledgeBaseProgress = function(kbId) {
    console.log(`🔍 Debugging Knowledge Base ${kbId}`);
    fetch(`/api/knowledge-base/${kbId}/processing-status`)
        .then(response => response.json())
        .then(data => {
            console.log('📊 Current status:', data);
        })
        .catch(error => {
            console.error('❌ Debug error:', error);
        });
};

// CSS for progress indicators
const style = document.createElement('style');
style.textContent = `
    .processing-indicator {
        display: none;
        width: 16px;
        height: 16px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    .processing-indicator.spinning {
        display: inline-block !important;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .progress-container {
        margin: 10px 0;
    }
    
    .progress {
        height: 25px;
        background-color: #374151;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #4B5563;
    }
    
    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #3B82F6, #8B5CF6);
        transition: width 0.5s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        font-weight: bold;
        position: relative;
    }
    
    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    .status-text {
        margin-top: 5px;
        font-size: 14px;
        color: #9CA3AF;
        font-weight: 500;
    }
    
    .chunk-count {
        font-weight: bold;
        color: #3B82F6;
    }
    
    .percentage-text {
        position: relative;
        z-index: 2;
        font-size: 11px;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }
    
    .progress-bar.bg-yellow-500 {
        background: linear-gradient(90deg, #F59E0B, #FBBF24) !important;
    }
    
    .progress-bar.bg-blue-500 {
        background: linear-gradient(90deg, #3B82F6, #8B5CF6) !important;
    }
    
    .progress-bar.bg-green-500 {
        background: linear-gradient(90deg, #10B981, #34D399) !important;
    }
    
    .progress-bar.bg-red-500 {
        background: linear-gradient(90deg, #EF4444, #F87171) !important;
    }
`;
document.head.appendChild(style);
