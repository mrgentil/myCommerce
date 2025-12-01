@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Détails du vendeur</h4>
                <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Vendor Info Card -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations du vendeur</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($vendor->profile_image)
                            <img src="{{ Storage::url($vendor->profile_image) }}" alt="{{ $vendor->name }}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2rem;">
                                {{ strtoupper(substr($vendor->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h4>{{ $vendor->name }}</h4>
                    <p class="text-muted">{{ $vendor->email }}</p>
                    
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'approved' => 'success',
                            'active' => 'success',
                            'rejected' => 'danger',
                            'inactive' => 'secondary',
                            'banned' => 'dark',
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$vendor->status] ?? 'secondary' }} fs-6">
                        {{ ucfirst($vendor->status) }}
                    </span>

                    @if($vendor->status === 'pending')
                        <div class="mt-3">
                            <button class="btn btn-success btn-sm" onclick="approveVendor({{ $vendor->id }})">
                                <i class="bi bi-check me-1"></i> Approuver
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="rejectVendor({{ $vendor->id }})">
                                <i class="bi bi-x me-1"></i> Rejeter
                            </button>
                        </div>
                    @endif
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Téléphone</span>
                        <span>{{ $vendor->phone ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Inscrit le</span>
                        <span>{{ $vendor->created_at->format('d M Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Produits</span>
                        <span class="badge bg-primary">{{ $vendor->products->count() }}</span>
                    </li>
                </ul>
            </div>

            <!-- Commission Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Paramètres de commission</h5>
                </div>
                <div class="card-body">
                    <form id="commissionForm">
                        <div class="mb-3">
                            <label class="form-label">Taux de commission (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="commission_rate" name="commission_rate" value="{{ $vendor->commission_rate ?? 10 }}" min="0" max="100" step="0.5">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-save me-1"></i> Mettre à jour
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Shop & Financial Info -->
        <div class="col-md-8">
            <!-- Shop Info -->
            @if($vendor->shop)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations de la boutique</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nom de la boutique:</strong> {{ $vendor->shop->name }}</p>
                            <p><strong>Slug:</strong> {{ $vendor->shop->slug }}</p>
                            <p><strong>Statut:</strong> 
                                <span class="badge bg-{{ $statusColors[$vendor->shop->status] ?? 'secondary' }}">
                                    {{ ucfirst($vendor->shop->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Description:</strong></p>
                            <p class="text-muted">{{ $vendor->shop->description ?? '-' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('shop.view', $vendor->shop->slug) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye me-1"></i> Voir la boutique
                    </a>
                </div>
            </div>
            @endif

            <!-- Financial Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Résumé financier</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h3 class="text-primary">${{ number_format($pendingBalance, 2) }}</h3>
                                <p class="text-muted mb-0">Solde en attente</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h3 class="text-success">${{ number_format($commissions->where('status', 'paid')->sum('vendor_amount'), 2) }}</h3>
                                <p class="text-muted mb-0">Total payé</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h3 class="text-info">${{ number_format($commissions->sum('commission_amount'), 2) }}</h3>
                                <p class="text-muted mb-0">Commission totale</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Commissions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Commissions récentes</h5>
                </div>
                <div class="card-body">
                    @if($commissions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Commande</th>
                                    <th>Montant</th>
                                    <th>Taux</th>
                                    <th>Commission</th>
                                    <th>Part vendeur</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($commissions as $commission)
                                <tr>
                                    <td>#{{ $commission->order_id }}</td>
                                    <td>${{ number_format($commission->order_amount, 2) }}</td>
                                    <td>{{ $commission->commission_rate }}%</td>
                                    <td class="text-danger">${{ number_format($commission->commission_amount, 2) }}</td>
                                    <td class="text-success">${{ number_format($commission->vendor_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $commission->status === 'paid' ? 'success' : 'warning' }}">
                                            {{ ucfirst($commission->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center">Aucune commission pour le moment.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function approveVendor(id) {
    if (confirm('Voulez-vous approuver ce vendeur ?')) {
        fetch(`/admin/vendors/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function rejectVendor(id) {
    if (confirm('Voulez-vous rejeter ce vendeur ?')) {
        fetch(`/admin/vendors/${id}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

document.getElementById('commissionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const rate = document.getElementById('commission_rate').value;
    
    fetch(`/admin/vendors/{{ $vendor->id }}/commission`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ commission_rate: rate })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        }
    });
});
</script>
@endpush
@endsection
