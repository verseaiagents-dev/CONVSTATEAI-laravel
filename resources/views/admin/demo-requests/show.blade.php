@extends('layouts.dashboard')

@section('title', 'Demo Talep Detayı')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Demo Talep Detayı</h1>
            <p class="text-gray-400 mt-2">Talep ID: {{ $demoRequest->id }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.demo-requests.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                ← Geri Dön
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Info -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Kişisel Bilgiler</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Ad Soyad</label>
                        <p class="text-white font-medium">{{ $demoRequest->full_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">E-posta</label>
                        <p class="text-white">{{ $demoRequest->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Telefon</label>
                        <p class="text-white">{{ $demoRequest->phone ?: 'Belirtilmemiş' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Site Ziyaretçi Sayısı</label>
                        <p class="text-white">{{ $demoRequest->site_visitor_count ? number_format($demoRequest->site_visitor_count) . '+' : 'Belirtilmemiş' }}</p>
                    </div>
                </div>
            </div>

            <!-- Status & Actions -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Durum ve İşlemler</h3>
                
                <div class="mb-4">
                    <label class="block text-sm text-gray-400 mb-2">Mevcut Durum</label>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                            'contacted' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                            'completed' => 'bg-green-500/20 text-green-400 border-green-500/30',
                            'cancelled' => 'bg-red-500/20 text-red-400 border-red-500/30'
                        ];
                        $statusLabels = [
                            'pending' => 'Bekleyen',
                            'contacted' => 'İletişim Kuruldu',
                            'completed' => 'Tamamlandı',
                            'cancelled' => 'İptal Edildi'
                        ];
                    @endphp
                    <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full border {{ $statusColors[$demoRequest->status] }}">
                        {{ $statusLabels[$demoRequest->status] }}
                    </span>
                </div>

                <div class="space-y-3">
                    <button onclick="updateStatus('contacted')" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        İletişim Kuruldu Olarak İşaretle
                    </button>
                    <button onclick="updateStatus('completed')" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        Tamamlandı Olarak İşaretle
                    </button>
                    <button onclick="updateStatus('cancelled')" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                        İptal Et
                    </button>
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Notlar</h3>
                <textarea id="notes" class="w-full h-32 px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:border-purple-500 focus:outline-none resize-none" placeholder="Bu talep hakkında notlarınızı buraya yazabilirsiniz...">{{ $demoRequest->notes }}</textarea>
                <button onclick="saveNotes()" class="mt-3 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                    Notları Kaydet
                </button>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Timeline -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Zaman Çizelgesi</h3>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-green-400 rounded-full mt-2"></div>
                        <div>
                            <p class="text-sm text-white">Demo talebi oluşturuldu</p>
                            <p class="text-xs text-gray-400">{{ $demoRequest->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($demoRequest->updated_at != $demoRequest->created_at)
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-blue-400 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm text-white">Son güncelleme</p>
                                <p class="text-xs text-gray-400">{{ $demoRequest->updated_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Hızlı İşlemler</h3>
                <div class="space-y-3">
                    <a href="mailto:{{ $demoRequest->email }}" class="block w-full px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors text-center">
                        📧 E-posta Gönder
                    </a>
                    @if($demoRequest->phone)
                        <a href="tel:{{ $demoRequest->phone }}" class="block w-full px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors text-center">
                            📞 Telefon Et
                        </a>
                    @endif
                    <button onclick="copyEmail()" class="w-full px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        📋 E-posta Kopyala
                    </button>
                </div>
            </div>

            <!-- Request Info -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Talep Bilgileri</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm text-gray-400">Talep ID</label>
                        <p class="text-white font-mono">{{ $demoRequest->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Oluşturulma Tarihi</label>
                        <p class="text-white">{{ $demoRequest->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Son Güncelleme</label>
                        <p class="text-white">{{ $demoRequest->updated_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(status) {
    const notes = document.getElementById('notes').value;
    
    if (confirm('Durumu güncellemek istediğinizden emin misiniz?')) {
        fetch(`{{ route('admin.demo-requests.update-status', $demoRequest) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: status,
                notes: notes
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

function saveNotes() {
    const notes = document.getElementById('notes').value;
    
    fetch(`{{ route('admin.demo-requests.update-status', $demoRequest) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: '{{ $demoRequest->status }}',
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Notlar kaydedildi!');
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu.');
    });
}

function copyEmail() {
    navigator.clipboard.writeText('{{ $demoRequest->email }}').then(function() {
        alert('E-posta adresi kopyalandı!');
    }, function(err) {
        console.error('Could not copy text: ', err);
        alert('Kopyalama başarısız!');
    });
}
</script>
@endsection
