@extends('admin.layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-warning text-dark">
            <div class="card-body text-center">
                <h3>{{ $stats['open'] }}</h3>
                <small>Litiges ouverts</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['escalated'] }}</h3>
                <small>Escaladés</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['resolved'] }}</h3>
                <small>Résolus</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header card-header-bg text-white d-flex justify-content-between align-items-center">
        <h6><i class="fas fa-gavel me-2"></i>Gestion des Litiges</h6>
        <div>
            <a href="?status=open" class="btn btn-sm {{ request('status') == 'open' ? 'btn-light' : 'btn-outline-light' }}">Ouverts</a>
            <a href="?status=escalated" class="btn btn-sm {{ request('status') == 'escalated' ? 'btn-light' : 'btn-outline-light' }}">Escaladés</a>
            <a href="{{ route('admin.disputes.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-light' : 'btn-outline-light' }}">Tous</a>
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
                    <th>Client</th>
                    <th>Vendeur</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($disputes as $dispute)
                    <tr>
                        <td>{{ $dispute->id }}</td>
                        <td><a href="#">#{{ $dispute->order_id }}</a></td>
                        <td>{{ $dispute->customer->name ?? 'N/A' }}</td>
                        <td>{{ $dispute->vendor->shop->name ?? $dispute->vendor->name ?? 'N/A' }}</td>
                        <td>{{ $dispute->type_label }}</td>
                        <td>{{ number_format($dispute->amount_disputed, 2) }}€</td>
                        <td><span class="badge bg-{{ $dispute->status_color }}">{{ $dispute->status_label }}</span></td>
                        <td>{{ $dispute->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.disputes.show', $dispute->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">Aucun litige</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $disputes->links() }}
    </div>
</div>
@endsection
