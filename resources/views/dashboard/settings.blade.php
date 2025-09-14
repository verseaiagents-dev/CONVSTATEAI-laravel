@extends('layouts.dashboard')

@section('title', 'Ayarlar')

@section('content')
<div class="space-y-6">
    <!-- Settings Dashboard Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-4xl font-bold mb-4">
                        <span class="gradient-text">Hesap Ayarları</span>
                    </h1>
                    <p class="text-xl text-gray-300">
                        Güvenlik ve hesap ayarlarınızı buradan yönetebilirsiniz.
                    </p>
                </div>
                

            </div>
        </div>
    </div>

    <!-- Profile Image and User Info -->
    <div class="glass-effect rounded-2xl p-8">
        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400 text-sm">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="flex justify-center">
            <div class="text-center">
                <!-- Avatar -->
                <div class="relative mb-6">
                    <img src="{{ auth()->user()->getAvatarUrl() }}" 
                         alt="Profile Avatar" 
                         class="w-24 h-24 border-4 border-purple-glow/30 rounded-full object-cover mx-auto">
                </div>
                
                <!-- Profile Image Selector -->
                <form method="POST" action="{{ route('dashboard.profile.avatar.update') }}" enctype="multipart/form-data" class="mt-4" id="avatarForm">
                    @csrf
                    <div class="flex flex-col items-center space-y-4">
                        <label for="avatar" class="cursor-pointer">
                            <div class="px-4 py-2 bg-purple-glow/20 hover:bg-purple-glow/30 text-purple-glow rounded-lg border border-purple-glow/30 transition-all duration-200 hover:scale-105">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span id="avatarButtonText">Profil Resmini Değiştir</span>
                            </div>
                        </label>
                        <input type="file" 
                               id="avatar" 
                               name="avatar" 
                               accept="image/*" 
                               class="hidden"
                               onchange="handleAvatarChange(this)">
                        
                        @if(!auth()->user()->avatar)
                            <p class="text-xs text-gray-400 text-center">
                                Varsayılan uygulama resmi kullanılıyor
                            </p>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Profile Information -->
    <div class="glass-effect rounded-2xl p-8">
        <h2 class="text-2xl font-bold mb-6 text-white">Profil Bilgileri</h2>
        
        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400">
                {{ session('success') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('dashboard.profile.update') }}" class="space-y-6">
            @csrf
            
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                    Ad Soyad
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', auth()->user()->name) }}"
                       class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400" 
                       placeholder="Adınızı ve soyadınızı girin"
                       required>
                @error('name')
                    <p class="mt-1 text-red-400 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email (Read-only) -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                    E-posta Adresi
                </label>
                <input type="email" 
                       id="email" 
                       value="{{ auth()->user()->email }}"
                       class="form-input w-full px-4 py-3 rounded-lg text-gray-500 bg-gray-800/50 cursor-not-allowed" 
                       readonly>
                <p class="mt-1 text-gray-400 text-sm">E-posta adresi değiştirilemez</p>
            </div>

            <!-- Bio -->
            <div>
                <label for="bio" class="block text-sm font-medium text-gray-300 mb-2">
                    Hakkımda
                </label>
                <textarea id="bio" 
                          name="bio" 
                          rows="4" 
                          class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400 resize-none" 
                          placeholder="Kendiniz hakkında kısa bir açıklama yazın...">{{ old('bio', auth()->user()->bio) }}</textarea>
                @error('bio')
                    <p class="mt-1 text-red-400 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" 
                        class="px-8 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105">
                    Profili Güncelle
                </button>
            </div>
        </form>
    </div>

    <!-- Password Change -->
    <div class="glass-effect rounded-2xl p-8">
        <h2 class="text-2xl font-bold mb-6 text-white">Şifre Değiştir</h2>
        
        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400">
                {{ session('success') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('dashboard.password.update') }}" class="space-y-6">
            @csrf
            
            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-300 mb-2">
                    Mevcut Şifre
                </label>
                <input type="password" 
                       id="current_password" 
                       name="current_password" 
                       class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400" 
                       placeholder="Mevcut şifrenizi girin"
                       required>
                @error('current_password')
                    <p class="mt-1 text-red-400 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                    Yeni Şifre
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400" 
                       placeholder="En az 8 karakter"
                       required>
                @error('password')
                    <p class="mt-1 text-red-400 text-sm">{{ $message }}</p>
                @enderror
                <div class="mt-2 space-y-1">
                    <div class="flex items-center space-x-2">
                        <div id="length-check" class="w-2 h-2 rounded-full bg-gray-600"></div>
                        <span class="text-xs text-gray-400">En az 8 karakter</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div id="uppercase-check" class="w-2 h-2 rounded-full bg-gray-600"></div>
                        <span class="text-xs text-gray-400">Büyük harf</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div id="number-check" class="w-2 h-2 rounded-full bg-gray-600"></div>
                        <span class="text-xs text-gray-400">Rakam</span>
                    </div>
                </div>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                    Yeni Şifre Tekrar
                </label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400" 
                       placeholder="Yeni şifrenizi tekrar girin"
                       required>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" 
                        class="px-8 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105">
                    Şifreyi Güncelle
                </button>
            </div>
        </form>
    </div>

    <!-- Account Info -->
    <div class="glass-effect rounded-2xl p-8">
        <h2 class="text-2xl font-bold mb-6 text-white">Hesap Bilgileri</h2>
        
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Member Since -->
            <div class="p-4 bg-gray-800/30 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-purple-glow/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Üye Olma Tarihi</p>
                        <p class="text-white font-medium">{{ auth()->user()->created_at->format('d.m.Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Last Login -->
            <div class="p-4 bg-gray-800/30 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-neon-purple/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-neon-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Son Giriş</p>
                        <p class="text-white font-medium">{{ auth()->user()->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            <!-- User ID -->
            <div class="p-4 bg-gray-800/30 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-purple-dark/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Kullanıcı ID</p>
                        <p class="text-white font-medium">#{{ auth()->user()->id }}</p>
                    </div>
                </div>
            </div>

            <!-- Account Status -->
            <div class="p-4 bg-gray-800/30 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Hesap Durumu</p>
                        <p class="text-green-400 font-medium">Aktif</p>
                    </div>
                </div>
            </div>
        </div>
       
            <div class="h-10"></div>
          <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" 
                      class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center space-x-2"
                      onclick="return confirm('Hesabınızdan çıkış yapmak istediğinizden emin misiniz?')">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                  </svg>
                  <span>Çıkış Yap</span>
              </button>
          </form>
  
    </div>





</div>

<script>
    // Password strength checker
    const passwordInput = document.getElementById('password');
    
    function checkPasswordStrength(password) {
        const checks = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            number: /\d/.test(password)
        };

        // Update visual indicators
        document.getElementById('length-check').className = `w-2 h-2 rounded-full ${checks.length ? 'bg-green-400' : 'bg-gray-600'}`;
        document.getElementById('uppercase-check').className = `w-2 h-2 rounded-full ${checks.uppercase ? 'bg-green-400' : 'bg-gray-600'}`;
        document.getElementById('number-check').className = `w-2 h-2 rounded-full ${checks.number ? 'bg-green-400' : 'bg-gray-600'}`;
    }

    passwordInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
    });

    // Avatar change handler
    function handleAvatarChange(input) {
        if (input.files && input.files[0]) {
            const buttonText = document.getElementById('avatarButtonText');
            const originalText = buttonText.textContent;
            
            // Show loading state
            buttonText.textContent = 'Yükleniyor...';
            buttonText.parentElement.classList.add('opacity-75');
            
            // Create FormData for AJAX upload
            const formData = new FormData();
            formData.append('avatar', input.files[0]);
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            
            // Try AJAX first, fallback to normal form submission
            try {
                // AJAX upload
                fetch('{{ route("dashboard.profile.avatar.update") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    
                    if (data.success) {
                        // Update all avatar images on the page
                        updateAllAvatars(data.avatar_url);
                        
                        // Show success message
                        showNotification('Profil resmi başarıyla güncellendi!', 'success');
                        
                        // Reset button text
                        buttonText.textContent = originalText;
                        buttonText.parentElement.classList.remove('opacity-75');
                        
                        // Clear file input
                        input.value = '';
                    } else {
                        throw new Error(data.message || 'Sunucu başarısız yanıt döndürdü');
                    }
                })
                .catch(error => {
                    console.error('AJAX error:', error);
                    showNotification(`AJAX hatası, normal form submission kullanılıyor...`, 'info');
                    
                    // Fallback to normal form submission
                    setTimeout(() => {
                        input.form.submit();
                    }, 1000);
                });
            } catch (error) {
                console.error('AJAX setup error:', error);
                showNotification('AJAX kurulum hatası, normal form submission kullanılıyor...', 'info');
                
                // Fallback to normal form submission
                setTimeout(() => {
                    input.form.submit();
                }, 1000);
            }
        }
    }

    // Update all avatar images on the page
    function updateAllAvatars(newAvatarUrl) {
        // Update main profile avatar
        const mainAvatar = document.querySelector('.glass-effect img[alt="Profile Avatar"]');
        if (mainAvatar) {
            mainAvatar.src = newAvatarUrl;
        }
        
        // Update sidebar avatar (if exists)
        const sidebarAvatar = document.querySelector('.sidebar img[alt*="avatar"], .sidebar img[alt*="profile"], .sidebar .avatar img');
        if (sidebarAvatar) {
            sidebarAvatar.src = newAvatarUrl;
        }
        
        // Update any other avatar images on the page
        const allAvatars = document.querySelectorAll('img[src*="avatars"], img[alt*="avatar"], img[alt*="profile"]');
        allAvatars.forEach(avatar => {
            if (avatar.src.includes('avatars') || avatar.alt.toLowerCase().includes('avatar') || avatar.alt.toLowerCase().includes('profile')) {
                avatar.src = newAvatarUrl;
            }
        });
        
        // Update avatar in any dropdown menus
        const dropdownAvatars = document.querySelectorAll('.dropdown img[src*="avatars"], .user-menu img[src*="avatars"]');
        dropdownAvatars.forEach(avatar => {
            avatar.src = newAvatarUrl;
        });
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-medium transition-all duration-300 transform translate-x-full ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            'bg-blue-500'
        }`;
        notification.textContent = message;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }


</script>
@endsection
