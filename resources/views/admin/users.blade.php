@extends('layouts.admin')

@section('title', 'Kullanıcı Yönetimi - Admin Panel')

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-4xl font-bold">
                    <span class="gradient-text">Kullanıcı Yönetimi</span> 👥
                </h1>
                <a href="{{ route('admin.subscriptions.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-purple-glow hover:bg-purple-dark text-white font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-purple-glow/25">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Abonelik Yönetimi
                </a>
            </div>
            <p class="text-xl text-gray-300 mb-6">
                Sistem kullanıcılarını yönetin.
            </p>
        </div>
    </div>


    <!-- Users Content -->
    <div id="users-content">
        <!-- Search and Filters -->
        <div class="glass-effect rounded-xl p-6">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Kullanıcı ara..." 
                               class="w-full px-4 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <select id="statusFilter" class="px-4 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none">
                        <option value="">Tüm Durumlar</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Pasif</option>
                    </select>
                    
                    <select id="roleFilter" class="px-4 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none">
                        <option value="">Tüm Roller</option>
                        <option value="admin">Admin</option>
                        <option value="user">Kullanıcı</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="glass-effect rounded-xl border border-gray-700">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Kullanıcı</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">E-posta</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Rol</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Plan</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Bitiş Tarihi</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Token</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Kayıt Tarihi</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Son Giriş</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Kampanyalar</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Durum</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-800/50 transition-colors" data-user-id="{{ $user->id }}">
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">{{ substr($user->name, 0, 2) }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-white">{{ $user->name }}</div>
                                    @if($user->bio)
                                    <div class="text-sm text-gray-400">{{ Str::limit($user->bio, 30) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="text-gray-300">{{ $user->email }}</div>
                        </td>
                        <td class="py-4 px-4">
                            @if($user->is_admin)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-900/50 text-purple-300 border border-purple-600/50">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Admin
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-900/50 text-gray-300 border border-gray-600/50">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Kullanıcı
                            </span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            @if($user->activeSubscription)
                                <div class="flex items-center space-x-2">
                                    <span class="text-white">{{ $user->activeSubscription->plan->name }}</span>
                                    <span class="text-sm text-gray-400">({{ $user->activeSubscription->plan->formatted_price }})</span>
                                </div>
                            @else
                                <span class="text-gray-500">Plan yok</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-gray-300">
                            @if($user->activeSubscription)
                                {{ $user->activeSubscription->end_date->format('d.m.Y') }}
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            @if($user->tokens_total > 0)
                                <div class="flex flex-col space-y-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-900/50 text-yellow-300 border border-yellow-600/50">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                            {{ number_format($user->tokens_remaining) }}/{{ number_format($user->tokens_total) }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        Kullanılan: {{ number_format($user->tokens_used) }}
                                    </div>
                                    @if($user->token_reset_date)
                                    <div class="text-xs text-gray-500">
                                        Reset: {{ $user->token_reset_date->format('d.m.Y') }}
                                    </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-500">Token yok</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-gray-300">
                            {{ $user->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="py-4 px-4 text-gray-300">
                            @if($user->last_login_at)
                            {{ $user->last_login_at->diffForHumans() }}
                            @else
                            <span class="text-gray-500">Hiç giriş yapmamış</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                                @if($user->campaigns->count() > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-900/50 text-blue-300 border border-blue-600/50">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    {{ $user->campaigns->count() }} Kampanya
                                </span>
                                @else
                                <span class="text-gray-500">Kampanya yok</span>
                                @endif
                                
                                <button onclick="openUserDetailsModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', {{ $user->campaigns->count() }}, {{ $user->tokens_total }}, {{ $user->tokens_used }}, {{ $user->tokens_remaining }}, '{{ $user->token_reset_date ? $user->token_reset_date->format('Y-m-d') : '' }}', {{ $user->max_projects ?? 0 }}, {{ $user->priority_support ? 'true' : 'false' }}, {{ $user->advanced_analytics ? 'true' : 'false' }}, {{ $user->custom_branding ? 'true' : 'false' }}, {{ $user->api_access ? 'true' : 'false' }})" 
                                        class="text-purple-400 hover:text-purple-300 transition-colors" 
                                        title="Detaylı Bilgiler">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900/50 text-green-300 border border-green-600/50">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-900/50 text-yellow-300 border border-yellow-600/50">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Beklemede
                            </span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                                <button onclick="openPlanHistoryModal({{ $user->id }})" class="text-purple-400 hover:text-purple-300 transition-colors" title="Plan Geçmişi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <button onclick="editUser({{ $user->id }})" class="text-blue-400 hover:text-blue-300 transition-colors" title="Düzenle">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                
                                @if($user->is_admin)
                                <button onclick="toggleAdmin({{ $user->id }})" class="text-yellow-400 hover:text-yellow-300 transition-colors" title="Admin Yetkisini Kaldır">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"></path>
                                    </svg>
                                </button>
                                @else
                                <button onclick="toggleAdmin({{ $user->id }})" class="text-purple-400 hover:text-purple-300 transition-colors" title="Admin Yap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                @endif
                                
                                <button onclick="deleteUser({{ $user->id }})" class="text-red-400 hover:text-red-300 transition-colors" title="Sil">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="py-8 px-4 text-center text-gray-400">
                            Henüz kullanıcı bulunamadı
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="glass-effect rounded-xl border border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-400">
                    Toplam {{ $users->total() }} kullanıcıdan {{ $users->firstItem() }}-{{ $users->lastItem() }} arası gösteriliyor
                </div>
                
                <div class="flex space-x-2">
                    @if($users->onFirstPage())
                    <span class="px-3 py-2 text-gray-500 bg-gray-800/50 rounded-lg cursor-not-allowed">Önceki</span>
                    @else
                    <a href="{{ $users->previousPageUrl() }}" class="px-3 py-2 text-gray-300 bg-gray-800/50 rounded-lg hover:bg-gray-700/50 transition-colors">Önceki</a>
                    @endif
                    
                    @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="px-3 py-2 text-gray-300 bg-gray-800/50 rounded-lg hover:bg-gray-700/50 transition-colors {{ $page == $users->currentPage() ? 'bg-purple-500/20 text-purple-400' : '' }}">
                        {{ $page }}
                    </a>
                    @endforeach
                    
                    @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="px-3 py-2 text-gray-300 bg-gray-800/50 rounded-lg hover:bg-gray-700/50 transition-colors">Sonraki</a>
                    @else
                    <span class="px-3 py-2 text-gray-500 bg-gray-800/50 rounded-lg cursor-not-allowed">Sonraki</span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

</div>

    <!-- Plan History Modal -->
    <div id="planHistoryModal" class="fixed inset-0 bg-black/50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-gray-700">
                    <h3 class="text-xl font-semibold text-white">Plan Geçmişi</h3>
                    <button onclick="closePlanHistoryModal()" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto max-h-[70vh]">
                    <div id="planHistoryContent">
                        <div class="flex items-center justify-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-glow"></div>
                            <span class="ml-3 text-gray-400">Yükleniyor...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Details Modal -->
    <div id="userDetailsModal" class="fixed inset-0 bg-black/50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-gray-700">
                    <h3 class="text-xl font-semibold text-white">Kullanıcı Detayları</h3>
                    <button onclick="closeUserDetailsModal()" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto max-h-[70vh]">
                    <div id="userDetailsContent">
                        <!-- Content will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const name = row.querySelector('td:first-child').textContent.toLowerCase();
        const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        
        if (name.includes(searchTerm) || email.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Filter functionality
document.getElementById('statusFilter').addEventListener('change', filterUsers);
document.getElementById('roleFilter').addEventListener('change', filterUsers);

function filterUsers() {
    const statusFilter = document.getElementById('statusFilter').value;
    const roleFilter = document.getElementById('roleFilter').value;
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        let showRow = true;
        
        // Status filter
        if (statusFilter) {
            const status = row.querySelector('td:nth-child(7)').textContent;
            if (statusFilter === 'active' && !status.includes('Aktif')) showRow = false;
            if (statusFilter === 'inactive' && !status.includes('Beklemede')) showRow = false;
        }
        
        // Role filter
        if (roleFilter) {
            const role = row.querySelector('td:nth-child(3)').textContent;
            if (roleFilter === 'admin' && !role.includes('Admin')) showRow = false;
            if (roleFilter === 'user' && !role.includes('Kullanıcı')) showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

// Edit user function
function editUser(userId) {
    // Fetch user data
    fetch(`/admin/users/${userId}`)
        .then(response => response.json())
        .then(user => {
            // Show edit modal
            showEditModal(user);
        })
        .catch(error => {
            console.error('Error fetching user:', error);
            showNotification('Kullanıcı bilgileri alınırken hata oluştu.', 'error');
        });
}

// Toggle admin status function
function toggleAdmin(userId) {
    if (!confirm('Bu kullanıcının admin yetkisini değiştirmek istediğinizden emin misiniz?')) {
        return;
    }
    
    fetch(`/admin/users/${userId}/toggle-admin`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            // Reload page to update UI
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error toggling admin status:', error);
        showNotification('Admin yetkisi değiştirilirken hata oluştu.', 'error');
    });
}

// Delete user function
function deleteUser(userId) {
    if (!confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!')) {
        return;
    }
    
    fetch(`/admin/users/${userId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            // Remove user row from table
            const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
            if (userRow) {
                userRow.remove();
            } else {
                // Reload page if row not found
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error deleting user:', error);
        showNotification('Kullanıcı silinirken hata oluştu.', 'error');
    });
}

// Show edit modal
function showEditModal(user) {
    // Create modal HTML
    const modalHTML = `
        <div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold text-white mb-4">Kullanıcı Düzenle</h3>
                
                <form id="editUserForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Ad Soyad</label>
                        <input type="text" id="editUserName" value="${user.name}" 
                               class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300 mb-2">E-posta</label>
                        <input type="email" id="editUserEmail" value="${user.email}" 
                               class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none">
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Hakkında</label>
                        <textarea id="editUserBio" rows="3" 
                                  class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none">${user.bio || ''}</textarea>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="submit" class="flex-1 px-4 py-2 bg-purple-glow hover:bg-purple-dark text-white rounded-lg transition-colors">
                            Güncelle
                        </button>
                        <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            İptal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Handle form submission
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        updateUser(user.id);
    });
}

// Close edit modal
function closeEditModal() {
    const modal = document.getElementById('editUserModal');
    if (modal) {
        modal.remove();
    }
}

// Update user function
function updateUser(userId) {
    const name = document.getElementById('editUserName').value;
    const email = document.getElementById('editUserEmail').value;
    const bio = document.getElementById('editUserBio').value;
    
    fetch(`/admin/users/${userId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            name: name,
            email: email,
            bio: bio
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeEditModal();
            // Reload page to update UI
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error updating user:', error);
        showNotification('Kullanıcı güncellenirken hata oluştu.', 'error');
    });
}

// Plan History Modal Functions
function openPlanHistoryModal(userId) {
    document.getElementById('planHistoryModal').classList.remove('hidden');
    document.getElementById('planHistoryContent').innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-glow"></div>
            <span class="ml-3 text-gray-400">Yükleniyor...</span>
        </div>
    `;
    
    fetch(`/admin/users/${userId}/plan-history`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderPlanHistory(data.data);
            } else {
                document.getElementById('planHistoryContent').innerHTML = `
                    <div class="text-center py-8">
                        <div class="text-red-400 mb-4">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-400">Plan geçmişi yüklenirken hata oluştu</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('planHistoryContent').innerHTML = `
                <div class="text-center py-8">
                    <div class="text-red-400 mb-4">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-400">Plan geçmişi yüklenirken hata oluştu</p>
                </div>
            `;
        });
}

function closePlanHistoryModal() {
    document.getElementById('planHistoryModal').classList.add('hidden');
}

function renderPlanHistory(data) {
    const { user, subscriptions } = data;
    
    let content = `
        <div class="space-y-6">
            <!-- User Info -->
            <div class="bg-gray-700/50 rounded-lg p-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-full bg-purple-500/20 flex items-center justify-center">
                        <span class="text-lg font-medium text-purple-400">${user.name.charAt(0).toUpperCase()}</span>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-white">${user.name}</h4>
                        <p class="text-gray-400">${user.email}</p>
                    </div>
                </div>
            </div>
            
            <!-- Subscriptions -->
            <div class="space-y-4">
                <h5 class="text-lg font-medium text-white">Plan Geçmişi</h5>
    `;
    
    if (subscriptions.length === 0) {
        content += `
            <div class="text-center py-8">
                <div class="text-gray-400 mb-4">
                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-gray-400">Henüz plan geçmişi bulunmuyor</p>
            </div>
        `;
    } else {
        subscriptions.forEach(subscription => {
            const statusColor = subscription.status === 'active' ? 'green' : 
                              subscription.status === 'cancelled' ? 'gray' : 'red';
            const statusText = subscription.status_text;
            
            content += `
                <div class="bg-gray-700/30 rounded-lg p-4 border border-gray-600/50">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-purple-500/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h6 class="font-medium text-white">${subscription.plan_name}</h6>
                                <p class="text-sm text-gray-400">${subscription.plan_price}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${statusColor}-900/50 text-${statusColor}-300 border border-${statusColor}-600/50">
                            ${statusText}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-400">
                        <div>
                            <span class="text-gray-500">Başlangıç:</span>
                            <span class="ml-2">${subscription.start_date}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Bitiş:</span>
                            <span class="ml-2">${subscription.end_date}</span>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    content += `
            </div>
        </div>
    `;
    
    document.getElementById('planHistoryContent').innerHTML = content;
}

// Show notification function
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-medium transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.textContent = message;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 5000);
}





// User Details Modal Functions
function openUserDetailsModal(userId, userName, userEmail, campaignCount, tokensTotal, tokensUsed, tokensRemaining, tokenResetDate, maxProjects, prioritySupport, advancedAnalytics, customBranding, apiAccess) {
    document.getElementById('userDetailsModal').classList.remove('hidden');
    
    // Calculate token usage percentage
    const usagePercentage = tokensTotal > 0 ? Math.round((tokensUsed / tokensTotal) * 100) : 0;
    
    // Format token reset date
    const resetDateFormatted = tokenResetDate ? new Date(tokenResetDate).toLocaleDateString('tr-TR') : 'Belirtilmemiş';
    
    // Create content HTML
    const content = `
        <div class="space-y-6">
            <!-- User Info Header -->
            <div class="bg-gray-700/50 rounded-lg p-6">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-full bg-purple-500/20 flex items-center justify-center">
                        <span class="text-2xl font-medium text-purple-400">${userName.charAt(0).toUpperCase()}</span>
                    </div>
                    <div>
                        <h4 class="text-2xl font-semibold text-white">${userName}</h4>
                        <p class="text-gray-400 text-lg">${userEmail}</p>
                        <p class="text-sm text-gray-500">Kullanıcı ID: ${userId}</p>
                    </div>
                </div>
            </div>
            
            <!-- Token Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-700/50 rounded-lg p-6">
                    <h5 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Token Bilgileri
                    </h5>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Toplam Token:</span>
                            <span class="text-white font-medium">${tokensTotal.toLocaleString()}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Kullanılan:</span>
                            <span class="text-white font-medium">${tokensUsed.toLocaleString()}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Kalan:</span>
                            <span class="text-green-400 font-medium">${tokensRemaining.toLocaleString()}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Kullanım Oranı:</span>
                            <span class="text-white font-medium">%${usagePercentage}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Reset Tarihi:</span>
                            <span class="text-white font-medium">${resetDateFormatted}</span>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-4">
                            <div class="w-full bg-gray-600 rounded-full h-2">
                                <div class="bg-gradient-to-r from-yellow-500 to-orange-500 h-2 rounded-full transition-all duration-300" 
                                     style="width: ${usagePercentage}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Project & Campaign Info -->
                <div class="bg-gray-700/50 rounded-lg p-6">
                    <h5 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Proje & Kampanya Bilgileri
                    </h5>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Kampanya Sayısı:</span>
                            <span class="text-white font-medium">${campaignCount}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Maksimum Proje:</span>
                            <span class="text-white font-medium">${maxProjects}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- VIP Features -->
            <div class="bg-gray-700/50 rounded-lg p-6">
                <h5 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    VIP Özellikler
                </h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center space-x-3 p-3 bg-gray-600/30 rounded-lg">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center ${prioritySupport ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-500'}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-white font-medium">Öncelikli Destek</span>
                            <p class="text-xs text-gray-400">7/24 öncelikli müşteri desteği</p>
                        </div>
                        <div class="ml-auto">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${prioritySupport ? 'bg-green-900/50 text-green-300' : 'bg-gray-900/50 text-gray-500'}">
                                ${prioritySupport ? 'Aktif' : 'Pasif'}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 p-3 bg-gray-600/30 rounded-lg">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center ${advancedAnalytics ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-500'}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-white font-medium">Gelişmiş Analitik</span>
                            <p class="text-xs text-gray-400">Detaylı kullanım raporları</p>
                        </div>
                        <div class="ml-auto">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${advancedAnalytics ? 'bg-green-900/50 text-green-300' : 'bg-gray-900/50 text-gray-500'}">
                                ${advancedAnalytics ? 'Aktif' : 'Pasif'}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 p-3 bg-gray-600/30 rounded-lg">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center ${customBranding ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-500'}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-white font-medium">Özel Markalama</span>
                            <p class="text-xs text-gray-400">Kendi markanızla özelleştirme</p>
                        </div>
                        <div class="ml-auto">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${customBranding ? 'bg-green-900/50 text-green-300' : 'bg-gray-900/50 text-gray-500'}">
                                ${customBranding ? 'Aktif' : 'Pasif'}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 p-3 bg-gray-600/30 rounded-lg">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center ${apiAccess ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-500'}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-white font-medium">API Erişimi</span>
                            <p class="text-xs text-gray-400">Gelişmiş API entegrasyonu</p>
                        </div>
                        <div class="ml-auto">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${apiAccess ? 'bg-green-900/50 text-green-300' : 'bg-gray-900/50 text-gray-500'}">
                                ${apiAccess ? 'Aktif' : 'Pasif'}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('userDetailsContent').innerHTML = content;
}

function closeUserDetailsModal() {
    document.getElementById('userDetailsModal').classList.add('hidden');
}

// ESC key to close modals
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePlanHistoryModal();
        closeUserDetailsModal();
    }
});
</script>
@endsection
