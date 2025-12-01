@extends('themes.xylo.layouts.master')

@section('css')
<style>
    .order-card {
        border: none;
        border-radius: 12px;
        margin-bottom: 20px;
        transition: all 0.2s;
    }
    .order-card:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .order-header {
        background: #f8f9fa;
        padding: 15px 20px;
        border-radius: 12px 12px 0 0;
        border-bottom: 1px solid #eee;
    }
    .product-thumb {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .progress-thin {
        height: 6px;
        border-radius: 3px;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-bag me-2"></i>Mes commandes</h3>
        <a href="{{ route('track.order') }}" class="btn btn-outline-primary">
            <i class="bi bi-search me-2"></i>Suivre une commande
        </a>
    </div>

    @forelse($orders as $order)
        <div class="card order-card shadow-sm">
            <div class="order-header d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <span class="text-muted">Commande</span>
                    <strong class="ms-1">#{{ $order->id }}</strong>
                    <span class="text-muted ms-3">{{ $order->created_at->format('d/m/Y') }}</span>
                </div>
                <div>
                    <span class="badge bg-{{ $order->status_color }} px-3 py-2">
                        {{ $order->status_label }}
                    </span>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Progress bar -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">Progression</small>
                        <small class="text-muted">{{ $order->progress_percentage }}%</small>
                    </div>
                    <div class="progress progress-thin">
                        <div class="progress-bar bg-{{ $order->status_color }}" 
                             style="width: {{ $order->progress_percentage }}%"></div>
                    </div>
                </div>

                <!-- Products -->
                <div class="row">
                    @foreach($order->details->take(3) as $detail)
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <img src="{{ $detail->product?->thumbnail?->image_url ?? '/images/placeholder.png' }}" 
                                     class="product-thumb me-3">
                                <div>
                                    <div class="fw-medium">{{ Str::limit($detail->product?->name, 30) }}</div>
                                    <small class="text-muted">Qté: {{ $detail->quantity }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($order->details->count() > 3)
                        <div class="col-12">
                            <small class="text-muted">+ {{ $order->details->count() - 3 }} autre(s) article(s)</small>
                        </div>
                    @endif
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted">Total:</span>
                        <strong class="ms-2 fs-5">{{ number_format($order->total_amount ?? $order->total_price, 2, ',', ' ') }} €</strong>
                    </div>
                    <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-primary">
                        <i class="bi bi-eye me-2"></i>Détails
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="bi bi-bag fs-1 text-muted"></i>
            <h5 class="mt-3">Aucune commande</h5>
            <p class="text-muted">Vous n'avez pas encore passé de commande</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary">
                <i class="bi bi-cart me-2"></i>Commencer vos achats
            </a>
        </div>
    @endforelse

    {{ $orders->links() }}
</div>
@endsection
