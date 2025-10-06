@extends('layouts.admin')

@section('title', 'Abonelikler - Admin Panel')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold gradient-text">Abonelikler</h1>
            <p class="mt-2 text-gray-400">Kullanıcı aboneliklerini ve plan taleplerini yönetin</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button onclick="openCreateSubscriptionModal()" class="inline-flex items-center px-4 py-2 bg-purple-glow hover:bg-purple-dark text-white font-medium rounded-lg transition-all duration-200 hover:shadow-lg hover:shadow-purple-glow/25">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Abonelik Ekle
            </button>
            <button onclick="openVipTokenModal()" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-medium rounded-lg transition-all duration-200 hover:shadow-lg hover:shadow-yellow-500/25">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                VIP Token Ekle
            </button>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-700">
        <nav class="-mb-px flex space-x-8">
            <button onclick="switchTab('subscriptions')" id="subscriptions-tab" class="tab-button active py-2 px-1 border-b-2 border-purple-glow font-medium text-sm text-purple-glow">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Abonelikler ({{ $subscriptions->count() }})
            </button>
            <button onclick="switchTab('requests')" id="requests-tab" class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-400 hover:text-gray-300 hover:border-gray-300">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Plan Talepleri ({{ $planRequests->where('status', 'pending')->count() }})
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div id="subscriptions-content" class="tab-content">
        <!-- Subscriptions List -->
        <div class="glass-effect rounded-xl border border-gray-700">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Kullanıcı</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Plan</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Başlangıç</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Bitiş</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Durum</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Token</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($subscriptions as $subscription)
                        <tr class="hover:bg-gray-800/50 transition-colors">
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">{{ substr($subscription->user->name, 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-white">{{ $subscription->user->name }}</div>
                                        <div class="text-sm text-gray-400">{{ $subscription->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-2">
                                    <span class="text-white">{{ $subscription->plan->name }}</span>
                                    <span class="text-sm text-gray-400">({{ $subscription->plan->formatted_price }})</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-gray-300">
                                {{ $subscription->start_date->format('d.m.Y') }}
                            </td>
                            <td class="py-4 px-4 text-gray-300">
                                {{ $subscription->end_date->format('d.m.Y') }}
                            </td>
                            <td class="py-4 px-4">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'expired' => 'bg-red-100 text-red-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $statusTexts = [
                                        'active' => 'Aktif',
                                        'expired' => 'Süresi Dolmuş',
                                        'cancelled' => 'İptal Edilmiş'
                                    ];
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$subscription->status] }}">
                                    {{ $statusTexts[$subscription->status] }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                @if($subscription->user->usage_token > 0)
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-900/50 text-yellow-300 border border-yellow-600/50">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                            {{ number_format($subscription->user->usage_token) }}
                                        </span>
                                        <button onclick="openVipTokenModal({{ $subscription->user->id }}, '{{ addslashes($subscription->user->name) }}', {{ $subscription->user->usage_token }}, {{ $subscription->user->max_projects ?? 0 }})" class="text-yellow-400 hover:text-yellow-300 transition-colors" title="VIP Token Düzenle">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <button onclick="openVipTokenModal({{ $subscription->user->id }}, '{{ addslashes($subscription->user->name) }}', 0, {{ $subscription->user->max_projects ?? 0 }})" class="text-gray-400 hover:text-yellow-400 transition-colors" title="VIP Token Ekle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-2">
                                    <button onclick="openPlanHistoryModal({{ $subscription->user->id }})" class="text-purple-400 hover:text-purple-300 transition-colors" title="Plan Geçmişi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="openEditSubscriptionModal({{ $subscription->id }}, '{{ addslashes($subscription->user->name) }}', '{{ addslashes($subscription->user->email) }}', {{ $subscription->plan->id }}, '{{ addslashes($subscription->plan->name) }}', '{{ $subscription->start_date->format('Y-m-d') }}', '{{ $subscription->end_date->format('Y-m-d') }}', '{{ $subscription->status }}', '{{ $subscription->trial_ends_at ? $subscription->trial_ends_at->format('Y-m-d\TH:i') : '' }}')" class="text-blue-400 hover:text-blue-300 transition-colors" title="Düzenle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <form action="{{ route('admin.subscriptions.destroy', $subscription) }}" method="POST" class="inline" onsubmit="return confirm('Bu aboneliği silmek istediğinizden emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 transition-colors">
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
                            <td colspan="7" class="py-8 px-4 text-center text-gray-400">
                                Henüz abonelik bulunmuyor
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    <!-- Plan Requests Tab -->
    <div id="requests-content" class="tab-content hidden">
        <div class="glass-effect rounded-xl border border-gray-700">
            <div class="p-6">
                <div class="w-full">
                    <table class="w-full table-fixed">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="w-1/4 text-left py-3 px-4 text-sm font-medium text-gray-300">Kullanıcı</th>
                                <th class="w-1/4 text-left py-3 px-4 text-sm font-medium text-gray-300">Talep Edilen Plan</th>
                                <th class="w-1/6 text-left py-3 px-4 text-sm font-medium text-gray-300">Talep Tarihi</th>
                                <th class="w-1/6 text-left py-3 px-4 text-sm font-medium text-gray-300">Durum</th>
                                <th class="w-1/6 text-left py-3 px-4 text-sm font-medium text-gray-300">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @php
                                // Group requests by user to show only one entry per user
                                $groupedRequests = $planRequests->groupBy('user_id');
                            @endphp
                            @forelse($groupedRequests as $userId => $userRequests)
                                @php
                                    // Get the latest request for this user
                                    $latestRequest = $userRequests->sortByDesc('created_at')->first();
                                @endphp
                                <tr class="hover:bg-gray-800/50 transition-colors">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">{{ substr($latestRequest->user->name, 0, 2) }}</span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="font-medium text-white truncate">{{ $latestRequest->user->name }}</div>
                                                <div class="text-sm text-gray-400 truncate">{{ $latestRequest->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex flex-col space-y-1">
                                            <span class="font-medium text-white truncate">{{ $latestRequest->plan->name }}</span>
                                            <span class="text-sm text-gray-400 truncate">{{ number_format($latestRequest->plan->price, 2) }} ₺/{{ $latestRequest->plan->billing_cycle_text }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-gray-300">
                                        {{ $latestRequest->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($latestRequest->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-900/50 text-yellow-300 border border-yellow-600/50">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Bekliyor
                                            </span>
                                        @elseif($latestRequest->status === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900/50 text-green-300 border border-green-600/50">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Onaylandı
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900/50 text-red-300 border border-red-600/50">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Reddedildi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-2">
                                            <!-- Plan History Button -->
                                            <button onclick="openPlanHistoryModal({{ $latestRequest->user->id }})" class="text-purple-400 hover:text-purple-300 transition-colors" title="Plan Geçmişi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                            
                                            <!-- Edit Button -->
                                            <button onclick="openEditRequestModal({{ $latestRequest->id }}, '{{ addslashes($latestRequest->user->name) }}', '{{ addslashes($latestRequest->user->email) }}', {{ $latestRequest->plan->id }}, '{{ addslashes($latestRequest->plan->name) }}', '{{ $latestRequest->status }}', '{{ addslashes($latestRequest->admin_notes ?? '') }}')" class="text-blue-400 hover:text-blue-300 transition-colors" title="Düzenle">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            
                                            @if($latestRequest->status === 'pending')
                                                <div class="flex space-x-1">
                                                    <button onclick="openApproveModal({{ $latestRequest->id }})" class="inline-flex items-center px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded transition-colors" title="Onayla">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </button>
                                                    <button onclick="openRejectModal({{ $latestRequest->id }})" class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors" title="Reddet">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @else
                                                @if($latestRequest->approvedBy)
                                                    <div class="relative group">
                                                        <button class="text-gray-400 hover:text-gray-300 transition-colors" title="Detay Bilgi">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        </button>
                                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-800 text-white text-xs rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                                            {{ $latestRequest->approvedBy->name }} tarafından
                                                            {{ $latestRequest->approved_at ? $latestRequest->approved_at->format('d.m.Y H:i') : '' }}
                                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-800"></div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-8 px-4 text-center text-gray-400">
                                    Henüz plan talebi bulunmuyor
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Subscription Modal -->
<div id="createSubscriptionModal" class="fixed inset-0 bg-black/50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-700">
                <h3 class="text-xl font-semibold text-white">Yeni Abonelik Oluştur</h3>
                <button onclick="closeCreateSubscriptionModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <form action="{{ route('admin.subscriptions.store') }}" method="POST" id="createSubscriptionForm">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- User -->
                        <div>
                            <label for="modal_tenant_id" class="block text-sm font-medium text-gray-300 mb-2">Kullanıcı</label>
                            <select id="modal_tenant_id" name="tenant_id" required 
                                    class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                                <option value="">Kullanıcı Seçin</option>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Plan -->
                        <div>
                            <label for="modal_plan_id" class="block text-sm font-medium text-gray-300 mb-2">Plan</label>
                            <select id="modal_plan_id" name="plan_id" required 
                                    class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                                    onchange="calculateEndDate()">
                                <option value="">Plan Seçin</option>
                                @foreach(\App\Models\Plan::where('is_active', true)->get() as $plan)
                                    <option value="{{ $plan->id }}" 
                                            data-billing-cycle="{{ $plan->billing_cycle }}" 
                                            data-trial-days="{{ $plan->trial_days ?? 0 }}">
                                        {{ $plan->name }} - {{ $plan->price }} ₺
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="modal_start_date" class="block text-sm font-medium text-gray-300 mb-2">Başlangıç Tarihi</label>
                            <input type="date" id="modal_start_date" name="start_date" required 
                                   class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                                   onchange="calculateEndDate()">
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="modal_end_date" class="block text-sm font-medium text-gray-300 mb-2">Bitiş Tarihi</label>
                            <input type="date" id="modal_end_date" name="end_date" required 
                                   class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="modal_status" class="block text-sm font-medium text-gray-300 mb-2">Durum</label>
                            <select id="modal_status" name="status" required 
                                    class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                                <option value="active">Aktif</option>
                                <option value="expired">Süresi Dolmuş</option>
                                <option value="cancelled">İptal Edilmiş</option>
                            </select>
                        </div>

                        <!-- Trial Ends At -->
                        <div>
                            <label for="modal_trial_ends_at" class="block text-sm font-medium text-gray-300 mb-2">Trial Bitiş Tarihi (Opsiyonel)</label>
                            <input type="datetime-local" id="modal_trial_ends_at" name="trial_ends_at" 
                                   class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <button type="button" onclick="closeCreateSubscriptionModal()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                            İptal
                        </button>
                        <button type="submit" class="px-6 py-3 bg-purple-glow hover:bg-purple-dark text-white font-medium rounded-lg transition-all duration-200 hover:shadow-lg hover:shadow-purple-glow/25">
                            Abonelik Oluştur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Subscription Modal -->
<div id="editSubscriptionModal" class="fixed inset-0 bg-black/50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-700">
                <h3 class="text-xl font-semibold text-white">Abonelik Düzenle</h3>
                <button onclick="closeEditSubscriptionModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <form action="" method="POST" id="editSubscriptionForm">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- User -->
                        <div>
                            <label for="edit_tenant_id" class="block text-sm font-medium text-gray-300 mb-2">Kullanıcı</label>
                            <select id="edit_tenant_id" name="tenant_id" required 
                                    class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                                <option value="">Kullanıcı Seçin</option>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Plan -->
                        <div>
                            <label for="edit_plan_id" class="block text-sm font-medium text-gray-300 mb-2">Plan</label>
                            <select id="edit_plan_id" name="plan_id" required 
                                    class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                                    onchange="calculateEditEndDate()">
                                <option value="">Plan Seçin</option>
                                @foreach(\App\Models\Plan::where('is_active', true)->get() as $plan)
                                    <option value="{{ $plan->id }}" 
                                            data-billing-cycle="{{ $plan->billing_cycle }}" 
                                            data-trial-days="{{ $plan->trial_days ?? 0 }}">
                                        {{ $plan->name }} - {{ $plan->price }} ₺
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="edit_start_date" class="block text-sm font-medium text-gray-300 mb-2">Başlangıç Tarihi</label>
                            <input type="date" id="edit_start_date" name="start_date" required 
                                   class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                                   onchange="calculateEditEndDate()">
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="edit_end_date" class="block text-sm font-medium text-gray-300 mb-2">Bitiş Tarihi</label>
                            <input type="date" id="edit_end_date" name="end_date" required 
                                   class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="edit_status" class="block text-sm font-medium text-gray-300 mb-2">Durum</label>
                            <select id="edit_status" name="status" required 
                                    class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                                <option value="active">Aktif</option>
                                <option value="expired">Süresi Dolmuş</option>
                                <option value="cancelled">İptal Edilmiş</option>
                            </select>
                        </div>

                        <!-- Trial Ends At -->
                        <div>
                            <label for="edit_trial_ends_at" class="block text-sm font-medium text-gray-300 mb-2">Trial Bitiş Tarihi (Opsiyonel)</label>
                            <input type="datetime-local" id="edit_trial_ends_at" name="trial_ends_at" 
                                   class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <button type="button" onclick="closeEditSubscriptionModal()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                            İptal
                        </button>
                        <button type="submit" class="px-6 py-3 bg-purple-glow hover:bg-purple-dark text-white font-medium rounded-lg transition-all duration-200 hover:shadow-lg hover:shadow-purple-glow/25">
                            Abonelik Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Approve Request Modal -->
<div id="approveRequestModal" class="fixed inset-0 bg-black/50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-md">
            <div class="flex items-center justify-between p-6 border-b border-gray-700">
                <h3 class="text-xl font-semibold text-white">Plan Talebini Onayla</h3>
                <button onclick="closeApproveModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form action="" method="POST" id="approveRequestForm">
                @csrf
                @method('POST')
                <div class="p-6">
                    <div class="mb-4">
                        <label for="approve_admin_notes" class="block text-sm font-medium text-gray-300 mb-2">Admin Notları (Opsiyonel)</label>
                        <textarea id="approve_admin_notes" name="admin_notes" rows="3" 
                                  class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                                  placeholder="Onaylama ile ilgili notlar..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeApproveModal()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                            İptal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                            Onayla
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Request Modal -->
<div id="rejectRequestModal" class="fixed inset-0 bg-black/50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-md">
            <div class="flex items-center justify-between p-6 border-b border-gray-700">
                <h3 class="text-xl font-semibold text-white">Plan Talebini Reddet</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form action="" method="POST" id="rejectRequestForm">
                @csrf
                @method('POST')
                <div class="p-6">
                    <div class="mb-4">
                        <label for="reject_admin_notes" class="block text-sm font-medium text-gray-300 mb-2">Red Sebebi <span class="text-red-400">*</span></label>
                        <textarea id="reject_admin_notes" name="admin_notes" rows="3" required
                                  class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                                  placeholder="Red sebebini açıklayın..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                            İptal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                            Reddet
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Request Modal -->
<div id="editRequestModal" class="fixed inset-0 bg-black/50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-700">
                <h3 class="text-xl font-semibold text-white">Plan Talebini Düzenle</h3>
                <button onclick="closeEditRequestModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <form action="" method="POST" id="editRequestForm">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- User Info (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Kullanıcı</label>
                            <div class="px-4 py-3 bg-gray-700/30 border border-gray-600 rounded-lg text-white">
                                <div id="edit_request_user_info"></div>
                            </div>
                        </div>

                        <!-- Plan -->
                        <div>
                            <label for="edit_request_plan_id" class="block text-sm font-medium text-gray-300 mb-2">Plan</label>
                            <select id="edit_request_plan_id" name="plan_id" required 
                                    class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                                <option value="">Plan Seçin</option>
                                @foreach(\App\Models\Plan::where('is_active', true)->get() as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }} - {{ $plan->price }} ₺</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="edit_request_status" class="block text-sm font-medium text-gray-300 mb-2">Durum</label>
                            <select id="edit_request_status" name="status" required 
                                    class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                                <option value="pending">Bekliyor</option>
                                <option value="approved">Onaylandı</option>
                                <option value="rejected">Reddedildi</option>
                            </select>
                        </div>

                        <!-- Admin Notes -->
                        <div class="md:col-span-2">
                            <label for="edit_request_admin_notes" class="block text-sm font-medium text-gray-300 mb-2">Admin Notları</label>
                            <textarea id="edit_request_admin_notes" name="admin_notes" rows="3" 
                                      class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                                      placeholder="Admin notları..."></textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <button type="button" onclick="closeEditRequestModal()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                            İptal
                        </button>
                        <button type="submit" class="px-6 py-3 bg-purple-glow hover:bg-purple-dark text-white font-medium rounded-lg transition-all duration-200 hover:shadow-lg hover:shadow-purple-glow/25">
                            Talebi Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
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

<!-- VIP Token Modal -->
<div id="vipTokenModal" class="fixed inset-0 bg-black/50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-700">
                <h3 class="text-xl font-semibold text-white">
                    <span class="flex items-center">
                        <svg class="w-6 h-6 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        VIP Token Yönetimi
                    </span>
                </h3>
                <button onclick="closeVipTokenModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <form action="{{ route('admin.subscriptions.vip-token') }}" method="POST" id="vipTokenForm">
                    @csrf
                    <div class="space-y-6">
                        <!-- User Selection -->
                        <div>
                            <label for="vip_user_select" class="block text-sm font-medium text-gray-300 mb-2">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Kullanıcı Seçin
                                </span>
                            </label>
                            <select id="vip_user_select" name="user_id" required 
                                    class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-400/20"
                                    onchange="updateVipUserInfo()">
                                <option value="">Kullanıcı Seçin</option>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}" 
                                            data-name="{{ $user->name }}" 
                                            data-email="{{ $user->email }}"
                                            data-tokens="{{ $user->usage_token ?? 0 }}"
                                            data-projects="{{ $user->max_projects ?? 0 }}"
                                            data-priority="{{ $user->priority_support ? '1' : '0' }}"
                                            data-analytics="{{ $user->advanced_analytics ? '1' : '0' }}"
                                            data-branding="{{ $user->custom_branding ? '1' : '0' }}"
                                            data-api="{{ $user->api_access ? '1' : '0' }}">
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- User Info Display -->
                        <div id="vip_user_info_display" class="bg-gray-700/30 rounded-lg p-4 border border-gray-600 hidden">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                                    <span class="text-lg font-medium text-yellow-400" id="vip_user_initial">U</span>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-white" id="vip_user_name">Kullanıcı</h4>
                                    <p class="text-gray-400" id="vip_user_email">email@example.com</p>
                                </div>
                            </div>
                        </div>

                        <!-- Usage Token -->
                        <div>
                            <label for="vip_usage_token" class="block text-sm font-medium text-gray-300 mb-2">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    Usage Token Miktarı
                                </span>
                            </label>
                            <input type="number" id="vip_usage_token" name="usage_token" min="0" step="1" required 
                                   class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-400/20"
                                   placeholder="Token miktarını girin">
                            <p class="text-xs text-gray-400 mt-1">VIP kullanıcılar için ek token miktarı</p>
                        </div>

                        <!-- Max Projects -->
                        <div>
                            <label for="vip_max_projects" class="block text-sm font-medium text-gray-300 mb-2">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    Maksimum Proje Sayısı
                                </span>
                            </label>
                            <input type="number" id="vip_max_projects" name="max_projects" min="0" step="1" required 
                                   class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-400/20"
                                   placeholder="Maksimum proje sayısını girin">
                            <p class="text-xs text-gray-400 mt-1">Kullanıcının oluşturabileceği maksimum proje sayısı</p>
                        </div>

                        <!-- Additional Features -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-3">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Ek Özellikler
                                </span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Priority Support -->
                                <div class="flex items-center space-x-3 p-3 bg-gray-700/30 rounded-lg border border-gray-600">
                                    <input type="checkbox" id="vip_priority_support" name="priority_support" value="1" 
                                           class="w-4 h-4 text-green-400 bg-gray-700 border-gray-600 rounded focus:ring-green-400 focus:ring-2">
                                    <label for="vip_priority_support" class="text-sm text-gray-300">
                                        <span class="font-medium">Öncelikli Destek</span>
                                        <p class="text-xs text-gray-400">7/24 öncelikli müşteri desteği</p>
                                    </label>
                                </div>

                                <!-- Advanced Analytics -->
                                <div class="flex items-center space-x-3 p-3 bg-gray-700/30 rounded-lg border border-gray-600">
                                    <input type="checkbox" id="vip_advanced_analytics" name="advanced_analytics" value="1" 
                                           class="w-4 h-4 text-green-400 bg-gray-700 border-gray-600 rounded focus:ring-green-400 focus:ring-2">
                                    <label for="vip_advanced_analytics" class="text-sm text-gray-300">
                                        <span class="font-medium">Gelişmiş Analitik</span>
                                        <p class="text-xs text-gray-400">Detaylı kullanım raporları</p>
                                    </label>
                                </div>

                                <!-- Custom Branding -->
                                <div class="flex items-center space-x-3 p-3 bg-gray-700/30 rounded-lg border border-gray-600">
                                    <input type="checkbox" id="vip_custom_branding" name="custom_branding" value="1" 
                                           class="w-4 h-4 text-green-400 bg-gray-700 border-gray-600 rounded focus:ring-green-400 focus:ring-2">
                                    <label for="vip_custom_branding" class="text-sm text-gray-300">
                                        <span class="font-medium">Özel Markalama</span>
                                        <p class="text-xs text-gray-400">Kendi markanızla özelleştirme</p>
                                    </label>
                                </div>

                                <!-- API Access -->
                                <div class="flex items-center space-x-3 p-3 bg-gray-700/30 rounded-lg border border-gray-600">
                                    <input type="checkbox" id="vip_api_access" name="api_access" value="1" 
                                           class="w-4 h-4 text-green-400 bg-gray-700 border-gray-600 rounded focus:ring-green-400 focus:ring-2">
                                    <label for="vip_api_access" class="text-sm text-gray-300">
                                        <span class="font-medium">API Erişimi</span>
                                        <p class="text-xs text-gray-400">Gelişmiş API entegrasyonu</p>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Notes -->
                        <div>
                            <label for="vip_admin_notes" class="block text-sm font-medium text-gray-300 mb-2">Admin Notları</label>
                            <textarea id="vip_admin_notes" name="admin_notes" rows="3" 
                                      class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                                      placeholder="VIP token ekleme ile ilgili notlar..."></textarea>
                        </div>
                    </div>

                    <!-- Hidden user ID -->
                    <input type="hidden" id="vip_user_id" name="user_id" value="">

                    <!-- Submit Button -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <button type="button" onclick="closeVipTokenModal()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                            İptal
                        </button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-medium rounded-lg transition-all duration-200 hover:shadow-lg hover:shadow-yellow-500/25">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                VIP Token Ekle
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openCreateSubscriptionModal() {
    document.getElementById('createSubscriptionModal').classList.remove('hidden');
    
    // Set start date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('modal_start_date').value = today;
    
    // Clear end date initially
    document.getElementById('modal_end_date').value = '';
}

function closeCreateSubscriptionModal() {
    document.getElementById('createSubscriptionModal').classList.add('hidden');
    // Reset form
    document.getElementById('createSubscriptionForm').reset();
}

function calculateEndDate() {
    const planSelect = document.getElementById('modal_plan_id');
    const startDateInput = document.getElementById('modal_start_date');
    const endDateInput = document.getElementById('modal_end_date');
    
    if (!planSelect.value || !startDateInput.value) {
        return;
    }
    
    const selectedOption = planSelect.options[planSelect.selectedIndex];
    const billingCycle = selectedOption.dataset.billingCycle;
    const trialDays = parseInt(selectedOption.dataset.trialDays) || 0;
    
    // Get start date
    const startDate = new Date(startDateInput.value);
    
    // Calculate end date based on billing cycle
    let endDate = new Date(startDate);
    
    if (billingCycle === 'yearly') {
        endDate.setFullYear(endDate.getFullYear() + 1);
    } else if (billingCycle === 'monthly') {
        endDate.setMonth(endDate.getMonth() + 1);
    } else if (billingCycle === 'trial') {
        // For trial plans, use trial days or default to 30 days
        endDate.setDate(endDate.getDate() + (trialDays || 30));
    } else {
        // Default to monthly
        endDate.setMonth(endDate.getMonth() + 1);
    }
    
    // Format date for input (YYYY-MM-DD)
    const endDateString = endDate.toISOString().split('T')[0];
    endDateInput.value = endDateString;
}

function calculateEditEndDate() {
    const planSelect = document.getElementById('edit_plan_id');
    const startDateInput = document.getElementById('edit_start_date');
    const endDateInput = document.getElementById('edit_end_date');
    
    if (!planSelect.value || !startDateInput.value) {
        return;
    }
    
    const selectedOption = planSelect.options[planSelect.selectedIndex];
    const billingCycle = selectedOption.dataset.billingCycle;
    const trialDays = parseInt(selectedOption.dataset.trialDays) || 0;
    
    // Get start date
    const startDate = new Date(startDateInput.value);
    
    // Calculate end date based on billing cycle
    let endDate = new Date(startDate);
    
    if (billingCycle === 'yearly') {
        endDate.setFullYear(endDate.getFullYear() + 1);
    } else if (billingCycle === 'monthly') {
        endDate.setMonth(endDate.getMonth() + 1);
    } else if (billingCycle === 'trial') {
        // For trial plans, use trial days or default to 30 days
        endDate.setDate(endDate.getDate() + (trialDays || 30));
    } else {
        // Default to monthly
        endDate.setMonth(endDate.getMonth() + 1);
    }
    
    // Format date for input (YYYY-MM-DD)
    const endDateString = endDate.toISOString().split('T')[0];
    endDateInput.value = endDateString;
}

function openEditSubscriptionModal(id, userName, userEmail, planId, planName, startDate, endDate, status, trialEndsAt) {
    // Set form action
    document.getElementById('editSubscriptionForm').action = `/admin/subscriptions/${id}`;
    
    // Populate form fields
    document.getElementById('edit_start_date').value = startDate || '';
    document.getElementById('edit_end_date').value = endDate || '';
    document.getElementById('edit_status').value = status || 'active';
    document.getElementById('edit_trial_ends_at').value = trialEndsAt || '';
    
    // Find and select the correct user
    const userSelect = document.getElementById('edit_tenant_id');
    for (let option of userSelect.options) {
        if (option.value && option.text.includes(userName) && option.text.includes(userEmail)) {
            option.selected = true;
            break;
        }
    }
    
    // Find and select the correct plan
    const planSelect = document.getElementById('edit_plan_id');
    for (let option of planSelect.options) {
        if (option.value && option.text.includes(planName)) {
            option.selected = true;
            break;
        }
    }
    
    // Show modal
    document.getElementById('editSubscriptionModal').classList.remove('hidden');
}

function closeEditSubscriptionModal() {
    document.getElementById('editSubscriptionModal').classList.add('hidden');
    // Reset form
    document.getElementById('editSubscriptionForm').reset();
}

// Close modal when clicking outside
document.addEventListener('click', (e) => {
    if (e.target.id === 'createSubscriptionModal') {
        closeCreateSubscriptionModal();
    }
    if (e.target.id === 'editSubscriptionModal') {
        closeEditSubscriptionModal();
    }
    if (e.target.id === 'editRequestModal') {
        closeEditRequestModal();
    }
});

// Tab switching
function switchTab(tabName) {
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
    document.getElementById(tabName + '-content').classList.remove('hidden');
    
    // Add active class to selected tab
    const activeTab = document.getElementById(tabName + '-tab');
    activeTab.classList.add('active', 'border-purple-glow', 'text-purple-glow');
    activeTab.classList.remove('border-transparent', 'text-gray-400');
}

// Plan request modals
function openApproveModal(requestId) {
    const form = document.getElementById('approveRequestForm');
    form.action = `/admin/subscriptions/requests/${requestId}/approve`;
    document.getElementById('approveRequestModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveRequestModal').classList.add('hidden');
    document.getElementById('approveRequestForm').reset();
}

function openRejectModal(requestId) {
    const form = document.getElementById('rejectRequestForm');
    form.action = `/admin/subscriptions/requests/${requestId}/reject`;
    document.getElementById('rejectRequestModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectRequestModal').classList.add('hidden');
    document.getElementById('rejectRequestForm').reset();
}

// Edit Request Modal
function openEditRequestModal(requestId, userName, userEmail, planId, planName, status, adminNotes) {
    // Set form action
    document.getElementById('editRequestForm').action = `/admin/subscriptions/requests/${requestId}`;
    
    // Set user info (read-only)
    document.getElementById('edit_request_user_info').innerHTML = `
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center">
                <span class="text-sm font-medium text-white">${userName.charAt(0).toUpperCase()}</span>
            </div>
            <div>
                <div class="font-medium text-white">${userName}</div>
                <div class="text-sm text-gray-400">${userEmail}</div>
            </div>
        </div>
    `;
    
    // Set plan
    document.getElementById('edit_request_plan_id').value = planId;
    
    // Set status
    document.getElementById('edit_request_status').value = status;
    
    // Set admin notes
    document.getElementById('edit_request_admin_notes').value = adminNotes || '';
    
    // Show modal
    document.getElementById('editRequestModal').classList.remove('hidden');
}

function closeEditRequestModal() {
    document.getElementById('editRequestModal').classList.add('hidden');
    // Reset form
    document.getElementById('editRequestForm').reset();
}

// Plan History Modal
function openPlanHistoryModal(userId) {
    document.getElementById('planHistoryModal').classList.remove('hidden');
    
    // Loading state
    document.getElementById('planHistoryContent').innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-glow"></div>
            <span class="ml-3 text-gray-400">Yükleniyor...</span>
        </div>
    `;
    
    // Fetch user plan history
    fetch(`/admin/subscriptions/user/${userId}/history`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderPlanHistory(data.data);
            } else {
                document.getElementById('planHistoryContent').innerHTML = `
                    <div class="text-center py-8">
                        <div class="text-red-400 mb-4">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-400">Plan geçmişi yüklenirken hata oluştu.</p>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-400">Plan geçmişi yüklenirken hata oluştu.</p>
                </div>
            `;
        });
}

function closePlanHistoryModal() {
    document.getElementById('planHistoryModal').classList.add('hidden');
}

function renderPlanHistory(data) {
    const { user, subscriptions } = data;
    
    let html = `
        <div class="mb-6">
            <div class="flex items-center space-x-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-gray-600 flex items-center justify-center">
                    <span class="text-lg font-medium text-white">${user.name.charAt(0).toUpperCase()}</span>
                </div>
                <div>
                    <h4 class="text-xl font-semibold text-white">${user.name}</h4>
                    <p class="text-gray-400">${user.email}</p>
                </div>
            </div>
        </div>
        
        <div class="space-y-4">
    `;
    
    if (subscriptions.length === 0) {
        html += `
            <div class="text-center py-8">
                <div class="text-gray-400 mb-4">
                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-gray-400">Bu kullanıcının henüz aboneliği bulunmuyor.</p>
            </div>
        `;
    } else {
        subscriptions.forEach((subscription, index) => {
            const statusColors = {
                'active': 'bg-green-900/50 text-green-300 border-green-600/50',
                'expired': 'bg-red-900/50 text-red-300 border-red-600/50',
                'cancelled': 'bg-gray-900/50 text-gray-300 border-gray-600/50'
            };
            
            html += `
                <div class="bg-gray-700/50 rounded-lg p-4 border border-gray-600">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-purple-600 flex items-center justify-center">
                                <span class="text-sm font-medium text-white">${index + 1}</span>
                            </div>
                            <div>
                                <h5 class="font-semibold text-white">${subscription.plan_name}</h5>
                                <p class="text-sm text-gray-400">${subscription.plan_price}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${statusColors[subscription.status]}">
                            ${subscription.status_text}
                        </span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-400">Başlangıç:</span>
                            <span class="text-white ml-2">${subscription.start_date}</span>
                        </div>
                        <div>
                            <span class="text-gray-400">Bitiş:</span>
                            <span class="text-white ml-2">${subscription.end_date}</span>
                        </div>
                        <div>
                            <span class="text-gray-400">Oluşturulma:</span>
                            <span class="text-white ml-2">${subscription.created_at}</span>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    html += `</div>`;
    
    document.getElementById('planHistoryContent').innerHTML = html;
}

// VIP Token Modal Functions
function openVipTokenModal(userId = null, userName = null, currentTokens = 0, maxProjects = 0) {
    if (userId && userName) {
        // Edit mode - populate with existing data
        document.getElementById('vip_user_select').value = userId;
        updateVipUserInfo();
        
        // Update modal title
        document.querySelector('#vipTokenModal h3 span').innerHTML = `
            <svg class="w-6 h-6 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            VIP Token Düzenle - ${userName}
        `;
        
        // Update submit button
        document.querySelector('#vipTokenForm button[type="submit"] span').innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            VIP Token Güncelle
        `;
    } else {
        // Create mode - reset form
        document.getElementById('vip_user_select').value = '';
        document.getElementById('vip_user_info_display').classList.add('hidden');
        document.getElementById('vip_usage_token').value = '';
        document.getElementById('vip_max_projects').value = '';
        
        // Reset modal title
        document.querySelector('#vipTokenModal h3 span').innerHTML = `
            <svg class="w-6 h-6 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            VIP Token Yönetimi
        `;
        
        // Reset submit button
        document.querySelector('#vipTokenForm button[type="submit"] span').innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            VIP Token Ekle
        `;
    }
    
    // Reset checkboxes
    document.querySelectorAll('#vipTokenForm input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Reset admin notes
    document.getElementById('vip_admin_notes').value = '';
    
    document.getElementById('vipTokenModal').classList.remove('hidden');
}

function updateVipUserInfo() {
    const select = document.getElementById('vip_user_select');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value) {
        // Show user info display
        document.getElementById('vip_user_info_display').classList.remove('hidden');
        
        // Update user info
        document.getElementById('vip_user_name').textContent = selectedOption.dataset.name;
        document.getElementById('vip_user_email').textContent = selectedOption.dataset.email;
        document.getElementById('vip_user_initial').textContent = selectedOption.dataset.name.charAt(0).toUpperCase();
        
        // Update form fields with existing data
        document.getElementById('vip_usage_token').value = selectedOption.dataset.tokens || 0;
        document.getElementById('vip_max_projects').value = selectedOption.dataset.projects || 0;
        
        // Update checkboxes
        document.getElementById('vip_priority_support').checked = selectedOption.dataset.priority === '1';
        document.getElementById('vip_advanced_analytics').checked = selectedOption.dataset.analytics === '1';
        document.getElementById('vip_custom_branding').checked = selectedOption.dataset.branding === '1';
        document.getElementById('vip_api_access').checked = selectedOption.dataset.api === '1';
    } else {
        // Hide user info display
        document.getElementById('vip_user_info_display').classList.add('hidden');
    }
}

function closeVipTokenModal() {
    document.getElementById('vipTokenModal').classList.add('hidden');
    document.getElementById('vipTokenForm').reset();
}

// Close modal when clicking outside
document.addEventListener('click', (e) => {
    if (e.target.id === 'createSubscriptionModal') {
        closeCreateSubscriptionModal();
    }
    if (e.target.id === 'editSubscriptionModal') {
        closeEditSubscriptionModal();
    }
    if (e.target.id === 'editRequestModal') {
        closeEditRequestModal();
    }
    if (e.target.id === 'vipTokenModal') {
        closeVipTokenModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeCreateSubscriptionModal();
        closeEditSubscriptionModal();
        closeEditRequestModal();
        closeApproveModal();
        closeRejectModal();
        closePlanHistoryModal();
        closeVipTokenModal();
    }
});
</script>
@endsection
