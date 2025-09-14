@extends('layouts.admin')

@section('title', 'Yeni Mail Template Oluştur')

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
                        Yeni <span class="gradient-text">Mail Template</span> Oluştur
                    </h1>
                    <p class="text-xl text-gray-300">
                        Kullanıcılara gönderilecek mail template'ini oluşturun
                    </p>
                </div>
                <a href="{{ route('admin.mail-templates.index') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Geri Dön
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="glass-effect rounded-2xl p-8">
        <form action="{{ route('admin.mail-templates.store') }}" method="POST" id="mailTemplateForm">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Sol Kolon -->
                <div class="space-y-6">
                    <!-- Template Adı -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                            Template Adı <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
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
                                <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
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
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
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
                                  placeholder="Template hakkında kısa açıklama">{old('description')}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Durum -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="form-checkbox mr-3">
                            <span class="text-sm font-medium text-gray-300">Template Aktif</span>
                        </label>
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
                            <!-- Debug bilgisi -->
                            @if(isset($variables))
                                <div class="mb-3 p-2 bg-blue-900/30 rounded text-xs text-blue-300">
                                    <strong>Debug:</strong> {{ count($variables) }} değişken bulundu
                                </div>
                            @else
                                <div class="mb-3 p-2 bg-red-900/30 rounded text-xs text-red-300">
                                    <strong>Hata:</strong> $variables tanımlı değil
                                </div>
                            @endif
                            
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                @if(isset($variables) && is_array($variables))
                                    @foreach($variables as $variable => $description)
                                        <div class="flex items-center space-x-2">
                                            <code class="text-purple-400 bg-gray-700 px-2 py-1 rounded text-xs">{{ $variable }}</code>
                                            <span class="text-gray-400">{{ $description }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-span-2 text-red-400 text-xs">
                                        Değişkenler yüklenemedi. Lütfen sayfayı yenileyin.
                                    </div>
                                @endif
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
                                    <strong>Konu:</strong> <span id="subjectPreview" class="text-white">Mail konusu burada görünecek</span>
                                </div>
                                <div>
                                    <strong>Kategori:</strong> <span id="categoryPreview" class="text-white">Seçilen kategori</span>
                                </div>
                            </div>
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
                              placeholder="HTML mail içeriğini buraya yazın...">{old('content')}</textarea>
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
                    Template Oluştur
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
    
    // Değişkeni {{}} formatında ekle
    const variableWithBrackets = '{{' + variable + '}}';
    content.value = text.substring(0, start) + variableWithBrackets + text.substring(end);
    content.focus();
    content.setSelectionRange(start + variableWithBrackets.length, start + variableWithBrackets.length);
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
