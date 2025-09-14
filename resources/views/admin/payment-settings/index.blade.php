@extends('layouts.admin')

@section('title', 'Ödeme Ayarları - ConvStateAI')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-bold mb-4">
                <span class="gradient-text">Ödeme Ayarları</span> 💳
            </h1>
            <p class="text-xl text-gray-300 mb-6">
                Ödeme sistemlerini yapılandırın ve fiyatlandırma ayarlarını yönetin.
            </p>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Stripe Settings -->
        <div class="glass-effect rounded-2xl p-8">
            <h2 class="text-2xl font-bold mb-6 text-white">Stripe Ayarları</h2>
            
            <div class="space-y-4">
                <div class="bg-gray-800/50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-lg font-semibold text-white">Stripe API</h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            Aktif
                        </span>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Publishable Key</label>
                            <input type="text" placeholder="pk_test_..." class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Secret Key</label>
                            <input type="password" placeholder="sk_test_..." class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Webhook Secret</label>
                            <input type="password" placeholder="whsec_..." class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-white mb-3">Test Modu</h4>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-300">Test modunu etkinleştir</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- PayPal Settings -->
        <div class="glass-effect rounded-2xl p-8">
            <h2 class="text-2xl font-bold mb-6 text-white">PayPal Ayarları</h2>
            
            <div class="space-y-4">
                <div class="bg-gray-800/50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-lg font-semibold text-white">PayPal API</h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Pasif
                        </span>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Client ID</label>
                            <input type="text" placeholder="PayPal Client ID" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Client Secret</label>
                            <input type="password" placeholder="PayPal Client Secret" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Webhook ID</label>
                            <input type="text" placeholder="PayPal Webhook ID" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-white mb-3">Sandbox Modu</h4>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-300">Sandbox modunu etkinleştir</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Settings -->
    <div class="glass-effect rounded-2xl p-8">
        <h2 class="text-2xl font-bold mb-6 text-white">Fiyatlandırma Ayarları</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-800/50 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-white mb-4">Para Birimi</h4>
                <select class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="USD">USD - Amerikan Doları</option>
                    <option value="EUR">EUR - Euro</option>
                    <option value="TRY">TRY - Türk Lirası</option>
                    <option value="GBP">GBP - İngiliz Sterlini</option>
                </select>
            </div>
            
            <div class="bg-gray-800/50 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-white mb-4">Vergi Oranı</h4>
                <div class="flex items-center space-x-2">
                    <input type="number" placeholder="18" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <span class="text-gray-400">%</span>
                </div>
            </div>
            
            <div class="bg-gray-800/50 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-white mb-4">İndirim Kodu</h4>
                <div class="flex items-center space-x-2">
                    <input type="text" placeholder="WELCOME10" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <button class="px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="glass-effect rounded-2xl p-8">
        <h2 class="text-2xl font-bold mb-6 text-white">Son İşlemler</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-400">
                <thead class="text-xs text-gray-400 uppercase bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3">Tarih</th>
                        <th scope="col" class="px-6 py-3">Kullanıcı</th>
                        <th scope="col" class="px-6 py-3">Plan</th>
                        <th scope="col" class="px-6 py-3">Tutar</th>
                        <th scope="col" class="px-6 py-3">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-gray-800 border-b border-gray-700">
                        <td class="px-6 py-4">15.01.2024</td>
                        <td class="px-6 py-4">john@example.com</td>
                        <td class="px-6 py-4">Professional</td>
                        <td class="px-6 py-4">$79.99</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Başarılı
                            </span>
                        </td>
                    </tr>
                    <tr class="bg-gray-800 border-b border-gray-700">
                        <td class="px-6 py-4">14.01.2024</td>
                        <td class="px-6 py-4">jane@example.com</td>
                        <td class="px-6 py-4">Starter</td>
                        <td class="px-6 py-4">$29.99</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>
                                Beklemede
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex justify-end">
        <button class="px-8 py-3 bg-gradient-to-r from-purple-glow to-neon-purple text-white font-semibold rounded-lg hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105">
            <i class="fas fa-save mr-2"></i>
            Ayarları Kaydet
        </button>
    </div>
</div>
@endsection
