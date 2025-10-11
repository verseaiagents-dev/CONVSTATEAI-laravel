@extends('layouts.dashboard')

@section('title', 'SSS Yönetimi')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- FAQ Management Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-4xl font-bold mb-4">
                        <span class="gradient-text">SSS Yönetimi</span>
                    </h1>
                    <p class="text-xl text-gray-300">
                        {{ $project->name ?? 'Proje' }} - Sık sorulan sorular yönetimi
                    </p>
                </div>
                
                <button onclick="openCreateModal()" class="px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Yeni SSS Oluştur</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="mb-8">
        <div class="max-w-md">
            <label for="search" class="sr-only">SSS Ara</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" id="search" placeholder="SSS Ara..." class="form-input w-full pl-12 pr-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
            </div>
        </div>
    </div>

    <!-- FAQ List -->
    <div class="glass-effect rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-gray-800 to-gray-900">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-white uppercase tracking-wider">Soru</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-white uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-white uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-white uppercase tracking-wider">Sıralama</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-white uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" id="faqsTableBody">
                    <!-- FAQs will be loaded here -->
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
            <span class="text-gray-300">SSS yükleniyor...</span>
        </div>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="hidden glass-effect rounded-2xl p-12 text-center">
        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="text-xl font-semibold text-white mb-2">Henüz SSS yok</h3>
        <p class="text-gray-400 mb-6">İlk SSS'nizi oluşturmaya başlayın ve müşteri sorularını yanıtlayın.</p>
        <button onclick="openCreateModal()" class="px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-xl text-white font-semibold hover:from-purple-dark hover:to-purple-glow transition-all duration-300 transform hover:scale-105 inline-flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>İlk SSS'nizi Oluşturun</span>
        </button>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="faqModal" class="fixed inset-0 bg-black bg-opacity-85 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50" onclick="closeModal()">
    <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 p-6 border border-gray-700 w-3/4 max-w-4xl max-h-[80vh] shadow-2xl rounded-xl glass-effect overflow-y-auto custom-scrollbar" onclick="event.stopPropagation()">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-white" id="modalTitle">Yeni SSS</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-white transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="faqForm" class="space-y-4">
                <input type="hidden" id="faqId" name="id">
                <input type="hidden" name="site_id" value="1">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Soru *</label>
                        <input type="text" id="title" name="title" required class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                    </div>
                    
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-300 mb-2">Kategori</label>
                        <input type="text" id="category" name="category" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200" placeholder="Genel, Teknik, Fiyat vb.">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Açıklama</label>
                    <textarea id="description" name="description" rows="2" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200" placeholder="SSS için kısa açıklama"></textarea>
                </div>
                
                <!-- Hidden project_id field - otomatik olarak doldurulacak -->
                <input type="hidden" id="project_id" name="project_id">

                <div>
                    <label for="answer" class="block text-sm font-medium text-gray-300 mb-2">Detaylı Cevap *</label>
                    <textarea id="answer" name="answer" rows="4" required class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-300 mb-2">Sıralama</label>
                        <input type="number" id="sort_order" name="sort_order" min="0" value="0" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                    </div>
                    
                    <div>
                        <label for="tags" class="block text-sm font-medium text-gray-300 mb-2">Etiketler</label>
                        <input type="text" id="tags" name="tags" placeholder="etiket1, etiket2, etiket3" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-gray-300 mb-2">Meta Başlık</label>
                        <input type="text" id="meta_title" name="meta_title" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                    </div>
                    
                    <div>
                        <label for="seo_url" class="block text-sm font-medium text-gray-300 mb-2">SEO URL</label>
                        <input type="text" id="seo_url" name="seo_url" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                    </div>
                </div>
                
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-300 mb-2">Meta Açıklama</label>
                    <textarea id="meta_description" name="meta_description" rows="2" class="form-input w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"></textarea>
                </div>

                <div class="flex items-center space-x-3">
                    <input type="checkbox" id="is_active" name="is_active" checked class="form-checkbox">
                    <label for="is_active" class="block text-sm text-gray-300">SSS'yi aktif yap</label>
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

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-85 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50" onclick="closeDeleteModal()">
    <div class="relative top-20 mx-auto p-6 border border-gray-700 w-96 shadow-2xl rounded-xl glass-effect" onclick="event.stopPropagation()">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-900/20 border border-red-500">
                <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-white mt-4">SSS'yi Sil</h3>
            <p class="text-gray-400 mt-2">Bu SSS'yi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.</p>
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
// Blade'den gelen project_id'yi JavaScript'e aktar
const PROJECT_ID = {!! json_encode($projectId ?? null) !!};
const PROJECT_NAME = {!! json_encode($project->name ?? '') !!};

let faqs = [];
let currentFaqId = null;

// Load FAQs on page load
document.addEventListener('DOMContentLoaded', function() {
    // Project ID kontrolü
    if (!PROJECT_ID) {
        showError('Proje ID\'si bulunamadı. Dashboard\'a yönlendiriliyorsunuz...');
        setTimeout(() => {
            window.location.href = '/dashboard';
        }, 2000);
        return;
    }
    
    loadFAQs();
    
    // Setup search functionality
    document.getElementById('search').addEventListener('input', function(e) {
        const query = e.target.value.trim();
        if (query.length > 2) {
            searchFAQs(query);
        } else if (query.length === 0) {
            loadFAQs();
        }
    });
});

// Load FAQs from API
async function loadFAQs() {
    try {
        showLoading();
        // Dinamik project_id kullan
        const response = await fetch(`/api/dashboard/faqs?project_id=${PROJECT_ID}`, {
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
            faqs = result.data || [];
            displayFAQs();
        } else {
            console.warn('FAQ API returned success=false:', result.message);
            faqs = [];
            displayFAQs();
        }
    } catch (error) {
        console.error('FAQ loading error:', error);
        faqs = [];
        displayFAQs();
    }
}

// Search FAQs
async function searchFAQs(query) {
    try {
        showLoading();
        // Dinamik project_id kullan
        const response = await fetch(`/api/faqs/search?q=${encodeURIComponent(query)}&project_id=${PROJECT_ID}`, {
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
            faqs = result.data || [];
            displayFAQs();
        } else {
            console.warn('FAQ search API returned success=false:', result.message);
            faqs = [];
            displayFAQs();
        }
    } catch (error) {
        console.error('FAQ search error:', error);
        faqs = [];
        displayFAQs();
    }
}

// Display FAQs in table
function displayFAQs() {
    const tbody = document.getElementById('faqsTableBody');
    const loadingState = document.getElementById('loadingState');
    const emptyState = document.getElementById('emptyState');
    
    if (faqs.length === 0) {
        loadingState.classList.add('hidden');
        emptyState.classList.remove('hidden');
        return;
    }
    
    loadingState.classList.add('hidden');
    emptyState.classList.add('hidden');
    
    tbody.innerHTML = faqs.map(faq => `
        <tr class="hover:bg-gray-800/30 transition-colors duration-200">
            <td class="px-6 py-4">
                <div class="text-sm font-semibold text-white">${faq.title || 'Başlık Yok'}</div>
                <div class="text-sm text-gray-400">${faq.description ? faq.description.substring(0, 100) + '...' : 'Açıklama Yok'}</div>
            </td>
            <td class="px-6 py-4">
                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-purple-glow/20 text-purple-glow border border-purple-glow/30">
                    ${faq.category || 'Genel'}
                </span>
            </td>
            <td class="px-6 py-4">
                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full ${faq.is_active ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30'}">
                    ${faq.is_active ? 'Aktif' : 'Pasif'}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-400">
                ${faq.sort_order}
            </td>
            <td class="px-6 py-4 text-sm font-medium">
                <button onclick="editFAQ(${faq.id})" class="text-purple-glow hover:text-neon-purple mr-3 transition-colors duration-200">Düzenle</button>
                <button onclick="deleteFAQ(${faq.id})" class="text-red-400 hover:text-red-300 transition-colors duration-200">Sil</button>
            </td>
        </tr>
    `).join('');
}

// Open create modal
function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Yeni SSS';
    document.getElementById('faqForm').reset();
    document.getElementById('faqId').value = '';
    currentFaqId = null;
    // Project ID'yi otomatik doldur
    if (PROJECT_ID) {
        document.getElementById('project_id').value = PROJECT_ID;
    }
    document.getElementById('faqModal').classList.remove('hidden');
}

// Open edit modal
function editFAQ(id) {
    const faq = faqs.find(f => f.id === id);
    if (!faq) return;
    
    currentFaqId = id;
    document.getElementById('modalTitle').textContent = 'SSS Düzenle';
    
    // Fill form fields
    document.getElementById('faqId').value = faq.id;
    document.getElementById('title').value = faq.title || '';
    document.getElementById('category').value = faq.category || '';
    document.getElementById('description').value = faq.description || '';
    document.getElementById('answer').value = faq.answer || '';
    document.getElementById('project_id').value = faq.project_id || '';
    document.getElementById('sort_order').value = faq.sort_order || 0;
    document.getElementById('tags').value = Array.isArray(faq.tags) ? faq.tags.join(', ') : (faq.tags || '');
    document.getElementById('is_active').checked = faq.is_active;
    
    document.getElementById('faqModal').classList.remove('hidden');
}

// Close modal
function closeModal() {
    document.getElementById('faqModal').classList.add('hidden');
}

// Handle form submission
document.getElementById('faqForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    // Convert checkbox value
    data.is_active = formData.get('is_active') === 'on';
    
    // Convert tags to array
    if (data.tags) {
        data.tags = data.tags.split(',').map(tag => tag.trim()).filter(tag => tag.length > 0);
    }
    
    // Project ID'yi otomatik ekle
    if (PROJECT_ID && !data.project_id) {
        data.project_id = PROJECT_ID;
    }
    
    try {
        const url = currentFaqId ? `/api/dashboard/faqs/${currentFaqId}` : '/api/dashboard/faqs';
        const method = currentFaqId ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeModal();
            loadFAQs();
            showSuccess(currentFaqId ? 'SSS güncellendi' : 'SSS oluşturuldu');
        } else {
            showError('Hata: ' + result.message);
        }
    } catch (error) {
        showError('İşlem sırasında hata oluştu: ' + error.message);
    }
});

// Delete FAQ
function deleteFAQ(id) {
    currentFaqId = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

// Close delete modal
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    currentFaqId = null;
}

// Confirm delete
async function confirmDelete() {
    try {
        const response = await fetch(`/api/dashboard/faqs/${currentFaqId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeDeleteModal();
            loadFAQs();
            showSuccess('SSS silindi');
        } else {
            showError('Hata: ' + result.message);
        }
    } catch (error) {
        showError('İşlem sırasında hata oluştu: ' + error.message);
    }
}

// Utility functions
function showLoading() {
    document.getElementById('loadingState').classList.remove('hidden');
    document.getElementById('emptyState').classList.add('hidden');
}

function showSuccess(message) {
    // Create a subtle success notification
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

function showError(message) {
    // Log error to console for debugging
    console.error('FAQ Error:', message);
    
    // Create a subtle error notification
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds (longer for errors)
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 5000);
}


</script>
@endpush
