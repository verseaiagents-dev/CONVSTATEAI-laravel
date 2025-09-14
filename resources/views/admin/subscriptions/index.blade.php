@extends('layouts.admin')

@section('title', 'Abonelikler - Admin Panel')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold gradient-text">Abonelikler</h1>
            <p class="mt-2 text-gray-400">Kullanıcı aboneliklerini yönetin</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button onclick="openCreateSubscriptionModal()" class="inline-flex items-center px-4 py-2 bg-purple-glow hover:bg-purple-dark text-white font-medium rounded-lg transition-all duration-200 hover:shadow-lg hover:shadow-purple-glow/25">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Abonelik Ekle
            </button>
        </div>
    </div>

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
                                <div class="flex items-center space-x-2">
                                    <button onclick="openEditSubscriptionModal({{ $subscription->id }}, '{{ addslashes($subscription->user->name) }}', '{{ addslashes($subscription->user->email) }}', {{ $subscription->plan->id }}, '{{ addslashes($subscription->plan->name) }}', '{{ $subscription->start_date->format('Y-m-d') }}', '{{ $subscription->end_date->format('Y-m-d') }}', '{{ $subscription->status }}', '{{ $subscription->trial_ends_at ? $subscription->trial_ends_at->format('Y-m-d\TH:i') : '' }}')" class="text-blue-400 hover:text-blue-300 transition-colors">
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
                            <td colspan="6" class="py-8 px-4 text-center text-gray-400">
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
                                    class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                                <option value="">Plan Seçin</option>
                                @foreach(\App\Models\Plan::where('is_active', true)->get() as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }} - {{ $plan->price }} ₺</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="modal_start_date" class="block text-sm font-medium text-gray-300 mb-2">Başlangıç Tarihi</label>
                            <input type="date" id="modal_start_date" name="start_date" required 
                                   class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
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
                                    class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                                <option value="">Plan Seçin</option>
                                @foreach(\App\Models\Plan::where('is_active', true)->get() as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }} - {{ $plan->price }} ₺</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="edit_start_date" class="block text-sm font-medium text-gray-300 mb-2">Başlangıç Tarihi</label>
                            <input type="date" id="edit_start_date" name="start_date" required 
                                   class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
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

<script>
function openCreateSubscriptionModal() {
    document.getElementById('createSubscriptionModal').classList.remove('hidden');
}

function closeCreateSubscriptionModal() {
    document.getElementById('createSubscriptionModal').classList.add('hidden');
    // Reset form
    document.getElementById('createSubscriptionForm').reset();
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
});

// Close modal on escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeCreateSubscriptionModal();
        closeEditSubscriptionModal();
    }
});
</script>
@endsection
