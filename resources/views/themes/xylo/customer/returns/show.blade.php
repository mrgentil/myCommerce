@extends('themes.xylo.layouts.master')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <a href="{{ route('customer.returns.index') }}" class="btn btn-link text-muted mb-3">
                <i class="bi bi-arrow-left me-2"></i>Mes retours
            </a>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Status card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center py-4">
                    <span class="badge bg-{{ $return->status_color }} px-4 py-2 fs-6 mb-3">
                        {{ $return->status_label }}
                    </span>
                    <h5>Retour #{{ $return->id }}</h5>
                    <p class="text-muted mb-0">Demandé le {{ $return->created_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Suivi du retour</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @php
                            $steps = [
                                ['status' => 'pending', 'label' => 'Demande envoyée', 'icon' => 'bi-send'],
                                ['status' => 'approved', 'label' => 'Approuvé par le vendeur', 'icon' => 'bi-check-circle'],
                                ['status' => 'shipped', 'label' => 'Article renvoyé', 'icon' => 'bi-truck'],
                                ['status' => 'received', 'label' => 'Reçu par le vendeur', 'icon' => 'bi-box-seam'],
                                ['status' => 'refunded', 'label' => 'Remboursé', 'icon' => 'bi-cash-stack'],
                            ];
                            $currentIndex = array_search($return->status, array_column($steps, 'status'));
                        @endphp

                        @foreach($steps as $index => $step)
                            <div class="d-flex align-items-start mb-3">
                                <div class="me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center 
                                         {{ $index <= $currentIndex ? 'bg-success text-white' : 'bg-light text-muted' }}"
                                         style="width: 35px; height: 35px;">
                                        <i class="bi {{ $step['icon'] }}"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0 {{ $index <= $currentIndex ? '' : 'text-muted' }}">{{ $step['label'] }}</h6>
                                    @if($step['status'] === 'approved' && $return->approved_at)
                                        <small class="text-muted">{{ $return->approved_at->format('d/m/Y H:i') }}</small>
                                    @elseif($step['status'] === 'shipped' && $return->shipped_at)
                                        <small class="text-muted">{{ $return->shipped_at->format('d/m/Y H:i') }}</small>
                                    @elseif($step['status'] === 'received' && $return->received_at)
                                        <small class="text-muted">{{ $return->received_at->format('d/m/Y H:i') }}</small>
                                    @elseif($step['status'] === 'refunded' && $return->refunded_at)
                                        <small class="text-muted">{{ $return->refunded_at->format('d/m/Y H:i') }}</small>
                                    @elseif($step['status'] === 'pending')
                                        <small class="text-muted">{{ $return->created_at->format('d/m/Y H:i') }}</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($return->status === 'rejected')
                        <div class="alert alert-danger mt-3">
                            <i class="bi bi-x-circle me-2"></i>
                            <strong>Demande refusée</strong>
                            @if($return->vendor_response)
                                <p class="mb-0 mt-2">{{ $return->vendor_response }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Add tracking if approved -->
            @if($return->status === 'approved')
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark py-3">
                        <h6 class="mb-0"><i class="bi bi-truck me-2"></i>Renvoyez l'article</h6>
                    </div>
                    <div class="card-body">
                        <p>Votre demande a été approuvée. Veuillez renvoyer l'article à l'adresse du vendeur et entrer le numéro de suivi ci-dessous.</p>
                        
                        <form action="{{ route('customer.returns.update-tracking', $return->id) }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="return_tracking" class="form-control" 
                                       placeholder="Numéro de suivi du colis" required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check me-1"></i>Confirmer l'envoi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Item details -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-box me-2"></i>Article concerné</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $return->orderDetail->product->name ?? 'Produit' }}</h6>
                            @if($return->orderDetail->productVariant)
                                <small class="text-muted">{{ $return->orderDetail->productVariant->variant_slug }}</small>
                            @endif
                            <p class="mb-0 mt-2">
                                <span class="text-muted">Quantité:</span> {{ $return->quantity }} / {{ $return->orderDetail->quantity }}
                            </p>
                        </div>
                        <div class="text-end">
                            <strong class="fs-5">{{ number_format($return->refund_amount, 2, ',', ' ') }} €</strong>
                            <small class="d-block text-muted">Montant du remboursement</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Détails</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Type:</strong> {{ $return->type_label }}
                    </p>
                    <p class="mb-2">
                        <strong>Raison:</strong> {{ $return->reason_label }}
                    </p>
                    <p class="mb-2">
                        <strong>Description:</strong><br>
                        <span class="text-muted">{{ $return->description }}</span>
                    </p>
                    @if($return->return_tracking)
                        <p class="mb-0">
                            <strong>N° de suivi retour:</strong><br>
                            <code>{{ $return->return_tracking }}</code>
                        </p>
                    @endif
                </div>
            </div>

            <!-- Images -->
            @if($return->images->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0"><i class="bi bi-images me-2"></i>Photos</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach($return->images as $image)
                                <div class="col-6">
                                    <a href="{{ $image->url }}" target="_blank">
                                        <img src="{{ $image->url }}" class="img-fluid rounded" alt="Photo">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Vendor -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-shop me-2"></i>Vendeur</h6>
                </div>
                <div class="card-body">
                    <strong>{{ $return->vendor->shop->name ?? $return->vendor->name }}</strong>
                    <a href="{{ route('customer.messages.create', ['vendor_id' => $return->vendor_id, 'order_id' => $return->order_id]) }}" 
                       class="btn btn-sm btn-outline-primary mt-3 w-100">
                        <i class="bi bi-chat me-1"></i>Contacter
                    </a>
                </div>
            </div>

            <!-- Cancel -->
            @if($return->status === 'pending')
                <form action="{{ route('customer.returns.cancel', $return->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100" 
                            onclick="return confirm('Annuler cette demande ?')">
                        <i class="bi bi-x-circle me-2"></i>Annuler la demande
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
