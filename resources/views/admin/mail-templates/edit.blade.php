@extends('layouts.admin')

@section('title', 'Mail Template Düzenle')

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
                        <span class="gradient-text">{{ $mailTemplate->name }}</span> Düzenle
                    </h1>
                    <p class="text-xl text-gray-300">
                        Mail template'ini güncelleyin ve düzenleyin
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.mail-templates.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Geri Dön
                    </a>
                    <a href="{{ route('admin.mail-templates.show', $mailTemplate->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Görüntüle
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="glass-effect rounded-2xl p-8">
        <form action="{{ route('admin.mail-templates.update', $mailTemplate->id) }}" method="POST" id="mailTemplateForm">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Sol Kolon -->
                <div class="space-y-6">
                    <!-- Template Adı -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                            Template Adı <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $mailTemplate->name) }}" required
                               class="w-full form-input rounded-lg px-4 py-3 text-white placeholder-gray-400"
                               placeholder="Örn: Hoşgeldin Maili">
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-300 mb-2">
                            Kategori <span class="text-red-400">*</span>
                        </label>
                        <select id="category" name="category" required
                                class="w-full form-input rounded-lg px-4 py-3 text-white">
                            <option value="">Kategori Seçin</option>
                            @foreach(\App\Models\MailTemplate::CATEGORIES as $key => $value)
                                <option value="{{ $key }}" {{ old('category', $mailTemplate->category) == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konu -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-300 mb-2">
                            Mail Konusu <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject', $mailTemplate->subject) }}" required
                               class="w-full form-input rounded-lg px-4 py-3 text-white placeholder-gray-400"
                               placeholder="Örn: Hoş Geldiniz! {sitename} ailesine katıldığınız için teşekkür ederiz">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Açıklama -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">
                            Açıklama
                        </label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full form-input rounded-lg px-4 py-3 text-white placeholder-gray-400"
                                  placeholder="Template hakkında kısa açıklama">{old('description', $mailTemplate->description)}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Durum -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $mailTemplate->is_active) ? 'checked' : '' }}
                                   class="form-checkbox mr-3">
                            <span class="text-sm font-medium text-gray-300">Template Aktif</span>
                        </label>
                    </div>

                    <!-- Template Bilgileri -->
                    <div class="bg-gray-800/30 rounded-lg p-4 border border-gray-700">
                        <h4 class="text-sm font-medium text-gray-300 mb-3">Template Bilgileri</h4>
                        <div class="space-y-2 text-xs text-gray-400">
                            <div class="flex justify-between">
                                <span>Oluşturulma:</span>
                                <span class="text-white">{{ $mailTemplate->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Son Güncelleme:</span>
                                <span class="text-white">{{ $mailTemplate->updated_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Kullanım Sayısı:</span>
                                <span class="text-white">{{ $mailTemplate->usage_count ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon -->
                <div class="space-y-6">
                    <!-- Kullanılabilir Değişkenler -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">
                            Kullanılabilir Değişkenler
                        </label>
                        <div class="bg-gray-800/30 rounded-lg p-4 border border-gray-700">
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                @foreach($variables as $variable => $description)
                                    <div class="flex items-center space-x-2">
                                        <code class="text-purple-400 bg-gray-700 px-2 py-1 rounded text-xs">{{ $variable }}</code>
                                        <span class="text-gray-400">{{ $description }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-3">
                                Bu değişkenleri konu ve içerik alanlarında kullanabilirsiniz.
                            </p>
                        </div>
                    </div>

                    <!-- Önizleme -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">
                            Önizleme
                        </label>
                        <div class="bg-gray-800/30 rounded-lg p-4 border border-gray-700">
                            <div class="text-sm text-gray-400">
                                <div class="mb-2">
                                    <strong>Konu:</strong> <span id="subjectPreview" class="text-white">{{ $mailTemplate->subject }}</span>
                                </div>
                                <div>
                                    <strong>Kategori:</strong> <span id="categoryPreview" class="text-white">{{ \App\Models\MailTemplate::CATEGORIES[$mailTemplate->category] ?? $mailTemplate->category }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hızlı İşlemler -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">
                            Hızlı İşlemler
                        </label>
                        <div class="space-y-2">
                            <button type="button" onclick="duplicateTemplate()" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all duration-200">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Template'i Kopyala
                            </button>
                            <button type="button" onclick="testTemplate()" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all duration-200">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Test Maili Gönder
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mail İçeriği -->
            <div class="mt-8">
                <label for="content" class="block text-sm font-medium text-gray-300 mb-3">
                    Mail İçeriği (HTML) <span class="text-red-400">*</span>
                </label>
                <div class="bg-gray-800/30 rounded-lg border border-gray-700">
                    <div class="border-b border-gray-700 p-3">
                        <div class="flex items-center space-x-4">
                            <button type="button" onclick="insertVariable('username')" class="text-xs bg-purple-500 hover:bg-purple-600 text-white px-2 py-1 rounded">
                                {username}
                            </button>
                            <button type="button" onclick="insertVariable('useremail')" class="text-xs bg-purple-500 hover:bg-purple-600 text-white px-2 py-1 rounded">
                                {useremail}
                            </button>
                            <button type="button" onclick="insertVariable('sitename')" class="text-xs bg-purple-500 hover:bg-purple-600 text-white px-2 py-1 rounded">
                                {sitename}
                            </button>
                            <button type="button" onclick="insertVariable('currentdate')" class="text-xs bg-purple-500 hover:bg-purple-600 text-white px-2 py-1 rounded">
                                {currentdate}
                            </button>
                        </div>
                    </div>
                    <textarea id="content" name="content" rows="15" required
                              class="w-full form-input rounded-b-lg px-4 py-3 text-white placeholder-gray-400 resize-none"
                              placeholder="HTML mail içeriğini buraya yazın...">{old('content', $mailTemplate->content)}</textarea>
                </div>
                @error('content')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Butonlar -->
            <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-700">
                <a href="{{ route('admin.mail-templates.index') }}" 
                   class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200">
                    İptal
                </a>
                <button type="submit" 
                        class="px-8 py-3 bg-purple-glow hover:bg-purple-dark text-white font-medium rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Değişiklikleri Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Önizleme güncelleme
document.getElementById('subject').addEventListener('input', function() {
    document.getElementById('subjectPreview').textContent = this.value || 'Mail konusu burada görünecek';
});

document.getElementById('category').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    document.getElementById('categoryPreview').textContent = selectedOption.text || 'Seçilen kategori';
});

// Değişken ekleme
function insertVariable(variable) {
    const content = document.getElementById('content');
    const start = content.selectionStart;
    const end = content.selectionEnd;
    const text = content.value;
    
   
    const variableWithBrackets = '{{' + variable + '}}';
    content.value = text.substring(0, start) + variableWithBrackets + text.substring(end);
    content.focus();
    content.setSelectionRange(start + variableWithBrackets.length, start + variableWithBrackets.length);
}

// Template kopyalama
function duplicateTemplate() {
    if (confirm("Bu template'i kopyalamak istediğinizden emin misiniz?")) {
        window.location.href = '{{ route("admin.mail-templates.duplicate", $mailTemplate->id) }}';
    }
}

// Test maili gönderme
function testTemplate() {
    window.open('{{ route("admin.mail-templates.test", $mailTemplate->id) }}', '_blank');
}

// Form validation
document.getElementById('mailTemplateForm').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const category = document.getElementById('category').value;
    const subject = document.getElementById('subject').value.trim();
    const content = document.getElementById('content').value.trim();
    
    if (!name || !category || !subject || !content) {
        e.preventDefault();
        alert('Lütfen tüm zorunlu alanları doldurun.');
        return false;
    }
});
</script>
@endpush
@endsection
