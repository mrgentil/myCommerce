@extends('admin.layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>{{ number_format($stats['total_points_issued']) }}</h3>
                <small>Points émis</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-dark">
            <div class="card-body text-center">
                <h3>{{ number_format($stats['total_points_redeemed']) }}</h3>
                <small>Points utilisés</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['active_customers'] }}</h3>
                <small>Clients avec points</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Rewards -->
        <div class="card mb-4">
            <div class="card-header card-header-bg text-white d-flex justify-content-between align-items-center">
                <h6><i class="fas fa-gift me-2"></i>Récompenses</h6>
                <a href="{{ route('admin.loyalty.rewards.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus me-1"></i>Nouvelle récompense
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Récompense</th>
                            <th>Points requis</th>
                            <th>Type</th>
                            <th>Valeur</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rewards as $reward)
                            <tr>
                                <td>{{ $reward->name }}</td>
                                <td><span class="badge bg-primary">{{ $reward->points_required }} pts</span></td>
                                <td>{{ $reward->reward_type_label }}</td>
                                <td>{{ $reward->reward_value }}</td>
                                <td>
                                    @if($reward->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.loyalty.rewards.edit', $reward->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.loyalty.rewards.delete', $reward->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3">Aucune récompense configurée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-history me-2"></i>Transactions récentes</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Points</th>
                            <th>Description</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $transaction)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.loyalty.customer-history', $transaction->customer_id) }}">
                                        {{ $transaction->customer->name ?? 'N/A' }}
                                    </a>
                                </td>
                                <td><span class="badge bg-{{ $transaction->type_color }}">{{ $transaction->type_label }}</span></td>
                                <td class="{{ $transaction->points > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->points > 0 ? '+' : '' }}{{ $transaction->points }}
                                </td>
                                <td>{{ $transaction->description }}</td>
                                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Add Points Manually -->
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-plus-circle me-2"></i>Ajouter des points</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.loyalty.add-points') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Client</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">Sélectionner...</option>
                            @foreach(\App\Models\Customer::orderBy('name')->get() as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Points</label>
                        <input type="number" name="points" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Raison</label>
                        <input type="text" name="reason" class="form-control" placeholder="Ex: Bonus spécial" required>
                    </div>
                    <button class="btn btn-success w-100">Ajouter les points</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
