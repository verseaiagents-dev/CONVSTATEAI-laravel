@extends('layouts.gift_sectors')

@section('title', 'Mobilya Sektörü - ConvStateAI')

@section('hero_title')
<h3 class="text-5xl sm:text-3xl md:text-5xl font-bold mb-6">
     <span class="gradient-text">Mobilya Sektöründe</span><div class="h-3 sm:h-5"></div>  
 Dijital Mağazalarınızı Büyütme Sırlarını Keşfedin
 </h3>
@endsection

@section('hero_subtitle')
<p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-2xl">
Mobilya sektörü liderlerinin sır gibi sakladığı E-ticaret mağazalarını büyütme sırlarını edinin.  
</p>
@endsection

@section('hero_buttons')
<div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start items-center mb-8">
    <button onclick="openDemoModal('furniture')" class="flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-purple-glow to-neon-purple rounded-xl text-lg font-semibold hover:from-purple-dark hover:to-purple-glow transition-all duration-300 transform hover:scale-105 animate-glow">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
        </svg>
        Hemen İndir
    </button>
</div>
@endsection

@section('hero_widget')
<!-- Furniture Photo Gallery Layout -->
<div class="glass-effect rounded-3xl p-6 max-w-4xl mx-auto">
    <div class="furniture-gallery-container relative w-full h-96 overflow-hidden rounded-2xl">
        <!-- Main Large Image (Left) -->
        <div class="main-image-container absolute left-0 top-0 w-2/3 h-full transition-all duration-1000 ease-in-out">
            <div class="main-image w-full h-full overflow-hidden rounded-2xl">
                <img src="/imgs/giftpage/furniture/1.jpg" alt="Furniture" class="w-full h-full object-cover rounded-2xl">
            </div>
        </div>
        
        <!-- Small Images Stack (Right) -->
        <div class="small-images-container absolute right-0 top-0 w-1/3 h-full flex flex-col space-y-2 p-2">
            <!-- Small Image 1 (Top) -->
            <div class="small-image-container flex-1 transition-all duration-1000 ease-in-out">
                <div class="small-image w-full h-full overflow-hidden rounded-2xl">
                    <img src="/imgs/giftpage/furniture/2.jpg" alt="Furniture" class="w-full h-full object-cover rounded-2xl">
                </div>
            </div>
            
            <!-- Small Image 2 (Middle) -->
            <div class="small-image-container flex-1 transition-all duration-1000 ease-in-out">
                <div class="small-image w-full h-full overflow-hidden rounded-2xl">
                    <img src="/imgs/giftpage/furniture/3.jpg" alt="Furniture" class="w-full h-full object-cover rounded-2xl">
                </div>
            </div>
            
            <!-- Small Image 3 (Bottom) -->
            <div class="small-image-container flex-1 transition-all duration-1000 ease-in-out">
                <div class="small-image w-full h-full overflow-hidden rounded-2xl">
                    <img src="/imgs/giftpage/furniture/4.jpg" alt="Furniture" class="w-full h-full object-cover rounded-2xl">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Elements -->
<div class="absolute -top-4 -left-4 w-8 h-8 bg-orange-400 rounded-full flex items-center justify-center animate-bounce">
    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
    </svg>
</div>
<div class="absolute -bottom-4 -right-4 w-8 h-8 bg-amber-400 rounded-full flex items-center justify-center animate-bounce" style="animation-delay: 0.5s;">
    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
    </svg>
</div>

<style>
/* Furniture Gallery Styles */
.furniture-gallery-container {
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

/* Responsive adjustments */
@media (max-width: 768px) {
    .furniture-gallery-container {
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
    .furniture-gallery-container {
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
// Furniture Gallery Rotation Functionality
let currentState = 1;
const totalStates = 4;
let galleryInterval;

// Image data for rotation
const imageData = [
    {
        main: { img: '/imgs/giftpage/furniture/1.jpg' },
        small1: { img: '/imgs/giftpage/furniture/2.jpg' },
        small2: { img: '/imgs/giftpage/furniture/3.jpg' },
        small3: { img: '/imgs/giftpage/furniture/4.jpg' }
    },
    {
        main: { img: '/imgs/giftpage/furniture/2.jpg' },
        small1: { img: '/imgs/giftpage/furniture/3.jpg' },
        small2: { img: '/imgs/giftpage/furniture/4.jpg' },
        small3: { img: '/imgs/giftpage/furniture/1.jpg' }
    },
    {
        main: { img: '/imgs/giftpage/furniture/3.jpg' },
        small1: { img: '/imgs/giftpage/furniture/4.jpg' },
        small2: { img: '/imgs/giftpage/furniture/1.jpg' },
        small3: { img: '/imgs/giftpage/furniture/2.jpg' }
    },
    {
        main: { img: '/imgs/giftpage/furniture/4.jpg' },
        small1: { img: '/imgs/giftpage/furniture/1.jpg' },
        small2: { img: '/imgs/giftpage/furniture/2.jpg' },
        small3: { img: '/imgs/giftpage/furniture/3.jpg' }
    }
];

function updateGallery(state) {
    const data = imageData[state - 1];
    const gallery = document.querySelector('.furniture-gallery-container');
    
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
    
    currentState = state;
}

function nextGalleryState() {
    const next = currentState === totalStates ? 1 : currentState + 1;
    updateGallery(next);
}

function startGalleryRotation() {
    galleryInterval = setInterval(nextGalleryState, 3000); // Change every 3 seconds
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
    const galleryContainer = document.querySelector('.furniture-gallery-container');
    if (galleryContainer) {
        galleryContainer.addEventListener('mouseenter', stopGalleryRotation);
        galleryContainer.addEventListener('mouseleave', startGalleryRotation);
    }
});
</script>
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
                ConvStateAI ile mobilya e-ticaret sitenizi geleceğe taşıyın
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
