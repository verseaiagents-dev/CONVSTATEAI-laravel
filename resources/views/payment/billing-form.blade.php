@extends('layouts.dashboard')

@section('title', 'Ödeme Bilgileri - ConvStateAI')

@section('content')
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="glass-effect rounded-2xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-4">
                    <span class="gradient-text">Ödeme Bilgileri</span> 💳
                </h1>
                <p class="text-xl text-gray-300 mb-6">
                    Abonelik işlemini tamamlamak için ödeme bilgilerinizi girin
                </p>
            </div>

            <!-- Plan Bilgileri -->
            <div class="bg-gray-800/50 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-white mb-4">Seçilen Plan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-300 mb-4">
                    <div>
                        <span class="text-gray-400">Plan:</span>
                        <span class="ml-2 font-semibold">{{ $plan->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Süre:</span>
                        <span class="ml-2">{{ $plan->billing_cycle_text }}</span>
                    </div>
                </div>
                
                <!-- Fiyat Detayları -->
                <div class="border-t border-gray-600 pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-gray-300">
                        <div>
                            <span class="text-gray-400">Plan Fiyatı:</span>
                            <span class="ml-2 font-semibold">{{ number_format($plan->price, 2) }} ₺</span>
                        </div>
                        <div>
                            <span class="text-blue-400">KDV (%20):</span>
                            <span class="ml-2 font-semibold text-blue-400">{{ number_format($plan->price * 0.20, 2) }} ₺</span>
                        </div>
                        <div>
                            <span class="text-gray-400">Toplam:</span>
                            <span class="ml-2 font-semibold text-green-400">{{ number_format($plan->price * 1.20, 2) }} ₺</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ödeme Bilgileri Formu -->
            <form action="{{ route('payment.checkout', $plan->id) }}" method="POST" class="space-y-6" id="paymentForm">
                @csrf
                
                <!-- PayTR Gerekli Bilgiler -->
                <div class="bg-gray-800/50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Ödeme Bilgileri</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-300 mb-2">Ad Soyad *</label>
                            <input type="text" id="full_name" name="full_name" required
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Ad ve soyadınızı girin"
                                   value="{{ old('full_name', auth()->user()->name ?? '') }}">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">E-posta *</label>
                            <input type="email" id="email" name="email" required
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="E-posta adresinizi girin"
                                   value="{{ old('email', auth()->user()->email ?? '') }}">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">Telefon *</label>
                            <input type="tel" id="phone" name="phone" required
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Telefon numaranızı girin"
                                   value="{{ old('phone') }}">
                        </div>
                        <div class="md:col-span-2">
                            <label for="address_line" class="block text-sm font-medium text-gray-300 mb-2">Adres *</label>
                            <textarea id="address_line" name="address_line" required rows="3"
                                      class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                      placeholder="Tam adresinizi girin">{{ old('address_line') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Şirket Bilgileri Checkbox -->
                <div class="bg-gray-800/50 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <input type="checkbox" id="company_info_required" name="company_info_required" 
                               class="w-4 h-4 text-purple-600 bg-gray-700 border-gray-600 rounded focus:ring-purple-500 focus:ring-2"
                               onchange="toggleCompanyInfo()">
                        <label for="company_info_required" class="ml-2 text-sm font-medium text-gray-300">
                            Fatura için şirket bilgileri gerekiyor
                        </label>
                    </div>
                    
                    <!-- Şirket Bilgileri Div (Başlangıçta gizli) -->
                    <div id="company_info_div" class="hidden overflow-hidden transition-all duration-500 ease-in-out">
                        <div class="border-t border-gray-600 pt-4 mt-4">
                            <h4 class="text-md font-semibold text-white mb-4">Şirket Bilgileri</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="company_name" class="block text-sm font-medium text-gray-300 mb-2">Şirket Adı</label>
                                    <input type="text" id="company_name" name="company_name"
                                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                           placeholder="Şirket adınızı girin"
                                           value="{{ old('company_name') }}">
                                </div>
                                <div>
                                    <label for="tax_number" class="block text-sm font-medium text-gray-300 mb-2">Vergi Numarası</label>
                                    <input type="text" id="tax_number" name="tax_number"
                                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                           placeholder="Vergi numaranızı girin"
                                           value="{{ old('tax_number') }}">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="tax_office" class="block text-sm font-medium text-gray-300 mb-2">Vergi Dairesi</label>
                                    <input type="text" id="tax_office" name="tax_office"
                                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                           placeholder="Vergi dairesini girin"
                                           value="{{ old('tax_office') }}">
                                </div>
                            </div>
                            
                            <!-- Adres Detayları (Opsiyonel) -->
                            <div class="mt-6 pt-4 border-t border-gray-600">
                                <h5 class="text-md font-semibold text-white mb-4">Adres Detayları (Opsiyonel)</h5>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="country" class="block text-sm font-medium text-gray-300 mb-2">Ülke</label>
                                        <select id="country" name="country"
                                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                            <option value="">Ülke seçin</option>
                                            <option value="TR" {{ old('country') == 'TR' ? 'selected' : '' }}>Türkiye</option>
                                            <option value="US" {{ old('country') == 'US' ? 'selected' : '' }}>Amerika Birleşik Devletleri</option>
                                            <option value="DE" {{ old('country') == 'DE' ? 'selected' : '' }}>Almanya</option>
                                            <option value="FR" {{ old('country') == 'FR' ? 'selected' : '' }}>Fransa</option>
                                            <option value="GB" {{ old('country') == 'GB' ? 'selected' : '' }}>İngiltere</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-300 mb-2">Şehir</label>
                                        <input type="text" id="city" name="city"
                                               class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                               placeholder="Şehir adını girin"
                                               value="{{ old('city') }}">
                                    </div>
                                    <div>
                                        <label for="district" class="block text-sm font-medium text-gray-300 mb-2">İlçe</label>
                                        <input type="text" id="district" name="district"
                                               class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                               placeholder="İlçe adını girin"
                                               value="{{ old('district') }}">
                                    </div>
                                    <div>
                                        <label for="postal_code" class="block text-sm font-medium text-gray-300 mb-2">Posta Kodu</label>
                                        <input type="text" id="postal_code" name="postal_code"
                                               class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                               placeholder="Posta kodunu girin"
                                               value="{{ old('postal_code') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Form Butonları -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('subscription.plans') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gray-700 text-white font-semibold rounded-lg hover:bg-gray-600 transition-all duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Geri Dön
                    </a>
                    
                    <button type="submit" 
                            class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-purple-glow to-neon-purple text-white font-semibold rounded-lg hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Ödemeye Geç
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleCompanyInfo() {
    const checkbox = document.getElementById('company_info_required');
    const companyDiv = document.getElementById('company_info_div');
    const companyInputs = companyDiv.querySelectorAll('input');
    const companySelects = companyDiv.querySelectorAll('select');
    
    if (checkbox.checked) {
        // Checkbox işaretlendiğinde div'i göster
        companyDiv.classList.remove('hidden');
        companyDiv.style.maxHeight = '0px';
        
        // Animasyon için setTimeout kullan
        setTimeout(() => {
            companyDiv.style.maxHeight = companyDiv.scrollHeight + 'px';
        }, 10);
        
        // Şirket alanlarını zorunlu yap
        companyInputs.forEach(input => {
            input.setAttribute('required', 'required');
        });
        companySelects.forEach(select => {
            select.setAttribute('required', 'required');
        });
    } else {
        // Checkbox işaretlenmediğinde div'i gizle
        companyDiv.style.maxHeight = '0px';
        
        setTimeout(() => {
            companyDiv.classList.add('hidden');
        }, 500);
        
        // Şirket alanlarını opsiyonel yap
        companyInputs.forEach(input => {
            input.removeAttribute('required');
            input.value = ''; // Değerleri temizle
        });
        companySelects.forEach(select => {
            select.removeAttribute('required');
            select.selectedIndex = 0; // İlk seçeneği seç (boş değer)
        });
    }
}

// Form submit kontrolü
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    const checkbox = document.getElementById('company_info_required');
    const companyInputs = document.querySelectorAll('#company_info_div input');
    const companySelects = document.querySelectorAll('#company_info_div select');
    
    if (checkbox.checked) {
        // Şirket bilgileri gerekiyorsa kontrol et
        let allFilled = true;
        
        // Input alanlarını kontrol et
        companyInputs.forEach(input => {
            if (!input.value.trim()) {
                allFilled = false;
                input.classList.add('border-red-500');
            } else {
                input.classList.remove('border-red-500');
            }
        });
        
        // Select alanlarını kontrol et
        companySelects.forEach(select => {
            if (!select.value.trim()) {
                allFilled = false;
                select.classList.add('border-red-500');
            } else {
                select.classList.remove('border-red-500');
            }
        });
        
        if (!allFilled) {
            e.preventDefault();
            alert('Şirket bilgileri gerekiyor. Lütfen tüm şirket alanlarını doldurun.');
            return false;
        }
    }
});
</script>
@endsection