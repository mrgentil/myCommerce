@extends('admin.layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body text-center">
                <h3>{{ $stats['pending'] }}</h3>
                <small>En attente</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['approved'] }}</h3>
                <small>Approuvés</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['shipped'] }}</h3>
                <small>En transit</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['completed'] }}</h3>
                <small>Complétés</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header card-header-bg text-white d-flex justify-content-between align-items-center">
        <h6><i class="fas fa-undo me-2"></i>Gestion des Retours</h6>
        <div>
            <a href="?status=pending" class="btn btn-sm {{ request('status') == 'pending' ? 'btn-light' : 'btn-outline-light' }}">En attente</a>
            <a href="?status=approved" class="btn btn-sm {{ request('status') == 'approved' ? 'btn-light' : 'btn-outline-light' }}">Approuvés</a>
            <a href="{{ route('admin.returns.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-light' : 'btn-outline-light' }}">Tous</a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Commande</th>
                    <th>Produit</th>
                    <th>Client</th>
                    <th>Vendeur</th>
                    <th>Raison</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $return)
                    <tr>
                        <td>{{ $return->id }}</td>
                        <td>#{{ $return->order_id }}</td>
                        <td>{{ $return->orderDetail?->product?->name ?? 'N/A' }}</td>
                        <td>{{ $return->customer->name ?? 'N/A' }}</td>
                        <td>{{ $return->vendor->shop->name ?? 'N/A' }}</td>
                        <td>{{ $return->reason_label }}</td>
                        <td>{{ number_format($return->refund_amount ?? 0, 2) }}€</td>
                        <td><span class="badge bg-{{ $return->status_color }}">{{ $return->status_label }}</span></td>
                        <td>
                            <a href="{{ route('admin.returns.show', $return->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">Aucun retour</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $returns->links() }}
    </div>
</div>
@endsection
