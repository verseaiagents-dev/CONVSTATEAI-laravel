@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="w-full">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Users Management</h3>
                    <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition-colors duration-200 flex items-center space-x-2" data-target="#addUserModal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Add User</span>
                    </button>
                </div>

                <div class="p-6">
                    <!-- Filters -->
                    <div class="mb-6">
                        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-4">
                            <div class="flex-1 min-w-64">
                                <input type="text" name="search" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white" 
                                       placeholder="Search users..." value="{{ request('search') }}">
                            </div>
                            
                            <div class="min-w-48">
                                <select name="plan" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    <option value="">All Plans</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" 
                                                {{ request('plan') == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="min-w-48">
                                <select name="subscription_status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('subscription_status') == 'active' ? 'selected' : '' }}>
                                        Active Subscription
                                    </option>
                                    <option value="inactive" {{ request('subscription_status') == 'inactive' ? 'selected' : '' }}>
                                        No Active Subscription
                                    </option>
                                </select>
                            </div>
                            
                            <div class="flex space-x-2">
                                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition-colors duration-200 flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <span>Filter</span>
                                </button>
                                
                                @if(request()->hasAny(['search', 'plan', 'subscription_status']))
                                    <a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors duration-200 flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>Clear</span>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Users Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Current Plan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Usage Tokens</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Joined</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($users as $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white font-semibold">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                                    @if($user->is_admin)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100 mt-1">Admin</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->subscriptions->where('status', 'active')->first())
                                                @php $activeSub = $user->subscriptions->where('status', 'active')->first(); @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                    {{ $activeSub->plan->name }}
                                                </span>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    @if($activeSub->expires_at)
                                                        Expires: {{ $activeSub->expires_at->format('M d, Y') }}
                                                    @else
                                                        No expiration date
                                                    @endif
                                                </div>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">No Plan</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->usageToken)
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    <span class="font-semibold">{{ $user->usageToken->tokens - $user->usageToken->used_tokens }}</span> / {{ $user->usageToken->tokens }}
                                                </div>
                                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-2">
                                                    @php
                                                        $percentage = $user->usageToken->tokens > 0 
                                                            ? (($user->usageToken->tokens - $user->usageToken->used_tokens) / $user->usageToken->tokens) * 100 
                                                            : 0;
                                                    @endphp
                                                    <div class="h-2 rounded-full {{ $percentage < 20 ? 'bg-red-500' : ($percentage < 50 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                                                         style="width: {{ $percentage }}%"></div>
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-500 dark:text-gray-400">No tokens</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->is_active)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">Active</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $user->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.users.show', $user) }}" 
                                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200" 
                                                   title="View Details">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                
                                                <a href="{{ route('admin.users.assign-plan', $user) }}" 
                                                   class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 transition-colors duration-200" 
                                                   title="Assign Plan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </a>
                                                
                                                <button onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', {{ $user->is_admin ? 'true' : 'false' }}, {{ $user->is_active ? 'true' : 'false' }})" 
                                                        class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 transition-colors duration-200" 
                                                        title="Edit User">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                
                                                <form action="{{ route('admin.users.toggle-status', $user) }}" 
                                                      method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="{{ $user->is_active ? 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300' : 'text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300' }} transition-colors duration-200" 
                                                            title="{{ $user->is_active ? 'Deactivate' : 'Activate' }} User">
                                                        @if($user->is_active)
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        @endif
                                                    </button>
                                                </form>
                                                
                                                @if(!$user->is_admin)
                                                    <form action="{{ route('admin.users.destroy', $user) }}" 
                                                          method="POST" class="inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-200" 
                                                                title="Delete User">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                                </svg>
                                                <p class="text-gray-500 dark:text-gray-400 text-lg">No users found</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($users->hasPages())
                        <div class="flex justify-center mt-6">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="addUserModal">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Add New User</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" onclick="closeModal()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                        <input type="text" name="name" id="name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email *</label>
                        <input type="email" name="email" id="email" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password *</label>
                        <input type="password" name="password" id="password" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_admin" id="is_admin" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 dark:border-gray-600 rounded">
                        <label for="is_admin" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Admin User</label>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors duration-200" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors duration-200">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="editUserModal">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit User</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" onclick="closeEditModal()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                        <input type="text" name="name" id="edit_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                    </div>
                    <div>
                        <label for="edit_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email *</label>
                        <input type="email" name="email" id="edit_email" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                    </div>
                    <div>
                        <label for="edit_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password (leave blank to keep current)</label>
                        <input type="password" name="password" id="edit_password" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_admin" id="edit_is_admin" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 dark:border-gray-600 rounded">
                        <label for="edit_is_admin" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Admin User</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="edit_is_active" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 dark:border-gray-600 rounded">
                        <label for="edit_is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Active User</label>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors duration-200" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors duration-200">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('addUserModal').classList.add('hidden');
}

function openEditModal(userId, name, email, isAdmin, isActive) {
    // Set form action URL
    document.getElementById('editUserForm').action = `/admin/users/${userId}`;
    
    // Populate form fields
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_is_admin').checked = isAdmin;
    document.getElementById('edit_is_active').checked = isActive;
    
    // Clear password field
    document.getElementById('edit_password').value = '';
    
    // Show modal
    document.getElementById('editUserModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editUserModal').classList.add('hidden');
}

// Modal açma butonuna event listener ekle
document.addEventListener('DOMContentLoaded', function() {
    const addButton = document.querySelector('[data-target="#addUserModal"]');
    if (addButton) {
        addButton.addEventListener('click', function(e) {
            e.preventDefault();
            openModal();
        });
    }
    
    // Modal dışına tıklandığında kapat
    document.getElementById('addUserModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    
    // Edit modal dışına tıklandığında kapat
    document.getElementById('editUserModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
});
</script>
@endsection
