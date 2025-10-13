@extends('layouts.admin')

@section('title', 'Giftbox Verileri')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Giftbox Verileri</h1>
            <p class="text-gray-400 mt-2">Gift formlarından gelen lead verilerini yönetin</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="bg-gray-800 rounded-lg px-4 py-2">
                <span class="text-sm text-gray-400">Toplam:</span>
                <span class="text-white font-semibold">{{ $totalCount }}</span>
            </div>
            <a href="{{ route('admin.giftbox-data.export', request()->query()) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                CSV İndir
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-gray-800 rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-400">Toplam</p>
                    <p class="text-2xl font-bold text-white">{{ $totalCount }}</p>
                </div>
            </div>
        </div>
        
        @foreach($sectorStats as $stat)
            @php
                $sectorColors = [
                    'fashion' => 'bg-pink-500/20 text-pink-400',
                    'furniture' => 'bg-brown-500/20 text-brown-400',
                    'home-appliances' => 'bg-blue-500/20 text-blue-400',
                    'health-beauty' => 'bg-green-500/20 text-green-400',
                    'electronics' => 'bg-purple-500/20 text-purple-400'
                ];
                $sectorLabels = [
                    'fashion' => 'Moda',
                    'furniture' => 'Mobilya',
                    'home-appliances' => 'Ev Aletleri',
                    'health-beauty' => 'Sağlık & Güzellik',
                    'electronics' => 'Elektronik'
                ];
                $sectorIcons = [
                    'fashion' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z',
                    'furniture' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z',
                    'home-appliances' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
                    'health-beauty' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                    'electronics' => 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z'
                ];
            @endphp
            <div class="bg-gray-800 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 {{ $sectorColors[$stat->sector] }} rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sectorIcons[$stat->sector] }}"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">{{ $sectorLabels[$stat->sector] ?? ucfirst($stat->sector) }}</p>
                        <p class="text-2xl font-bold text-white">{{ $stat->count }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Filters -->
    <div class="bg-gray-800 rounded-lg p-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ad, soyad, email veya telefon ile ara..." class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
            </div>
            <div class="min-w-48">
                <select name="sector" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                    <option value="">Tüm Sektörler</option>
                    <option value="fashion" {{ request('sector') == 'fashion' ? 'selected' : '' }}>Moda</option>
                    <option value="furniture" {{ request('sector') == 'furniture' ? 'selected' : '' }}>Mobilya</option>
                    <option value="home-appliances" {{ request('sector') == 'home-appliances' ? 'selected' : '' }}>Ev Aletleri</option>
                    <option value="health-beauty" {{ request('sector') == 'health-beauty' ? 'selected' : '' }}>Sağlık & Güzellik</option>
                    <option value="electronics" {{ request('sector') == 'electronics' ? 'selected' : '' }}>Elektronik</option>
                </select>
            </div>
            <div class="min-w-32">
                <select name="sort_by" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tarih</option>
                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Ad</option>
                    <option value="surname" {{ request('sort_by') == 'surname' ? 'selected' : '' }}>Soyad</option>
                    <option value="mail" {{ request('sort_by') == 'mail' ? 'selected' : '' }}>Email</option>
                    <option value="sector" {{ request('sort_by') == 'sector' ? 'selected' : '' }}>Sektör</option>
                </select>
            </div>
            <div class="min-w-32">
                <select name="sort_order" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Azalan</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Artan</option>
                </select>
            </div>
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition-colors">
                Filtrele
            </button>
            @if(request()->hasAny(['search', 'sector', 'sort_by', 'sort_order']))
                <a href="{{ route('admin.giftbox-data.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Temizle
                </a>
            @endif
        </form>
    </div>

    <!-- Giftbox Users Table -->
    <div class="bg-gray-800 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">Giftbox Kullanıcıları</h3>
        </div>
        
        @if($giftboxUsers->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Kişi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">İletişim</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Ziyaretçi Sayısı</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Sektör</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Kayıt Tarihi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($giftboxUsers as $user)
                            <tr class="hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-white">{{ $user->name }} {{ $user->surname }}</div>
                                        <div class="text-sm text-gray-400">ID: {{ $user->id }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm text-white">{{ $user->mail }}</div>
                                        @if($user->phone)
                                            <div class="text-sm text-gray-400">{{ $user->phone }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->visitors)
                                        <span class="text-sm text-white">{{ $user->visitors }}</span>
                                    @else
                                        <span class="text-sm text-gray-400">Belirtilmemiş</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $sectorColors = [
                                            'fashion' => 'bg-pink-500/20 text-pink-400',
                                            'furniture' => 'bg-brown-500/20 text-brown-400',
                                            'home-appliances' => 'bg-blue-500/20 text-blue-400',
                                            'health-beauty' => 'bg-green-500/20 text-green-400',
                                            'electronics' => 'bg-purple-500/20 text-purple-400'
                                        ];
                                        $sectorLabels = [
                                            'fashion' => 'Moda',
                                            'furniture' => 'Mobilya',
                                            'home-appliances' => 'Ev Aletleri',
                                            'health-beauty' => 'Sağlık & Güzellik',
                                            'electronics' => 'Elektronik'
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $sectorColors[$user->sector] ?? 'bg-gray-500/20 text-gray-400' }}">
                                        {{ $sectorLabels[$user->sector] ?? ucfirst($user->sector) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ $user->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.giftbox-data.show', $user) }}" class="text-purple-400 hover:text-purple-300 transition-colors" title="Detayları Görüntüle">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <button onclick="deleteUser({{ $user->id }})" class="text-red-400 hover:text-red-300 transition-colors" title="Sil">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-700">
                {{ $giftboxUsers->appends(request()->query())->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-400">Henüz giftbox verisi yok</h3>
                <p class="mt-1 text-sm text-gray-500">Gift formlarından gelen veriler burada görünecek.</p>
            </div>
        @endif
    </div>
</div>

<script>
function deleteUser(userId) {
    if (confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')) {
        fetch(`/admin/giftbox-data/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Silme işlemi sırasında bir hata oluştu.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu.');
        });
    }
}
</script>
@endsection
