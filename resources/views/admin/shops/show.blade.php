@extends('admin.layouts.admin')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">{{ $shop->name }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.shops.index') }}">Boutiques</a></li>
                    <li class="breadcrumb-item active">{{ $shop->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('shop.view', $shop->slug) }}" target="_blank" class="btn btn-primary">
                <i class="bi bi-eye me-1"></i> Voir la boutique
            </a>
            <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Shop Info Card -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-shop me-2"></i>Informations de la boutique</h5>
                </div>
                <div class="card-body text-center">
                    @if($shop->logo)
                        <img src="{{ asset('storage/' . $shop->logo) }}" alt="{{ $shop->name }}" class="img-fluid rounded mb-3" style="max-height: 150px;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 150px;">
                            <i class="bi bi-shop fs-1 text-muted"></i>
                        </div>
                    @endif
                    
                    <h5>{{ $shop->name }}</h5>
                    <p class="text-muted mb-2">{{ $shop->slug }}</p>
                    
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'approved' => 'success',
                            'active' => 'success',
                            'rejected' => 'danger',
                            'inactive' => 'secondary',
                        ];
                        $statusLabels = [
                            'pending' => 'En attente',
                            'approved' => 'Approuvée',
                            'active' => 'Active',
                            'rejected' => 'Rejetée',
                            'inactive' => 'Inactive',
                        ];
                    @endphp
                    
                    <span class="badge bg-{{ $statusColors[$shop->status] ?? 'secondary' }} fs-6">
                        {{ $statusLabels[$shop->status] ?? ucfirst($shop->status) }}
                    </span>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="bi bi-telephone me-2"></i>Téléphone</span>
                        <span>{{ $shop->phone ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="bi bi-geo-alt me-2"></i>Adresse</span>
                        <span>{{ $shop->address ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="bi bi-calendar me-2"></i>Créée le</span>
                        <span>{{ $shop->created_at->format('d/m/Y') }}</span>
                    </li>
                </ul>
            </div>

            <!-- Actions Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Actions</h5>
                </div>
                <div class="card-body">
                    @if($shop->status === 'pending')
                        <button class="btn btn-success w-100 mb-2" onclick="approveShop({{ $shop->id }})">
                            <i class="bi bi-check-circle me-1"></i> Approuver
                        </button>
                        <button class="btn btn-danger w-100" onclick="rejectShop({{ $shop->id }})">
                            <i class="bi bi-x-circle me-1"></i> Rejeter
                        </button>
                    @elseif($shop->status === 'approved' || $shop->status === 'active')
                        <button class="btn btn-warning w-100" onclick="suspendShop({{ $shop->id }})">
                            <i class="bi bi-pause-circle me-1"></i> Suspendre
                        </button>
                    @elseif($shop->status === 'inactive' || $shop->status === 'rejected')
                        <button class="btn btn-success w-100" onclick="approveShop({{ $shop->id }})">
                            <i class="bi bi-check-circle me-1"></i> Réactiver
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Vendor Info -->
            @if($shop->vendor)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>Informations du vendeur</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nom:</strong> {{ $shop->vendor->name }}</p>
                            <p><strong>Email:</strong> {{ $shop->vendor->email }}</p>
                            <p><strong>Téléphone:</strong> {{ $shop->vendor->phone ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Statut vendeur:</strong> 
                                <span class="badge bg-{{ $statusColors[$shop->vendor->status] ?? 'secondary' }}">
                                    {{ $statusLabels[$shop->vendor->status] ?? ucfirst($shop->vendor->status) }}
                                </span>
                            </p>
                            <p><strong>Commission:</strong> {{ $shop->vendor->commission_rate }}%</p>
                            <p><strong>Inscrit le:</strong> {{ $shop->vendor->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.vendors.show', $shop->vendor->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye me-1"></i> Voir le profil vendeur
                    </a>
                </div>
            </div>
            @endif

            <!-- Description -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-text-paragraph me-2"></i>Description</h5>
                </div>
                <div class="card-body">
                    <p>{{ $shop->description ?? 'Aucune description fournie.' }}</p>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Statistiques</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h3 class="text-primary mb-0">{{ $productsCount }}</h3>
                                <small class="text-muted">Produits totaux</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h3 class="text-success mb-0">{{ $activeProductsCount }}</h3>
                                <small class="text-muted">Produits actifs</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banner -->
            @if($shop->banner)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-image me-2"></i>Bannière</h5>
                </div>
                <div class="card-body">
                    <img src="{{ asset('storage/' . $shop->banner) }}" alt="Bannière" class="img-fluid rounded">
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
function approveShop(id) {
    if (confirm('Voulez-vous approuver cette boutique ?')) {
        $.ajax({
            url: '/admin/shops/' + id + '/approve',
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message, "Succès");
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function() { toastr.error("Erreur lors de l'approbation", "Erreur"); }
        });
    }
}

function rejectShop(id) {
    if (confirm('Voulez-vous rejeter cette boutique ?')) {
        $.ajax({
            url: '/admin/shops/' + id + '/reject',
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
                if (response.success) {
                    toastr.warning(response.message, "Boutique rejetée");
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function() { toastr.error("Erreur lors du rejet", "Erreur"); }
        });
    }
}

function suspendShop(id) {
    if (confirm('Voulez-vous suspendre cette boutique ?')) {
        $.ajax({
            url: '/admin/shops/' + id + '/suspend',
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
                if (response.success) {
                    toastr.warning(response.message, "Boutique suspendue");
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function() { toastr.error("Erreur lors de la suspension", "Erreur"); }
        });
    }
}
</script>
@endsection
