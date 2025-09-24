@extends('layouts.dashboard')

@section('title', 'Kampanya Yönetimi')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Campaign Management Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-4xl font-bold mb-4">
                        <span class="gradient-text">Kampanya Yönetimi</span>
                    </h1>
                    <p class="text-xl text-gray-300">
                        Kampanya performansı ve yönetimi
                    </p>
                </div>
                
                <button onclick="openCreateModal()" class="px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Yeni Kampanya Oluştur</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Campaign List -->
    <div class="glass-effect rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-gray-800 to-gray-900">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-white uppercase tracking-wider">Kampanya</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-white uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-white uppercase tracking-wider">İndirim</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-white uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-white uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" id="campaignsTableBody">
                    <!-- Campaigns will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="glass-effect rounded-2xl p-8 text-center">
        <div class="inline-flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-purple-glow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-300">Kampanyalar yükleniyor...</span>
        </div>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="hidden glass-effect rounded-2xl p-12 text-center">
        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
        </svg>
        <h3 class="text-xl font-semibold text-white mb-2">Henüz kampanya yok</h3>
        <p class="text-gray-400 mb-6">İlk kampanyanızı oluşturmaya başlayın ve müşterilerinizi memnun edin.</p>
        <button onclick="openCreateModal()" class="px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-xl text-white font-semibold hover:from-purple-dark hover:to-purple-glow transition-all duration-300 transform hover:scale-105 inline-flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>İlk Kampanyanızı Oluşturun</span>
        </button>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="campaignModal" class="fixed inset-0 bg-black bg-opacity-85 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50" onclick="closeModal()">
    <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 p-6 border border-gray-700 w-3/4 max-w-4xl max-h-[80vh] shadow-2xl rounded-xl glass-effect overflow-y-auto custom-scrollbar" onclick="event.stopPropagation()">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-white" id="modalTitle">Yeni Kampanya</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-white transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Campaign Creation Form -->
            <div class="space-y-6">
                <h4 class="text-lg font-semibold text-white mb-4">Kampanya Ayarları</h4>
                <form id="campaignForm" class="space-y-4">
                    <input type="hidden" id="campaignId" name="id">
                    <input type="hidden" name="project_id" value="1">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Kampanya Başlığı *</label>
                            <input type="text" id="title" name="title" required class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                        </div>
                        
                        <div>
                            <label for="campaign_code" class="block text-sm font-medium text-gray-300 mb-2">Kampanya Kodu</label>
                            <input type="text" id="campaign_code" name="campaign_code" readonly class="form-input w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-gray-300 cursor-not-allowed" placeholder="Otomatik oluşturulur">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-300 mb-2">Kategori *</label>
                            <select id="category" name="category" required class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                                <option value="" class="bg-gray-800 text-white">Kategori Seçin</option>
                                <option value="Moda" class="bg-gray-800 text-white">Moda</option>
                                <option value="Elektronik" class="bg-gray-800 text-white">Elektronik</option>
                                <option value="Ev & Yaşam" class="bg-gray-800 text-white">Ev & Yaşam</option>
                                <option value="Spor" class="bg-gray-800 text-white">Spor</option>
                                <option value="Kozmetik" class="bg-gray-800 text-white">Kozmetik</option>
                                <option value="Genel" class="bg-gray-800 text-white">Genel</option>
                                <option value="Üyelik" class="bg-gray-800 text-white">Üyelik</option>
                                <option value="Ödeme" class="bg-gray-800 text-white">Ödeme</option>
                                <option value="Kargo" class="bg-gray-800 text-white">Kargo</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="priority_level" class="block text-sm font-medium text-gray-300 mb-2">Öncelik Seviyesi</label>
                            <select id="priority_level" name="priority_level" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                                <option value="1" class="bg-gray-800 text-white">Düşük</option>
                                <option value="2" class="bg-gray-800 text-white" selected>Normal</option>
                                <option value="3" class="bg-gray-800 text-white">Orta</option>
                                <option value="4" class="bg-gray-800 text-white">Yüksek</option>
                                <option value="5" class="bg-gray-800 text-white">Kritik</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Açıklama *</label>
                        <textarea id="description" name="description" rows="3" required class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"></textarea>
                    </div>

                    <div>
                        <label for="target_audience" class="block text-sm font-medium text-gray-300 mb-2">Hedef Kitle</label>
                        <textarea id="target_audience" name="target_audience" rows="2" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200" placeholder="Örn: 18-35 yaş arası, teknoloji meraklısı müşteriler"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="discount" class="block text-sm font-medium text-gray-300 mb-2">İndirim Açıklaması *</label>
                            <input type="text" id="discount" name="discount" required class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200" placeholder="Örn: %20 İndirim">
                        </div>
                        
                        <div>
                            <label for="discount_type" class="block text-sm font-medium text-gray-300 mb-2">İndirim Türü *</label>
                            <select id="discount_type" name="discount_type" required class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                                <option value="buy_x_get_y" class="bg-gray-800 text-white">2 Al 1 Bedava</option>
                                <option value="percentage" class="bg-gray-800 text-white">Yüzde İndirim</option>
                                <option value="fixed" class="bg-gray-800 text-white">Sabit İndirim</option>
                                <option value="free_shipping" class="bg-gray-800 text-white">Ücretsiz Kargo</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label for="discount_value" class="block text-sm font-medium text-gray-300 mb-2">İndirim Değeri</label>
                        <input type="number" id="discount_value" name="discount_value" step="0.01" min="0" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-300 mb-2">Başlangıç Tarihi</label>
                            <input type="datetime-local" id="start_date" name="start_date" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                        </div>
                        
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-white mb-2">Bitiş Tarihi</label>
                            <input type="datetime-local" id="end_date" name="end_date" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="minimum_order_amount" class="block text-sm font-medium text-gray-300 mb-2">Minimum Sipariş Tutarı</label>
                            <input type="number" id="minimum_order_amount" name="minimum_order_amount" step="0.01" min="0" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                        </div>
                        
                        <div>
                            <label for="budget_limit" class="block text-sm font-medium text-gray-300 mb-2">Bütçe Limiti (TL)</label>
                            <input type="number" id="budget_limit" name="budget_limit" step="0.01" min="0" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="max_usage" class="block text-sm font-medium text-gray-300 mb-2">Maksimum Kullanım</label>
                            <input type="number" id="max_usage" name="max_usage" min="1" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                        </div>
                        
                        <div>
                            <label for="approval_status" class="block text-sm font-medium text-gray-300 mb-2">Onay Durumu</label>
                            <select id="approval_status" name="approval_status" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                                <option value="pending" class="bg-gray-800 text-white">Beklemede</option>
                                <option value="approved" class="bg-gray-800 text-white">Onaylandı</option>
                                <option value="rejected" class="bg-gray-800 text-white">Reddedildi</option>
                                <option value="draft" class="bg-gray-800 text-white">Taslak</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="image_url" class="block text-sm font-medium text-gray-300 mb-2">Resim URL</label>
                        <input type="url" id="image_url" name="image_url" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                    </div>

                    <div>
                        <label for="terms_conditions" class="block text-sm font-medium text-gray-300 mb-2">Şartlar ve Koşullar</label>
                        <textarea id="terms_conditions" name="terms_conditions" rows="3" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"></textarea>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-300 mb-2">Notlar</label>
                        <textarea id="notes" name="notes" rows="2" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200" placeholder="Kampanya hakkında ek notlar..."></textarea>
                    </div>

                    <div class="flex items-center space-x-6">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="is_active" name="is_active" checked class="form-checkbox">
                            <label for="is_active" class="block text-sm text-gray-300">Kampanyayı aktif yap</label>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="requires_approval" name="requires_approval" class="form-checkbox">
                            <label for="requires_approval" class="block text-sm text-gray-300">Onay gerektir</label>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6">
                        <button type="button" onclick="closeModal()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all duration-200">
                            İptal
                        </button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-purple-glow transition-all duration-300 transform hover:scale-105">
                            Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-85 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50" onclick="closeDeleteModal()">
    <div class="relative top-20 mx-auto p-6 border border-gray-700 w-96 shadow-2xl rounded-xl glass-effect" onclick="event.stopPropagation()">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-900/20 border border-red-500">
                <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-white mt-4">Kampanyayı Sil</h3>
            <p class="text-gray-400 mt-2">Bu kampanyayı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.</p>
            <div class="flex justify-center space-x-3 mt-6">
                <button onclick="closeDeleteModal()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all duration-200">
                    İptal
                </button>
                <button onclick="confirmDelete()" class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-lg transition-all duration-300 transform hover:scale-105">
                    Sil
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let campaigns = [];
let currentCampaignId = null;
let currentAISuggestions = null; // AI suggestions for current session

// Load campaigns on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCampaigns();
});

// Load campaigns from API
async function loadCampaigns() {
    try {
        showLoading();
        const response = await fetch('/api/campaigns?project_id=1', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            campaigns = result.data || [];
            displayCampaigns();
        } else {
            console.warn('Campaign API returned success=false:', result.message);
            campaigns = [];
            displayCampaigns();
        }
    } catch (error) {
        console.error('Campaign loading error:', error);
        campaigns = [];
        displayCampaigns();
    }
}

// Display campaigns in table
function displayCampaigns() {
    const tbody = document.getElementById('campaignsTableBody');
    const loadingState = document.getElementById('loadingState');
    const emptyState = document.getElementById('emptyState');
    
    if (campaigns.length === 0) {
        loadingState.classList.add('hidden');
        emptyState.classList.remove('hidden');
        return;
    }
    
    loadingState.classList.add('hidden');
    emptyState.classList.add('hidden');
    
    tbody.innerHTML = campaigns.map(campaign => `
        <tr class="hover:bg-gray-800/30 transition-colors duration-200">
            <td class="px-6 py-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12">
                        <img class="h-12 w-12 rounded-xl object-cover border border-gray-600" src="${campaign.image_url || 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjMzc0MTUxIi8+CjxwYXRoIGQ9Ik0yMCAxMkMxNi42ODYzIDEyIDEzLjYyNjEgMTMuNzQwOSAxMiAxNi41QzEzLjYyNjEgMTkuMjU5MSAxNi42ODYzIDIxIDIwIDIxQzIzLjMxMzcgMjEgMjYuMzczOSAxOS4yNTkxIDI4IDE2LjVDMjYuMzczOSAxMy43NDA5IDIzLjMxMzcgMTIgMjAgMTJaIiBmaWxsPSIjNkI3Mjg4Ii8+CjxwYXRoIGQ9Ik0zMiAyOEMzMCAyNC42ODYzIDI2LjMxMzcgMjIgMjIgMjJIMThDMTMuNjg2MyAyMiAxMCAyNC42ODYzIDEwIDI4IiBmaWxsPSIjNkI3Mjg4Ii8+Cjwvc3ZnPg=='}" alt="" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjMzc0MTUxIi8+CjxwYXRoIGQ9Ik0yMCAxMkMxNi42ODYzIDEyIDEzLjYyNjEgMTMuNzQwOSAxMiAxNi41QzEzLjYyNjEgMTkuMjU5MSAxNi42ODYzIDIxIDIwIDIxQzIzLjMxMzcgMjEgMjYuMzczOSAxOS4yNTkxIDI4IDE2LjVDMjYuMzczOSAxMy43NDA5IDIzLjMxMzcgMTIgMjAgMTJaIiBmaWxsPSIjNkI3Mjg4Ii8+CjxwYXRoIGQ9Ik0zMiAyOEMzMCAyNC42ODYzIDI2LjMxMzcgMjIgMjIgMjJIMThDMTMuNjg2MyAyMiAxMCAyNC42ODYzIDEwIDI4IiBmaWxsPSIjNkI3Mjg4Ii8+Cjwvc3ZnPg=='">
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-semibold text-white">${campaign.title}</div>
                        <div class="text-sm text-gray-400">${campaign.description.substring(0, 50)}${campaign.description.length > 50 ? '...' : ''}</div>
                        ${campaign.campaign_code ? `<div class="text-xs text-gray-500 mt-1">Kod: ${campaign.campaign_code}</div>` : ''}
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="space-y-1">
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-purple-glow/20 text-purple-glow border border-purple-glow/30">
                        ${campaign.category}
                    </span>
                    ${campaign.priority_level ? `<div class="text-xs text-gray-400">Öncelik: ${getPriorityText(campaign.priority_level)}</div>` : ''}
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="space-y-1">
                    <div class="text-sm text-gray-300">${campaign.discount}</div>
                    ${campaign.discount_value ? `<div class="text-xs text-gray-400">Değer: ${campaign.discount_value}</div>` : ''}
                    ${campaign.minimum_order_amount ? `<div class="text-xs text-gray-400">Min: ${campaign.minimum_order_amount} TL</div>` : ''}
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="space-y-1">
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full ${campaign.is_active ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30'}">
                        ${campaign.is_active ? 'Aktif' : 'Pasif'}
                    </span>
                    ${campaign.approval_status ? `<div class="text-xs text-gray-400">${getApprovalStatusText(campaign.approval_status)}</div>` : ''}
                    ${campaign.end_date ? `<div class="text-xs text-gray-400">Bitiş: ${formatDate(campaign.end_date)}</div>` : ''}
                </div>
            </td>
            <td class="px-6 py-4 text-sm font-medium">
                <button onclick="editCampaign(${campaign.id})" class="text-purple-glow hover:text-neon-purple mr-3 transition-colors duration-200">Düzenle</button>
                <button onclick="deleteCampaign(${campaign.id})" class="text-red-400 hover:text-red-300 transition-colors duration-200">Sil</button>
            </td>
        </tr>
    `).join('');
}

// Open create modal
function openCreateModal() {
    try {
        console.log('Opening create modal...');
        
        const modalTitle = document.getElementById('modalTitle');
        const campaignForm = document.getElementById('campaignForm');
        const campaignId = document.getElementById('campaignId');
        const campaignModal = document.getElementById('campaignModal');
        
        if (!modalTitle || !campaignForm || !campaignId || !campaignModal) {
            console.error('Required elements not found:', {
                modalTitle: !!modalTitle,
                campaignForm: !!campaignForm,
                campaignId: !!campaignId,
                campaignModal: !!campaignModal
            });
            return;
        }
        
        modalTitle.textContent = 'Yeni Kampanya';
        campaignForm.reset();
        campaignId.value = '';
        currentCampaignId = null;
        
        // Kampanya kodu alanını readonly yap ve temizle
        const campaignCodeField = document.getElementById('campaign_code');
        campaignCodeField.value = '';
        campaignCodeField.setAttribute('readonly', 'readonly');
        
        campaignModal.classList.remove('hidden');
        
        console.log('Modal opened successfully');
    } catch (error) {
        console.error('Error opening modal:', error);
    }
}

// Open edit modal
function editCampaign(id) {
    const campaign = campaigns.find(c => c.id === id);
    if (!campaign) return;
    
    currentCampaignId = id;
    document.getElementById('modalTitle').textContent = 'Kampanya Düzenle';
    
    // Fill form fields
    document.getElementById('campaignId').value = campaign.id;
    document.getElementById('title').value = campaign.title;
    document.getElementById('campaign_code').value = campaign.campaign_code || '';
    document.getElementById('campaign_code').setAttribute('readonly', 'readonly');
    document.getElementById('description').value = campaign.description;
    document.getElementById('category').value = campaign.category;
    document.getElementById('priority_level').value = campaign.priority_level || 2;
    document.getElementById('target_audience').value = campaign.target_audience || '';
    document.getElementById('discount').value = campaign.discount || '';
    document.getElementById('discount_type').value = campaign.discount_type;
    document.getElementById('discount_value').value = campaign.discount_value || '';
    document.getElementById('start_date').value = campaign.start_date ? campaign.start_date.slice(0, 16) : '';
    document.getElementById('end_date').value = campaign.end_date ? campaign.end_date.slice(0, 16) : '';
    document.getElementById('minimum_order_amount').value = campaign.minimum_order_amount || '';
    document.getElementById('budget_limit').value = campaign.budget_limit || '';
    document.getElementById('max_usage').value = campaign.max_usage || '';
    document.getElementById('approval_status').value = campaign.approval_status || 'pending';
    document.getElementById('image_url').value = campaign.image_url || '';
    document.getElementById('terms_conditions').value = campaign.terms_conditions || '';
    document.getElementById('notes').value = campaign.notes || '';
    document.getElementById('is_active').checked = campaign.is_active;
    document.getElementById('requires_approval').checked = campaign.requires_approval || false;
    
    document.getElementById('campaignModal').classList.remove('hidden');
}

// Close modal
function closeModal() {
    document.getElementById('campaignModal').classList.add('hidden');
}

// Handle form submission
document.getElementById('campaignForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    console.log('Form submission started...');
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    // Convert checkbox values
    data.is_active = formData.get('is_active') === 'on';
    data.requires_approval = formData.get('requires_approval') === 'on';
    
    console.log('Form data:', data);
    console.log('Current campaign ID:', currentCampaignId);
    
    try {
        const url = currentCampaignId ? `/api/dashboard/campaigns/${currentCampaignId}` : '/api/dashboard/campaigns';
        const method = currentCampaignId ? 'PUT' : 'POST';
        
        console.log('API URL:', url);
        console.log('Method:', method);
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        const result = await response.json();
        console.log('Response result:', result);
        
        if (result.success) {
            closeModal();
            loadCampaigns();
            showSuccess(currentCampaignId ? 'Kampanya güncellendi' : 'Kampanya oluşturuldu');
        } else {
            showError('Hata: ' + result.message);
        }
    } catch (error) {
        console.error('Form submission error:', error);
        showError('İşlem sırasında hata oluştu: ' + error.message);
    }
});


// Delete campaign
function deleteCampaign(id) {
    currentCampaignId = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

// Close delete modal
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    currentCampaignId = null;
}

// Confirm delete
async function confirmDelete() {
    try {
        const response = await fetch(`/api/dashboard/campaigns/${currentCampaignId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeDeleteModal();
            loadCampaigns();
            showSuccess('Kampanya silindi');
        } else {
            showError('Hata: ' + result.message);
        }
    } catch (error) {
        showError('İşlem sırasında hata oluştu: ' + error.message);
    }
}



// Add missing utility functions
function showLoading() {
    const loadingState = document.getElementById('loadingState');
    const emptyState = document.getElementById('emptyState');
    if (loadingState) loadingState.classList.remove('hidden');
    if (emptyState) emptyState.classList.add('hidden');
}

// Helper functions for display
function getPriorityText(priorityLevel) {
    const priorities = {
        1: 'Düşük',
        2: 'Normal', 
        3: 'Orta',
        4: 'Yüksek',
        5: 'Kritik'
    };
    return priorities[priorityLevel] || 'Normal';
}

function getApprovalStatusText(status) {
    const statuses = {
        'pending': 'Beklemede',
        'approved': 'Onaylandı',
        'rejected': 'Reddedildi',
        'draft': 'Taslak'
    };
    return statuses[status] || 'Beklemede';
}

function formatDate(dateString) {
    if (!dateString) return 'Sürekli';
    
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('tr-TR');
    } catch {
        return 'Geçersiz tarih';
    }
}

function showSuccess(message) {
    console.log('Success:', message);
    
    // Create a temporary success message
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    successDiv.innerHTML = `
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(successDiv);
    
    // Remove after 3 seconds
    setTimeout(() => {
        if (successDiv.parentNode) {
            successDiv.parentNode.removeChild(successDiv);
        }
    }, 3000);
}

function showError(message) {
    console.error('Error:', message);
    
    // Create a temporary error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    errorDiv.innerHTML = `
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(errorDiv);
    
    // Remove after 5 seconds
    setTimeout(() => {
        if (errorDiv.parentNode) {
            errorDiv.parentNode.removeChild(errorDiv);
        }
    }, 5000);
}






</script>
@endpush
