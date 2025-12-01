@extends('themes.xylo.layouts.master')

@section('content')
<div class="container py-5">
    <h3 class="mb-4"><i class="bi bi-arrow-return-left me-2"></i>Mes retours</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @forelse($returns as $return)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="badge bg-{{ $return->status_color }} px-3 py-2">
                                    {{ $return->status_label }}
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $return->orderDetail->product->name ?? 'Produit' }}</h6>
                                <small class="text-muted">
                                    Retour #{{ $return->id }} · Commande #{{ $return->order_id }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <small class="text-muted d-block">Type</small>
                        <strong>{{ $return->type_label }}</strong>
                    </div>
                    <div class="col-md-3 text-end">
                        <small class="text-muted d-block">{{ $return->created_at->format('d/m/Y') }}</small>
                        <a href="{{ route('customer.returns.show', $return->id) }}" class="btn btn-sm btn-primary mt-2">
                            Voir détails
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="bi bi-box-arrow-left fs-1 text-muted"></i>
            <h5 class="mt-3">Aucune demande de retour</h5>
            <p class="text-muted">Vos demandes de retour apparaîtront ici</p>
        </div>
    @endforelse

    {{ $returns->links() }}
</div>
@endsection
