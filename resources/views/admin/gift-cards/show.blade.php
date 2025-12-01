@extends('admin.layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header card-header-bg text-white">
                <h6><i class="fas fa-gift me-2"></i>Carte Cadeau</h6>
            </div>
            <div class="card-body text-center">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="p-4 bg-gradient rounded mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h2 class="text-white mb-3">🎁</h2>
                    <h3 class="text-white"><code>{{ $giftCard->code }}</code></h3>
                    <h4 class="text-white">{{ number_format($giftCard->current_balance, 2) }}€</h4>
                    <small class="text-white-50">sur {{ number_format($giftCard->initial_balance, 2) }}€</small>
                </div>

                <table class="table text-start">
                    <tr>
                        <th>Statut:</th>
                        <td>
                            @if(!$giftCard->is_active)
                                <span class="badge bg-danger">Désactivée</span>
                            @elseif($giftCard->current_balance <= 0)
                                <span class="badge bg-secondary">Épuisée</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Achetée le:</th>
                        <td>{{ $giftCard->purchased_at?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Expire le:</th>
                        <td>{{ $giftCard->expires_at?->format('d/m/Y') ?? 'Jamais' }}</td>
                    </tr>
                    <tr>
                        <th>Acheteur:</th>
                        <td>{{ $giftCard->purchaser->name ?? 'Admin' }}</td>
                    </tr>
                    @if($giftCard->recipient_name || $giftCard->recipient_email)
                        <tr>
                            <th>Destinataire:</th>
                            <td>{{ $giftCard->recipient_name }} {{ $giftCard->recipient_email ? '('.$giftCard->recipient_email.')' : '' }}</td>
                        </tr>
                    @endif
                    @if($giftCard->message)
                        <tr>
                            <th>Message:</th>
                            <td>{{ $giftCard->message }}</td>
                        </tr>
                    @endif
                </table>

                <div class="d-flex gap-2 justify-content-center">
                    <form action="{{ route('admin.gift-cards.toggle', $giftCard->id) }}" method="POST">
                        @csrf
                        <button class="btn {{ $giftCard->is_active ? 'btn-warning' : 'btn-success' }}">
                            {{ $giftCard->is_active ? 'Désactiver' : 'Activer' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <!-- Adjust Balance -->
        <div class="card mb-4">
            <div class="card-header">
                <h6>Ajuster le solde</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.gift-cards.adjust', $giftCard->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Montant</label>
                        <div class="input-group">
                            <input type="number" name="amount" class="form-control" step="0.01" required>
                            <span class="input-group-text">€</span>
                        </div>
                        <small class="text-muted">Positif pour ajouter, négatif pour retirer</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Raison</label>
                        <input type="text" name="reason" class="form-control" required>
                    </div>
                    <button class="btn btn-primary">Ajuster</button>
                </form>
            </div>
        </div>

        <!-- Transactions -->
        <div class="card">
            <div class="card-header">
                <h6>Historique des transactions</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Solde</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($giftCard->transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($transaction->type == 'purchase')
                                        <span class="badge bg-success">Achat</span>
                                    @elseif($transaction->type == 'redemption')
                                        <span class="badge bg-warning">Utilisation</span>
                                    @else
                                        <span class="badge bg-info">{{ $transaction->type }}</span>
                                    @endif
                                </td>
                                <td class="{{ $transaction->amount > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount, 2) }}€
                                </td>
                                <td>{{ number_format($transaction->balance_after, 2) }}€</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
