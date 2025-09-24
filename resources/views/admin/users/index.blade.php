@extends('layouts.admin')

@section('title', 'Users Management')

<style>
/* Shimmer Loading Effect */
.shimmer {
    background: linear-gradient(90deg, #374151 25%, #4B5563 50%, #374151 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

.shimmer-text {
    height: 1rem;
    border-radius: 0.25rem;
    margin-bottom: 0.5rem;
}

.shimmer-text.short {
    width: 60%;
}

.shimmer-text.medium {
    width: 80%;
}

.shimmer-text.long {
    width: 100%;
}

.shimmer-badge {
    height: 1.5rem;
    width: 4rem;
    border-radius: 9999px;
}

.shimmer-avatar {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
}

.fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

@section('content')
<div class="space-y-6">
    <!-- Users Management Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-4xl font-bold">
                    <span class="gradient-text">Kullanıcı Yönetimi</span> 👥
                </h1>
                <button class="bg-purple-glow hover:bg-purple-dark text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-purple-glow/25 flex items-center space-x-2" data-target="#addUserModal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Kullanıcı Ekle</span>
                </button>
            </div>
            <p class="text-xl text-gray-300 mb-6">
                Sistem kullanıcılarını yönetin ve izleyin.
            </p>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="glass-effect rounded-xl border border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">Kullanıcı Yönetimi</h3>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-700">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button type="button" onclick="switchTab('users')" id="users-tab" class="tab-button active py-4 px-1 border-b-2 border-purple-glow font-medium text-sm text-purple-glow hover:bg-gray-800/50 transition-colors duration-200">
                    Kayıtlı Kullanıcılar
                </button>
                <button type="button" onclick="switchTab('subscriptions')" id="subscriptions-tab" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-400 hover:text-gray-300 hover:bg-gray-800/50 transition-colors duration-200">
                    Abonelik Yönetimi
                </button>
                <button type="button" onclick="switchTab('demo')" id="demo-tab" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-400 hover:text-gray-300 hover:bg-gray-800/50 transition-colors duration-200">
                    Demo Kullanıcıları
                </button>
            </nav>
        </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Users Tab -->
                    <div id="users-content" class="tab-content">
        <!-- Filters -->
        <div class="p-6">
            <div class="glass-effect rounded-xl p-6 mb-6">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-64">
                        <input type="text" name="search" class="w-full px-4 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20" 
                               placeholder="Kullanıcı ara..." value="{{ request('search') }}">
                    </div>
                    
                    <div class="min-w-48">
                        <select name="plan" class="w-full px-4 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none">
                            <option value="">Tüm Planlar</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" 
                                        {{ request('plan') == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="min-w-48">
                        <select name="subscription_status" class="w-full px-4 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none">
                            <option value="">Tüm Durumlar</option>
                            <option value="active" {{ request('subscription_status') == 'active' ? 'selected' : '' }}>
                                Aktif Abonelik
                            </option>
                            <option value="inactive" {{ request('subscription_status') == 'inactive' ? 'selected' : '' }}>
                                Abonelik Yok
                            </option>
                        </select>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-purple-glow hover:bg-purple-dark text-white px-6 py-2 rounded-lg transition-all duration-200 shadow-lg hover:shadow-purple-glow/25 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span>Filtrele</span>
                        </button>
                        
                        @if(request()->hasAny(['search', 'plan', 'subscription_status']))
                            <a href="{{ route('admin.users.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-all duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span>Temizle</span>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Kullanıcı</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Mevcut Plan</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Token Kullanımı</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Durum</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Kayıt Tarihi</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-800/50 transition-colors">
                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-purple-500/20 flex items-center justify-center">
                                            <span class="text-sm font-medium text-purple-400">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-white">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-400">{{ $user->email }}</div>
                                            @if($user->is_admin)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-900/50 text-purple-300 border border-purple-600/50 mt-1">Admin</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    @if($user->subscriptions->where('status', 'active')->first())
                                        @php $activeSub = $user->subscriptions->where('status', 'active')->first(); @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900/50 text-green-300 border border-green-600/50">
                                            {{ $activeSub->plan->name }}
                                        </span>
                                        <div class="text-xs text-gray-400 mt-1">
                                            @if($activeSub->expires_at)
                                                Bitiş: {{ $activeSub->expires_at->format('d.m.Y') }}
                                            @else
                                                Süresiz
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-900/50 text-gray-300 border border-gray-600/50">Plan Yok</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @if($user->tokens_total > 0)
                                        <div class="text-sm text-white">
                                            <span class="font-semibold">{{ $user->tokens_remaining }}</span> / {{ $user->tokens_total }}
                                        </div>
                                        <div class="w-full bg-gray-700 rounded-full h-2 mt-2">
                                            @php
                                                $percentage = $user->tokens_total > 0 
                                                    ? ($user->tokens_remaining / $user->tokens_total) * 100 
                                                    : 0;
                                            @endphp
                                            <div class="h-2 rounded-full {{ $percentage < 20 ? 'bg-red-500' : ($percentage < 50 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                                                 style="width: {{ $percentage }}%"></div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">Token yok</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900/50 text-green-300 border border-green-600/50">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900/50 text-red-300 border border-red-600/50">Pasif</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-300">
                                    {{ $user->created_at->format('d.m.Y') }}
                                </td>
                                <td class="py-4 px-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.users.show', $user) }}" 
                                           class="text-blue-400 hover:text-blue-300 transition-colors duration-200" 
                                           title="Detayları Görüntüle">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        
                                        <a href="{{ route('admin.users.assign-plan', $user) }}" 
                                           class="text-purple-400 hover:text-purple-300 transition-colors duration-200" 
                                           title="Plan Ata">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </a>
                                        
                                        <button onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', {{ $user->is_admin ? 'true' : 'false' }}, {{ $user->is_active ? 'true' : 'false' }})" 
                                                class="text-yellow-400 hover:text-yellow-300 transition-colors duration-200" 
                                                title="Düzenle">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        
                                        <form action="{{ route('admin.users.toggle-status', $user) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="{{ $user->is_active ? 'text-gray-400 hover:text-gray-300' : 'text-green-400 hover:text-green-300' }} transition-colors duration-200" 
                                                    title="{{ $user->is_active ? 'Pasifleştir' : 'Aktifleştir' }}">
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
                                                  onsubmit="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-400 hover:text-red-300 transition-colors duration-200" 
                                                        title="Sil">
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
                                <td colspan="6" class="py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                        <p class="text-gray-400 text-lg">Kullanıcı bulunamadı</p>
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

        <!-- Subscriptions Tab -->
        <div id="subscriptions-content" class="tab-content hidden">
            <div class="p-6">
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-white mb-4">Aktif Abonelikler</h4>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Kullanıcı</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Plan</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Başlangıç</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Bitiş</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Durum</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @forelse($subscriptions as $subscription)
                                <tr class="hover:bg-gray-800/50 transition-colors fade-in">
                                    <td class="py-4 px-4">
                                        <div class="text-sm font-medium text-white">{{ $subscription->user->name }}</div>
                                        <div class="text-sm text-gray-400">{{ $subscription->user->email }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="text-sm text-white">{{ $subscription->plan->name }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-sm text-gray-300">
                                        {{ $subscription->start_date->format('d.m.Y') }}
                                    </td>
                                    <td class="py-4 px-4 text-sm text-gray-300">
                                        {{ $subscription->end_date->format('d.m.Y') }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-900/50 text-green-300 border border-green-600/50">
                                            Aktif
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-gray-400 text-lg">Aktif abonelik bulunamadı</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            
                            <!-- Shimmer Loading Rows -->
                            <tr id="subscriptions-loading" class="hidden">
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text medium"></div>
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text medium"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-badge"></div>
                                </td>
                            </tr>
                            
                            <tr id="subscriptions-loading-2" class="hidden">
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text medium"></div>
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text medium"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-badge"></div>
                                </td>
                            </tr>
                            
                            <tr id="subscriptions-loading-3" class="hidden">
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text medium"></div>
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text medium"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-badge"></div>
                                </td>
                            </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

        <!-- Demo Users Tab -->
        <div id="demo-content" class="tab-content hidden">
            <div class="p-6">
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-white mb-4">Demo Talepleri</h4>
                    <p class="text-sm text-gray-400">Ana sayfadan gelen demo talepleri</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Kişi</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">İletişim</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Site Trafiği</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Durum</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Tarih</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @forelse($demoRequests as $request)
                                <tr class="hover:bg-gray-800/50 transition-colors fade-in">
                                    <td class="py-4 px-4">
                                        <div>
                                            <div class="text-sm font-medium text-white">{{ $request->full_name }}</div>
                                            <div class="text-sm text-gray-400">ID: {{ $request->id }}</div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div>
                                            <div class="text-sm text-white">{{ $request->email }}</div>
                                            @if($request->phone)
                                                <div class="text-sm text-gray-400">{{ $request->phone }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($request->site_visitor_count)
                                            <span class="text-sm text-white">{{ number_format($request->site_visitor_count) }}+</span>
                                        @else
                                            <span class="text-sm text-gray-400">Belirtilmemiş</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-900/50 text-yellow-300 border border-yellow-600/50',
                                                'contacted' => 'bg-blue-900/50 text-blue-300 border border-blue-600/50',
                                                'completed' => 'bg-green-900/50 text-green-300 border border-green-600/50',
                                                'cancelled' => 'bg-red-900/50 text-red-300 border border-red-600/50'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Bekleyen',
                                                'contacted' => 'İletişim Kuruldu',
                                                'completed' => 'Tamamlandı',
                                                'cancelled' => 'İptal Edildi'
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$request->status] }}">
                                            {{ $statusLabels[$request->status] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-sm text-gray-300">
                                        {{ $request->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="py-4 px-4 text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <!-- Status Dropdown -->
                                            <select onchange="updateDemoRequestStatus({{ $request->id }}, this.value)" 
                                                    class="text-xs px-2 py-1 rounded border border-gray-600 bg-gray-800/50 text-white focus:border-purple-glow focus:outline-none">
                                                <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>Bekleyen</option>
                                                <option value="contacted" {{ $request->status == 'contacted' ? 'selected' : '' }}>İletişim Kuruldu</option>
                                                <option value="completed" {{ $request->status == 'completed' ? 'selected' : '' }}>Tamamlandı</option>
                                                <option value="cancelled" {{ $request->status == 'cancelled' ? 'selected' : '' }}>İptal Edildi</option>
                                            </select>
                                            
                                            <!-- Action Buttons -->
                                            <a href="{{ route('admin.demo-requests.show', $request) }}" class="text-purple-400 hover:text-purple-300" title="Detayları Görüntüle">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="mailto:{{ $request->email }}" class="text-blue-400 hover:text-blue-300" title="E-posta Gönder">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-gray-400 text-lg">Henüz demo talebi yok</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            
                            <!-- Shimmer Loading Rows for Demo Requests -->
                            <tr id="demo-loading" class="hidden">
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text medium"></div>
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text medium"></div>
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-badge"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="shimmer shimmer-text short" style="width: 5rem;"></div>
                                        <div class="shimmer shimmer-avatar"></div>
                                        <div class="shimmer shimmer-avatar"></div>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr id="demo-loading-2" class="hidden">
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text medium"></div>
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text medium"></div>
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-badge"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="shimmer shimmer-text short" style="width: 5rem;"></div>
                                        <div class="shimmer shimmer-avatar"></div>
                                        <div class="shimmer shimmer-avatar"></div>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr id="demo-loading-3" class="hidden">
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text medium"></div>
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text medium"></div>
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-badge"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="shimmer shimmer-text short"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="shimmer shimmer-text short" style="width: 5rem;"></div>
                                        <div class="shimmer shimmer-avatar"></div>
                                        <div class="shimmer shimmer-avatar"></div>
                                    </div>
                                </td>
                            </tr>
                                </tbody>
                            </table>
                        </div>
                        
                <!-- Demo Requests Pagination -->
                @if($demoRequests->hasPages())
                    <div class="flex justify-center mt-6">
                        {{ $demoRequests->links() }}
                    </div>
                @endif
            </div>
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

// Tab switching functionality
function switchTab(tabName) {
    console.log('Switching to tab:', tabName); // Debug log
    
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.add('hidden');
        console.log('Hiding content:', content.id); // Debug log
    });
    
    // Remove active class from all tabs
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('active', 'border-purple-500', 'text-purple-600', 'dark:text-purple-400');
        button.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    });
    
    // Show selected tab content
    const selectedContent = document.getElementById(tabName + '-content');
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
        console.log('Showing content:', selectedContent.id); // Debug log
    } else {
        console.error('Content not found:', tabName + '-content'); // Debug log
    }
    
    // Add active class to selected tab
    const selectedTab = document.getElementById(tabName + '-tab');
    if (selectedTab) {
        selectedTab.classList.add('active', 'border-purple-500', 'text-purple-600', 'dark:text-purple-400');
        selectedTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        console.log('Activated tab:', selectedTab.id); // Debug log
    } else {
        console.error('Tab button not found:', tabName + '-tab'); // Debug log
    }
}

// Initialize tabs on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing tabs');
    // Ensure first tab is active by default
    switchTab('users');
});

// Demo Request Status Update Function
function updateDemoRequestStatus(requestId, newStatus) {
    if (!confirm('Demo talebinin durumunu değiştirmek istediğinizden emin misiniz?')) {
        // Reset dropdown to original value
        location.reload();
        return;
    }
    
    fetch(`/admin/demo-requests/${requestId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            // Update the status badge in the table
            updateStatusBadge(requestId, newStatus);
        } else {
            showNotification(data.message, 'error');
            location.reload(); // Reset on error
        }
    })
    .catch(error => {
        console.error('Error updating demo request status:', error);
        showNotification('Durum güncellenirken hata oluştu.', 'error');
        location.reload(); // Reset on error
    });
}

// Update status badge in the table
function updateStatusBadge(requestId, newStatus) {
    const statusColors = {
        'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'contacted': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    };
    
    const statusLabels = {
        'pending': 'Bekleyen',
        'contacted': 'İletişim Kuruldu',
        'completed': 'Tamamlandı',
        'cancelled': 'İptal Edildi'
    };
    
    // Find the row and update the status badge
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const selectElement = row.querySelector(`select[onchange*="${requestId}"]`);
        if (selectElement) {
            const statusCell = row.querySelector('td:nth-child(4)'); // Status column
            if (statusCell) {
                const badge = statusCell.querySelector('span');
                if (badge) {
                    badge.className = `inline-flex px-2 py-1 text-xs font-medium rounded-full ${statusColors[newStatus]}`;
                    badge.textContent = statusLabels[newStatus];
                }
            }
        }
    });
}

// Tab switching with shimmer loading
function switchTab(tabName) {
    console.log('Switching to tab:', tabName);
    
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-purple-glow', 'text-purple-glow');
        button.classList.add('border-transparent', 'text-gray-400');
    });
    
    // Show selected tab content
    const selectedContent = document.getElementById(tabName + '-content');
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
        
        // Add shimmer loading effect for subscriptions and demo tabs
        if (tabName === 'subscriptions' || tabName === 'demo') {
            showShimmerLoading(tabName);
            
            // Simulate data loading delay
            setTimeout(() => {
                hideShimmerLoading(tabName);
            }, 1500);
        }
    }
    
    // Add active class to selected tab
    const selectedTab = document.getElementById(tabName + '-tab');
    if (selectedTab) {
        selectedTab.classList.add('active', 'border-purple-glow', 'text-purple-glow');
        selectedTab.classList.remove('border-transparent', 'text-gray-400');
    }
}

// Show shimmer loading effect
function showShimmerLoading(tabName) {
    const loadingRows = document.querySelectorAll(`#${tabName}-loading, #${tabName}-loading-2, #${tabName}-loading-3`);
    loadingRows.forEach(row => {
        row.classList.remove('hidden');
    });
}

// Hide shimmer loading effect
function hideShimmerLoading(tabName) {
    const loadingRows = document.querySelectorAll(`#${tabName}-loading, #${tabName}-loading-2, #${tabName}-loading-3`);
    loadingRows.forEach(row => {
        row.classList.add('hidden');
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

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Set initial tab
    switchTab('users');
});
</script>
@endsection
