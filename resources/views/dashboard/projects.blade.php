@extends('layouts.dashboard')

@section('title', 'Projelerim')

@section('content')
<style>
@keyframes pulse-glow {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

@keyframes slide-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in-scale {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes shimmer {
    0% { background-position: -200px 0; }
    100% { background-position: calc(200px + 100%) 0; }
}

.slide-in-up {
    animation: slide-in-up 0.4s ease-out;
}

.fade-in-scale {
    animation: fade-in-scale 0.3s ease-out;
}

.shimmer {
    background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
    background-size: 200px 100%;
    animation: shimmer 1.5s infinite;
}
</style>
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden slide-in-up">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-bold mb-4">
                <span class="gradient-text">Projelerim</span>
            </h1>
            <p class="text-xl text-gray-300">
                Projelerinizi yönetin ve organize edin
            </p>
        </div>
    </div>

    <!-- Create Project Button -->
    <div class="flex justify-end slide-in-up">
        <button onclick="openCreateProjectModal()" class="px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin-round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Yeni Proje Oluştur
        </button>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="glass-effect rounded-2xl p-12 text-center fade-in-scale">
        <div class="space-y-6">
            <!-- Multi-colored spinner -->
            <div class="flex justify-center">
                <div class="relative">
                    <div class="w-16 h-16 border-4 border-purple-glow/30 rounded-full animate-spin"></div>
                    <div class="absolute top-0 left-0 w-16 h-16 border-4 border-transparent border-t-purple-glow rounded-full animate-spin"></div>
                    <div class="absolute top-0 left-0 w-16 h-16 border-4 border-transparent border-r-neon-purple rounded-full animate-spin" style="animation-delay: -0.3s;"></div>
                    <div class="absolute top-0 left-0 w-16 h-16 border-4 border-transparent border-b-blue-400 rounded-full animate-spin" style="animation-delay: -0.6s;"></div>
                </div>
            </div>
            
            <div>
                <h3 class="text-xl font-semibold text-white mb-2">Projeler Yükleniyor</h3>
                <p class="text-gray-400">Proje verileriniz hazırlanıyor...</p>
            </div>
            
            <!-- Progress bar -->
            <div class="w-full max-w-md mx-auto">
                <div class="w-full bg-gray-700 rounded-full h-2">
                    <div id="progress-bar" class="bg-gradient-to-r from-purple-glow via-neon-purple to-blue-400 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skeleton State -->
    <div id="skeletonState" class="hidden space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @for($i = 0; $i < 6; $i++)
            <div class="glass-effect rounded-2xl p-6 animate-pulse">
                <div class="space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 space-y-3">
                            <div class="h-6 bg-gray-700 rounded w-3/4"></div>
                            <div class="h-4 bg-gray-700 rounded w-full"></div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-4 bg-gray-700 rounded w-16"></div>
                            <div class="h-4 bg-gray-700 rounded w-20"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="h-16 bg-gray-700 rounded"></div>
                        <div class="h-16 bg-gray-700 rounded"></div>
                    </div>
                    <div class="flex justify-between">
                        <div class="h-3 bg-gray-700 rounded w-20"></div>
                        <div class="h-3 bg-gray-700 rounded w-16"></div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Content Container (Hidden initially) -->
    <div id="contentContainer" class="hidden space-y-6 slide-in-up">
        <!-- Projects Grid will be populated here -->
        <div id="projects-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Projects will be populated via JavaScript -->
        </div>
    </div>

    <!-- Error State -->
    <div id="errorState" class="hidden glass-effect rounded-2xl p-8 fade-in-scale mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">Projeler Yüklenemedi</h3>
                    <p class="text-gray-400 text-sm">Proje verileri yüklenirken bir hata oluştu, ancak yeni proje oluşturabilirsiniz.</p>
                </div>
            </div>
            <button 
                onclick="retryLoading()"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded-lg text-white text-sm transition-all duration-200"
            >
                Tekrar Dene
            </button>
        </div>
    </div>
</div>

<!-- Create Project Modal -->
<div id="createProjectModal" class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
    <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 p-6 border border-gray-700 w-96 shadow-2xl rounded-xl glass-effect">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-white">Yeni Proje Oluştur</h3>
            <button onclick="closeCreateProjectModal()" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="createProjectForm" class="space-y-4">
            @csrf
            <div>
                <label for="project_name" class="block text-sm font-medium text-gray-300 mb-2">Proje Adı</label>
                <input type="text" id="project_name" name="name" required class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700">
            </div>
            
            <div>
                <label for="project_description" class="block text-sm font-medium text-gray-300 mb-2">Açıklama</label>
                <textarea id="project_description" name="description" rows="3" class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700 resize-none"></textarea>
            </div>
            
            <div>
                <label for="project_url" class="block text-sm font-medium text-gray-300 mb-2">URL <span class="text-red-400">*</span></label>
                <div class="flex space-x-2">
                    <input type="url" id="project_url" name="url" required placeholder="https://example.com" class="form-input flex-1 px-4 py-3 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700">
                    <button type="button" id="test_url_btn" class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="test_url_text">Test Et</span>
                        <span id="test_url_loading" class="hidden">Test Ediliyor...</span>
                    </button>
                </div>
                <div id="url_test_result" class="mt-2 text-sm hidden"></div>
            </div>
            
            <div>
                <label for="project_status" class="block text-sm font-medium text-gray-300 mb-2">Durum</label>
                <select id="project_status" name="status" required class="form-select w-full px-4 py-3 rounded-lg text-white bg-gray-800/50 border border-gray-700">
                    <option value="active">Aktif</option>
                    <option value="inactive">Pasif</option>
                    <option value="completed">Tamamlandı</option>
                    <option value="archived">Arşivlendi</option>
                </select>
            </div>
            
            <div class="flex items-center space-x-3 pt-4">
                <button type="submit" class="flex-1 px-4 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300">
                    Proje Oluştur
                </button>
                <button type="button" onclick="closeCreateProjectModal()" class="px-4 py-3 bg-gray-600 hover:bg-gray-500 rounded-lg text-white font-semibold transition-colors">
                    İptal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Project Actions Modal -->
<div id="projectActionsModal" class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
    <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 p-6 border border-gray-700 w-96 shadow-2xl rounded-xl glass-effect">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-white" id="projectModalTitle">Proje İşlemleri</h3>
            <button onclick="closeProjectModal()" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="space-y-4">

                 <!-- Knowledge Base -->
                 <a href="#" id="knowledgeBaseLink" class="flex items-center space-x-4 p-4 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:bg-purple-500/30 transition-all duration-200">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-white font-medium">Bilgi Tabanı</h4>
                        <p class="text-gray-400 text-sm">Proje bilgi tabanını yönetin</p>
                    </div>
                </a>
            <!-- Chat Sessions -->
            <a href="#" id="chatSessionsLink" class="flex items-center space-x-4 p-4 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center group-hover:bg-green-500/30 transition-all duration-200">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-white font-medium">Chat Oturumları</h4>
                    <p class="text-gray-400 text-sm">Proje chat oturumlarını yönetin</p>
                </div>
            </a>
            
     
            
            <!-- Widget Customization -->
            <a href="#" id="widgetCustomizationLink" class="flex items-center space-x-4 p-4 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center group-hover:bg-blue-500/30 transition-all duration-200">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-white font-medium">Widget Özelleştirme</h4>
                    <p class="text-gray-400 text-sm">Proje widget'ını özelleştirin</p>
                </div>
            </a>
        </div>
        
        <div class="mt-6 pt-4 border-t border-gray-700">
            <button onclick="closeProjectModal()" class="w-full px-4 py-3 bg-gray-600 hover:bg-gray-500 rounded-lg text-white font-semibold transition-colors">
                Kapat
            </button>
        </div>
    </div>
</div>

<!-- Edit Project Modal -->
<div id="editProjectModal" class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
    <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 p-6 border border-gray-700 w-96 shadow-2xl rounded-xl glass-effect">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-white">Proje Düzenle</h3>
            <button onclick="closeEditProjectModal()" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="editProjectForm" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_project_id" name="project_id">
            
            <div>
                <label for="edit_project_name" class="block text-sm font-medium text-gray-300 mb-2">Proje Adı</label>
                <input type="text" id="edit_project_name" name="name" required class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700">
            </div>
            
            <div>
                <label for="edit_project_description" class="block text-sm font-medium text-gray-300 mb-2">Açıklama</label>
                <textarea id="edit_project_description" name="description" rows="3" class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700 resize-none"></textarea>
            </div>
            
            <div>
                <label for="edit_project_url" class="block text-sm font-medium text-gray-300 mb-2">URL <span class="text-red-400">*</span></label>
                <div class="flex space-x-2">
                    <input type="url" id="edit_project_url" name="url" required placeholder="https://example.com" class="form-input flex-1 px-4 py-3 rounded-lg text-white placeholder-gray-400 bg-gray-800/50 border border-gray-700">
                    <button type="button" id="test_edit_url_btn" class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="test_edit_url_text">Test Et</span>
                        <span id="test_edit_url_loading" class="hidden">Test Ediliyor...</span>
                    </button>
                </div>
                <div id="edit_url_test_result" class="mt-2 text-sm hidden"></div>
            </div>
            
            <div>
                <label for="edit_project_status" class="block text-sm font-medium text-gray-300 mb-2">Durum</label>
                <select id="edit_project_status" name="status" required class="form-select w-full px-4 py-3 rounded-lg text-white bg-gray-800/50 border border-gray-700">
                    <option value="active">Aktif</option>
                    <option value="inactive">Pasif</option>
                    <option value="completed">Tamamlandı</option>
                    <option value="archived">Arşivlendi</option>
                </select>
            </div>
            
            <div class="flex items-center space-x-3 pt-4">
                <button type="submit" class="flex-1 px-4 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300">
                    Güncelle
                </button>
                <button type="button" onclick="closeEditProjectModal()" class="px-4 py-3 bg-gray-600 hover:bg-gray-500 rounded-lg text-white font-semibold transition-colors">
                    İptal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Global variables
let projects = [];
let loadingInterval;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    startLoading();
    loadContent();
});

// Start loading animation
function startLoading() {
    const progressBar = document.getElementById('progress-bar');
    let progress = 0;
    
    loadingInterval = setInterval(() => {
        progress += Math.random() * 20;
        if (progress > 90) progress = 90;
        progressBar.style.width = progress + '%';
    }, 150);
}

// Complete loading
function completeLoading() {
    clearInterval(loadingInterval);
    
    setTimeout(() => {
        document.getElementById('loadingState').classList.add('hidden');
        document.getElementById('skeletonState').classList.remove('hidden');
        
        // Show skeleton for a moment to simulate content loading
        setTimeout(() => {
            document.getElementById('skeletonState').classList.add('hidden');
            document.getElementById('contentContainer').classList.remove('hidden');
            
            // Add fade-in animation
            const contentContainer = document.getElementById('contentContainer');
            contentContainer.style.opacity = '0';
            contentContainer.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                contentContainer.style.transition = 'all 0.3s ease-out';
                contentContainer.style.opacity = '1';
                contentContainer.style.transform = 'translateY(0)';
                
                // Populate content
                populateContent();
            }, 50);
        }, 400); // Show skeleton for 400ms
    }, 500);
}

// Load content from server
async function loadContent() {
    try {
        const url = '{{ route("dashboard.projects.load-content") }}';
        
        console.log('Loading projects from:', url);
        
        // CSRF token'ı al
        let csrfToken = '{{ csrf_token() }}';
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('Response data:', result);
        
        if (result.success) {
            if (Array.isArray(result.data.projects)) {
                projects = result.data.projects;
                console.log('Projects count:', projects.length);
                completeLoading();
            } else {
                console.error('Invalid projects data:', result.data);
                throw new Error('Projects data is not an array');
            }
        } else {
            throw new Error(result.message || 'Projeler yüklenemedi');
        }
        
    } catch (error) {
        console.error('Loading error:', error);
        showErrorState();
    }
}

// Populate content with loaded data
function populateContent() {
    const projectsGrid = document.getElementById('projects-grid');
    
    if (!projects || projects.length === 0) {
        projectsGrid.innerHTML = `
            <div class="col-span-full text-center py-12">
                <div class="text-gray-400 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-300 mb-2">Henüz Proje Yok</h3>
                <p class="text-gray-400">İlk projenizi oluşturmak için yukarıdaki butona tıklayın.</p>
            </div>
        `;
        return;
    }
    
    projectsGrid.innerHTML = projects.map(project => `
        <div class="glass-effect rounded-2xl p-6 hover:shadow-2xl transition-all duration-300 cursor-pointer" onclick="openProjectModal(${project.id}, '${project.name}')">
            <!-- Project Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xl font-bold text-white">${project.name}</h3>
                        <div class="flex items-center space-x-2">
                            <button onclick="showEmbedCode(${project.id})" class="text-green-400 hover:text-green-300 transition-colors duration-200" title="Embed Kodu">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                </svg>
                            </button>
                            <button onclick="editProject(${project.id})" class="text-blue-400 hover:text-blue-300 transition-colors duration-200" title="Düzenle">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteProject(${project.id})" class="text-red-400 hover:text-red-300 transition-colors duration-200" title="Sil">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm mb-3">${project.description ? project.description.substring(0, 100) + (project.description.length > 100 ? '...' : '') : 'Açıklama yok'}</p>
                    ${project.url ? `<div class="flex items-center text-blue-400 text-sm mb-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        <a href="${project.url}" target="_blank" class="hover:text-blue-300 transition-colors" onclick="event.stopPropagation()">${project.url}</a>
                    </div>` : ''}
                </div>
                <div class="flex items-center space-x-2">
                    ${project.is_featured ? '<span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs">Öne Çıkan</span>' : ''}
                    <span class="px-2 py-1 bg-${getStatusColor(project.status)}-500/20 text-${getStatusColor(project.status)}-400 rounded-full text-xs">
                        ${getStatusText(project.status)}
                    </span>
                </div>
            </div>

            <!-- Project Stats -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="text-center p-4 bg-gray-800/40 rounded-lg border border-gray-700/50">
                    <div class="text-3xl font-bold text-purple-400 mb-1">${project.knowledge_bases ? project.knowledge_bases.length : 0}</div>
                    <div class="text-sm text-gray-300 font-medium">Knowledge Base</div>
                </div>
                <div class="text-center p-4 bg-gray-800/40 rounded-lg border border-gray-700/50">
                    <div class="text-3xl font-bold text-purple-500 mb-1">${project.chat_sessions ? project.chat_sessions.length : 0}</div>
                    <div class="text-sm text-gray-300 font-medium">Chat Session</div>
                </div>
            </div>

        </div>
    `).join('');
    
    // Add staggered animations to project cards
    const projectCards = document.querySelectorAll('#projects-grid > div');
    projectCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.3s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + (index * 50));
    });
}

// Helper functions
function getStatusColor(status) {
    const colors = {
        'active': 'green',
        'inactive': 'gray',
        'completed': 'blue',
        'archived': 'yellow'
    };
    return colors[status] || 'gray';
}

function getStatusText(status) {
    const texts = {
        'active': 'Aktif',
        'inactive': 'Pasif',
        'completed': 'Tamamlandı',
        'archived': 'Arşivlendi'
    };
    return texts[status] || status;
}

// Show error state
function showErrorState() {
    clearInterval(loadingInterval);
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('errorState').classList.remove('hidden');
}

// Retry loading
function retryLoading() {
    document.getElementById('errorState').classList.add('hidden');
    document.getElementById('loadingState').classList.remove('hidden');
    startLoading();
    loadContent();
}

// Create Project Modal
function openCreateProjectModal() {
    document.getElementById('createProjectModal').classList.remove('hidden');
}

function closeCreateProjectModal() {
    document.getElementById('createProjectModal').classList.add('hidden');
    document.getElementById('createProjectForm').reset();
}

// Edit Project Modal
function openEditProjectModal() {
    document.getElementById('editProjectModal').classList.remove('hidden');
}

function closeEditProjectModal() {
    document.getElementById('editProjectModal').classList.add('hidden');
}

function editProject(projectId) {
    // Fetch project data and populate form
    fetch(`/dashboard/projects/${projectId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const project = data.project;
                document.getElementById('edit_project_id').value = project.id;
                document.getElementById('edit_project_name').value = project.name;
                document.getElementById('edit_project_description').value = project.description || '';
                document.getElementById('edit_project_url').value = project.url || '';
                document.getElementById('edit_project_status').value = project.status;
                openEditProjectModal();
            }
        });
}

function deleteProject(projectId) {
    if (confirm('Bu projeyi silmek istediğinizden emin misiniz?')) {
        fetch(`/dashboard/projects/${projectId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

// Form submissions
document.getElementById('createProjectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/dashboard/projects', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCreateProjectModal();
            location.reload();
        }
    });
});

document.getElementById('editProjectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const projectId = document.getElementById('edit_project_id').value;
    
    fetch(`/dashboard/projects/${projectId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditProjectModal();
            location.reload();
        }
            });
    });
    
    // Project Actions Modal Functions
    function openProjectModal(projectId, projectName) {
        // Update modal title
        document.getElementById('projectModalTitle').textContent = `${projectName} - İşlemler`;
        
        // Update links with project ID
        document.getElementById('chatSessionsLink').href = `{{ route('dashboard.chat-sessions') }}?project_id=${projectId}`;
        document.getElementById('knowledgeBaseLink').href = `{{ route('dashboard.knowledge-base') }}?project_id=${projectId}`;
        document.getElementById('widgetCustomizationLink').href = `{{ route('dashboard.widget-design') }}?project_id=${projectId}`;
        
        // Show modal
        document.getElementById('projectActionsModal').classList.remove('hidden');
    }
    
    function closeProjectModal() {
        document.getElementById('projectActionsModal').classList.add('hidden');
    }
    
    // Close modal when clicking outside
    document.getElementById('projectActionsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeProjectModal();
        }
    });

    // URL Test functionality
    function testUrl(url, testBtn, textSpan, loadingSpan, resultDiv) {
        if (!url || url.trim() === '') {
            showUrlTestResult(resultDiv, 'Lütfen bir URL girin.', 'error');
            return;
        }

        // Show loading state
        testBtn.disabled = true;
        textSpan.classList.add('hidden');
        loadingSpan.classList.remove('hidden');

        fetch('/api/test-url', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ url: url })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showUrlTestResult(resultDiv, data.message, 'success');
            } else {
                showUrlTestResult(resultDiv, data.message, 'error');
            }
        })
        .catch(error => {
            showUrlTestResult(resultDiv, 'Test sırasında hata oluştu: ' + error.message, 'error');
        })
        .finally(() => {
            // Reset button state
            testBtn.disabled = false;
            textSpan.classList.remove('hidden');
            loadingSpan.classList.add('hidden');
        });
    }

    function showUrlTestResult(resultDiv, message, type) {
        resultDiv.textContent = message;
        resultDiv.classList.remove('hidden', 'text-green-400', 'text-red-400');
        
        if (type === 'success') {
            resultDiv.classList.add('text-green-400');
        } else {
            resultDiv.classList.add('text-red-400');
        }
    }

    // Add event listeners for URL test buttons
    document.getElementById('test_url_btn').addEventListener('click', function() {
        const url = document.getElementById('project_url').value;
        const testBtn = this;
        const textSpan = document.getElementById('test_url_text');
        const loadingSpan = document.getElementById('test_url_loading');
        const resultDiv = document.getElementById('url_test_result');
        
        testUrl(url, testBtn, textSpan, loadingSpan, resultDiv);
    });

    document.getElementById('test_edit_url_btn').addEventListener('click', function() {
        const url = document.getElementById('edit_project_url').value;
        const testBtn = this;
        const textSpan = document.getElementById('test_edit_url_text');
        const loadingSpan = document.getElementById('test_edit_url_loading');
        const resultDiv = document.getElementById('edit_url_test_result');
        
        testUrl(url, testBtn, textSpan, loadingSpan, resultDiv);
    });
</script>

<!-- Embed Code Modal -->
<div id="embedCodeModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="glass-effect rounded-2xl p-8 max-w-4xl w-full max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-white">
                <span class="gradient-text">Embed Kodu</span>
            </h3>
            <button id="closeEmbedModal" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mb-6">
            <p class="text-gray-300 mb-4">Widget'ınızı web sitenize entegre etmek için aşağıdaki kodu kullanın:</p>
            
            <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-white">Embed Script</h4>
                    <button id="copyEmbedCodeBtn" class="px-4 py-2 bg-gradient-to-r from-purple-glow to-neon-purple text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <span>Kodu Kopyala</span>
                    </button>
                </div>
                
                <div class="bg-gray-900/50 rounded-lg p-4 border border-gray-600">
                    <pre id="embedCodeDisplay" class="text-green-400 font-mono text-sm break-all whitespace-pre-wrap overflow-x-auto"></pre>
                </div>
            </div>
        </div>
        
        <div class="bg-blue-900/20 border border-blue-500/30 rounded-lg p-4">
            <h4 class="text-blue-400 font-semibold mb-2">Nasıl Kullanılır?</h4>
            <ol class="text-gray-300 text-sm space-y-2 list-decimal list-inside">
                <li>Yukarıdaki kodu kopyalayın</li>
                <li>Web sitenizin HTML kodunda, &lt;/body&gt; etiketinden hemen önce yapıştırın</li>
                <li>Sayfanızı kaydedin ve yenileyin</li>
                <li>Widget otomatik olarak görünecektir</li>
            </ol>
        </div>
    </div>
</div>

<script>
// Embed Code Modal Functions
function showEmbedCode(projectId) {
    // Fetch project data and generate embed code
    fetch(`/dashboard/projects/${projectId}/embed-code`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                // Embed kodunu göster
                document.getElementById('embedCodeDisplay').textContent = data.embedCode;
                
                // Token bilgisini göster
                const tokenDisplay = document.getElementById('customizationTokenDisplay');
                if (tokenDisplay) {
                    tokenDisplay.textContent = data.customizationToken;
                }
                
                // Modal'ı aç
                document.getElementById('embedCodeModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                // Başarılı mesajı
                Swal.fire({
                    title: 'Başarılı!',
                    text: 'Embed kodu başarıyla oluşturuldu.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    title: 'Hata!',
                    text: data.message || 'Embed kodu alınamadı.',
                    icon: 'error',
                    confirmButtonText: 'Tamam',
                    confirmButtonColor: '#3B82F6'
                });
            }
        })
        .catch(error => {
            console.error('Embed code fetch error:', error);
            let errorMessage = 'Embed kodu alınırken bir hata oluştu.';
            
            if (error.message.includes('HTTP error!')) {
                errorMessage = 'Sunucu hatası oluştu. Lütfen daha sonra tekrar deneyin.';
            } else if (error.message.includes('NetworkError')) {
                errorMessage = 'Ağ bağlantısı hatası. İnternet bağlantınızı kontrol edin.';
            } else if (error.message.includes('Failed to fetch')) {
                errorMessage = 'Sunucuya ulaşılamıyor. Lütfen daha sonra tekrar deneyin.';
            }
            
            Swal.fire({
                title: 'Hata!',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'Tamam',
                confirmButtonColor: '#3B82F6'
            });
        });
}

// Close embed modal
function closeEmbedModal() {
    document.getElementById('embedCodeModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Event listeners for embed modal
document.addEventListener('DOMContentLoaded', function() {
    const closeEmbedModalBtn = document.getElementById('closeEmbedModal');
    const embedCodeModal = document.getElementById('embedCodeModal');
    const copyEmbedCodeBtn = document.getElementById('copyEmbedCodeBtn');
    
    if (closeEmbedModalBtn) {
        closeEmbedModalBtn.addEventListener('click', closeEmbedModal);
    }
    
    if (embedCodeModal) {
        embedCodeModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEmbedModal();
            }
        });
    }
    
    if (copyEmbedCodeBtn) {
        copyEmbedCodeBtn.addEventListener('click', function() {
            const embedCode = document.getElementById('embedCodeDisplay').textContent;
            
            if (embedCode) {
                navigator.clipboard.writeText(embedCode).then(function() {
                    // Show success feedback
                    const originalText = copyEmbedCodeBtn.querySelector('span').textContent;
                    copyEmbedCodeBtn.querySelector('span').textContent = 'Kopyalandı!';
                    copyEmbedCodeBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                    copyEmbedCodeBtn.classList.remove('from-purple-glow', 'to-neon-purple', 'hover:from-purple-600', 'hover:to-purple-700');
                    
                    // Reset after 2 seconds
                    setTimeout(function() {
                        copyEmbedCodeBtn.querySelector('span').textContent = originalText;
                        copyEmbedCodeBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                        copyEmbedCodeBtn.classList.add('from-purple-glow', 'to-neon-purple', 'hover:from-purple-600', 'hover:to-purple-700');
                    }, 2000);
                }).catch(function(err) {
                    console.error('Embed code kopyalanamadı: ', err);
                    alert('Embed kodu kopyalanamadı. Lütfen manuel olarak kopyalayın.');
                });
            } else {
                alert('Embed kodu bulunamadı.');
            }
        });
    }
});
</script>

@endsection
