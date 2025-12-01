@extends('vendor.layouts.master')

@section('css')
<style>
    .finance-card {
        border-radius: 16px;
        border: none;
        transition: all 0.3s;
        overflow: hidden;
    }
    .finance-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .balance-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 30px;
    }
    .balance-card .balance-amount {
        font-size: 42px;
        font-weight: 700;
    }
    .stat-mini {
        background: rgba(255,255,255,0.15);
        border-radius: 12px;
        padding: 15px;
        text-align: center;
    }
    .stat-mini .value {
        font-size: 20px;
        font-weight: 700;
    }
    .stat-mini .label {
        font-size: 12px;
        opacity: 0.8;
    }
    .transaction-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .transaction-item:last-child {
        border-bottom: none;
    }
    .transaction-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }
    .transaction-icon.income {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    .transaction-icon.payout {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    .payout-status {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 20px;
    }
    .payout-status.pending { background: #fff3cd; color: #856404; }
    .payout-status.completed { background: #d4edda; color: #155724; }
    .payout-status.rejected { background: #f8d7da; color: #721c24; }
    .chart-container {
        height: 280px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-wallet2 me-2"></i>Mes finances</h4>
            <p class="text-muted mb-0">Suivez vos revenus et demandez des retraits</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Balance Card -->
            <div class="balance-card mb-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-1 opacity-75">Solde disponible</p>
                        <div class="balance-amount">{{ number_format($availableBalance, 2, ',', ' ') }} €</div>
                        <button class="btn btn-light mt-3" data-bs-toggle="modal" data-bs-target="#payoutModal">
                            <i class="bi bi-cash-stack me-2"></i>Demander un retrait
                        </button>
                    </div>
                    <div class="col-md-6 mt-4 mt-md-0">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="stat-mini">
                                    <div class="value">{{ number_format($todayEarnings, 0, ',', ' ') }} €</div>
                                    <div class="label">Aujourd'hui</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-mini">
                                    <div class="value">{{ number_format($weekEarnings, 0, ',', ' ') }} €</div>
                                    <div class="label">Cette semaine</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-mini">
                                    <div class="value">{{ number_format($monthEarnings, 0, ',', ' ') }} €</div>
                                    <div class="label">Ce mois</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-mini">
                                    <div class="value">{{ number_format($totalEarnings, 0, ',', ' ') }} €</div>
                                    <div class="label">Total</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings Chart -->
            <div class="card finance-card mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2"></i>Évolution des revenus</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="earningsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="card finance-card">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Transactions récentes</h6>
                </div>
                <div class="card-body">
                    @forelse($recentTransactions as $transaction)
                        <div class="transaction-item">
                            <div class="transaction-icon income">
                                <i class="bi bi-arrow-down-left"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-medium">Commande #{{ $transaction->id }}</div>
                                <small class="text-muted">{{ $transaction->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <div class="text-success fw-bold">
                                +{{ number_format($transaction->total_amount, 2, ',', ' ') }} €
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mb-0 mt-2">Aucune transaction</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Summary -->
            <div class="card finance-card mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-calculator me-2"></i>Résumé</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Gains bruts</span>
                        <span class="fw-medium">{{ number_format($totalEarnings, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Commission ({{ $commissionRate }}%)</span>
                        <span class="text-danger">-{{ number_format($totalCommission, 2, ',', ' ') }} €</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Gains nets</span>
                        <span class="fw-bold">{{ number_format($netEarnings, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Déjà versé</span>
                        <span class="text-success">{{ number_format($totalPaid, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">En attente</span>
                        <span class="text-warning">{{ number_format($pendingPayouts, 2, ',', ' ') }} €</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Solde disponible</span>
                        <span class="fw-bold text-primary fs-5">{{ number_format($availableBalance, 2, ',', ' ') }} €</span>
                    </div>
                </div>
            </div>

            <!-- Payout History -->
            <div class="card finance-card">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-cash-coin me-2"></i>Historique des retraits</h6>
                </div>
                <div class="card-body">
                    @forelse($payouts as $payout)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="fw-medium">{{ number_format($payout->amount, 2, ',', ' ') }} €</div>
                                <small class="text-muted">{{ $payout->created_at->format('d/m/Y') }}</small>
                            </div>
                            <span class="payout-status {{ $payout->status }}">
                                @if($payout->status == 'pending')
                                    En attente
                                @elseif($payout->status == 'completed')
                                    Versé
                                @else
                                    Rejeté
                                @endif
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-wallet2 fs-3"></i>
                            <p class="mb-0 mt-2">Aucun retrait</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payout Modal -->
<div class="modal fade" id="payoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cash-stack me-2"></i>Demander un retrait</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('vendor.finance.payout') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Solde disponible : <strong>{{ number_format($availableBalance, 2, ',', ' ') }} €</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Montant à retirer</label>
                        <div class="input-group">
                            <input type="number" name="amount" class="form-control" min="10" max="{{ $availableBalance }}" step="0.01" required>
                            <span class="input-group-text">€</span>
                        </div>
                        <small class="text-muted">Minimum : 10 €</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mode de paiement</label>
                        <select name="payment_method" class="form-select">
                            <option value="bank_transfer">Virement bancaire</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes (optionnel)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Informations supplémentaires..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i>Envoyer la demande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('earningsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($monthlyEarnings['labels']),
            datasets: [{
                label: 'Revenus (€)',
                data: @json($monthlyEarnings['data']),
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: '#667eea',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>
@endsection
