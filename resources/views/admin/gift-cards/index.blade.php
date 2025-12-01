@extends('admin.layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['total'] }}</h3>
                <small>Total cartes</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['active'] }}</h3>
                <small>Cartes actives</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3>{{ number_format($stats['total_value'], 2) }}€</h3>
                <small>Valeur totale</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header card-header-bg text-white d-flex justify-content-between align-items-center">
        <h6><i class="fas fa-gift me-2"></i>Cartes Cadeaux</h6>
        <a href="{{ route('admin.gift-cards.create') }}" class="btn btn-light btn-sm">
            <i class="fas fa-plus me-1"></i>Créer des cartes
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="mb-3">
            <a href="?status=active" class="btn btn-sm {{ request('status') == 'active' ? 'btn-success' : 'btn-outline-success' }}">Actives</a>
            <a href="?status=used" class="btn btn-sm {{ request('status') == 'used' ? 'btn-secondary' : 'btn-outline-secondary' }}">Utilisées</a>
            <a href="{{ route('admin.gift-cards.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">Toutes</a>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Valeur initiale</th>
                    <th>Solde actuel</th>
                    <th>Acheteur</th>
                    <th>Destinataire</th>
                    <th>Expire le</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($giftCards as $card)
                    <tr>
                        <td><code>{{ $card->code }}</code></td>
                        <td>{{ number_format($card->initial_balance, 2) }}€</td>
                        <td class="{{ $card->current_balance > 0 ? 'text-success' : 'text-muted' }}">
                            {{ number_format($card->current_balance, 2) }}€
                        </td>
                        <td>{{ $card->purchaser->name ?? 'Admin' }}</td>
                        <td>{{ $card->recipient_name ?? $card->recipient_email ?? '-' }}</td>
                        <td>{{ $card->expires_at?->format('d/m/Y') ?? '-' }}</td>
                        <td>
                            @if(!$card->is_active)
                                <span class="badge bg-danger">Désactivée</span>
                            @elseif($card->current_balance <= 0)
                                <span class="badge bg-secondary">Épuisée</span>
                            @elseif($card->expires_at && $card->expires_at->isPast())
                                <span class="badge bg-warning">Expirée</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.gift-cards.show', $card->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ route('admin.gift-cards.toggle', $card->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm {{ $card->is_active ? 'btn-warning' : 'btn-success' }}">
                                    <i class="fas fa-{{ $card->is_active ? 'ban' : 'check' }}"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">Aucune carte cadeau</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $giftCards->links() }}
    </div>
</div>
@endsection
