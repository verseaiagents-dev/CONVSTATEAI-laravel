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
            <h1 class="text-4xl font-bold mb-4">
                <span class="gradient-text">Kullanıcı Yönetimi</span> 👥
            </h1>
            <p class="text-xl text-gray-300 mb-6">
                Sistem kullanıcılarını görüntüleyin, yönetin ve takip edin.
            </p>
        </div>
    </div>

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
    <div class="glass-effect rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Kullanıcı</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">E-posta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Kayıt Tarihi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Son Giriş</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Kampanyalar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-800/30 transition-colors duration-200" data-user-id="{{ $user->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-purple-500/20 rounded-full flex items-center justify-center">
                                    <span class="text-purple-400 font-medium">{{ substr($user->name, 0, 2) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-white">{{ $user->name }}</div>
                                    @if($user->bio)
                                    <div class="text-sm text-gray-400">{{ Str::limit($user->bio, 30) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-300">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->is_admin)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-500/20 text-purple-400">
                                Admin
                            </span>
                            @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-500/20 text-gray-400">
                                Kullanıcı
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                            {{ $user->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                            @if($user->last_login_at)
                            {{ $user->last_login_at->diffForHumans() }}
                            @else
                            <span class="text-gray-500">Hiç giriş yapmamış</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                            @if($user->campaigns->count() > 0)
                            <div class="space-y-1">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-500/20 text-blue-400">
                                    {{ $user->campaigns->count() }} Kampanya
                                </span>
                                <div class="text-xs text-gray-400">
                                    @foreach($user->campaigns->take(2) as $campaign)
                                        <div>{{ Str::limit($campaign->title, 20) }}</div>
                                    @endforeach
                                    @if($user->campaigns->count() > 2)
                                        <div class="text-gray-500">+{{ $user->campaigns->count() - 2 }} daha</div>
                                    @endif
                                </div>
                            </div>
                            @else
                            <span class="text-gray-500">Kampanya yok</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->email_verified_at)
                            <span class="text-green-400">Aktif</span>
                            @else
                            <span class="text-yellow-400">Beklemede</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="editUser({{ $user->id }})" class="text-blue-400 hover:text-blue-300 transition-colors" title="Düzenle">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                
                                @if($user->is_admin)
                                <button onclick="toggleAdmin({{ $user->id }})" class="text-yellow-400 hover:text-yellow-300 transition-colors" title="Admin Yetkisini Kaldır">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"></path>
                                    </svg>
                                </button>
                                @else
                                <button onclick="toggleAdmin({{ $user->id }})" class="text-purple-400 hover:text-purple-300 transition-colors" title="Admin Yap">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                @endif
                                
                                <button onclick="deleteUser({{ $user->id }})" class="text-red-400 hover:text-red-300 transition-colors" title="Sil">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-400">
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
    <div class="glass-effect rounded-xl p-4">
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
</script>
@endsection
