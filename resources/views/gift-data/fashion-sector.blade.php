@extends('layouts.gift_sectors')

@section('title', 'Moda Sektörü - ConvStateAI')

@section('hero_title')
<h3 class="text-5xl sm:text-3xl md:text-5xl font-bold mb-6">
     <span class="gradient-text">Giyim Sektöründe</span><div class="h-3 sm:h-5"></div>  
 Dijital Mağazalarınızı Büyütme Sırlarını Keşfedin
 </h3>
@endsection

@section('hero_subtitle')
<p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-2xl">
Giyim sektörü liderlerinin sır gibi sakladığı E-ticaret mağazalarını büyütme sırlarını edinin.  
</p>
@endsection

@section('hero_buttons')
<div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start items-center mb-8">
    <button onclick="openDemoModal('fashion')" class="flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-purple-glow to-neon-purple rounded-xl text-lg font-semibold hover:from-purple-dark hover:to-purple-glow transition-all duration-300 transform hover:scale-105 animate-glow">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
        </svg>
        Hemen İndir
    </button>
</div>
@endsection








@section('cta_section')
<div class="max-w-4xl mx-auto text-center px-4">
    <div class="glass-effect rounded-3xl p-12 relative overflow-hidden">
        <!-- Background Effects -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">
                <span class="gradient-text">Dönüşüm oranınızı artırın</span> 
            </h2>
            <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                ConvStateAI ile moda e-ticaret sitenizi geleceğe taşıyın
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-6">
                <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient-to-r from-purple-glow to-neon-purple rounded-xl text-lg font-semibold hover:from-purple-dark hover:to-purple-glow transition-all duration-300 transform hover:scale-105 animate-glow flex items-center justify-center">
                 Hemen Kayıt Ol
                </a>
            </div>
            @guest
            <p class="text-sm text-gray-400 mt-6">
                Zaten hesabınız var mı? <a href="{{ route('login') }}" class="text-purple-glow hover:text-neon-purple">Giriş yapın</a>
            </p>
            @endguest
        </div>
    </div>
</div>
@endsection

@section('hero_widget')
<!-- Fashion Photo Gallery Layout -->
<div class="glass-effect rounded-3xl p-6 max-w-4xl mx-auto">
    <div class="fashion-gallery-container relative w-full h-96 overflow-hidden rounded-2xl">
        <!-- Main Large Image (Left) -->
        <div class="main-image-container absolute left-0 top-0 w-2/3 h-full transition-all duration-1000 ease-in-out">
            <div class="main-image w-full h-full overflow-hidden rounded-2xl">
                <img src="/imgs/giftpage/fashion/2.png" alt="Fashion" class="w-full h-full object-cover rounded-2xl">
            </div>
        </div>
        
        <!-- Small Images Stack (Right) -->
        <div class="small-images-container absolute right-0 top-0 w-1/3 h-full flex flex-col space-y-2 p-2">
            <!-- Small Image 1 (Top) -->
            <div class="small-image-container flex-1 transition-all duration-1000 ease-in-out">
                <div class="small-image w-full h-full overflow-hidden rounded-2xl">
                    <img src="/imgs/giftpage/fashion/3.png" alt="Fashion" class="w-full h-full object-cover rounded-2xl">
                </div>
            </div>
            
            <!-- Small Image 2 (Middle) -->
            <div class="small-image-container flex-1 transition-all duration-1000 ease-in-out">
                <div class="small-image w-full h-full overflow-hidden rounded-2xl">
                    <img src="/imgs/giftpage/fashion/4.png" alt="Fashion" class="w-full h-full object-cover rounded-2xl">
                </div>
            </div>
            
            <!-- Small Image 3 (Bottom) -->
            <div class="small-image-container flex-1 transition-all duration-1000 ease-in-out">
                <div class="small-image w-full h-full overflow-hidden rounded-2xl">
                    <img src="/imgs/giftpage/fashion/5.png" alt="Fashion" class="w-full h-full object-cover rounded-2xl">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Elements -->
<div class="absolute -top-4 -left-4 w-8 h-8 bg-green-400 rounded-full flex items-center justify-center animate-bounce">
    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
</div>
<div class="absolute -bottom-4 -right-4 w-8 h-8 bg-blue-400 rounded-full flex items-center justify-center animate-bounce" style="animation-delay: 0.5s;">
    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
    </svg>
</div>

<style>
/* Fashion Gallery Styles */
.fashion-gallery-container {
    perspective: 1000px;
}

.main-image-container {
    transform-style: preserve-3d;
    backface-visibility: hidden;
}

.small-image-container {
    transform-style: preserve-3d;
    backface-visibility: hidden;
}

.main-image, .small-image {
    transform-style: preserve-3d;
    backface-visibility: hidden;
}

/* Gallery rotation states */
.gallery-state-1 .main-image-container { transform: translateX(0) scale(1); }
.gallery-state-1 .small-image-container:nth-child(1) { transform: translateX(0) scale(0.8); }
.gallery-state-1 .small-image-container:nth-child(2) { transform: translateX(0) scale(0.8); }
.gallery-state-1 .small-image-container:nth-child(3) { transform: translateX(0) scale(0.8); }

.gallery-state-2 .main-image-container { transform: translateX(0) scale(1); }
.gallery-state-2 .small-image-container:nth-child(1) { transform: translateX(0) scale(0.8); }
.gallery-state-2 .small-image-container:nth-child(2) { transform: translateX(0) scale(0.8); }
.gallery-state-2 .small-image-container:nth-child(3) { transform: translateX(0) scale(0.8); }

.gallery-state-3 .main-image-container { transform: translateX(0) scale(1); }
.gallery-state-3 .small-image-container:nth-child(1) { transform: translateX(0) scale(0.8); }
.gallery-state-3 .small-image-container:nth-child(2) { transform: translateX(0) scale(0.8); }
.gallery-state-3 .small-image-container:nth-child(3) { transform: translateX(0) scale(0.8); }

.gallery-state-4 .main-image-container { transform: translateX(0) scale(1); }
.gallery-state-4 .small-image-container:nth-child(1) { transform: translateX(0) scale(0.8); }
.gallery-state-4 .small-image-container:nth-child(2) { transform: translateX(0) scale(0.8); }
.gallery-state-4 .small-image-container:nth-child(3) { transform: translateX(0) scale(0.8); }

/* Responsive adjustments */
@media (max-width: 768px) {
    .fashion-gallery-container {
        height: 80vh;
        max-height: 400px;
    }
    
    .main-image-container {
        width: 60%;
    }
    
    .small-images-container {
        width: 40%;
    }
}

@media (max-width: 640px) {
    .fashion-gallery-container {
        height: 70vh;
        max-height: 350px;
    }
    
    .main-image-container {
        width: 100%;
        height: 60%;
    }
    
    .small-images-container {
        width: 100%;
        height: 40%;
        flex-direction: row;
        top: 60%;
    }
    
    .small-image-container {
        flex: 1;
        margin-right: 0.5rem;
    }
    
    .small-image-container:last-child {
        margin-right: 0;
    }
}
</style>

<script>
// Fashion Gallery Rotation Functionality
let currentState = 1;
const totalStates = 4;
let galleryInterval;

// Image data for rotation - only image paths, no background colors
const imageData = [
    {
        main: { img: '/imgs/giftpage/fashion/2.png' },
        small1: { img: '/imgs/giftpage/fashion/3.png' },
        small2: { img: '/imgs/giftpage/fashion/4.png' },
        small3: { img: '/imgs/giftpage/fashion/5.png' }
    },
    {
        main: { img: '/imgs/giftpage/fashion/3.png' },
        small1: { img: '/imgs/giftpage/fashion/4.png' },
        small2: { img: '/imgs/giftpage/fashion/5.png' },
        small3: { img: '/imgs/giftpage/fashion/2.png' }
    },
    {
        main: { img: '/imgs/giftpage/fashion/4.png' },
        small1: { img: '/imgs/giftpage/fashion/5.png' },
        small2: { img: '/imgs/giftpage/fashion/2.png' },
        small3: { img: '/imgs/giftpage/fashion/3.png' }
    },
    {
        main: { img: '/imgs/giftpage/fashion/5.png' },
        small1: { img: '/imgs/giftpage/fashion/2.png' },
        small2: { img: '/imgs/giftpage/fashion/3.png' },
        small3: { img: '/imgs/giftpage/fashion/4.png' }
    }
];

function updateGallery(state) {
    const data = imageData[state - 1];
    const gallery = document.querySelector('.fashion-gallery-container');
    
    if (!gallery) return;
    
    // Update main image
    const mainImage = gallery.querySelector('.main-image');
    if (mainImage) {
        const img = mainImage.querySelector('img');
        if (img) {
            img.src = data.main.img;
        }
    }
    
    // Update small images
    const smallImages = gallery.querySelectorAll('.small-image');
    smallImages.forEach((img, index) => {
        const smallData = [data.small1, data.small2, data.small3][index];
        const imgElement = img.querySelector('img');
        if (imgElement) {
            imgElement.src = smallData.img;
        }
    });
    
    // Update gallery state class
    gallery.className = `fashion-gallery-container relative w-full h-96 overflow-hidden rounded-2xl gallery-state-${state}`;
    
    currentState = state;
}

function nextGalleryState() {
    const next = currentState === totalStates ? 1 : currentState + 1;
    updateGallery(next);
}

function startGalleryRotation() {
    galleryInterval = setInterval(nextGalleryState, 2000); // Change every 2 seconds
}

function stopGalleryRotation() {
    if (galleryInterval) {
        clearInterval(galleryInterval);
    }
}

// Initialize gallery when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Start auto-rotation
    startGalleryRotation();
    
    // Pause on hover
    const galleryContainer = document.querySelector('.fashion-gallery-container');
    if (galleryContainer) {
        galleryContainer.addEventListener('mouseenter', stopGalleryRotation);
        galleryContainer.addEventListener('mouseleave', startGalleryRotation);
    }
});
</script>
@endsection
