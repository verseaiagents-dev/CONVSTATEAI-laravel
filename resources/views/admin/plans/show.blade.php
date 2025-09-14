@extends('layouts.admin')

@section('title', 'Plan Detayı - ConvStateAI')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-4">
                        <span class="gradient-text">{{ $plan->name }}</span> Plan Detayı
                    </h1>
                    <p class="text-xl text-gray-300 mb-6">
                        Plan bilgilerini görüntüleyin ve düzenleyin.
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-white">{{ number_format($plan->price, 2) }} ₺</div>
                    <div class="text-gray-400">{{ $plan->billing_cycle_text }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Plan Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Basic Info -->
        <div class="glass-effect rounded-2xl p-8">
            <h2 class="text-2xl font-bold mb-6 text-white">Temel Bilgiler</h2>
            
            <div class="space-y-4">
                <div class="bg-gray-800/50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-400 mb-2">Plan Adı</h4>
                    <p class="text-white text-lg">{{ $plan->name }}</p>
                </div>
                
                <div class="bg-gray-800/50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-400 mb-2">Fiyat</h4>
                    <p class="text-white text-lg">{{ number_format($plan->price, 2) }} ₺ / {{ $plan->billing_cycle_text }}</p>
                </div>
                
                @if($plan->yearly_price)
                <div class="bg-gray-800/50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-400 mb-2">Yıllık Fiyat</h4>
                    <p class="text-white text-lg">{{ number_format($plan->yearly_price, 2) }} ₺ / yıl</p>
                </div>
                @endif
                
                <div class="bg-gray-800/50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-400 mb-2">Durum</h4>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        <i class="fas {{ $plan->is_active ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                        {{ $plan->is_active ? 'Aktif' : 'Pasif' }}
                    </span>
                </div>
                
                @if($plan->trial_days)
                <div class="bg-gray-800/50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-400 mb-2">Deneme Süresi</h4>
                    <p class="text-white text-lg">{{ $plan->trial_days }} gün</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Features -->
        <div class="glass-effect rounded-2xl p-8">
            <h2 class="text-2xl font-bold mb-6 text-white">Özellikler</h2>
            
            <div class="space-y-3">
                @foreach($plan->features as $feature => $value)
                <div class="flex items-center justify-between py-2 border-b border-gray-700 last:border-b-0">
                    <span class="text-gray-300 capitalize">{{ str_replace('_', ' ', $feature) }}</span>
                    <span class="text-white font-semibold">
                        @if($value === true)
                            <i class="fas fa-check text-green-400"></i>
                        @elseif($value === false)
                            <i class="fas fa-times text-red-400"></i>
                        @elseif($value === -1)
                            <span class="text-purple-400">Sınırsız</span>
                        @else
                            {{ $value }}
                        @endif
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Subscribers -->
    <div class="glass-effect rounded-2xl p-8">
        <h2 class="text-2xl font-bold mb-6 text-white">Aboneler ({{ $plan->subscriptions->count() }})</h2>
        
        @if($plan->subscriptions->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-400">
                <thead class="text-xs text-gray-400 uppercase bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3">Kullanıcı</th>
                        <th scope="col" class="px-6 py-3">Başlangıç</th>
                        <th scope="col" class="px-6 py-3">Bitiş</th>
                        <th scope="col" class="px-6 py-3">Durum</th>
                        <th scope="col" class="px-6 py-3">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plan->subscriptions as $subscription)
                    <tr class="bg-gray-800 border-b border-gray-700">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-r from-purple-glow to-neon-purple rounded-full flex items-center justify-center mr-3">
                                    <span class="text-sm font-bold text-white">{{ substr($subscription->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="text-white">{{ $subscription->user->name }}</div>
                                    <div class="text-gray-400 text-sm">{{ $subscription->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $subscription->start_date->format('d.m.Y') }}</td>
                        <td class="px-6 py-4">{{ $subscription->end_date->format('d.m.Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas {{ $subscription->status === 'active' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="text-purple-400 hover:text-purple-300 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8">
            <i class="fas fa-users text-4xl text-gray-600 mb-4"></i>
            <p class="text-gray-400">Bu plana henüz abone olan kullanıcı bulunmuyor.</p>
        </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="flex justify-between">
        <a href="{{ route('admin.plans.index') }}" class="px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Geri Dön
        </a>
        
        <div class="space-x-3">
            <a href="{{ route('admin.plans.edit', $plan) }}" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>
                Düzenle
            </a>
            
            <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="inline" onsubmit="return confirm('Bu planı silmek istediğinizden emin misiniz?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-2"></i>
                    Sil
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
