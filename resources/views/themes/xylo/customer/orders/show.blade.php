@extends('themes.xylo.layouts.master')

@section('css')
<style>
    .tracking-timeline {
        position: relative;
        padding-left: 30px;
    }
    .tracking-timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    .tracking-item {
        position: relative;
        padding-bottom: 25px;
    }
    .tracking-item:last-child {
        padding-bottom: 0;
    }
    .tracking-dot {
        position: absolute;
        left: -25px;
        top: 0;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        background: white;
        border: 2px solid #e9ecef;
    }
    .tracking-item.active .tracking-dot {
        background: #667eea;
        border-color: #667eea;
        color: white;
    }
    .tracking-item.completed .tracking-dot {
        background: #28a745;
        border-color: #28a745;
        color: white;
    }
    .progress-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }
    .progress-step {
        text-align: center;
        flex: 1;
        position: relative;
    }
    .progress-step::after {
        content: '';
        position: absolute;
        top: 15px;
        left: 50%;
        width: 100%;
        height: 3px;
        background: #e9ecef;
        z-index: 0;
    }
    .progress-step:last-child::after {
        display: none;
    }
    .progress-step.completed::after {
        background: #28a745;
    }
    .step-icon {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: #e9ecef;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
    }
    .progress-step.completed .step-icon {
        background: #28a745;
        color: white;
    }
    .progress-step.active .step-icon {
        background: #667eea;
        color: white;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(102, 126, 234, 0); }
        100% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0); }
    }
    .product-thumb-sm {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <a href="{{ route('customer.orders.index') }}" class="btn btn-link text-muted mb-3">
        <i class="bi bi-arrow-left me-2"></i>Retour aux commandes
    </a>

    <div class="row">
        <div class="col-lg-8">
            <!-- Order Progress -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-1">Commande #{{ $order->id }}</h5>
                            <small class="text-muted">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</small>
                        </div>
                        <span class="badge bg-{{ $order->status_color }} px-3 py-2 fs-6">
                            {{ $order->status_label }}
                        </span>
                    </div>

                    <!-- Visual Progress -->
                    @php
                        $steps = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
                        $currentIndex = array_search($order->status, $steps);
                        if ($currentIndex === false) $currentIndex = -1;
                    @endphp
                    <div class="progress-steps mb-4">
                        @foreach(['Confirmée', 'Préparation', 'Expédiée', 'Livrée'] as $index => $label)
                            @php
                                $stepStatus = '';
                                if ($index < $currentIndex) $stepStatus = 'completed';
                                elseif ($index == $currentIndex || ($order->status == 'pending' && $index == 0)) $stepStatus = 'active';
                            @endphp
                            <div class="progress-step {{ $stepStatus }}">
                                <div class="step-icon">
                                    @if($stepStatus == 'completed')
                                        <i class="bi bi-check"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <small class="fw-medium">{{ $label }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($order->tracking_number)
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="bi bi-truck me-3 fs-4"></i>
                            <div>
                                <strong>Numéro de suivi:</strong> {{ $order->tracking_number }}
                                @if($order->carrier)
                                    <span class="ms-2">({{ $order->carrier }})</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($order->estimated_delivery)
                        <p class="text-muted mb-0">
                            <i class="bi bi-calendar me-2"></i>
                            Livraison estimée: <strong>{{ $order->estimated_delivery->format('d/m/Y') }}</strong>
                        </p>
                    @endif
                </div>
            </div>

            <!-- Tracking Timeline -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historique de suivi</h6>
                </div>
                <div class="card-body">
                    @if($order->trackings->count() > 0)
                        <div class="tracking-timeline">
                            @foreach($order->trackings as $index => $tracking)
                                <div class="tracking-item {{ $index === 0 ? 'active' : 'completed' }}">
                                    <div class="tracking-dot">
                                        <i class="bi {{ $tracking->icon }}"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $tracking->title }}</h6>
                                        @if($tracking->description)
                                            <p class="text-muted mb-1">{{ $tracking->description }}</p>
                                        @endif
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $tracking->occurred_at?->format('d/m/Y H:i') ?? $tracking->created_at->format('d/m/Y H:i') }}
                                            @if($tracking->location)
                                                <span class="ms-2">
                                                    <i class="bi bi-geo-alt me-1"></i>{{ $tracking->location }}
                                                </span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-clock-history fs-1"></i>
                            <p class="mt-2 mb-0">Aucun événement de suivi pour le moment</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Products -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-box me-2"></i>Articles commandés</h6>
                </div>
                <div class="card-body">
                    @foreach($order->details as $detail)
                        <div class="d-flex align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <img src="{{ $detail->product?->thumbnail?->image_url ?? '/images/placeholder.png' }}" 
                                 class="product-thumb-sm me-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $detail->product?->name }}</h6>
                                @if($detail->productVariant)
                                    <small class="text-muted">{{ $detail->productVariant->variant_slug }}</small>
                                @endif
                            </div>
                            <div class="text-end">
                                <div class="fw-medium">{{ number_format($detail->price, 2, ',', ' ') }} €</div>
                                <small class="text-muted">Qté: {{ $detail->quantity }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Récapitulatif</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Sous-total</span>
                        <span>{{ number_format($order->total_amount ?? $order->total_price, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Livraison</span>
                        <span>Gratuite</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total</strong>
                        <strong class="fs-5 text-primary">{{ number_format($order->total_amount ?? $order->total_price, 2, ',', ' ') }} €</strong>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Adresse de livraison</h6>
                </div>
                <div class="card-body">
                    @php $address = json_decode($order->shipping_address); @endphp
                    @if($address)
                        <p class="mb-0">
                            <strong>{{ $address->first_name ?? '' }} {{ $address->last_name ?? '' }}</strong><br>
                            {{ $address->address ?? '' }}<br>
                            @if($address->suite ?? false){{ $address->suite }}<br>@endif
                            {{ $address->zipcode ?? '' }} {{ $address->city ?? '' }}<br>
                            @if($address->phone ?? false)
                                <i class="bi bi-telephone me-1"></i>{{ $address->phone }}
                            @endif
                        </p>
                    @else
                        <p class="text-muted mb-0">Non spécifiée</p>
                    @endif
                </div>
            </div>

            <!-- Vendor Info -->
            @if($order->vendor)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0"><i class="bi bi-shop me-2"></i>Vendeur</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="{{ $order->vendor->shop->logo ?? '/images/default-shop.png' }}" 
                                 class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0">{{ $order->vendor->shop->name ?? $order->vendor->name }}</h6>
                                <a href="{{ route('customer.messages.create', ['vendor_id' => $order->vendor_id, 'order_id' => $order->id]) }}" 
                                   class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-chat me-1"></i>Contacter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <a href="#" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="bi bi-printer me-2"></i>Imprimer la facture
                    </a>
                    @if(in_array($order->status, ['delivered']))
                        <a href="{{ route('customer.returns.create', $order->id) }}" class="btn btn-outline-warning w-100">
                            <i class="bi bi-arrow-return-left me-2"></i>Retourner un article
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
