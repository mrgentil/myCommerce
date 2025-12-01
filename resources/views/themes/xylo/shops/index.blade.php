@extends('themes.xylo.layouts.master')

@section('title', __('All Shops'))

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-3">{{ __('Our Vendors') }}</h1>
            <p class="text-muted">{{ __('Discover products from our trusted vendors') }}</p>
        </div>
    </div>

    <div class="row">
        @forelse($shops as $shop)
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100 shop-card">
                    <div class="card-img-top shop-banner" style="height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        @if($shop->banner)
                            <img src="{{ Storage::url($shop->banner) }}" alt="{{ $shop->name }}" class="w-100 h-100" style="object-fit: cover;">
                        @endif
                    </div>
                    <div class="card-body text-center">
                        <div class="shop-logo mx-auto mb-3" style="margin-top: -40px; width: 80px; height: 80px; border-radius: 50%; border: 3px solid #fff; overflow: hidden; background: #f8f9fa;">
                            @if($shop->logo)
                                <img src="{{ Storage::url($shop->logo) }}" alt="{{ $shop->name }}" class="w-100 h-100" style="object-fit: cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100 bg-primary text-white fs-4">
                                    {{ strtoupper(substr($shop->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <h5 class="card-title">{{ $shop->name }}</h5>
                        <p class="text-muted small mb-2">
                            <i class="fa fa-box me-1"></i> {{ $shop->products_count }} {{ __('products') }}
                        </p>
                        @if($shop->description)
                            <p class="card-text small text-muted">{{ Str::limit($shop->description, 80) }}</p>
                        @endif
                        <a href="{{ route('shop.view', $shop->slug) }}" class="btn btn-outline-primary btn-sm">
                            {{ __('Visit Shop') }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    {{ __('No shops available at the moment.') }}
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $shops->links() }}
    </div>
</div>

<style>
.shop-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.shop-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
</style>
@endsection
