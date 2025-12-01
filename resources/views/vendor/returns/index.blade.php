@extends('vendor.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-arrow-return-left me-2"></i>Demandes de retour</h4>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="text-warning mb-0">{{ $stats['pending'] }}</h3>
                    <small class="text-muted">En attente</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="text-info mb-0">{{ $stats['approved'] }}</h3>
                    <small class="text-muted">Approuvées</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="text-primary mb-0">{{ $stats['shipped'] }}</h3>
                    <small class="text-muted">En transit</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="text-success mb-0">{{ $stats['completed'] }}</h3>
                    <small class="text-muted">Terminées</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Statut</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Renvoyé</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Reçu</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Remboursé</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Refusé</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Returns list -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Produit</th>
                        <th>Type</th>
                        <th>Raison</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                        <tr>
                            <td><strong>{{ $return->id }}</strong></td>
                            <td>{{ $return->customer->name ?? 'Client' }}</td>
                            <td>{{ Str::limit($return->orderDetail->product->name ?? 'Produit', 30) }}</td>
                            <td><span class="badge bg-secondary">{{ $return->type_label }}</span></td>
                            <td>{{ Str::limit($return->reason_label, 20) }}</td>
                            <td><strong>{{ number_format($return->refund_amount, 2, ',', ' ') }} €</strong></td>
                            <td><span class="badge bg-{{ $return->status_color }}">{{ $return->status_label }}</span></td>
                            <td>{{ $return->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('vendor.returns.show', $return->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                Aucune demande de retour
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $returns->links() }}
    </div>
</div>
@endsection
