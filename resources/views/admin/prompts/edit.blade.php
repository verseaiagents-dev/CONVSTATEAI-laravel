@extends('layouts.admin')

@section('title', 'Prompt Düzenle - Admin Panel')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Prompt Düzenle</h1>
            <p class="text-gray-400 mt-2">{{ $prompt->name }} - v{{ $prompt->version }}</p>
        </div>
        <a href="{{ route('admin.prompts.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Geri Dön</span>
        </a>
    </div>

    <!-- Form -->
    <div class="bg-gray-800/50 rounded-lg border border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.prompts.update', $prompt) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Prompt Adı *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $prompt->name) }}" required
                           class="w-full form-input rounded-lg @error('name') border-red-500 @enderror"
                           placeholder="Örn: Ürün Önerisi Prompt'u">
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-300 mb-2">Kategori *</label>
                    <select id="category" name="category" required
                            class="w-full form-input rounded-lg @error('category') border-red-500 @enderror">
                        <option value="">Kategori Seçin</option>
                        @foreach($categories as $key => $value)
                            <option value="{{ $key }}" {{ old('category', $prompt->category) == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Açıklama</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full form-input rounded-lg @error('description') border-red-500 @enderror"
                          placeholder="Prompt'un ne için kullanıldığını açıklayın">{{ old('description', $prompt->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-300 mb-2">Prompt İçeriği *</label>
                <textarea id="content" name="content" rows="10" required
                          class="w-full form-input rounded-lg @error('content') border-red-500 @enderror"
                          placeholder="Prompt içeriğini buraya yazın. Değişkenler için {variable_name} formatını kullanın.">{{ old('content', $prompt->content) }}</textarea>
                <p class="mt-1 text-sm text-gray-400">Değişkenler için {variable_name} formatını kullanın. Örn: {user_name}, {product_name}</p>
                @error('content')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Settings -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="environment" class="block text-sm font-medium text-gray-300 mb-2">Ortam *</label>
                    <select id="environment" name="environment" required
                            class="w-full form-input rounded-lg @error('environment') border-red-500 @enderror">
                        <option value="test" {{ old('environment', $prompt->environment) == 'test' ? 'selected' : '' }}>Test</option>
                        <option value="production" {{ old('environment', $prompt->environment) == 'production' ? 'selected' : '' }}>Production</option>
                    </select>
                    @error('environment')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-300 mb-2">Öncelik (0-100)</label>
                    <input type="number" id="priority" name="priority" value="{{ old('priority', $prompt->priority) }}" min="0" max="100"
                           class="w-full form-input rounded-lg @error('priority') border-red-500 @enderror">
                    @error('priority')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="language" class="block text-sm font-medium text-gray-300 mb-2">Dil</label>
                    <select id="language" name="language"
                            class="w-full form-input rounded-lg @error('language') border-red-500 @enderror">
                        <option value="tr" {{ old('language', $prompt->language) == 'tr' ? 'selected' : '' }}>Türkçe</option>
                        <option value="en" {{ old('language', $prompt->language) == 'en' ? 'selected' : '' }}>English</option>
                    </select>
                    @error('language')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Variables -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Değişkenler</label>
                <div id="variables-container" class="space-y-2">
                    @if(count($prompt->variables) > 0)
                        @foreach($prompt->variables as $variable)
                            <div class="flex space-x-2">
                                <input type="text" name="variables[]" value="{{ $variable }}" placeholder="Değişken adı" 
                                       class="flex-1 form-input rounded-lg">
                                <button type="button" onclick="removeVariable(this)" class="text-red-400 hover:text-red-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="flex space-x-2">
                            <input type="text" name="variables[]" placeholder="Değişken adı" 
                                   class="flex-1 form-input rounded-lg">
                            <button type="button" onclick="removeVariable(this)" class="text-red-400 hover:text-red-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
                <button type="button" onclick="addVariable()" class="mt-2 text-purple-400 hover:text-purple-300 text-sm">
                    + Değişken Ekle
                </button>
            </div>

            <!-- Tags -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Etiketler</label>
                <div id="tags-container" class="space-y-2">
                    @if(count($prompt->tags) > 0)
                        @foreach($prompt->tags as $tag)
                            <div class="flex space-x-2">
                                <input type="text" name="tags[]" value="{{ $tag }}" placeholder="Etiket" 
                                       class="flex-1 form-input rounded-lg">
                                <button type="button" onclick="removeTag(this)" class="text-red-400 hover:text-red-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="flex space-x-2">
                            <input type="text" name="tags[]" placeholder="Etiket" 
                                   class="flex-1 form-input rounded-lg">
                            <button type="button" onclick="removeTag(this)" class="text-red-400 hover:text-red-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
                <button type="button" onclick="addTag()" class="mt-2 text-purple-400 hover:text-purple-300 text-sm">
                    + Etiket Ekle
                </button>
            </div>

            <!-- Status -->
            <div class="flex items-center space-x-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $prompt->is_active) ? 'checked' : '' }}
                           class="form-checkbox rounded">
                    <span class="ml-2 text-sm text-gray-300">Aktif</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.prompts.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    İptal
                </a>
                <button type="submit" 
                        class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    Güncelle
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function addVariable() {
    const container = document.getElementById('variables-container');
    const div = document.createElement('div');
    div.className = 'flex space-x-2';
    div.innerHTML = `
        <input type="text" name="variables[]" placeholder="Değişken adı" 
               class="flex-1 form-input rounded-lg">
        <button type="button" onclick="removeVariable(this)" class="text-red-400 hover:text-red-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function removeVariable(button) {
    button.parentElement.remove();
}

function addTag() {
    const container = document.getElementById('tags-container');
    const div = document.createElement('div');
    div.className = 'flex space-x-2';
    div.innerHTML = `
        <input type="text" name="tags[]" placeholder="Etiket" 
               class="flex-1 form-input rounded-lg">
        <button type="button" onclick="removeTag(this)" class="text-red-400 hover:text-red-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function removeTag(button) {
    button.parentElement.remove();
}

// Auto-detect variables from content
document.getElementById('content').addEventListener('input', function() {
    const content = this.value;
        const variables = content.match(/\{(\w+)\}/g);
    
    if (variables) {
        const uniqueVariables = [...new Set(variables.map(v => v.replace(/\{|\}/g, '')))];
        const container = document.getElementById('variables-container');
        
        // Clear existing inputs
        container.innerHTML = '';
        
        // Add detected variables
        uniqueVariables.forEach(variable => {
            addVariable();
            container.lastElementChild.querySelector('input').value = variable;
        });
        
        // Add one empty input if no variables
        if (uniqueVariables.length === 0) {
            addVariable();
        }
    }
});
</script>
@endpush
