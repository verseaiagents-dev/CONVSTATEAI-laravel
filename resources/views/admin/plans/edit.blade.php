@extends('layouts.admin')

@section('title', 'Plan Düzenle - ConvStateAI')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-bold mb-4">
                <span class="gradient-text">Plan Düzenle</span> ✏️
            </h1>
            <p class="text-xl text-gray-300 mb-6">
                {{ $plan->name }} planını düzenleyin.
            </p>
        </div>
    </div>

    <!-- Edit Form -->
    <form action="{{ route('admin.plans.update', $plan) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="glass-effect rounded-2xl p-8">
                <h2 class="text-2xl font-bold mb-6 text-white">Temel Bilgiler</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Plan Adı</label>
                        <input type="text" name="name" value="{{ old('name', $plan->name) }}" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                        @error('name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Aylık Fiyat (₺)</label>
                        <input type="number" name="price" value="{{ old('price', $plan->price) }}" step="0.01" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                        @error('price')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Yıllık Fiyat (₺)</label>
                        <input type="number" name="yearly_price" value="{{ old('yearly_price', $plan->yearly_price) }}" step="0.01" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('yearly_price')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Faturalandırma Döngüsü</label>
                        <select name="billing_cycle" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent" required>
                            <option value="monthly" {{ old('billing_cycle', $plan->billing_cycle) === 'monthly' ? 'selected' : '' }}>Aylık</option>
                            <option value="yearly" {{ old('billing_cycle', $plan->billing_cycle) === 'yearly' ? 'selected' : '' }}>Yıllık</option>
                        </select>
                        @error('billing_cycle')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Deneme Süresi (gün)</label>
                        <input type="number" name="trial_days" value="{{ old('trial_days', $plan->trial_days) }}" min="0" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('trial_days')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Kategori</label>
                        <select name="category" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="free" {{ old('category', $plan->category) === 'free' ? 'selected' : '' }}>Ücretsiz</option>
                            <option value="starter" {{ old('category', $plan->category) === 'starter' ? 'selected' : '' }}>Başlangıç</option>
                            <option value="professional" {{ old('category', $plan->category) === 'professional' ? 'selected' : '' }}>Profesyonel</option>
                            <option value="enterprise" {{ old('category', $plan->category) === 'enterprise' ? 'selected' : '' }}>Kurumsal</option>
                        </select>
                        @error('category')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }} class="w-4 h-4 text-purple-600 bg-gray-700 border-gray-600 rounded focus:ring-purple-500">
                        <label class="ml-2 text-sm text-gray-300">Aktif</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="installment" value="1" {{ old('installment', $plan->installment) ? 'checked' : '' }} class="w-4 h-4 text-purple-600 bg-gray-700 border-gray-600 rounded focus:ring-purple-500">
                        <label class="ml-2 text-sm text-gray-300">Taksitli Ödeme</label>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="glass-effect rounded-2xl p-8">
                <h2 class="text-2xl font-bold mb-6 text-white">Özellikler</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Maksimum Proje Sayısı</label>
                        <input type="number" name="features[max_projects]" value="{{ old('features.max_projects', $plan->features['max_projects'] ?? '') }}" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">-1 yazarsanız sınırsız olur</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Maksimum Knowledge Base Sayısı</label>
                        <input type="number" name="features[max_knowledge_bases]" value="{{ old('features.max_knowledge_bases', $plan->features['max_knowledge_bases'] ?? '') }}" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Maksimum Chat Session Sayısı</label>
                        <input type="number" name="features[max_chat_sessions]" value="{{ old('features.max_chat_sessions', $plan->features['max_chat_sessions'] ?? '') }}" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Maksimum Ürün Sayısı</label>
                        <input type="number" name="features[max_products]" value="{{ old('features.max_products', $plan->features['max_products'] ?? '') }}" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Aylık AI Yanıt Sayısı</label>
                        <input type="number" name="features[ai_responses_per_month]" value="{{ old('features.ai_responses_per_month', $plan->features['ai_responses_per_month'] ?? '') }}" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input type="checkbox" name="features[widget_customization]" value="1" {{ old('features.widget_customization', $plan->features['widget_customization'] ?? false) ? 'checked' : '' }} class="w-4 h-4 text-purple-600 bg-gray-700 border-gray-600 rounded focus:ring-purple-500">
                            <label class="ml-2 text-sm text-gray-300">Widget Özelleştirme</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="features[api_access]" value="1" {{ old('features.api_access', $plan->features['api_access'] ?? false) ? 'checked' : '' }} class="w-4 h-4 text-purple-600 bg-gray-700 border-gray-600 rounded focus:ring-purple-500">
                            <label class="ml-2 text-sm text-gray-300">API Erişimi</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="features[priority_support]" value="1" {{ old('features.priority_support', $plan->features['priority_support'] ?? false) ? 'checked' : '' }} class="w-4 h-4 text-purple-600 bg-gray-700 border-gray-600 rounded focus:ring-purple-500">
                            <label class="ml-2 text-sm text-gray-300">Öncelikli Destek</label>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Analytics Seviyesi</label>
                        <select name="features[analytics]" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="basic" {{ old('features.analytics', $plan->features['analytics'] ?? '') === 'basic' ? 'selected' : '' }}>Temel</option>
                            <option value="standard" {{ old('features.analytics', $plan->features['analytics'] ?? '') === 'standard' ? 'selected' : '' }}>Standart</option>
                            <option value="advanced" {{ old('features.analytics', $plan->features['analytics'] ?? '') === 'advanced' ? 'selected' : '' }}>Gelişmiş</option>
                            <option value="enterprise" {{ old('features.analytics', $plan->features['analytics'] ?? '') === 'enterprise' ? 'selected' : '' }}>Kurumsal</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between">
            <a href="{{ route('admin.plans.show', $plan) }}" class="px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Geri Dön
            </a>
            
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-purple-glow to-neon-purple text-white font-semibold rounded-lg hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-save mr-2"></i>
                Güncelle
            </button>
        </div>
    </form>
</div>
@endsection
