@extends('admin.layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header card-header-bg text-white d-flex justify-content-between">
                <h6><i class="fas fa-undo me-2"></i>Retour #{{ $return->id }}</h6>
                <span class="badge bg-{{ $return->status_color }}">{{ $return->status_label }}</span>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Produit</h6>
                        <p>{{ $return->orderDetail?->product?->name ?? 'N/A' }}</p>
                        <p>Quantité: {{ $return->quantity }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Montant</h6>
                        <p class="h4 text-success">{{ number_format($return->refund_amount ?? 0, 2) }}€</p>
                    </div>
                </div>

                <div class="mb-4">
                    <h6>Raison du retour</h6>
                    <p><strong>{{ $return->reason_label }}</strong></p>
                    <p class="bg-light p-3 rounded">{{ $return->description }}</p>
                </div>

                @if($return->images->count())
                    <div class="mb-4">
                        <h6>Photos</h6>
                        <div class="row">
                            @foreach($return->images as $image)
                                <div class="col-md-3">
                                    <a href="{{ $image->url }}" target="_blank">
                                        <img src="{{ $image->url }}" class="img-fluid rounded">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($return->return_tracking_number)
                    <div class="alert alert-info">
                        <strong>Suivi retour:</strong> {{ $return->return_tracking_number }}
                        @if($return->return_carrier)
                            ({{ $return->return_carrier }})
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6>Client</h6>
            </div>
            <div class="card-body">
                <p><strong>{{ $return->customer->name ?? 'N/A' }}</strong></p>
                <p>{{ $return->customer->email ?? '' }}</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h6>Vendeur</h6>
            </div>
            <div class="card-body">
                <p><strong>{{ $return->vendor->shop->name ?? $return->vendor->name ?? 'N/A' }}</strong></p>
                <p>{{ $return->vendor->email ?? '' }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Actions Admin</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.returns.status', $return->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ $return->status == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="approved" {{ $return->status == 'approved' ? 'selected' : '' }}>Approuvé</option>
                            <option value="rejected" {{ $return->status == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                            <option value="shipped" {{ $return->status == 'shipped' ? 'selected' : '' }}>Expédié</option>
                            <option value="received" {{ $return->status == 'received' ? 'selected' : '' }}>Reçu</option>
                            <option value="refunded" {{ $return->status == 'refunded' ? 'selected' : '' }}>Remboursé</option>
                            <option value="completed" {{ $return->status == 'completed' ? 'selected' : '' }}>Complété</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes admin</label>
                        <textarea name="admin_notes" class="form-control" rows="3">{{ $return->admin_notes }}</textarea>
                    </div>
                    <button class="btn btn-primary w-100">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
