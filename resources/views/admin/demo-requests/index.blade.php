@extends('layouts.dashboard')

@section('title', 'Demo Talepleri')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Demo Talepleri</h1>
            <p class="text-gray-400 mt-2">Gelen demo taleplerini yönetin ve takip edin</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="bg-gray-800 rounded-lg px-4 py-2">
                <span class="text-sm text-gray-400">Toplam:</span>
                <span class="text-white font-semibold">{{ $demoRequests->total() }}</span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-800 rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-400">Bekleyen</p>
                    <p class="text-2xl font-bold text-white">{{ $demoRequests->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-400">İletişim Kuruldu</p>
                    <p class="text-2xl font-bold text-white">{{ $demoRequests->where('status', 'contacted')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-400">Tamamlandı</p>
                    <p class="text-2xl font-bold text-white">{{ $demoRequests->where('status', 'completed')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-400">İptal Edildi</p>
                    <p class="text-2xl font-bold text-white">{{ $demoRequests->where('status', 'cancelled')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Demo Requests Table -->
    <div class="bg-gray-800 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">Demo Talepleri</h3>
        </div>
        
        @if($demoRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Kişi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">İletişim</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Site Trafiği</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Durum</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Tarih</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($demoRequests as $request)
                            <tr class="hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-white">{{ $request->full_name }}</div>
                                        <div class="text-sm text-gray-400">ID: {{ $request->id }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm text-white">{{ $request->email }}</div>
                                        @if($request->phone)
                                            <div class="text-sm text-gray-400">{{ $request->phone }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($request->site_visitor_count)
                                        <span class="text-sm text-white">{{ number_format($request->site_visitor_count) }}+</span>
                                    @else
                                        <span class="text-sm text-gray-400">Belirtilmemiş</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-500/20 text-yellow-400',
                                            'contacted' => 'bg-blue-500/20 text-blue-400',
                                            'completed' => 'bg-green-500/20 text-green-400',
                                            'cancelled' => 'bg-red-500/20 text-red-400'
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Bekleyen',
                                            'contacted' => 'İletişim Kuruldu',
                                            'completed' => 'Tamamlandı',
                                            'cancelled' => 'İptal Edildi'
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$request->status] }}">
                                        {{ $statusLabels[$request->status] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ $request->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.demo-requests.show', $request) }}" class="text-purple-400 hover:text-purple-300 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <button onclick="updateStatus({{ $request->id }}, 'contacted')" class="text-blue-400 hover:text-blue-300 transition-colors" title="İletişim Kuruldu">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="updateStatus({{ $request->id }}, 'completed')" class="text-green-400 hover:text-green-300 transition-colors" title="Tamamlandı">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="updateStatus({{ $request->id }}, 'cancelled')" class="text-red-400 hover:text-red-300 transition-colors" title="İptal Et">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
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
                {{ $demoRequests->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-400">Henüz demo talebi yok</h3>
                <p class="mt-1 text-sm text-gray-500">Demo talepleri burada görünecek.</p>
            </div>
        @endif
    </div>
</div>

<script>
function updateStatus(requestId, status) {
    if (confirm('Durumu güncellemek istediğinizden emin misiniz?')) {
        fetch(`/admin/demo-requests/${requestId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Hata: ' + data.message);
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
