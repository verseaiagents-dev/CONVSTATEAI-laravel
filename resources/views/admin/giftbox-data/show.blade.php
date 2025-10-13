@extends('layouts.admin')

@section('title', 'Giftbox Kullanıcı Detayı')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Giftbox Kullanıcı Detayı</h1>
            <p class="text-gray-400 mt-2">Kullanıcı ID: {{ $giftboxUser->id }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.giftbox-data.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                ← Geri Dön
            </a>
            <button onclick="deleteUser({{ $giftboxUser->id }})" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                Kullanıcıyı Sil
            </button>
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
                        <label class="block text-sm text-gray-400 mb-1">Ad</label>
                        <p class="text-white font-medium">{{ $giftboxUser->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Soyad</label>
                        <p class="text-white font-medium">{{ $giftboxUser->surname }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">E-posta</label>
                        <p class="text-white">{{ $giftboxUser->mail }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Telefon</label>
                        <p class="text-white">{{ $giftboxUser->phone ?: 'Belirtilmemiş' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Ziyaretçi Sayısı</label>
                        <p class="text-white">{{ $giftboxUser->visitors ?: 'Belirtilmemiş' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Sektör</label>
                        @php
                            $sectorColors = [
                                'fashion' => 'bg-pink-500/20 text-pink-400 border-pink-500/30',
                                'furniture' => 'bg-brown-500/20 text-brown-400 border-brown-500/30',
                                'home-appliances' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                'health-beauty' => 'bg-green-500/20 text-green-400 border-green-500/30',
                                'electronics' => 'bg-purple-500/20 text-purple-400 border-purple-500/30'
                            ];
                            $sectorLabels = [
                                'fashion' => 'Moda',
                                'furniture' => 'Mobilya',
                                'home-appliances' => 'Ev Aletleri',
                                'health-beauty' => 'Sağlık & Güzellik',
                                'electronics' => 'Elektronik'
                            ];
                        @endphp
                        <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full border {{ $sectorColors[$giftboxUser->sector] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/30' }}">
                            {{ $sectorLabels[$giftboxUser->sector] ?? ucfirst($giftboxUser->sector) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Form Submission Info -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Form Bilgileri</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Form URL</label>
                        <p class="text-white text-sm">{{ route('gift-data.' . $giftboxUser->sector . '-sector') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">IP Adresi</label>
                        <p class="text-white">{{ request()->ip() }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">User Agent</label>
                        <p class="text-white text-sm break-all">{{ request()->userAgent() }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Referrer</label>
                        <p class="text-white text-sm">{{ request()->header('referer') ?: 'Direkt erişim' }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4">İşlemler</h3>
                <div class="space-y-3">
                    <a href="mailto:{{ $giftboxUser->mail }}" class="block w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-center">
                        📧 E-posta Gönder
                    </a>
                    @if($giftboxUser->phone)
                        <a href="tel:{{ $giftboxUser->phone }}" class="block w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors text-center">
                            📞 Telefon Et
                        </a>
                    @endif
                    <button onclick="copyEmail()" class="w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        📋 E-posta Kopyala
                    </button>
                    <button onclick="copyPhone()" class="w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        📋 Telefon Kopyala
                    </button>
                </div>
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
                            <p class="text-sm text-white">Giftbox formu dolduruldu</p>
                            <p class="text-xs text-gray-400">{{ $giftboxUser->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($giftboxUser->updated_at != $giftboxUser->created_at)
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-blue-400 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm text-white">Son güncelleme</p>
                                <p class="text-xs text-gray-400">{{ $giftboxUser->updated_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- User Info -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Kullanıcı Bilgileri</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm text-gray-400">Kullanıcı ID</label>
                        <p class="text-white font-mono">{{ $giftboxUser->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Kayıt Tarihi</label>
                        <p class="text-white">{{ $giftboxUser->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Son Güncelleme</label>
                        <p class="text-white">{{ $giftboxUser->updated_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Tam Ad</label>
                        <p class="text-white">{{ $giftboxUser->name }} {{ $giftboxUser->surname }}</p>
                    </div>
                </div>
            </div>

            <!-- Sector Info -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Sektör Bilgileri</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm text-gray-400">Sektör</label>
                        <p class="text-white">{{ $sectorLabels[$giftboxUser->sector] ?? ucfirst($giftboxUser->sector) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Form Sayfası</label>
                        <a href="{{ route('gift-data.' . $giftboxUser->sector . '-sector') }}" target="_blank" class="text-purple-400 hover:text-purple-300 text-sm">
                            Formu Görüntüle →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Hızlı İstatistikler</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Form Doldurma</span>
                        <span class="text-white">{{ $giftboxUser->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Günler Önce</span>
                        <span class="text-white">{{ $giftboxUser->created_at->diffInDays() }} gün</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Saatler Önce</span>
                        <span class="text-white">{{ $giftboxUser->created_at->diffInHours() }} saat</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteUser(userId) {
    if (confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
        fetch(`/admin/giftbox-data/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.href = '{{ route("admin.giftbox-data.index") }}';
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

function copyEmail() {
    navigator.clipboard.writeText('{{ $giftboxUser->mail }}').then(function() {
        alert('E-posta adresi kopyalandı!');
    }, function(err) {
        console.error('Could not copy text: ', err);
        alert('Kopyalama başarısız!');
    });
}

function copyPhone() {
    @if($giftboxUser->phone)
        navigator.clipboard.writeText('{{ $giftboxUser->phone }}').then(function() {
            alert('Telefon numarası kopyalandı!');
        }, function(err) {
            console.error('Could not copy text: ', err);
            alert('Kopyalama başarısız!');
        });
    @else
        alert('Telefon numarası bulunamadı!');
    @endif
}
</script>
@endsection
