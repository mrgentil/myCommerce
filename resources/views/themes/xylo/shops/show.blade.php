@extends('themes.xylo.layouts.master')

@section('title', $shop->name)

@section('content')
@php 
    $currency = activeCurrency();
    $currencySymbol = $currency->symbol ?? '$';
@endphp

<!-- Shop Hero Section -->
<div class="shop-hero mb-4" style="background: {{ $shop->hero_background ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' }}; min-height: 350px; position: relative; overflow: hidden;">
    <!-- Background Banner Image -->
    @if($shop->banner)
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.3;">
            <img src="{{ Storage::url($shop->banner) }}" alt="{{ $shop->name }}" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    @endif
    
    <div class="container h-100 position-relative" style="z-index: 2;">
        <div class="row h-100 align-items-center py-5">
            <div class="col-lg-8">
                <!-- Shop Logo & Name -->
                <div class="d-flex align-items-center mb-4">
                    <div class="shop-logo me-4" style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; border: 4px solid rgba(255,255,255,0.3); box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                        @if($shop->logo)
                            <img src="{{ Storage::url($shop->logo) }}" alt="{{ $shop->name }}" class="w-100 h-100" style="object-fit: cover;">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 bg-white text-primary fs-1 fw-bold">
                                {{ strtoupper(substr($shop->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h1 class="mb-0 fw-bold" style="color: {{ $shop->hero_text_color ?? '#ffffff' }}; font-size: 2.5rem;">
                            {{ $shop->name }}
                        </h1>
                        @if($shop->vendor)
                            <p class="mb-0" style="color: {{ $shop->hero_text_color ?? '#ffffff' }}; opacity: 0.8;">
                                <i class="fa fa-user me-1"></i> {{ __('by') }} {{ $shop->vendor->name }}
                            </p>
                        @endif
                    </div>
                </div>
                
                <!-- Hero Title & Subtitle -->
                @if($shop->hero_title)
                    <h2 class="mb-3" style="color: {{ $shop->hero_text_color ?? '#ffffff' }}; font-size: 2rem;">
                        {{ $shop->hero_title }}
                    </h2>
                @endif
                
                @if($shop->hero_subtitle)
                    <p class="mb-4" style="color: {{ $shop->hero_text_color ?? '#ffffff' }}; opacity: 0.9; font-size: 1.1rem;">
                        {{ $shop->hero_subtitle }}
                    </p>
                @elseif($shop->description)
                    <p class="mb-4" style="color: {{ $shop->hero_text_color ?? '#ffffff' }}; opacity: 0.9;">
                        {{ Str::limit($shop->description, 150) }}
                    </p>
                @endif
                
                <!-- Stats & Button -->
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <span class="badge bg-white text-dark fs-6 px-3 py-2">
                        <i class="fa fa-box me-1"></i> {{ $products->total() }} Produits
                    </span>
                    @if($shop->phone)
                        <span class="badge bg-white bg-opacity-25 fs-6 px-3 py-2" style="color: {{ $shop->hero_text_color ?? '#ffffff' }};">
                            <i class="fa fa-phone me-1"></i> {{ $shop->phone }}
                        </span>
                    @endif
                    @if($shop->address)
                        <span class="badge bg-white bg-opacity-25 fs-6 px-3 py-2" style="color: {{ $shop->hero_text_color ?? '#ffffff' }};">
                            <i class="fa fa-map-marker me-1"></i> {{ $shop->address }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Decorative shapes -->
    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.1);"></div>
    <div style="position: absolute; bottom: -30px; left: 10%; width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
</div>

<!-- Products -->
<div class="container py-4">
    <h3 class="mb-4">{{ __('Products from this shop') }}</h3>
    
    <div class="row">
        @forelse($products as $product)
            @php
                $translation = $product->translations->where('language_code', app()->getLocale())->first()
                    ?? $product->translations->first();
            @endphp
            <div class="col-6 col-md-4 col-lg-3 mb-4">
                <div class="card h-100 product-card">
                    <a href="{{ route('product.show', $product->slug) }}">
                        <div class="product-image" style="height: 200px; overflow: hidden;">
                            @if($product->thumbnail)
                                <img src="{{ Storage::url($product->thumbnail->image_url) }}" 
                                     class="card-img-top h-100 w-100" 
                                     style="object-fit: cover;" 
                                     alt="{{ $translation->name ?? $product->slug }}">
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                    <i class="fa fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                    </a>
                    <div class="card-body">
                        <h6 class="card-title">
                            <a href="{{ route('product.show', $product->slug) }}" class="text-dark text-decoration-none">
                                {{ Str::limit($translation->name ?? $product->slug, 40) }}
                            </a>
                        </h6>
                        @if($product->category)
                            <p class="small text-muted mb-2">{{ $product->category->translation->name ?? '' }}</p>
                        @endif
                        <p class="fw-bold text-primary mb-0">
                            {{ $currencySymbol }} {{ number_format(convert_price($product->variants->first()->price ?? 0), 2) }}
                        </p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <button class="btn btn-primary btn-sm w-100 add-to-cart-btn"
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $translation->name ?? $product->slug }}">
                            <i class="fa fa-shopping-cart me-1"></i> {{ __('Add to Cart') }}
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    {{ __('No products available in this shop yet.') }}
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
</div>

<style>
.product-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
</style>
@endsection
