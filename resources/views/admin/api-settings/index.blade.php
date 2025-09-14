@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">API Ayarları</h1>
            <p class="text-gray-400 mt-2">AI servisleri için API anahtarlarını yönetin</p>
        </div>
        <button onclick="openCreateModal()" class="px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Yeni API Ekle</span>
        </button>
    </div>

    <!-- API Settings Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($apiSettings as $apiSetting)
        <div class="glass-effect rounded-2xl p-6 hover:shadow-2xl transition-all duration-300">
            <!-- API Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <h3 class="text-xl font-bold text-white">{{ $apiSetting->name }}</h3>
                        @if($apiSetting->is_active)
                            <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded-full text-xs">Aktif</span>
                        @else
                            <span class="px-2 py-1 bg-red-500/20 text-red-400 rounded-full text-xs">Pasif</span>
                        @endif
                    </div>
                    <p class="text-gray-400 text-sm mb-3">{{ $apiSetting->provider }}</p>
                    @if($apiSetting->description)
                        <p class="text-gray-300 text-sm">{{ Str::limit($apiSetting->description, 100) }}</p>
                    @endif
                </div>
            </div>

            <!-- API Key Preview -->
            <div class="mb-4 p-3 bg-gray-800/30 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="text-gray-400 text-sm">API Key:</span>
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-300 text-sm font-mono">
                            @php
                                $key = $apiSetting->api_key;
                                $length = strlen($key);
                                if ($length > 20) {
                                    echo substr($key, 0, 8) . '...' . substr($key, -8);
                                } else {
                                    echo $key;
                                }
                            @endphp
                        </span>
                        <button onclick="copyToClipboard('{{ $apiSetting->api_key }}')" class="text-blue-400 hover:text-blue-300 transition-colors" title="Kopyala">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                @if($apiSetting->base_url)
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-gray-400 text-sm">Base URL:</span>
                    <span class="text-gray-300 text-sm">{{ $apiSetting->base_url }}</span>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <button onclick="toggleActive({{ $apiSetting->id }})" id="toggle-btn-{{ $apiSetting->id }}" class="px-3 py-1 text-xs rounded-lg transition-colors {{ $apiSetting->is_active ? 'bg-red-500/20 text-red-400 hover:bg-red-500/30' : 'bg-green-500/20 text-green-400 hover:bg-green-500/30' }}">
                        {{ $apiSetting->is_active ? 'Pasif Yap' : 'Aktif Yap' }}
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="openEditModal({{ $apiSetting->id }})" class="text-blue-400 hover:text-blue-300 transition-colors" title="Düzenle">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button onclick="deleteApiSetting({{ $apiSetting->id }})" id="delete-btn-{{ $apiSetting->id }}" class="text-red-400 hover:text-red-300 transition-colors" title="Sil">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Created Date -->
            <div class="mt-4 pt-4 border-t border-gray-700">
                <div class="text-xs text-gray-500">
                    Oluşturulma: {{ $apiSetting->created_at->format('d.m.Y H:i') }}
                </div>
            </div>
        </div>
        @empty

        <!-- Empty State -->
        <div class="col-span-full text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-300 mb-2">Henüz API Ayarı Yok</h3>
            <p class="text-gray-400 mb-6">İlk API ayarınızı oluşturmak için yukarıdaki butona tıklayın.</p>
            <button onclick="openCreateModal()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                İlk API'yi Ekle
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- Create API Modal -->
<div id="createApiModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-white">Yeni API Ayarı Ekle</h2>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <form id="createApiSettingForm" class="space-y-6">
                @csrf
                
                <!-- API Name -->
                <div>
                    <label for="create_name" class="block text-sm font-medium text-gray-300 mb-2">API Adı <span class="text-red-400">*</span></label>
                    <input type="text" id="create_name" name="name" required class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700" placeholder="Örn: OpenAI Production">
                </div>

                <!-- Provider -->
                <div>
                    <label for="create_provider" class="block text-sm font-medium text-gray-300 mb-2">Sağlayıcı <span class="text-red-400">*</span></label>
                    <select id="create_provider" name="provider" required class="form-select w-full px-4 py-3 rounded-lg text-white bg-gray-800/50 border border-gray-700">
                        <option value="">Sağlayıcı Seçin</option>
                        <optgroup label="AI Servisleri">
                            <option value="openai">OpenAI (GPT-4, GPT-3.5)</option>
                            <option value="google">Google AI (Gemini)</option>
                            <option value="anthropic">Anthropic Claude</option>
                            <option value="cohere">Cohere</option>
                            <option value="huggingface">Hugging Face</option>
                        </optgroup>
                        <optgroup label="Sosyal Medya API'leri">
                            <option value="twitter">Twitter API</option>
                            <option value="facebook">Facebook API</option>
                            <option value="instagram">Instagram API</option>
                            <option value="linkedin">LinkedIn API</option>
                            <option value="youtube">YouTube API</option>
                        </optgroup>
                        <optgroup label="E-posta Servisleri">
                            <option value="sendgrid">SendGrid</option>
                            <option value="mailgun">Mailgun</option>
                            <option value="ses">Amazon SES</option>
                            <option value="mandrill">Mandrill</option>
                        </optgroup>
                        <optgroup label="Ödeme Sistemleri">
                            <option value="stripe">Stripe</option>
                            <option value="paypal">PayPal</option>
                            <option value="iyzico">İyzico</option>
                        </optgroup>
                        <optgroup label="Harita Servisleri">
                            <option value="google_maps">Google Maps</option>
                            <option value="mapbox">Mapbox</option>
                            <option value="here">HERE Maps</option>
                        </optgroup>
                        <optgroup label="Diğer Servisler">
                            <option value="twilio">Twilio (SMS)</option>
                            <option value="aws">Amazon AWS</option>
                            <option value="azure">Microsoft Azure</option>
                            <option value="firebase">Firebase</option>
                            <option value="custom">Özel API</option>
                        </optgroup>
                    </select>
                </div>

                <!-- API Key -->
                <div>
                    <label for="create_api_key" class="block text-sm font-medium text-gray-300 mb-2">API Key <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input type="password" id="create_api_key" name="api_key" required class="form-input w-full px-4 py-3 pr-12 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700" placeholder="API anahtarınızı girin">
                        <button type="button" onclick="togglePasswordVisibility('create_api_key')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white transition-colors">
                            <svg id="create_api_key_eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Base URL -->
                <div>
                    <label for="create_base_url" class="block text-sm font-medium text-gray-300 mb-2">Base URL (Opsiyonel)</label>
                    <input type="url" id="create_base_url" name="base_url" class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700" placeholder="https://api.openai.com/v1">
                </div>

                <!-- Description -->
                <div>
                    <label for="create_description" class="block text-sm font-medium text-gray-300 mb-2">Açıklama (Opsiyonel)</label>
                    <textarea id="create_description" name="description" rows="3" class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700 resize-none" placeholder="Bu API ayarı hakkında açıklama..."></textarea>
                </div>

                <!-- Status Options -->
                <div class="flex items-center space-x-3">
                    <input type="checkbox" id="create_is_active" name="is_active" value="1" checked class="w-4 h-4 text-purple-glow bg-gray-800 border-gray-600 rounded focus:ring-purple-glow focus:ring-2">
                    <label for="create_is_active" class="text-sm font-medium text-gray-300">Aktif</label>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center space-x-4 pt-6">
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300">
                        API Ayarını Kaydet
                    </button>
                    <button type="button" onclick="closeCreateModal()" class="px-6 py-3 bg-gray-600 hover:bg-gray-500 rounded-lg text-white font-semibold transition-colors">
                        İptal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit API Modal -->
<div id="editApiModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-white">API Ayarını Düzenle</h2>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <form id="editApiSettingForm" class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_api_id" name="api_id">
                
                <!-- API Name -->
                <div>
                    <label for="edit_name" class="block text-sm font-medium text-gray-300 mb-2">API Adı <span class="text-red-400">*</span></label>
                    <input type="text" id="edit_name" name="name" required class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700" placeholder="Örn: OpenAI Production">
                </div>

                <!-- Provider -->
                <div>
                    <label for="edit_provider" class="block text-sm font-medium text-gray-300 mb-2">Sağlayıcı <span class="text-red-400">*</span></label>
                    <select id="edit_provider" name="provider" required class="form-select w-full px-4 py-3 rounded-lg text-white bg-gray-800/50 border border-gray-700">
                        <option value="">Sağlayıcı Seçin</option>
                        <optgroup label="AI Servisleri">
                            <option value="openai">OpenAI (GPT-4, GPT-3.5)</option>
                            <option value="google">Google AI (Gemini)</option>
                            <option value="anthropic">Anthropic Claude</option>
                            <option value="cohere">Cohere</option>
                            <option value="huggingface">Hugging Face</option>
                        </optgroup>
                        <optgroup label="Sosyal Medya API'leri">
                            <option value="twitter">Twitter API</option>
                            <option value="facebook">Facebook API</option>
                            <option value="instagram">Instagram API</option>
                            <option value="linkedin">LinkedIn API</option>
                            <option value="youtube">YouTube API</option>
                        </optgroup>
                        <optgroup label="E-posta Servisleri">
                            <option value="sendgrid">SendGrid</option>
                            <option value="mailgun">Mailgun</option>
                            <option value="ses">Amazon SES</option>
                            <option value="mandrill">Mandrill</option>
                        </optgroup>
                        <optgroup label="Ödeme Sistemleri">
                            <option value="stripe">Stripe</option>
                            <option value="paypal">PayPal</option>
                            <option value="iyzico">İyzico</option>
                        </optgroup>
                        <optgroup label="Harita Servisleri">
                            <option value="google_maps">Google Maps</option>
                            <option value="mapbox">Mapbox</option>
                            <option value="here">HERE Maps</option>
                        </optgroup>
                        <optgroup label="Diğer Servisler">
                            <option value="twilio">Twilio (SMS)</option>
                            <option value="aws">Amazon AWS</option>
                            <option value="azure">Microsoft Azure</option>
                            <option value="firebase">Firebase</option>
                            <option value="custom">Özel API</option>
                        </optgroup>
                    </select>
                </div>

                <!-- API Key -->
                <div>
                    <label for="edit_api_key" class="block text-sm font-medium text-gray-300 mb-2">API Key <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input type="password" id="edit_api_key" name="api_key" required class="form-input w-full px-4 py-3 pr-12 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700" placeholder="API anahtarınızı girin">
                        <button type="button" onclick="togglePasswordVisibility('edit_api_key')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white transition-colors">
                            <svg id="edit_api_key_eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Base URL -->
                <div>
                    <label for="edit_base_url" class="block text-sm font-medium text-gray-300 mb-2">Base URL (Opsiyonel)</label>
                    <input type="url" id="edit_base_url" name="base_url" class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700" placeholder="https://api.openai.com/v1">
                </div>

                <!-- Description -->
                <div>
                    <label for="edit_description" class="block text-sm font-medium text-gray-300 mb-2">Açıklama (Opsiyonel)</label>
                    <textarea id="edit_description" name="description" rows="3" class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700 resize-none" placeholder="Bu API ayarı hakkında açıklama..."></textarea>
                </div>

                <!-- Status Options -->
                <div class="flex items-center space-x-3">
                    <input type="checkbox" id="edit_is_active" name="is_active" value="1" class="w-4 h-4 text-purple-glow bg-gray-800 border-gray-600 rounded focus:ring-purple-glow focus:ring-2">
                    <label for="edit_is_active" class="text-sm font-medium text-gray-300">Aktif</label>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center space-x-4 pt-6">
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300">
                        Değişiklikleri Kaydet
                    </button>
                    <button type="button" onclick="closeEditModal()" class="px-6 py-3 bg-gray-600 hover:bg-gray-500 rounded-lg text-white font-semibold transition-colors">
                        İptal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// CSRF Token helper
function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('API Key kopyalandı!', 'success');
    });
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500/20 border-green-500/30 text-green-400' : 'bg-red-500/20 border-red-500/30 text-red-400';
    
    toast.className = `fixed top-4 right-4 ${bgColor} border px-4 py-2 rounded-lg z-50 transition-all duration-300 transform translate-x-full`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

function toggleActive(id) {
    const button = document.getElementById(`toggle-btn-${id}`);
    if (!button) {
        showToast('Buton bulunamadı!', 'error');
        return;
    }
    
    const originalText = button.textContent;
    
    // Show loading state
    button.disabled = true;
    button.textContent = 'İşleniyor...';
    button.classList.add('opacity-50');
    
    const csrfToken = getCSRFToken();
    if (!csrfToken) {
        showToast('CSRF token bulunamadı!', 'error');
        button.disabled = false;
        button.textContent = originalText;
        button.classList.remove('opacity-50');
        return;
    }
    
    fetch(`/admin/api-settings/${id}/toggle-active`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            showToast(data.message || 'API durumu güncellendi', 'success');
            // Reload page to show updated status
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast('Hata: ' + (data.message || 'Bilinmeyen hata oluştu'), 'error');
            // Reset button state
            button.disabled = false;
            button.textContent = originalText;
            button.classList.remove('opacity-50');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        // Reset button state
        button.disabled = false;
        button.textContent = originalText;
        button.classList.remove('opacity-50');
    });
}



function deleteApiSetting(id) {
    if (confirm('Bu API ayarını silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
        const button = document.getElementById(`delete-btn-${id}`);
        
        // Show loading state
        button.disabled = true;
        button.classList.add('opacity-50');
        
        fetch(`/admin/api-settings/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast(data.message || 'API ayarı silindi', 'success');
                // Reload page to show updated list
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showToast('Hata: ' + (data.message || 'Bilinmeyen hata oluştu'), 'error');
                // Reset button state
                button.disabled = false;
                button.classList.remove('opacity-50');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
            // Reset button state
            button.disabled = false;
            button.classList.remove('opacity-50');
        });
    }
}

// Modal Functions
function openCreateModal() {
    document.getElementById('createApiModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCreateModal() {
    document.getElementById('createApiModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('createApiSettingForm').reset();
}

function openEditModal(id) {
    // Fetch API setting data
    fetch(`/admin/api-settings/${id}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const apiSetting = data.data;
            
            // Populate form fields
            document.getElementById('edit_api_id').value = apiSetting.id;
            document.getElementById('edit_name').value = apiSetting.name;
            document.getElementById('edit_provider').value = apiSetting.provider;
            document.getElementById('edit_api_key').value = apiSetting.api_key;
            document.getElementById('edit_base_url').value = apiSetting.base_url || '';
            document.getElementById('edit_description').value = apiSetting.description || '';
            document.getElementById('edit_is_active').checked = apiSetting.is_active;
            
            // Show modal
            document.getElementById('editApiModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    });
}

function closeEditModal() {
    document.getElementById('editApiModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const eye = document.getElementById(fieldId + '_eye');
    
    if (field.type === 'password') {
        field.type = 'text';
        eye.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
        `;
    } else {
        field.type = 'password';
        eye.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
        `;
    }
}

// Form submissions
document.getElementById('createApiSettingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("admin.api-settings.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'API ayarı başarıyla oluşturuldu', 'success');
            closeCreateModal();
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast('Hata: ' + (data.message || 'Bilinmeyen hata oluştu'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
    });
});

document.getElementById('editApiSettingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const apiId = document.getElementById('edit_api_id').value;
    
    fetch(`/admin/api-settings/${apiId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'API ayarı başarıyla güncellendi', 'success');
            closeEditModal();
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast('Hata: ' + (data.message || 'Bilinmeyen hata oluştu'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
    });
});

// Close modals when clicking outside
document.getElementById('createApiModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCreateModal();
    }
});

document.getElementById('editApiModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endsection
