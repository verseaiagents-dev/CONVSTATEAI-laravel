@extends('layouts.admin')

@section('title', $mailTemplate->name)

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
                        <span class="gradient-text">{{ $mailTemplate->name }}</span>
                    </h1>
                    <p class="text-xl text-gray-300">
                        {{ $mailTemplate->description ?: 'Mail template detayları' }}
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.mail-templates.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Geri Dön
                    </a>
                    <a href="{{ route('admin.mail-templates.edit', $mailTemplate->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Düzenle
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sol Kolon - Template Bilgileri -->
        <div class="space-y-6">
            <!-- Template Detayları -->
            <div class="glass-effect rounded-2xl p-6">
                <h3 class="text-xl font-semibold text-white mb-4">Template Bilgileri</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-700">
                        <span class="text-gray-400">Kategori:</span>
                        <span class="text-white font-medium">{{ \App\Models\MailTemplate::CATEGORIES[$mailTemplate->category] ?? $mailTemplate->category }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-700">
                        <span class="text-gray-400">Durum:</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $mailTemplate->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $mailTemplate->is_active ? 'Aktif' : 'Pasif' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-700">
                        <span class="text-gray-400">Oluşturulma:</span>
                        <span class="text-white">{{ $mailTemplate->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-700">
                        <span class="text-gray-400">Son Güncelleme:</span>
                        <span class="text-white">{{ $mailTemplate->updated_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-400">Kullanım Sayısı:</span>
                        <span class="text-white font-medium">{{ $mailTemplate->usage_count ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Hızlı İşlemler -->
            <div class="glass-effect rounded-2xl p-6">
                <h3 class="text-xl font-semibold text-white mb-4">Hızlı İşlemler</h3>
                <div class="space-y-3">
                    <button onclick="toggleStatus()" class="w-full px-4 py-3 {{ $mailTemplate->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-medium rounded-lg transition-all duration-200">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $mailTemplate->is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"></path>
                        </svg>
                        {{ $mailTemplate->is_active ? 'Pasif Yap' : 'Aktif Yap' }}
                    </button>
                    
                    <button onclick="duplicateTemplate()" class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Template'i Kopyala
                    </button>
                    
                    <button onclick="testTemplate()" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Test Maili Gönder
                    </button>
                </div>
            </div>

            <!-- Kullanılabilir Değişkenler -->
            <div class="glass-effect rounded-2xl p-6">
                <h3 class="text-xl font-semibold text-white mb-4">Kullanılabilir Değişkenler</h3>
                <div class="space-y-2">
                    @foreach((new \App\Models\MailTemplate())::DEFAULT_VARIABLES as $variable => $description)
                        <div class="flex items-center space-x-2 text-sm">
                            <code class="text-purple-400 bg-gray-700 px-2 py-1 rounded text-xs">{{ $variable }}</code>
                            <span class="text-gray-400">{{ $description }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Orta ve Sağ Kolon - Mail İçeriği -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Mail Konusu -->
            <div class="glass-effect rounded-2xl p-6">
                <h3 class="text-xl font-semibold text-white mb-4">Mail Konusu</h3>
                <div class="bg-gray-800/30 rounded-lg p-4 border border-gray-700">
                    <p class="text-white text-lg">{{ $mailTemplate->subject }}</p>
                </div>
            </div>

            <!-- Mail İçeriği -->
            <div class="glass-effect rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-white">Mail İçeriği</h3>
                    <div class="flex items-center space-x-2">
                        <button onclick="copyContent()" class="px-3 py-1 bg-gray-600 hover:bg-gray-500 text-white text-sm rounded transition-all duration-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Kopyala
                        </button>
                        <button onclick="previewMail()" class="px-3 py-1 bg-blue-600 hover:bg-blue-500 text-white text-sm rounded transition-all duration-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Önizle
                        </button>
                    </div>
                </div>
                <div class="bg-gray-800/30 rounded-lg border border-gray-700">
                    <div class="border-b border-gray-700 p-3">
                        <span class="text-sm text-gray-400">HTML İçerik</span>
                    </div>
                    <div class="p-4">
                        <pre class="text-sm text-gray-300 whitespace-pre-wrap overflow-x-auto">{{ $mailTemplate->content }}</pre>
                    </div>
                </div>
            </div>

            <!-- Mail Önizleme -->
            <div class="glass-effect rounded-2xl p-6">
                <h3 class="text-xl font-semibold text-white mb-4">Mail Önizleme</h3>
                <div class="bg-gray-800/30 rounded-lg border border-gray-700">
                    <div class="border-b border-gray-700 p-3">
                        <span class="text-sm text-gray-400">Önizleme</span>
                    </div>
                    <div class="p-4">
                        <div id="mailPreview" class="bg-white text-black rounded p-4">
                            <!-- Mail önizlemesi burada gösterilecek -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Mail Modal -->
<div id="testMailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-900 rounded-2xl p-6 w-full max-w-md">
            <h3 class="text-xl font-semibold text-white mb-4">Test Maili Gönder</h3>
            <form id="testMailForm">
                <div class="mb-4">
                    <label for="testEmail" class="block text-sm font-medium text-gray-300 mb-2">
                        Test E-posta Adresi
                    </label>
                    <input type="email" id="testEmail" name="test_email" required
                           class="w-full form-input rounded-lg px-4 py-3 text-white placeholder-gray-400"
                           placeholder="test@example.com">
                </div>
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" onclick="closeTestModal()" 
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200">
                        İptal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200">
                        Test Maili Gönder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Durum değiştirme
function toggleStatus() {
    if (confirm('Template durumunu değiştirmek istediğinizden emin misiniz?')) {
        fetch('{{ route("admin.mail-templates.toggle-status", $mailTemplate->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
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

// Template kopyalama
function duplicateTemplate() {
    if (confirm('Bu template\'i kopyalamak istediğinizden emin misiniz?')) {
        window.location.href = '{{ route("admin.mail-templates.duplicate", $mailTemplate->id) }}';
    }
}

// Test maili gönderme
function testTemplate() {
    document.getElementById('testMailModal').classList.remove('hidden');
}

// Test modal'ı kapatma
function closeTestModal() {
    document.getElementById('testMailModal').classList.add('hidden');
}

// Test maili form submit
document.getElementById('testMailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const testEmail = document.getElementById('testEmail').value;
    
    fetch('{{ route("admin.mail-templates.send-test", $mailTemplate->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            test_email: testEmail
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test maili başarıyla gönderildi!');
            closeTestModal();
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu.');
    });
});

// İçerik kopyalama
function copyContent() {
    const content = `{{ $mailTemplate->content }}`;
    navigator.clipboard.writeText(content).then(function() {
        alert('İçerik kopyalandı!');
    });
}

// Mail önizleme
function previewMail() {
    const content = `{{ $mailTemplate->content }}`;
    const preview = document.getElementById('mailPreview');
    preview.innerHTML = content;
}

// Sayfa yüklendiğinde önizlemeyi göster
document.addEventListener('DOMContentLoaded', function() {
    previewMail();
});
</script>
@endpush
@endsection
