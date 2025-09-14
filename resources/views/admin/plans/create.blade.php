@extends('layouts.admin')

@section('title', 'Plan Ekle - Admin Panel')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold gradient-text">Plan Ekle</h1>
            <p class="mt-2 text-gray-400">Yeni abonelik planı oluşturun</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-500 text-white font-medium rounded-lg transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                </svg>
                Geri Dön
            </a>
        </div>
    </div>

    <!-- Create Plan Form -->
    <div class="glass-effect rounded-xl border border-gray-700">
        <div class="p-6">
            <form action="{{ route('admin.plans.store') }}" method="POST" id="createPlanForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Plan Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Plan Adı</label>
                        <input type="text" id="name" name="name" required 
                               class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                               placeholder="Örn: Pro Plan">
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Fiyat</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-400">$</span>
                            <input type="number" id="price" name="price" step="0.01" min="0" required 
                                   class="form-input w-full pl-8 pr-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                                   placeholder="29.99">
                        </div>
                    </div>

                    <!-- Billing Cycle -->
                    <div>
                        <label for="billing_cycle" class="block text-sm font-medium text-gray-300 mb-2">Faturalama Döngüsü</label>
                        <select id="billing_cycle" name="billing_cycle" required 
                                class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                            <option value="">Seçiniz</option>
                            <option value="monthly">Aylık</option>
                            <option value="yearly">Yıllık</option>
                            <option value="trial">Deneme</option>
                        </select>
                    </div>

                    <!-- Trial Days -->
                    <div id="trial_days_field" style="display: none;">
                        <label for="trial_days" class="block text-sm font-medium text-gray-300 mb-2">Deneme Süresi (Gün)</label>
                        <input type="number" id="trial_days" name="trial_days" min="1" max="365"
                               class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                               placeholder="7">
                    </div>

                    <!-- Is Active -->
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-300 mb-2">Durum</label>
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked
                                   class="form-checkbox w-5 h-5 text-purple-glow bg-gray-800 border-gray-600 rounded focus:ring-purple-glow focus:ring-2">
                            <span class="text-gray-300">Aktif</span>
                        </div>
                    </div>

                    <!-- Usage Tokens -->
                    <div>
                        <label for="usage_tokens" class="block text-sm font-medium text-gray-300 mb-2">Usage Tokens</label>
                        <input type="number" id="usage_tokens" name="usage_tokens" required min="-1" 
                               class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20"
                               placeholder="1000 (-1 = sınırsız)">
                    </div>

                    <!-- Token Reset Period -->
                    <div>
                        <label for="token_reset_period" class="block text-sm font-medium text-gray-300 mb-2">Token Reset Periyodu</label>
                        <select id="token_reset_period" name="token_reset_period" required 
                                class="form-input w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                            <option value="monthly">Aylık</option>
                            <option value="yearly">Yıllık</option>
                        </select>
                    </div>
                </div>

                <!-- Features -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Plan Özellikleri</label>
                    <div class="space-y-4" id="featuresContainer">
                        <!-- Ürün Limiti -->
                        <div class="flex items-center space-x-3">
                            <label class="w-48 text-sm text-gray-300">Maksimum Ürün Sayısı:</label>
                            <input type="number" name="features[max_products]" placeholder="Örn: 100" min="0"
                                   class="form-input flex-1 px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                        
                        <!-- Mesaj Limiti -->
                        <div class="flex items-center space-x-3">
                            <label class="w-48 text-sm text-gray-300">Maksimum Mesaj Sayısı:</label>
                            <input type="number" name="features[max_messages]" placeholder="Örn: 10000" min="0"
                                   class="form-input flex-1 px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                        
                        <!-- Session Limiti -->
                        <div class="flex items-center space-x-3">
                            <label class="w-48 text-sm text-gray-300">Maksimum Session Sayısı:</label>
                            <input type="number" name="features[max_sessions]" placeholder="Örn: 1000" min="0"
                                   class="form-input flex-1 px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                        
                        <!-- AI Reklam Önerileri -->
                        <div class="flex items-center space-x-3">
                            <label class="w-48 text-sm text-gray-300">AI Reklam Önerileri:</label>
                            <input type="number" name="features[ai_ad_suggestions]" placeholder="Örn: 10" min="0"
                                   class="form-input flex-1 px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                        
                        <!-- Widget Kampanyalar -->
                        <div class="flex items-center space-x-3">
                            <label class="w-48 text-sm text-gray-300">Widget Kampanyalar:</label>
                            <input type="number" name="features[widget_campaigns]" placeholder="Örn: 5" min="0"
                                   class="form-input flex-1 px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                        
                        <!-- Widget SSS -->
                        <div class="flex items-center space-x-3">
                            <label class="w-48 text-sm text-gray-300">Widget SSS:</label>
                            <input type="number" name="features[widget_faq]" placeholder="Örn: 5" min="0"
                                   class="form-input flex-1 px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                        
                        <!-- AI Kişiselleştirilmiş Kampanya -->
                        <div class="flex items-center space-x-3">
                            <label class="w-48 text-sm text-gray-300">AI Kişiselleştirilmiş Kampanya:</label>
                            <input type="number" name="features[ai_personalized_campaigns]" placeholder="Örn: 50" min="0"
                                   class="form-input flex-1 px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                        
                        <!-- Destek Türü -->
                        <div class="flex items-center space-x-3">
                            <label class="w-48 text-sm text-gray-300">Destek Türü:</label>
                            <input type="text" name="features[support]" placeholder="Örn: Email Support" 
                                   class="form-input flex-1 px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-purple-glow focus:outline-none focus:ring-2 focus:ring-purple-glow/20">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-purple-glow hover:bg-purple-dark text-white font-medium rounded-lg transition-all duration-200 hover:shadow-lg hover:shadow-purple-glow/25">
                        Plan Oluştur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Billing cycle değiştiğinde trial days alanını göster/gizle
document.getElementById('billing_cycle').addEventListener('change', function() {
    const trialDaysField = document.getElementById('trial_days_field');
    if (this.value === 'trial') {
        trialDaysField.style.display = 'block';
        document.getElementById('trial_days').required = true;
    } else {
        trialDaysField.style.display = 'none';
        document.getElementById('trial_days').required = false;
    }
});
</script>
@endsection
