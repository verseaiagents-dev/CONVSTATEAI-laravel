@extends('layouts.admin')

@section('title', 'Planlar - Admin Panel')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold gradient-text">Planlar</h1>
            <p class="mt-2 text-gray-400">Abonelik planlarını yönetin</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button onclick="openCreatePlanModal()" class="inline-flex items-center px-4 py-2 bg-purple-glow hover:bg-purple-dark text-white font-medium rounded-lg transition-all duration-200 hover:shadow-lg hover:shadow-purple-glow/25">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Plan Ekle
            </button>
        </div>
    </div>

    <!-- Plans List -->
    <div class="glass-effect rounded-xl border border-gray-700">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Plan Adı</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Fiyat</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Döngü</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Usage Tokens</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Kalan Tokens</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Deneme Süresi</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Durum</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Abonelik Sayısı</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($plans as $plan)
                        <tr class="hover:bg-gray-800/50 transition-colors">
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 rounded-full {{ $plan->is_active ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                    <span class="font-medium text-white">{{ $plan->name }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-gray-300">
                                {{ $plan->formatted_price }}
                            </td>
                            <td class="py-4 px-4 text-gray-300">
                                {{ $plan->billing_cycle_text }}
                            </td>
                            <td class="py-4 px-4 text-gray-300">
                                @if($plan->usage_tokens == -1)
                                    <span class="text-green-400 font-medium">Sınırsız</span>
                                @else
                                    {{ number_format($plan->usage_tokens) }}
                                @endif
                            </td>
                            <td class="py-4 px-4 text-gray-300">
                                @php
                                    $totalRemainingTokens = $plan->usageTokens()->sum('tokens_remaining');
                                @endphp
                                @if($plan->usage_tokens == -1)
                                    <span class="text-green-400 font-medium">Sınırsız</span>
                                @else
                                    <span class="text-blue-400 font-medium">{{ number_format($totalRemainingTokens) }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-gray-300">
                                @if($plan->trial_days)
                                    {{ $plan->trial_days }} gün
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $plan->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-gray-300">
                                {{ $plan->subscriptions->count() }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-2">
                                    <button onclick="openEditPlanModal({{ $plan->id }})" class="text-blue-400 hover:text-blue-300 transition-colors" title="Plan Düzenle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="openTokenManagementModal({{ $plan->id }})" class="text-green-400 hover:text-green-300 transition-colors" title="Token Yönetimi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </button>
                                    <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="inline" onsubmit="return confirm('Bu planı silmek istediğinizden emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 transition-colors" title="Plan Sil">
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
                            <td colspan="9" class="py-8 px-4 text-center text-gray-400">
                                Henüz plan bulunmuyor
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Plan Modal -->
<div id="createPlanModal" class="fixed inset-0 bg-black/50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-700">
                <h3 class="text-xl font-semibold text-white">Yeni Plan Oluştur</h3>
                <button onclick="closeCreatePlanModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <form action="{{ route('admin.plans.store') }}" method="POST" id="createPlanForm">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Plan Name -->
                        <div>
                            <label for="modal_name" class="block text-sm font-medium text-gray-300 mb-2">Plan Adı</label>
                            <input type="text" id="modal_name" name="name" required 
                                   class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                                   placeholder="Örn: Pro Plan">
                        </div>

                        <!-- Price -->
                        <div>
                            <label for="modal_price" class="block text-sm font-medium text-gray-300 mb-2">Fiyat</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400">$</span>
                                <input type="number" id="modal_price" name="price" step="0.01" min="0" required 
                                       class="form-input w-full pl-8 pr-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                                       placeholder="29.99">
                            </div>
                        </div>

                        <!-- Billing Cycle -->
                        <div>
                            <label for="modal_billing_cycle" class="block text-sm font-medium text-gray-300 mb-2">Faturalama Döngüsü</label>
                            <select id="modal_billing_cycle" name="billing_cycle" required 
                                    class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                                <option value="">Seçiniz</option>
                                <option value="monthly">Aylık</option>
                                <option value="yearly">Yıllık</option>
                                <option value="trial">Deneme</option>
                            </select>
                        </div>

                        <!-- Trial Days -->
                        <div id="modal_trial_days_field" style="display: none;">
                            <label for="modal_trial_days" class="block text-sm font-medium text-gray-300 mb-2">Deneme Süresi (Gün)</label>
                            <input type="number" id="modal_trial_days" name="trial_days" min="1" max="365"
                                   class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                                   placeholder="7">
                        </div>

                        <!-- Is Active -->
                        <div>
                            <label for="modal_is_active" class="block text-sm font-medium text-gray-300 mb-2">Durum</label>
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" id="modal_is_active" name="is_active" value="1" checked
                                       class="form-checkbox w-5 h-5 text-purple-glow bg-gray-700 border-gray-600 rounded focus:ring-purple-glow focus:ring-2">
                                <span class="text-gray-300">Aktif</span>
                            </div>
                        </div>

                        <!-- Usage Tokens -->
                        <div>
                            <label for="modal_usage_tokens" class="block text-sm font-medium text-gray-300 mb-2">Usage Tokens</label>
                            <input type="number" id="modal_usage_tokens" name="usage_tokens" required min="-1" 
                                   class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                                   placeholder="1000 (-1 = sınırsız)">
                        </div>

                        <!-- Token Reset Period -->
                        <div>
                            <label for="modal_token_reset_period" class="block text-sm font-medium text-gray-300 mb-2">Token Reset Periyodu</label>
                            <select id="modal_token_reset_period" name="token_reset_period" required 
                                    class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                                <option value="monthly">Aylık</option>
                                <option value="yearly">Yıllık</option>
                            </select>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Plan Özellikleri</label>
                        <div class="space-y-4" id="modalFeaturesContainer">
                            
                            <!-- Ürün Limiti -->
                            <div class="flex items-center space-x-3">
                                <label class="w-48 text-sm text-gray-300">Maksimum Ürün Sayısı:</label>
                                <input type="number" name="features[max_products]" placeholder="Örn: 100" min="0"
                                       class="form-input flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                            </div>
                            
                            <!-- Mesaj Limiti -->
                            <div class="flex items-center space-x-3">
                                <label class="w-48 text-sm text-gray-300">Maksimum Mesaj Sayısı:</label>
                                <input type="number" name="features[max_messages]" placeholder="Örn: 10000" min="0"
                                       class="form-input flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                            </div>
                            
                            <!-- Session Limiti -->
                            <div class="flex items-center space-x-3">
                                <label class="w-48 text-sm text-gray-300">Maksimum Session Sayısı:</label>
                                <input type="number" name="features[max_sessions]" placeholder="Örn: 1000" min="0"
                                       class="form-input flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                            </div>
                            
                            <!-- AI Reklam Önerileri -->
                            <div class="flex items-center space-x-3">
                                <label class="w-48 text-sm text-gray-300">AI Reklam Önerileri:</label>
                                <input type="number" name="features[ai_ad_suggestions]" placeholder="Örn: 10" min="0"
                                       class="form-input flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                            </div>
                            
                            <!-- Widget Kampanyalar -->
                            <div class="flex items-center space-x-3">
                                <label class="w-48 text-sm text-gray-300">Widget Kampanyalar:</label>
                                <input type="number" name="features[widget_campaigns]" placeholder="Örn: 5" min="0"
                                       class="form-input flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                            </div>
                            
                            <!-- Widget SSS -->
                            <div class="flex items-center space-x-3">
                                <label class="w-48 text-sm text-gray-300">Widget SSS:</label>
                                <input type="number" name="features[widget_faq]" placeholder="Örn: 5" min="0"
                                       class="form-input flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                            </div>
                            
                            <!-- AI Kişiselleştirilmiş Kampanya -->
                            <div class="flex items-center space-x-3">
                                <label class="w-48 text-sm text-gray-300">AI Kişiselleştirilmiş Kampanya:</label>
                                <input type="number" name="features[ai_personalized_campaigns]" placeholder="Örn: 50" min="0"
                                       class="form-input flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                            </div>
                            
                            <!-- Destek Türü -->
                            <div class="flex items-center space-x-3">
                                <label class="w-48 text-sm text-gray-300">Destek Türü:</label>
                                <input type="text" name="features[support]" placeholder="Örn: Email Support" 
                                       class="form-input flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <button type="button" onclick="closeCreatePlanModal()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                            İptal
                        </button>
                        <button type="submit" class="px-6 py-3 bg-purple-glow hover:bg-purple-dark text-white font-medium rounded-lg transition-all duration-200 hover:shadow-lg hover:shadow-purple-glow/25">
                            Plan Oluştur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Plan Modal -->
<div id="editPlanModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 rounded-xl border border-gray-700 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold gradient-text">Plan Düzenle</h2>
                <button onclick="closeEditPlanModal()" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="editPlanForm" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Plan Name -->
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-300 mb-2">Plan Adı</label>
                        <input type="text" id="edit_name" name="name" required 
                               class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                               placeholder="Örn: Pro Plan">
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="edit_price" class="block text-sm font-medium text-gray-300 mb-2">Fiyat</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-400">₺</span>
                            <input type="number" id="edit_price" name="price" step="0.01" min="0" required 
                                   class="form-input w-full pl-8 pr-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                                   placeholder="1500">
                        </div>
                    </div>

                    <!-- Billing Cycle -->
                    <div>
                        <label for="edit_billing_cycle" class="block text-sm font-medium text-gray-300 mb-2">Faturalama Döngüsü</label>
                        <select id="edit_billing_cycle" name="billing_cycle" required 
                                class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                            <option value="">Seçiniz</option>
                            <option value="monthly">Aylık</option>
                            <option value="yearly">Yıllık</option>
                            <option value="trial">Deneme</option>
                        </select>
                    </div>

                    <!-- Trial Days -->
                    <div id="edit_trial_days_field" style="display: none;">
                        <label for="edit_trial_days" class="block text-sm font-medium text-gray-300 mb-2">Deneme Süresi (Gün)</label>
                        <input type="number" id="edit_trial_days" name="trial_days" min="1" max="365"
                               class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                               placeholder="7">
                    </div>

                    <!-- Is Active -->
                    <div>
                        <label for="edit_is_active" class="block text-sm font-medium text-gray-300 mb-2">Durum</label>
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="edit_is_active" name="is_active" value="1"
                                   class="form-checkbox w-5 h-5 text-purple-glow bg-gray-700 border-gray-600 rounded focus:ring-purple-glow focus:ring-2">
                            <span class="text-gray-300">Aktif</span>
                        </div>
                    </div>

                    <!-- Usage Tokens -->
                    <div>
                        <label for="edit_usage_tokens" class="block text-sm font-medium text-gray-300 mb-2">Usage Tokens</label>
                        <input type="number" id="edit_usage_tokens" name="usage_tokens" required min="-1" 
                               class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                               placeholder="1000 (-1 = sınırsız)">
                    </div>

                    <!-- Token Reset Period -->
                    <div>
                        <label for="edit_token_reset_period" class="block text-sm font-medium text-gray-300 mb-2">Token Reset Periyodu</label>
                        <select id="edit_token_reset_period" name="token_reset_period" required 
                                class="form-input w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                            <option value="monthly">Aylık</option>
                            <option value="yearly">Yıllık</option>
                        </select>
                    </div>
                </div>

                <!-- Features -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Plan Özellikleri</label>
                    <div class="space-y-4" id="editFeaturesContainer">
                        
                        <!-- Ürün Limiti -->
                        <div class="flex items-center space-x-3">
                            <label class="w-48 text-sm text-gray-300">Maksimum Ürün Sayısı:</label>
                            <input type="number" name="features[max_products]" placeholder="Örn: 100" min="0"
                                   class="form-input flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                        
                        <!-- Mesaj Limiti -->
                        <div class="flex items-center space-x-3">
                            <label class="w-48 text-sm text-gray-300">Maksimum Mesaj Sayısı:</label>
                            <input type="number" name="features[max_messages]" placeholder="Örn: 10000" min="0"
                                   class="form-input flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                        
                        <!-- Session Limiti -->
                        <div class="flex items-center space-x-3">
                            <label class="w-48 text-sm text-gray-300">Maksimum Session Sayısı:</label>
                            <input type="number" name="features[max_sessions]" placeholder="Örn: 1000" min="0"
                                   class="form-input flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                        
                        <!-- AI Reklam Önerileri -->
                        <div class="flex items-center space-x-3">
                            <label class="w-48 text-sm text-gray-300">AI Reklam Önerileri:</label>
                            <input type="number" name="features[ai_ad_suggestions]" placeholder="Örn: 10" min="0"
                                   class="form-input flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                        
                        <!-- Widget Kampanyalar -->
                        <div class="flex items-center space-x-3">
                            <label class="w-48 text-sm text-gray-300">Widget Kampanyalar:</label>
                            <input type="number" name="features[widget_campaigns]" placeholder="Örn: 5" min="0"
                                   class="form-input flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                        
                        <!-- Widget SSS -->
                        <div class="flex items-center space-x-3">
                            <label class="w-48 text-sm text-gray-300">Widget SSS:</label>
                            <input type="number" name="features[widget_faq]" placeholder="Örn: 5" min="0"
                                   class="form-input flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                        
                        <!-- AI Kişiselleştirilmiş Kampanya -->
                        <div class="flex items-center space-x-3">
                            <label class="w-48 text-sm text-gray-300">AI Kişiselleştirilmiş Kampanya:</label>
                            <input type="number" name="features[ai_personalized_campaigns]" placeholder="Örn: 50" min="0"
                                   class="form-input flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                        
                        <!-- Destek Türü -->
                        <div class="flex items-center space-x-3">
                            <label class="w-48 text-sm text-gray-300">Destek Türü:</label>
                            <input type="text" name="features[support]" placeholder="Örn: Email Support" 
                                   class="form-input flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" onclick="closeEditPlanModal()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                        İptal
                    </button>
                    <button type="submit" class="px-6 py-3 bg-purple-glow hover:bg-purple-dark text-white font-medium rounded-lg transition-all duration-200 hover:shadow-lg hover:shadow-purple-glow/25">
                        Planı Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Token Management Modal -->
<div id="tokenManagementModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 rounded-xl border border-gray-700 w-full max-w-6xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold gradient-text">Token Yönetimi</h2>
                <button onclick="closeTokenManagementModal()" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="tokenManagementContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function openCreatePlanModal() {
    document.getElementById('createPlanModal').classList.remove('hidden');
}

function closeCreatePlanModal() {
    document.getElementById('createPlanModal').classList.add('hidden');
    // Reset form
    document.getElementById('createPlanForm').reset();
}

// Billing cycle değiştiğinde trial days alanını göster/gizle
document.getElementById('modal_billing_cycle').addEventListener('change', function() {
    const trialDaysField = document.getElementById('modal_trial_days_field');
    if (this.value === 'trial') {
        trialDaysField.style.display = 'block';
        document.getElementById('modal_trial_days').required = true;
    } else {
        trialDaysField.style.display = 'none';
        document.getElementById('modal_trial_days').required = false;
    }
});

// Close modal when clicking outside
document.addEventListener('click', (e) => {
    if (e.target.id === 'createPlanModal') {
        closeCreatePlanModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeCreatePlanModal();
        closeEditPlanModal();
    }
});

// Edit Plan Modal Functions
function openEditPlanModal(planId) {
    // Plan verilerini AJAX ile yükle
    fetch(`/admin/plans/${planId}/edit`)
        .then(response => response.json())
        .then(data => {
            // Form alanlarını doldur
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_price').value = data.price;
            document.getElementById('edit_billing_cycle').value = data.billing_cycle;
            document.getElementById('edit_trial_days').value = data.trial_days || '';
            document.getElementById('edit_is_active').checked = data.is_active;
            document.getElementById('edit_usage_tokens').value = data.usage_tokens || '';
            document.getElementById('edit_token_reset_period').value = data.token_reset_period || 'monthly';
            
            // Features alanlarını doldur
            if (data.features) {
                Object.keys(data.features).forEach(key => {
                    const input = document.querySelector(`input[name="features[${key}]"]`);
                    if (input) {
                        input.value = data.features[key];
                    }
                });
            }
            
            // Form action'ını ayarla
            document.getElementById('editPlanForm').action = `/admin/plans/${planId}`;
            
            // Modal'ı göster
            document.getElementById('editPlanModal').classList.remove('hidden');
            
            // Trial days alanını göster/gizle (modal açıldıktan sonra)
            setTimeout(() => {
                toggleEditTrialDaysField();
            }, 100);
        })
        .catch(error => {
            console.error('Error loading plan data:', error);
            alert('Plan verileri yüklenirken hata oluştu.');
        });
}

function closeEditPlanModal() {
    document.getElementById('editPlanModal').classList.add('hidden');
    // Reset form
    document.getElementById('editPlanForm').reset();
}

function toggleEditTrialDaysField() {
    const billingCycle = document.getElementById('edit_billing_cycle').value;
    const trialDaysField = document.getElementById('edit_trial_days_field');
    const trialDaysInput = document.getElementById('edit_trial_days');
    
    if (billingCycle === 'trial') {
        trialDaysField.style.display = 'block';
        trialDaysInput.required = true;
    } else {
        trialDaysField.style.display = 'none';
        trialDaysInput.required = false;
    }
}

// Edit modal billing cycle change event
document.getElementById('edit_billing_cycle').addEventListener('change', toggleEditTrialDaysField);

// Close edit modal when clicking outside
document.addEventListener('click', (e) => {
    if (e.target.id === 'editPlanModal') {
        closeEditPlanModal();
    }
    if (e.target.id === 'tokenManagementModal') {
        closeTokenManagementModal();
    }
});

// Token Management Functions
function openTokenManagementModal(planId) {
    // Plan token bilgilerini yükle
    fetch(`/admin/usage-tokens/get-plan-tokens?plan_id=${planId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayTokenManagementContent(data.data);
                document.getElementById('tokenManagementModal').classList.remove('hidden');
            } else {
                alert('Token bilgileri yüklenirken hata oluştu: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error loading token data:', error);
            alert('Token bilgileri yüklenirken hata oluştu.');
        });
}

function closeTokenManagementModal() {
    document.getElementById('tokenManagementModal').classList.add('hidden');
    document.getElementById('tokenManagementContent').innerHTML = '';
}

function displayTokenManagementContent(data) {
    const content = `
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-white mb-2">${data.plan_name}</h3>
            <p class="text-gray-400">Plan Usage Tokens: ${data.plan_usage_tokens === -1 ? 'Sınırsız' : data.plan_usage_tokens.toLocaleString()}</p>
            <p class="text-gray-400">Toplam Kalan Tokens: ${data.total_remaining.toLocaleString()}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Kullanıcı</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Toplam Tokens</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Kalan Tokens</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Kullanılan</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">Kullanım %</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-300">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    ${data.users.map(user => `
                        <tr class="hover:bg-gray-800/50 transition-colors">
                            <td class="py-4 px-4">
                                <div>
                                    <div class="font-medium text-white">${user.user_name}</div>
                                    <div class="text-sm text-gray-400">${user.user_email}</div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-gray-300">${user.tokens_total.toLocaleString()}</td>
                            <td class="py-4 px-4">
                                <span class="text-blue-400 font-medium">${user.tokens_remaining.toLocaleString()}</span>
                            </td>
                            <td class="py-4 px-4 text-gray-300">${user.tokens_used.toLocaleString()}</td>
                            <td class="py-4 px-4">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-700 rounded-full h-2 mr-2">
                                        <div class="bg-purple-glow h-2 rounded-full" style="width: ${user.usage_percentage}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-300">${user.usage_percentage.toFixed(1)}%</span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-2">
                                    <button onclick="openEditUserTokensModal(${user.user_id}, '${user.user_name}', ${user.tokens_total}, ${user.tokens_remaining})" 
                                            class="text-blue-400 hover:text-blue-300 transition-colors" title="Token Düzenle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="openAddTokensModal(${user.user_id}, '${user.user_name}')" 
                                            class="text-green-400 hover:text-green-300 transition-colors" title="Token Ekle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
    
    document.getElementById('tokenManagementContent').innerHTML = content;
}

function openEditUserTokensModal(userId, userName, tokensTotal, tokensRemaining) {
    const newTotal = prompt(`${userName} için yeni toplam token sayısı:`, tokensTotal);
    if (newTotal === null) return;
    
    const newRemaining = prompt(`${userName} için yeni kalan token sayısı:`, tokensRemaining);
    if (newRemaining === null) return;
    
    if (isNaN(newTotal) || isNaN(newRemaining) || newTotal < 0 || newRemaining < 0) {
        alert('Geçerli sayılar giriniz!');
        return;
    }
    
    if (parseInt(newRemaining) > parseInt(newTotal)) {
        alert('Kalan token sayısı toplam token sayısından fazla olamaz!');
        return;
    }
    
    // API'ye gönder
    fetch('/admin/usage-tokens/update-user-tokens', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            user_id: userId,
            tokens_total: parseInt(newTotal),
            tokens_remaining: parseInt(newRemaining)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Token bilgileri başarıyla güncellendi!');
            // Modal'ı yenile
            const currentPlanId = document.querySelector('[onclick*="openTokenManagementModal"]').onclick.toString().match(/\d+/)[0];
            openTokenManagementModal(currentPlanId);
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Token güncellenirken hata oluştu!');
    });
}

function openAddTokensModal(userId, userName) {
    const amount = prompt(`${userName} için eklenecek token sayısı:`, '100');
    if (amount === null) return;
    
    if (isNaN(amount) || amount <= 0) {
        alert('Geçerli bir sayı giriniz!');
        return;
    }
    
    // API'ye gönder
    fetch('/admin/usage-tokens/add-tokens', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            user_id: userId,
            amount: parseInt(amount)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Token başarıyla eklendi!');
            // Modal'ı yenile
            const currentPlanId = document.querySelector('[onclick*="openTokenManagementModal"]').onclick.toString().match(/\d+/)[0];
            openTokenManagementModal(currentPlanId);
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Token eklenirken hata oluştu!');
    });
}
</script>
@endsection
