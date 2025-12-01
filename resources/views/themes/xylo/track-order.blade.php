@extends('themes.xylo.layouts.master')

@section('css')
<style>
    .track-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 60px 0;
        color: white;
    }
    .track-form-card {
        margin-top: -50px;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
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
    .progress-visual {
        display: flex;
        justify-content: space-between;
        margin: 30px 0;
    }
    .progress-step {
        flex: 1;
        text-align: center;
        position: relative;
    }
    .progress-step::after {
        content: '';
        position: absolute;
        top: 20px;
        left: 50%;
        width: 100%;
        height: 4px;
        background: #e9ecef;
    }
    .progress-step:last-child::after {
        display: none;
    }
    .progress-step.completed::after {
        background: #28a745;
    }
    .step-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: #e9ecef;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
        font-size: 18px;
    }
    .progress-step.completed .step-circle {
        background: #28a745;
        color: white;
    }
    .progress-step.active .step-circle {
        background: #667eea;
        color: white;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.4); }
        70% { box-shadow: 0 0 0 15px rgba(102, 126, 234, 0); }
        100% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0); }
    }
</style>
@endsection

@section('content')
<div class="track-hero text-center">
    <div class="container">
        <h2><i class="bi bi-truck me-2"></i>Suivi de commande</h2>
        <p class="mb-0 opacity-75">Entrez votre numéro de commande pour voir son statut</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card track-form-card">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('track.order') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">Numéro de commande</label>
                                <input type="text" name="order" class="form-control form-control-lg" 
                                       placeholder="Ex: 12345" value="{{ request('order') }}" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control form-control-lg" 
                                       placeholder="votre@email.com" value="{{ request('email') }}" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    @if(isset($error))
                        <div class="alert alert-danger mt-4">
                            <i class="bi bi-exclamation-circle me-2"></i>{{ $error }}
                        </div>
                    @endif
                </div>
            </div>

            @if(isset($order))
                <!-- Order Found -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="mb-1">Commande #{{ $order->id }}</h5>
                                <small class="text-muted">{{ $order->created_at->format('d/m/Y') }}</small>
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
                        <div class="progress-visual">
                            @foreach([
                                ['icon' => 'bi-check-circle', 'label' => 'Confirmée'],
                                ['icon' => 'bi-gear', 'label' => 'Préparation'],
                                ['icon' => 'bi-truck', 'label' => 'Expédiée'],
                                ['icon' => 'bi-house-check', 'label' => 'Livrée']
                            ] as $index => $step)
                                @php
                                    $stepStatus = '';
                                    if ($index < $currentIndex) $stepStatus = 'completed';
                                    elseif ($index == $currentIndex) $stepStatus = 'active';
                                @endphp
                                <div class="progress-step {{ $stepStatus }}">
                                    <div class="step-circle">
                                        <i class="bi {{ $step['icon'] }}"></i>
                                    </div>
                                    <div class="mt-2">
                                        <small class="fw-medium">{{ $step['label'] }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($order->tracking_number)
                            <div class="alert alert-info mt-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-box-seam fs-4 me-3"></i>
                                    <div>
                                        <strong>Numéro de suivi:</strong> {{ $order->tracking_number }}
                                        @if($order->carrier)
                                            <span class="ms-2 badge bg-secondary">{{ $order->carrier }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($order->estimated_delivery)
                            <p class="text-muted">
                                <i class="bi bi-calendar-event me-2"></i>
                                Livraison estimée: <strong>{{ $order->estimated_delivery->format('d/m/Y') }}</strong>
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Tracking History -->
                @if($order->trackings->count() > 0)
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historique</h6>
                        </div>
                        <div class="card-body">
                            <div class="tracking-timeline">
                                @foreach($order->trackings as $index => $tracking)
                                    <div class="tracking-item {{ $index === 0 ? 'active' : '' }}">
                                        <div class="tracking-dot">
                                            <i class="bi {{ $tracking->icon }}"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $tracking->title }}</h6>
                                            @if($tracking->description)
                                                <p class="text-muted small mb-1">{{ $tracking->description }}</p>
                                            @endif
                                            <small class="text-muted">
                                                {{ $tracking->occurred_at?->format('d/m/Y H:i') ?? $tracking->created_at->format('d/m/Y H:i') }}
                                                @if($tracking->location)
                                                    · {{ $tracking->location }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Order Items -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0"><i class="bi bi-box me-2"></i>Articles ({{ $order->details->count() }})</h6>
                    </div>
                    <div class="card-body">
                        @foreach($order->details as $detail)
                            <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="flex-grow-1">
                                    <strong>{{ $detail->product?->name }}</strong>
                                    <small class="text-muted d-block">Quantité: {{ $detail->quantity }}</small>
                                </div>
                                <strong>{{ number_format($detail->price * $detail->quantity, 2, ',', ' ') }} €</strong>
                            </div>
                        @endforeach
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total</strong>
                            <strong class="text-primary fs-5">{{ number_format($order->total_amount ?? $order->total_price, 2, ',', ' ') }} €</strong>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
