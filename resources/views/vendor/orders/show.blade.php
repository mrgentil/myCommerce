@extends('vendor.layouts.master')

@section('css')
<style>
    .order-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        color: white;
        padding: 25px 30px;
        margin-bottom: 25px;
    }
    .status-select {
        min-width: 150px;
    }
    .status-badge {
        font-size: 14px;
        padding: 8px 16px;
        border-radius: 25px;
        font-weight: 500;
    }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-processing { background: #cce5ff; color: #004085; }
    .status-shipped { background: #d4edda; color: #155724; }
    .status-completed { background: #d1e7dd; color: #0a3622; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    .info-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
    }
    .info-card .card-header {
        background: transparent;
        border-bottom: 1px solid #f0f0f0;
        padding: 15px 20px;
    }
    .info-card .card-body {
        padding: 20px;
    }
    .product-img {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        object-fit: cover;
    }
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -24px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #667eea;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #667eea;
    }
    .timeline-item.completed::before {
        background: #28a745;
        box-shadow: 0 0 0 2px #28a745;
    }
    .timeline-item.pending::before {
        background: #e9ecef;
        box-shadow: 0 0 0 2px #dee2e6;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Order Header -->
    <div class="order-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <a href="{{ route('vendor.orders.index') }}" class="text-white-50 text-decoration-none mb-2 d-inline-block">
                    <i class="bi bi-arrow-left me-1"></i> Retour aux commandes
                </a>
                <h3 class="mb-1">Commande #{{ $order->id }}</h3>
                <p class="mb-0 opacity-75">
                    <i class="bi bi-calendar3 me-1"></i> {{ $order->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <span class="status-badge status-{{ $order->status }}" id="currentStatus">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Status Update -->
            <div class="card info-card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-arrow-repeat me-2"></i>Mettre à jour le statut</h6>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <select id="statusSelect" class="form-select status-select">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>En cours de traitement</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Expédiée</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Complétée</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                            </select>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <button type="button" class="btn btn-primary" id="updateStatusBtn">
                                <i class="bi bi-check-lg me-1"></i> Mettre à jour
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card info-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2"></i>Produits commandés</h6>
                    <span class="badge bg-primary">{{ $vendorItems->count() }} article(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;"></th>
                                    <th>Produit</th>
                                    <th class="text-center">Quantité</th>
                                    <th class="text-end">Prix unitaire</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vendorItems as $item)
                                    <tr>
                                        <td>
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                     alt="{{ $item->product->name }}" class="product-img">
                                            @else
                                                <div class="product-img bg-light d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-medium">{{ $item->product->name ?? 'Produit supprimé' }}</div>
                                            @if($item->product && $item->product->sku)
                                                <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">{{ number_format($item->price, 2, ',', ' ') }} €</td>
                                        <td class="text-end fw-bold">{{ number_format($item->quantity * $item->price, 2, ',', ' ') }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Sous-total (vos produits) :</td>
                                    <td class="text-end fw-bold text-primary">{{ number_format($vendorTotal, 2, ',', ' ') }} €</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            @if($order->notes)
                <div class="card info-card">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-chat-left-text me-2"></i>Notes de la commande</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Customer Info -->
            <div class="card info-card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-person me-2"></i>Client</h6>
                </div>
                <div class="card-body">
                    @if($order->customer)
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-person text-primary fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $order->customer->name }}</div>
                                <small class="text-muted">{{ $order->customer->email }}</small>
                            </div>
                        </div>
                        @if($order->customer->phone)
                            <div class="d-flex align-items-center text-muted">
                                <i class="bi bi-telephone me-2"></i>
                                {{ $order->customer->phone }}
                            </div>
                        @endif
                    @else
                        <div class="text-muted">
                            <i class="bi bi-person me-2"></i>
                            {{ $order->guest_email ?? 'Client invité' }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Shipping Address -->
            @if($order->shippingAddress)
                <div class="card info-card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-truck me-2"></i>Adresse de livraison</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">
                            {{ $order->shippingAddress->address_line_1 }}<br>
                            @if($order->shippingAddress->address_line_2)
                                {{ $order->shippingAddress->address_line_2 }}<br>
                            @endif
                            {{ $order->shippingAddress->postal_code }} {{ $order->shippingAddress->city }}<br>
                            {{ $order->shippingAddress->country }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Payment Info -->
            <div class="card info-card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-credit-card me-2"></i>Paiement</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Méthode</span>
                        <span class="fw-medium">{{ ucfirst($order->payment_method ?? 'N/A') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Statut</span>
                        <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                            {{ $order->payment_status == 'paid' ? 'Payé' : 'En attente' }}
                        </span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total commande</span>
                        <span class="fw-bold text-primary fs-5">{{ number_format($order->total_amount, 2, ',', ' ') }} €</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card info-card">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-lightning me-2"></i>Actions rapides</h6>
                </div>
                <div class="card-body">
                    <button class="btn btn-outline-primary w-100 mb-2" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>Imprimer
                    </button>
                    <a href="mailto:{{ $order->customer->email ?? $order->guest_email }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-envelope me-2"></i>Contacter le client
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.getElementById('updateStatusBtn').addEventListener('click', function() {
    const status = document.getElementById('statusSelect').value;
    const btn = this;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mise à jour...';

    fetch('{{ route("vendor.orders.updateStatus", $order->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update status badge
            const badge = document.getElementById('currentStatus');
            badge.className = 'status-badge status-' + status;
            badge.textContent = data.status;
            
            toastr.success(data.message);
        } else {
            toastr.error('Erreur lors de la mise à jour');
        }
    })
    .catch(error => {
        toastr.error('Erreur de connexion');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Mettre à jour';
    });
});
</script>
@endsection
