@extends('admin.layouts.admin')

@section('content')
<div class="card mt-4">
    <div class="card-header card-header-bg text-white d-flex justify-content-between align-items-center">
        <h6><i class="fas fa-bolt me-2"></i>Ventes Flash</h6>
        <a href="{{ route('admin.flash-sales.create') }}" class="btn btn-light btn-sm">
            <i class="fas fa-plus me-1"></i>Nouvelle Vente Flash
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Période</th>
                    <th>Produits</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($flashSales as $sale)
                    <tr>
                        <td>{{ $sale->title }}</td>
                        <td>
                            <small>
                                {{ $sale->starts_at->format('d/m/Y H:i') }}<br>
                                → {{ $sale->ends_at->format('d/m/Y H:i') }}
                            </small>
                        </td>
                        <td>{{ $sale->products_count }} produits</td>
                        <td>
                            @if($sale->isLive())
                                <span class="badge bg-success">En cours</span>
                            @elseif($sale->starts_at > now())
                                <span class="badge bg-info">À venir</span>
                            @else
                                <span class="badge bg-secondary">Terminée</span>
                            @endif
                            @if(!$sale->is_active)
                                <span class="badge bg-danger">Désactivée</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.flash-sales.edit', $sale->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.flash-sales.destroy', $sale->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette vente flash ?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">Aucune vente flash</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $flashSales->links() }}
    </div>
</div>
@endsection
