@extends('layouts.admin')

@section('title', 'Mail Template Yönetimi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-4">
                        <span class="gradient-text">Mail Template</span> Yönetimi
                    </h1>
                    <p class="text-xl text-gray-300">
                        Kullanıcılara gönderilecek mail template'lerini yönetin
                    </p>
                </div>
                <a href="{{ route('admin.mail-templates.create') }}" class="px-6 py-3 bg-purple-glow hover:bg-purple-dark text-white font-medium rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Yeni Template Oluştur
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Toplam Template -->
        <div class="glass-effect rounded-2xl p-6 border border-gray-700 hover:border-purple-500/50 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Toplam Template</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total'] }}</p>
                </div>
                <div class="p-3 bg-purple-500/20 rounded-full">
                    <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Aktif Template -->
        <div class="glass-effect rounded-2xl p-6 border border-gray-700 hover:border-green-500/50 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Aktif Template</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['active'] }}</p>
                </div>
                <div class="p-3 bg-green-500/20 rounded-full">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pasif Template -->
        <div class="glass-effect rounded-2xl p-6 border border-gray-700 hover:border-yellow-500/50 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Pasif Template</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['inactive'] }}</p>
                </div>
                <div class="p-3 bg-yellow-500/20 rounded-full">
                    <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Kategori Sayısı -->
        <div class="glass-effect rounded-2xl p-6 border border-gray-700 hover:border-blue-500/50 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Kategori</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ count($categories) }}</p>
                </div>
                <div class="p-3 bg-blue-500/20 rounded-full">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="glass-effect rounded-2xl p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                <!-- Kategori Filtresi -->
                <div class="flex-1 sm:w-48">
                    <select id="category-filter" class="w-full form-input rounded-lg px-4 py-2 text-white">
                        <option value="">Tüm Kategoriler</option>
                        @foreach($categories as $key => $category)
                            <option value="{{ $key }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Durum Filtresi -->
                <div class="flex-1 sm:w-32">
                    <select id="status-filter" class="w-full form-input rounded-lg px-4 py-2 text-white">
                        <option value="">Tüm Durumlar</option>
                        <option value="1">Aktif</option>
                        <option value="0">Pasif</option>
                    </select>
                </div>
            </div>

            <!-- Arama -->
            <div class="flex-1 lg:max-w-md">
                <div class="relative">
                    <input type="text" id="search-input" placeholder="Template ara..." 
                           class="w-full form-input rounded-lg pl-10 pr-4 py-2 text-white placeholder-gray-400">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates Table -->
    <div class="glass-effect rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Template
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Kategori
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Durum
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Son Güncelleme
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            İşlemler
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-gray-800/30 divide-y divide-gray-700" id="templates-table">
                    @forelse($mailTemplates as $template)
                        <tr class="hover:bg-gray-700/30 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-white">{{ $template->name }}</div>
                                    <div class="text-sm text-gray-400">{{ Str::limit($template->subject, 60) }}</div>
                                    @if($template->description)
                                        <div class="text-xs text-gray-500 mt-1">{{ Str::limit($template->description, 80) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-500/20 text-purple-400">
                                    {{ $categories[$template->category] ?? $template->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $template->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                    {{ $template->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                {{ $template->updated_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.mail-templates.show', $template->id) }}" 
                                       class="text-blue-400 hover:text-blue-300 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.mail-templates.edit', $template->id) }}" 
                                       class="text-yellow-400 hover:text-yellow-300 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button onclick="toggleTemplateStatus({{ $template->id }}, {{ $template->is_active ? 'true' : 'false' }})" 
                                            class="text-{{ $template->is_active ? 'red' : 'green' }}-400 hover:text-{{ $template->is_active ? 'red' : 'green' }}-300 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $template->is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"></path>
                                        </svg>
                                    </button>
                                    <button onclick="deleteTemplate({{ $template->id }})" 
                                            class="text-red-400 hover:text-red-300 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium mb-2">Henüz template oluşturulmamış</p>
                                    <p class="text-sm">İlk mail template'inizi oluşturmaya başlayın</p>
                                    <a href="{{ route('admin.mail-templates.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-purple-glow hover:bg-purple-dark text-white font-medium rounded-lg transition-all duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Template Oluştur
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($mailTemplates->hasPages())
        <div class="glass-effect rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-400">
                    Toplam {{ $mailTemplates->total() }} template'den {{ $mailTemplates->firstItem() }}-{{ $mailTemplates->lastItem() }} arası gösteriliyor
                </div>
                <div class="flex items-center space-x-2">
                    {{ $mailTemplates->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
// Filtreleme ve arama işlemleri
document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.getElementById('category-filter');
    const statusFilter = document.getElementById('status-filter');
    const searchInput = document.getElementById('search-input');
    const tableBody = document.getElementById('templates-table');

    function filterTemplates() {
        const category = categoryFilter.value;
        const status = statusFilter.value;
        const search = searchInput.value.toLowerCase();

        const rows = tableBody.querySelectorAll('tr');
        
        rows.forEach(row => {
            if (row.cells.length < 5) return; // Header row veya empty row

            const templateName = row.cells[0].textContent.toLowerCase();
            const templateCategory = row.cells[1].textContent.toLowerCase();
            const templateStatus = row.cells[2].textContent.toLowerCase();

            let show = true;

            // Kategori filtresi
            if (category && !templateCategory.includes(category.toLowerCase())) {
                show = false;
            }

            // Durum filtresi
            if (status !== '') {
                const isActive = status === '1';
                const templateIsActive = templateStatus.includes('aktif');
                if (isActive !== templateIsActive) {
                    show = false;
                }
            }

            // Arama filtresi
            if (search && !templateName.includes(search)) {
                show = false;
            }

            row.style.display = show ? '' : 'none';
        });
    }

    categoryFilter.addEventListener('change', filterTemplates);
    statusFilter.addEventListener('change', filterTemplates);
    searchInput.addEventListener('input', filterTemplates);
});

// Template durum değiştirme
function toggleTemplateStatus(templateId, currentStatus) {
    const action = currentStatus ? 'pasif yapmak' : 'aktif yapmak';
    if (confirm(`Bu template'i ${action} istediğinizden emin misiniz?`)) {
        fetch(`/admin/mail-templates/${templateId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Hata: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu.');
        });
    }
}

// Template silme
function deleteTemplate(templateId) {
    if (confirm('Bu template\'i silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
        fetch(`/admin/mail-templates/${templateId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Hata: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu.');
        });
    }
}
</script>
@endpush
@endsection
