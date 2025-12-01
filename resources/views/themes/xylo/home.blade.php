@extends('themes.xylo.layouts.master')

@section('css')
<style>
/* Hero Section Styles */
.hero-section {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    min-height: 600px;
}

.hero-slider .slick-slide {
    opacity: 0.3;
    transition: opacity 0.5s ease;
}

.hero-slider .slick-slide.slick-active {
    opacity: 1;
}

.hero-slide {
    position: relative;
    min-height: 600px;
    display: flex !important;
    align-items: center;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    color: #fff;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    opacity: 0;
    transform: translateY(30px);
    animation: slideUp 0.8s ease forwards;
    animation-delay: 0.3s;
}

.hero-title span {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: 1.25rem;
    color: rgba(255,255,255,0.8);
    margin-bottom: 2rem;
    opacity: 0;
    transform: translateY(30px);
    animation: slideUp 0.8s ease forwards;
    animation-delay: 0.5s;
}

.hero-buttons {
    opacity: 0;
    transform: translateY(30px);
    animation: slideUp 0.8s ease forwards;
    animation-delay: 0.7s;
}

.btn-hero {
    padding: 15px 40px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 50px;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
}

.btn-hero-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: #fff;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.btn-hero-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
    color: #fff;
}

.btn-hero-outline {
    background: transparent;
    border: 2px solid rgba(255,255,255,0.5);
    color: #fff;
    margin-left: 15px;
}

.btn-hero-outline:hover {
    background: #fff;
    color: #1a1a2e;
    border-color: #fff;
}

.hero-image-wrapper {
    position: relative;
    opacity: 0;
    transform: scale(0.8) translateX(50px);
    animation: zoomIn 1s ease forwards;
    animation-delay: 0.5s;
}

.hero-image {
    max-height: 500px;
    width: auto;
    filter: drop-shadow(0 30px 60px rgba(0,0,0,0.4));
    animation: float 4s ease-in-out infinite;
}

.hero-shape {
    position: absolute;
    border-radius: 50%;
    opacity: 0.1;
}

.hero-shape-1 {
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    top: -100px;
    right: -100px;
    animation: pulse 4s ease-in-out infinite;
}

.hero-shape-2 {
    width: 200px;
    height: 200px;
    background: #fff;
    bottom: 50px;
    left: 10%;
    animation: pulse 3s ease-in-out infinite 1s;
}

.hero-dots {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
}

.hero-slider .slick-dots {
    bottom: 30px;
}

.hero-slider .slick-dots li button:before {
    font-size: 12px;
    color: rgba(255,255,255,0.5);
}

.hero-slider .slick-dots li.slick-active button:before {
    color: #667eea;
}

.hero-slider .slick-prev,
.hero-slider .slick-next {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    z-index: 10;
    transition: all 0.3s ease;
}

.hero-slider .slick-prev:hover,
.hero-slider .slick-next:hover {
    background: rgba(255,255,255,0.2);
}

.hero-slider .slick-prev {
    left: 30px;
}

.hero-slider .slick-next {
    right: 30px;
}

.hero-slider .slick-prev:before,
.hero-slider .slick-next:before {
    font-size: 24px;
    opacity: 1;
}

/* Animations */
@keyframes slideUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes zoomIn {
    to {
        opacity: 1;
        transform: scale(1) translateX(0);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 0.1;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.15;
    }
}

/* Stats Bar */
.hero-stats {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(10px);
    padding: 20px 0;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.stat-item {
    text-align: center;
    color: #fff;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.stat-label {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.7);
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    .hero-slide {
        min-height: 500px;
    }
    .hero-image {
        max-height: 300px;
    }
    .btn-hero {
        padding: 12px 25px;
        font-size: 0.9rem;
    }
    .btn-hero-outline {
        margin-left: 0;
        margin-top: 10px;
    }
}
</style>
@endsection

@section('content')
    @php $currency = activeCurrency(); @endphp
    
    {{-- Hero Section Start --}}
    <section class="hero-section">
        <!-- Background Shapes -->
        <div class="hero-shape hero-shape-1"></div>
        <div class="hero-shape hero-shape-2"></div>
        
        <div class="hero-slider">
            @if(isset($heroSlides) && $heroSlides->count() > 0)
                {{-- Slides from database --}}
                @foreach($heroSlides as $slide)
                <div class="hero-slide" style="background: {{ $slide->background_color }};">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-6 col-md-6">
                                <div class="hero-content">
                                    <h1 class="hero-title" style="color: {{ $slide->text_color }};">
                                        <span>{{ $slide->title }}</span>
                                    </h1>
                                    @if($slide->subtitle)
                                    <p class="hero-subtitle" style="color: {{ $slide->text_color }};">{{ $slide->subtitle }}</p>
                                    @endif
                                    <div class="hero-buttons">
                                        <a href="{{ $slide->button_link }}" class="btn btn-hero btn-hero-primary">
                                            <i class="fa fa-shopping-bag me-2"></i>{{ $slide->button_text }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="hero-image-wrapper text-center">
                                    @if($slide->image)
                                        <img src="{{ asset('storage/' . $slide->image) }}" 
                                             class="hero-image img-fluid" 
                                             alt="{{ $slide->title }}">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                {{-- Default slides if no slides in database --}}
                <div class="hero-slide">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-6 col-md-6">
                                <div class="hero-content">
                                    <h1 class="hero-title">
                                        <span>Nouveautés</span>
                                        <br>2025
                                    </h1>
                                    <p class="hero-subtitle">Découvrez notre nouvelle collection avec les dernières tendances mode.</p>
                                    <div class="hero-buttons">
                                        <a href="{{ route('shop.index') }}" class="btn btn-hero btn-hero-primary">
                                            <i class="fa fa-shopping-bag me-2"></i>Explorer
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="hero-image-wrapper text-center">
                                    <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600" 
                                         class="hero-image img-fluid" 
                                         alt="Nouveautés">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="hero-slide" style="background: linear-gradient(135deg, #0f3460 0%, #16213e 50%, #1a1a2e 100%);">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-6 col-md-6">
                                <div class="hero-content">
                                    <h1 class="hero-title">
                                        <span>Best</span>
                                        <br>Sellers
                                    </h1>
                                    <p class="hero-subtitle">Les produits les plus appréciés par nos clients.</p>
                                    <div class="hero-buttons">
                                        <a href="{{ route('shop.index') }}" class="btn btn-hero btn-hero-primary">
                                            <i class="fa fa-fire me-2"></i>Voir les tops
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="hero-image-wrapper text-center">
                                    <img src="https://images.unsplash.com/photo-1549298916-b41d501d3772?w=600" 
                                         class="hero-image img-fluid" 
                                         alt="Best Sellers">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="hero-slide" style="background: linear-gradient(135deg, #1a1a2e 0%, #4a1942 50%, #6b2450 100%);">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-6 col-md-6">
                                <div class="hero-content">
                                    <h1 class="hero-title">
                                        <span>-50%</span>
                                        <br>Promos
                                    </h1>
                                    <p class="hero-subtitle">Profitez de réductions exceptionnelles sur des centaines d'articles.</p>
                                    <div class="hero-buttons">
                                        <a href="{{ route('shop.index') }}" class="btn btn-hero btn-hero-primary">
                                            <i class="fa fa-tag me-2"></i>Voir les promos
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="hero-image-wrapper text-center">
                                    <img src="https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600" 
                                         class="hero-image img-fluid" 
                                         alt="Promotions">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Stats Bar -->
        <div class="hero-stats d-none d-md-block">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Produits</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">50+</div>
                            <div class="stat-label">Boutiques</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">10K+</div>
                            <div class="stat-label">Clients satisfaits</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">24/7</div>
                            <div class="stat-label">Support</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Hero Section End --}}
    <section class="cat-slider animate-on-scroll">
        <div class="container">
            <h2 class="text-start pb-5 sec-heading">{{ __('store.home.explore_popular_categories') }}</h2>
            <div class="category-slider">
                @foreach($categories as $category)
                <div>
                    <div class="cat-card">
                        <a href="{{ route('category.show', $category->slug) }}">
                            <h3>{{ $category->translation->name ?? 'No Translation' }}</h3>
                            <div class="catcard-img">
                                <img src="{{ Storage::url(optional($category->translation)->image_url ?? 'default.jpg') }}" alt="{{ $category->translation->name ?? 'No Translation' }}">
                            </div>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="trending-products animate-on-scroll">
        <div class="container position-relative">
            <h1 class="text-start pb-5 sec-heading">{{ __('store.home.trending_products') }}</h1>

            <div class="product-slider">
                @foreach ($products as $product)
                    <div class="product-card">
                        <div class="product-img">
                            <img src="{{ Storage::url(optional($product->thumbnail)->image_url ?? 'default.jpg') }}" 
                                alt="{{ $product->translation->name ?? 'Product Name Not Available' }}">
                                <button class="wishlist-btn" data-product-id="{{ $product->id }}">
                                    <i class="fa-solid fa-heart"></i>
                                </button>
                        </div>
                        <div class="product-info mt-4">
                            <div class="top-info">
                                <div class="reviews">
                                    <i class="fa-solid fa-star"></i> ({{ $product->reviews_count }} {{ __('store.home.reviews') }})
                                </div>
                            </div>
                            <div class="bottom-info">
                                <div class="left">
                                    <h3>
                                        <a href="{{ route('product.show', $product->slug) }}" class="product-title">
                                            {{ $product->translation->name ?? 'Product Name Not Available' }}
                                        </a>
                                    </h3>
                                    <p class="price">
                                        <span class="original {{ optional($product->primaryVariant)->converted_discount_price ? 'has-discount' : '' }}">
                                            {{ $currency->symbol }}{{ optional($product->primaryVariant)->converted_price ?? 'N/A' }}
                                        </span>

                                        @if(optional($product->primaryVariant)->converted_discount_price)
                                            <span class="discount"> 
                                                {{ $currency->symbol }}{{ $product->primaryVariant->converted_discount_price }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <button class="cart-btn" onclick="addToCart({{ $product->id }})">
                                    <i class="fa fa-shopping-bag"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Custom Arrows -->
            <div class="custom-arrows">
                <button class="prev"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="next"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>
    </section>


    <section class="sale-banner pt-5 pb-5 animate-on-scroll">
        <div class="container">
            <div class="sale-banner-content text-center py-5 px-4 rounded-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h2 class="text-white mb-3" style="font-size: 2.5rem; font-weight: 700;">🔥 Soldes Exceptionnelles</h2>
                <p class="text-white mb-4" style="font-size: 1.2rem; opacity: 0.9;">Jusqu'à -50% sur une sélection de produits</p>
                <a href="{{ route('shop.index') }}" class="btn btn-light btn-lg px-5 py-3" style="border-radius: 50px; font-weight: 600;">
                    Découvrir <i class="fa fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="products-home py-5 animate-on-scroll">
        <div class="container">
            <h1 class="sec-heading mb-5">{{ __('store.home.featured_products') }}</h1>
            <div class="row">
                @foreach ($products as $product)
                <div class="col-md-3">
                    <div class="product-card">
                        <div class="product-img">
                            <img src="{{ Storage::url(optional($product->thumbnail)->image_url ?? 'default.jpg') }}" alt="{{ $product->translation->name ?? 'Product Name Not Available' }}">
                            <button class="wishlist-btn"><i class="fa-solid fa-heart"></i></button>
                        </div>
                        <div class="product-info mt-4">
                            <div class="top-info">
                                <div class="reviews"><i class="fa-solid fa-star"></i>({{ $product->reviews_count }} {{ __('store.home.reviews') }})</div>
                            </div>
                            <div class="bottom-info">
                                <div class="left">
                                    <h3>
                                        <a href="{{ route('product.show', $product->slug) }}" class="product-title">
                                            {{ $product->translation->name ?? 'Product Name Not Available' }}
                                        </a>
                                    </h3>
                                    <p class="price">
                                        <span class="original {{ optional($product->primaryVariant)->converted_discount_price ? 'has-discount' : '' }}">
                                            {{ $currency->symbol }}{{ optional($product->primaryVariant)->converted_price ?? 'N/A' }}
                                        </span>

                                        @if(optional($product->primaryVariant)->converted_discount_price)
                                            <span class="discount"> 
                                                {{ $currency->symbol }}{{ $product->primaryVariant->converted_discount_price }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <button class="cart-btn" onclick="addToCart({{ $product->id }})">
                                    <i class="fa fa-shopping-bag"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="view-button text-center mt-4">
                <a href="{{ route('shop.index') }}" class="read-more pe-4 ps-4">{{ __('store.home.view_all') }}</a>
            </div>

        </div>
    </section>

    <section class="why-choose-us py-5 animate-on-scroll">
        <div class="container">
            <h1 class="sec-heading text-start mb-5">{{ __('store.home.why_choose_us') }}</h1>
            <div class="row">
                <!-- Feature Box 1 -->
                <div class="col-md-3">
                    <div class="feature-box text-start">
                        <div class="feature-icon">
                            <img src="https://i.ibb.co/WNQXhLnP/choose-icon1.png" alt="">
                        </div>
                        <h3>{{ __('store.home.fast_delivery_title') }}</h3>
                        <p>{{ __('store.home.fast_delivery_text') }}</p>
                    </div>
                </div>
                <!-- Feature Box 2 -->
                <div class="col-md-3">
                    <div class="feature-box text-start">
                        <div class="feature-icon">
                            <img src="https://i.ibb.co/FkmgGPrr/choose-icon2.png" alt="">
                        </div>
                        <h3>{{ __('store.home.customer_support_title') }}</h3>
                        <p>{{ __('store.home.customer_support_text') }}</p>
                    </div>
                </div>
                <!-- Feature Box 3 -->
                <div class="col-md-3">
                    <div class="feature-box text-start">
                        <div class="feature-icon">
                            <img src="https://i.ibb.co/CffNqX9/choose-icon3.png" alt="">
                        </div>
                        <h3>{{ __('store.home.trusted_worldwide_title') }}</h3>
                        <p>{{ __('store.home.trusted_worldwide_text') }}</p>
                    </div>
                </div>
                <!-- Feature Box 4 -->
                <div class="col-md-3">
                    <div class="feature-box text-start">
                        <div class="feature-icon">
                            <img src="https://i.ibb.co/XPvjQGG/choose-icon4.png" alt="">
                        </div>
                        <h3>{{ __('store.home.ten_years_services_title') }}</h3>
                        <p>{{ __('store.home.ten_years_services_text') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
        function addToCart(productId) {

            fetch("{{ route('cart.add') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                toastr.success("{{ session('success') }}", data.message, {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-right",
                    timeOut: 5000
                });
                updateCartCount(data.cart);
            })
            .catch(error => console.error("Error:", error));
        }

        function updateCartCount(cart) {
            let totalCount = Object.values(cart).reduce((sum, item) => sum + item.quantity, 0);
            document.getElementById("cart-count").textContent = totalCount;
        }
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.getAttribute('data-product-id');

            fetch('/customer/wishlist', {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json",
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => {
                if (response.status === 401) {
                    // Not logged in
                    window.location.href = '/customer/login';
                } else if (response.ok) {
                    return response.json();
                } else {
                    throw new Error('Something went wrong');
                }
            })
            .then(data => {
                if (data?.message) {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});
</script>

<script>
// Hero Slider Initialization - wait for all scripts to load
$(window).on('load', function() {
    if ($('.hero-slider').length && !$('.hero-slider').hasClass('slick-initialized')) {
        $('.hero-slider').slick({
            dots: true,
            arrows: true,
            infinite: true,
            speed: 800,
            fade: true,
            cssEase: 'ease-in-out',
            autoplay: true,
            autoplaySpeed: 5000,
            pauseOnHover: true,
            adaptiveHeight: false,
            prevArrow: '<button type="button" class="slick-prev"><i class="fa fa-chevron-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="fa fa-chevron-right"></i></button>'
        });
        
        console.log('Hero slider initialized');
        
        // Restart animations on slide change
        $('.hero-slider').on('afterChange', function(event, slick, currentSlide) {
            var $currentSlide = $(slick.$slides.get(currentSlide));
            
            // Reset and restart animations
            $currentSlide.find('.hero-title').css('animation', 'none');
            $currentSlide.find('.hero-subtitle').css('animation', 'none');
            $currentSlide.find('.hero-buttons').css('animation', 'none');
            $currentSlide.find('.hero-image-wrapper').css('animation', 'none');
            
            setTimeout(function() {
                $currentSlide.find('.hero-title').css('animation', 'slideUp 0.8s ease forwards');
                $currentSlide.find('.hero-subtitle').css('animation', 'slideUp 0.8s ease forwards 0.2s');
                $currentSlide.find('.hero-buttons').css('animation', 'slideUp 0.8s ease forwards 0.4s');
                $currentSlide.find('.hero-image-wrapper').css('animation', 'zoomIn 1s ease forwards 0.3s');
            }, 50);
        });
    }
});
</script>
@endsection