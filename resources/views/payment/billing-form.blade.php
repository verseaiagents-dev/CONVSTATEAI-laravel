@extends('layouts.app')

@section('title', 'Fatura Bilgileri - ConvStateAI')

@section('content')
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="glass-effect rounded-2xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-4">
                    <span class="gradient-text">Fatura Bilgileri</span> 📋
                </h1>
                <p class="text-xl text-gray-300 mb-6">
                    Abonelik işlemini tamamlamak için fatura bilgilerinizi girin
                </p>
            </div>

            <!-- Plan Bilgileri -->
            <div class="bg-gray-800/50 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-white mb-4">Seçilen Plan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-gray-300">
                    <div>
                        <span class="text-gray-400">Plan:</span>
                        <span class="ml-2 font-semibold">{{ $plan->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Fiyat:</span>
                        <span class="ml-2 font-semibold text-green-400">{{ number_format($plan->price, 2) }} ₺</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Süre:</span>
                        <span class="ml-2">{{ $plan->billing_cycle_text }}</span>
                    </div>
                </div>
            </div>

            <!-- Fatura Bilgileri Formu -->
            <form action="{{ route('payment.checkout', $plan->id) }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Şirket Bilgileri -->
                <div class="bg-gray-800/50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Şirket Bilgileri</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-300 mb-2">Şirket Adı *</label>
                            <input type="text" id="company_name" name="company_name" required
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Şirket adınızı girin">
                        </div>
                        <div>
                            <label for="tax_number" class="block text-sm font-medium text-gray-300 mb-2">Vergi Numarası *</label>
                            <input type="text" id="tax_number" name="tax_number" required
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Vergi numaranızı girin">
                        </div>
                        <div class="md:col-span-2">
                            <label for="tax_office" class="block text-sm font-medium text-gray-300 mb-2">Vergi Dairesi *</label>
                            <input type="text" id="tax_office" name="tax_office" required
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Vergi dairesini girin">
                        </div>
                    </div>
                </div>

                <!-- Kişi Bilgileri -->
                <div class="bg-gray-800/50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Kişi Bilgileri</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-300 mb-2">Ad Soyad *</label>
                            <input type="text" id="full_name" name="full_name" required
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Ad ve soyadınızı girin">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">E-posta *</label>
                            <input type="email" id="email" name="email" required
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="E-posta adresinizi girin">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">Telefon *</label>
                            <input type="tel" id="phone" name="phone" required
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Telefon numaranızı girin">
                        </div>
                    </div>
                </div>

                <!-- Adres Bilgileri -->
                <div class="bg-gray-800/50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Adres Bilgileri</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-300 mb-2">Ülke *</label>
                            <select id="country" name="country" required
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Ülke seçin</option>
                                <option value="TR">Türkiye</option>
                                <option value="US">Amerika Birleşik Devletleri</option>
                                <option value="DE">Almanya</option>
                                <option value="FR">Fransa</option>
                                <option value="GB">İngiltere</option>
                            </select>
                        </div>
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-300 mb-2">Şehir *</label>
                            <input type="text" id="city" name="city" required
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Şehir adını girin">
                        </div>
                        <div>
                            <label for="district" class="block text-sm font-medium text-gray-300 mb-2">İlçe *</label>
                            <input type="text" id="district" name="district" required
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="İlçe adını girin">
                        </div>
                        <div class="md:col-span-2">
                            <label for="address_line" class="block text-sm font-medium text-gray-300 mb-2">Adres *</label>
                            <textarea id="address_line" name="address_line" required rows="3"
                                      class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                      placeholder="Tam adresinizi girin"></textarea>
                        </div>
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-300 mb-2">Posta Kodu *</label>
                            <input type="text" id="postal_code" name="postal_code" required
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Posta kodunu girin">
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
@endsection
