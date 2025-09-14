@auth
    <script>window.location.href = '{{ route("dashboard") }}';</script>
@endauth

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Giriş Yap - ConvStateAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'purple-glow': '#8B5CF6',
                        'purple-dark': '#4C1D95',
                        'neon-purple': '#A855F7'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' }
                        },
                        glow: {
                            '0%': { boxShadow: '0 0 20px #8B5CF6' },
                            '100%': { boxShadow: '0 0 40px #8B5CF6, 0 0 60px #8B5CF6' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #8B5CF6, #A855F7, #EC4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .form-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .form-input:focus {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
            background: rgba(255, 255, 255, 0.1);
        }
        .form-input:hover {
            border-color: rgba(255, 255, 255, 0.3);
        }
        .form-checkbox {
            appearance: none;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.375rem;
            width: 1.25rem;
            height: 1.25rem;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .form-checkbox:checked {
            background: linear-gradient(135deg, #8B5CF6, #A855F7);
            border-color: #8B5CF6;
        }
        .form-checkbox:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-black text-white">
    <!-- Background Effects -->
    <div class="absolute inset-0">
        <div class="absolute top-20 left-20 w-72 h-72 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float" style="animation-delay: -2s;"></div>
        <div class="absolute bottom-20 left-1/2 w-80 h-80 bg-purple-dark rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float" style="animation-delay: -4s;"></div>
    </div>

    <!-- Navigation -->
    <nav class="relative z-50 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <a href="/" class="flex items-center">
                                            <img src="{{ asset('imgs/ai-conversion-logo.svg') }}" alt="ConvStateAI Logo" class="w-10 h-10">
                        <span class="ml-3 text-xl font-bold">ConvStateAI</span>
                </a>
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-gray-300 hover:text-purple-glow transition-colors">Ana Sayfa</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-purple-glow hover:text-white transition-colors">Kayıt Ol</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="min-h-screen flex items-center justify-center relative z-10 px-4">
        <div class="max-w-md w-full">
            <div class="glass-effect rounded-3xl p-8 relative overflow-hidden">
                <!-- Background Effects -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
                
                <div class="relative z-10">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold mb-2">
                            <span class="gradient-text">Giriş Yap</span>
                        </h1>
                        <p class="text-gray-400">ConvStateAI hesabınıza erişin</p>
                    </div>

                    @if (session('status'))
                        <div class="mb-4 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                                E-posta Adresi
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400" 
                                   placeholder="ornek@email.com"
                                   required 
                                   autofocus>
                            @error('email')
                                <p class="mt-1 text-red-400 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                                Şifre
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400" 
                                   placeholder="Şifrenizi girin"
                                   required>
                            @error('password')
                                <p class="mt-1 text-red-400 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="form-checkbox">
                                <span class="text-sm text-gray-300">Beni hatırla</span>
                            </label>
                            <a href="{{ route('password.request') }}" class="text-sm text-purple-glow hover:text-neon-purple transition-colors">
                                Şifremi unuttum
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105 animate-glow">
                            Giriş Yap
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="my-6 flex items-center">
                        <div class="flex-1 border-t border-gray-700"></div>
                        <span class="px-4 text-gray-400 text-sm">veya</span>
                        <div class="flex-1 border-t border-gray-700"></div>
                    </div>

                    <!-- Register Link -->
                    <div class="text-center">
                        <p class="text-gray-400">
                            Hesabınız yok mu? 
                            <a href="{{ route('register') }}" class="text-purple-glow hover:text-neon-purple font-semibold transition-colors">
                                Hemen kayıt olun
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="relative z-10 py-8 text-center">
        <p class="text-gray-400 text-sm">
            © 2024 ConvStateAI. Tüm hakları saklıdır.
        </p>
    </footer>

    <script>
        // Form submission tracking
        let formSubmitted = false;
        
        // Track form submission
        document.querySelector('form').addEventListener('submit', function() {
            formSubmitted = true;
        });
    </script>
</body>
</html>
