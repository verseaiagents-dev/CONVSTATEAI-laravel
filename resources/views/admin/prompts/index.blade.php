@extends('layouts.admin')

@section('title', 'Prompt Yönetimi - Admin Panel')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Prompt Yönetimi</h1>
            <p class="text-gray-400 mt-2">AI prompt template'lerini yönetin ve optimize edin</p>
        </div>
        <button onclick="openPromptModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-colors duration-200 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Yeni Prompt</span>
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gray-800/50 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Toplam Prompt</p>
                    <p class="text-2xl font-bold text-white">{{ $statistics['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Aktif Prompt</p>
                    <p class="text-2xl font-bold text-green-400">{{ $statistics['active'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Production</p>
                    <p class="text-2xl font-bold text-purple-400">{{ $statistics['production'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Tamamlanma Oranı</p>
                    <p class="text-2xl font-bold text-yellow-400">{{ $statistics['completion_rate'] }}%</p>
                </div>
                <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800/50 rounded-lg p-6 border border-gray-700">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Kategori</label>
                <select name="category" class="w-full form-input rounded-lg">
                    <option value="">Tüm Kategoriler</option>
                    @foreach($categories as $key => $value)
                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Ortam</label>
                <select name="environment" class="w-full form-input rounded-lg">
                    <option value="">Tüm Ortamlar</option>
                    <option value="production" {{ request('environment') == 'production' ? 'selected' : '' }}>Production</option>
                    <option value="test" {{ request('environment') == 'test' ? 'selected' : '' }}>Test</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Durum</label>
                <select name="is_active" class="w-full form-input rounded-lg">
                    <option value="">Tüm Durumlar</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Arama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Prompt ara..." class="w-full form-input rounded-lg">
            </div>

            <div class="md:col-span-4 flex space-x-4">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    Filtrele
                </button>
                <a href="{{ route('admin.prompts.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    Temizle
                </a>
            </div>
        </form>
    </div>

    <!-- Prompts Table -->
    <div class="bg-gray-800/50 rounded-lg border border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Prompt</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Ortam</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Versiyon</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Güncellenme</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($prompts as $prompt)
                        <tr class="hover:bg-gray-700/30 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-white">{{ $prompt->name }}</div>
                                    <div class="text-sm text-gray-400 truncate max-w-xs">{{ Str::limit($prompt->description, 50) }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $categories[$prompt->category] ?? $prompt->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $prompt->environment === 'production' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $prompt->environment === 'production' ? 'Production' : 'Test' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button onclick="togglePrompt({{ $prompt->id }})" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $prompt->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $prompt->is_active ? 'Aktif' : 'Pasif' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-300">
                                v{{ $prompt->version }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-300">
                                {{ $prompt->updated_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.prompts.edit', $prompt) }}" class="text-blue-400 hover:text-blue-300 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button onclick="testPrompt({{ $prompt->id }})" class="text-green-400 hover:text-green-300 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1m-6-8h8a2 2 0 012 2v8a2 2 0 01-2 2H8a2 2 0 01-2-2V6a2 2 0 012-2z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="analyzePrompt({{ $prompt->id }})" class="text-purple-400 hover:text-purple-300 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.prompts.destroy', $prompt) }}" class="inline" onsubmit="return confirm('Bu prompt\'u silmek istediğinizden emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-medium">Henüz prompt bulunmuyor</p>
                                <p class="text-sm">İlk prompt'unuzu oluşturmak için yukarıdaki butona tıklayın.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($prompts->hasPages())
            <div class="px-6 py-4 border-t border-gray-700">
                {{ $prompts->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Test Modal -->
<div id="testModal" class="fixed inset-0 bg-black/50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg max-w-2xl w-full max-h-[80vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-white">Prompt Test</h3>
                    <button onclick="closeTestModal()" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="testContent" class="space-y-4">
                    <!-- Test content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Analysis Modal -->
<div id="analysisModal" class="fixed inset-0 bg-black/50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg max-w-4xl w-full max-h-[80vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-white">Prompt Analizi</h3>
                    <button onclick="closeAnalysisModal()" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="analysisContent" class="space-y-4">
                    <!-- Analysis content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePrompt(promptId) {
    fetch(`/admin/prompts/${promptId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Hata: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu');
    });
}

function testPrompt(promptId) {
    document.getElementById('testContent').innerHTML = '<div class="text-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500 mx-auto"></div><p class="mt-2 text-gray-400">Test yükleniyor...</p></div>';
    document.getElementById('testModal').classList.remove('hidden');
    
    fetch(`/admin/prompts/${promptId}/test`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('testContent').innerHTML = `
                <div class="space-y-4">
                    <div class="bg-gray-700 rounded-lg p-4">
                        <h4 class="font-medium text-white mb-2">İşlenmiş İçerik</h4>
                        <pre class="text-sm text-gray-300 whitespace-pre-wrap">${data.processed_content}</pre>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-white mb-2">Uzunluk</h4>
                            <p class="text-2xl font-bold text-blue-400">${data.length}</p>
                        </div>
                        <div class="bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-white mb-2">Kelime Sayısı</h4>
                            <p class="text-2xl font-bold text-green-400">${data.word_count}</p>
                        </div>
                        <div class="bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-white mb-2">Tahmini Token</h4>
                            <p class="text-2xl font-bold text-purple-400">${data.estimated_tokens}</p>
                        </div>
                        <div class="bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-white mb-2">Değişken Sayısı</h4>
                            <p class="text-2xl font-bold text-yellow-400">${data.variables_used.length}</p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            document.getElementById('testContent').innerHTML = `<div class="text-red-400">Hata: ${data.error}</div>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('testContent').innerHTML = '<div class="text-red-400">Test sırasında bir hata oluştu</div>';
    });
}

function analyzePrompt(promptId) {
    document.getElementById('analysisContent').innerHTML = '<div class="text-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500 mx-auto"></div><p class="mt-2 text-gray-400">Analiz yükleniyor...</p></div>';
    document.getElementById('analysisModal').classList.remove('hidden');
    
    fetch(`/admin/prompts/${promptId}/analyze`)
    .then(response => response.json())
    .then(data => {
        if (data.performance) {
            document.getElementById('analysisContent').innerHTML = `
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-white mb-4">Temel Metrikler</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Uzunluk:</span>
                                    <span class="text-white">${data.performance.basic_metrics.length}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Kelime Sayısı:</span>
                                    <span class="text-white">${data.performance.basic_metrics.word_count}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Tahmini Token:</span>
                                    <span class="text-white">${data.performance.basic_metrics.estimated_tokens}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Değişken Sayısı:</span>
                                    <span class="text-white">${data.performance.basic_metrics.variable_count}</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-white mb-4">Karmaşıklık</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Puan:</span>
                                    <span class="text-white">${data.performance.complexity.score}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Seviye:</span>
                                    <span class="text-white">${data.performance.complexity.level}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Cümle Sayısı:</span>
                                    <span class="text-white">${data.performance.complexity.sentence_count}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-4">
                        <h4 class="font-medium text-white mb-4">Öneriler</h4>
                        <ul class="space-y-2">
                            ${data.performance.recommendations.map(rec => `<li class="text-gray-300">• ${rec}</li>`).join('')}
                        </ul>
                    </div>
                </div>
            `;
        } else {
            document.getElementById('analysisContent').innerHTML = `<div class="text-red-400">Hata: ${data.error}</div>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('analysisContent').innerHTML = '<div class="text-red-400">Analiz sırasında bir hata oluştu</div>';
    });
}

function closeTestModal() {
    document.getElementById('testModal').classList.add('hidden');
}

function closeAnalysisModal() {
    document.getElementById('analysisModal').classList.add('hidden');
}

// Modal functions for prompt creation
function openPromptModal() {
    document.getElementById('promptModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePromptModal() {
    document.getElementById('promptModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    resetPromptForm();
}

function resetPromptForm() {
    document.getElementById('promptForm').reset();
    
    // Reset variables container
    const variablesContainer = document.getElementById('modal_variables_container');
    variablesContainer.innerHTML = `
        <div class="flex space-x-2">
            <input type="text" name="variables[]" placeholder="Değişken adı" 
                   class="flex-1 form-input rounded-lg">
            <button type="button" onclick="removeModalVariable(this)" class="text-red-400 hover:text-red-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    // Reset tags container
    const tagsContainer = document.getElementById('modal_tags_container');
    tagsContainer.innerHTML = `
        <div class="flex space-x-2">
            <input type="text" name="tags[]" placeholder="Etiket" 
                   class="flex-1 form-input rounded-lg">
            <button type="button" onclick="removeModalTag(this)" class="text-red-400 hover:text-red-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
}

function addModalVariable() {
    const container = document.getElementById('modal_variables_container');
    const div = document.createElement('div');
    div.className = 'flex space-x-2';
    div.innerHTML = `
        <input type="text" name="variables[]" placeholder="Değişken adı" 
               class="flex-1 form-input rounded-lg">
        <button type="button" onclick="removeModalVariable(this)" class="text-red-400 hover:text-red-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function removeModalVariable(button) {
    button.parentElement.remove();
}

function addModalTag() {
    const container = document.getElementById('modal_tags_container');
    const div = document.createElement('div');
    div.className = 'flex space-x-2';
    div.innerHTML = `
        <input type="text" name="tags[]" placeholder="Etiket" 
               class="flex-1 form-input rounded-lg">
        <button type="button" onclick="removeModalTag(this)" class="text-red-400 hover:text-red-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function removeModalTag(button) {
    button.parentElement.remove();
}

function submitPromptForm() {
    const form = document.getElementById('promptForm');
    const formData = new FormData(form);
    
    // Show loading state
    const submitBtn = document.querySelector('button[onclick="submitPromptForm()"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Oluşturuluyor...';
    submitBtn.disabled = true;
    
    // Convert FormData to JSON
    const data = {};
    formData.forEach((value, key) => {
        if (key.endsWith('[]')) {
            const arrayKey = key.slice(0, -2);
            if (!data[arrayKey]) {
                data[arrayKey] = [];
            }
            data[arrayKey].push(value);
        } else {
            data[key] = value;
        }
    });
    
    fetch('{{ route("admin.prompts.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Prompt başarıyla oluşturuldu!', 'success');
            closePromptModal();
            // Refresh the page or update the list
            location.reload();
        } else {
            showNotification('Hata: ' + (data.message || 'Bilinmeyen hata'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
    })
    .finally(() => {
        // Reset button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// Auto-detect variables from content
document.addEventListener('DOMContentLoaded', function() {
    const contentTextarea = document.getElementById('modal_content');
    if (contentTextarea) {
        contentTextarea.addEventListener('input', function() {
            const content = this.value;
            const variables = content.match(/\{(\w+)\}/g);
            
            if (variables) {
                const uniqueVariables = [...new Set(variables.map(v => v.replace(/\{|\}/g, '')))];
                const container = document.getElementById('modal_variables_container');
                
                // Clear existing inputs except the first one
                while (container.children.length > 1) {
                    container.removeChild(container.lastChild);
                }
                
                // Add detected variables
                uniqueVariables.forEach(variable => {
                    if (container.children[0].querySelector('input').value !== variable) {
                        addModalVariable();
                        container.lastElementChild.querySelector('input').value = variable;
                    }
                });
            }
        });
    }
});

// Notification function
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePromptModal();
    }
});
</script>
@endpush