@extends('layouts.admin')

@section('title', 'Giftbox Lead Yönetimi - ConvStateAI')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-rose-500 rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-bold mb-4">
                🎁 <span class="gradient-text">Giftbox Lead Yönetimi</span>
            </h1>
            <p class="text-xl text-gray-300 mb-6">
                Gift formlarından gelen lead verilerini görüntüleyin ve yönetin.
            </p>
            
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.giftbox-data.export') }}" class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg text-white font-semibold hover:from-green-600 hover:to-emerald-600 transition-all duration-300 transform hover:scale-105">
                    📊 CSV Export
                </a>
                <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 rounded-lg text-white font-semibold hover:from-gray-600 hover:to-gray-700 transition-all duration-300 transform hover:scale-105">
                    ← Admin Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Leads -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-pink-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-pink-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Toplam Lead</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $totalCount }}</p>
                </div>
                <div class="p-3 bg-pink-500/20 rounded-full">
                    <svg class="w-8 h-8 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Sector Stats -->
        @foreach($sectorStats as $stat)
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-blue-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">{{ ucfirst($stat->sector) }}</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stat->count }}</p>
                </div>
                <div class="p-3 bg-blue-500/20 rounded-full">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Filters and Search -->
    <div class="glass-effect rounded-xl p-6">
        <form method="GET" action="{{ route('admin.giftbox-data.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-300 mb-2">Arama</label>
                <input type="text" name="search" value="{{ $request->search }}" placeholder="Ad, soyad, email veya telefon ile ara..." class="w-full px-4 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-pink-500 focus:ring-1 focus:ring-pink-500">
            </div>
            
            <div class="min-w-48">
                <label class="block text-sm font-medium text-gray-300 mb-2">Sektör</label>
                <select name="sector" class="w-full px-4 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-pink-500 focus:ring-1 focus:ring-pink-500">
                    <option value="">Tüm Sektörler</option>
                    <option value="fashion" {{ $request->sector == 'fashion' ? 'selected' : '' }}>Fashion</option>
                    <option value="furniture" {{ $request->sector == 'furniture' ? 'selected' : '' }}>Furniture</option>
                    <option value="home-appliances" {{ $request->sector == 'home-appliances' ? 'selected' : '' }}>Home Appliances</option>
                    <option value="health-beauty" {{ $request->sector == 'health-beauty' ? 'selected' : '' }}>Health & Beauty</option>
                    <option value="electronics" {{ $request->sector == 'electronics' ? 'selected' : '' }}>Electronics</option>
                </select>
            </div>
            
            <div class="min-w-48">
                <label class="block text-sm font-medium text-gray-300 mb-2">Sıralama</label>
                <select name="sort_by" class="w-full px-4 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-pink-500 focus:ring-1 focus:ring-pink-500">
                    <option value="created_at" {{ $request->sort_by == 'created_at' ? 'selected' : '' }}>Kayıt Tarihi</option>
                    <option value="name" {{ $request->sort_by == 'name' ? 'selected' : '' }}>Ad</option>
                    <option value="surname" {{ $request->sort_by == 'surname' ? 'selected' : '' }}>Soyad</option>
                    <option value="mail" {{ $request->sort_by == 'mail' ? 'selected' : '' }}>Email</option>
                    <option value="sector" {{ $request->sort_by == 'sector' ? 'selected' : '' }}>Sektör</option>
                </select>
            </div>
            
            <div class="min-w-32">
                <label class="block text-sm font-medium text-gray-300 mb-2">Sıra</label>
                <select name="sort_order" class="w-full px-4 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white focus:border-pink-500 focus:ring-1 focus:ring-pink-500">
                    <option value="desc" {{ $request->sort_order == 'desc' ? 'selected' : '' }}>Azalan</option>
                    <option value="asc" {{ $request->sort_order == 'asc' ? 'selected' : '' }}>Artan</option>
                </select>
            </div>
            
            <button type="submit" class="px-6 py-2 bg-pink-500 hover:bg-pink-600 text-white rounded-lg transition-colors">
                🔍 Filtrele
            </button>
            
            @if($request->hasAny(['search', 'sector', 'sort_by', 'sort_order']))
            <a href="{{ route('admin.giftbox-data.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                🗑️ Temizle
            </a>
            @endif
        </form>
    </div>

    <!-- Leads Table -->
    <div class="glass-effect rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Ad Soyad</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Telefon</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Ziyaretçi</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Sektör</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Kayıt Tarihi</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($giftboxUsers as $user)
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $user->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-white">{{ $user->name }} {{ $user->surname }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-300">{{ $user->mail }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-300">{{ $user->phone ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-300">{{ $user->visitors ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($user->sector) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                            {{ $user->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.giftbox-data.show', $user) }}" class="text-blue-400 hover:text-blue-300 transition-colors">
                                    👁️ Görüntüle
                                </a>
                                <form method="POST" action="{{ route('admin.giftbox-data.destroy', $user) }}" class="inline" onsubmit="return confirm('Bu lead\'i silmek istediğinizden emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 transition-colors">
                                        🗑️ Sil
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                </svg>
                                <p class="text-lg font-medium">Henüz lead verisi bulunmuyor</p>
                                <p class="text-sm">Gift formlarından gelen veriler burada görüntülenecek.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($giftboxUsers->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $giftboxUsers->appends($request->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
