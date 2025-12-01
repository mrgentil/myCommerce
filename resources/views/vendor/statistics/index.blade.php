@extends('vendor.layouts.master')

@section('css')
<style>
    .stat-card {
        border-radius: 16px;
        border: none;
        transition: all 0.3s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .stat-value {
        font-size: 28px;
        font-weight: 700;
    }
    .stat-label {
        font-size: 13px;
        color: #6c757d;
        text-transform: uppercase;
    }
    .stat-change {
        font-size: 12px;
        padding: 2px 8px;
        border-radius: 20px;
    }
    .stat-change.positive { background: rgba(40, 167, 69, 0.1); color: #28a745; }
    .stat-change.negative { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
    .chart-container {
        height: 300px;
    }
    .chart-container-sm {
        height: 200px;
    }
    .product-rank {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        background: #e9ecef;
        color: #6c757d;
    }
    .product-rank.gold { background: linear-gradient(135deg, #ffd700, #ffb347); color: #333; }
    .product-rank.silver { background: linear-gradient(135deg, #c0c0c0, #a8a8a8); color: #333; }
    .product-rank.bronze { background: linear-gradient(135deg, #cd7f32, #b8860b); color: #fff; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-graph-up me-2"></i>Statistiques</h4>
            <p class="text-muted mb-0">Analysez les performances de votre boutique</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-secondary active">Ce mois</button>
            <button class="btn btn-outline-secondary">Cette année</button>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label mb-1">Chiffre d'affaires</p>
                            <h3 class="stat-value mb-0">{{ number_format($totalSales, 0, ',', ' ') }} €</h3>
                            @if($salesGrowth != 0)
                                <span class="stat-change {{ $salesGrowth >= 0 ? 'positive' : 'negative' }}">
                                    <i class="bi bi-arrow-{{ $salesGrowth >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($salesGrowth) }}% vs mois dernier
                                </span>
                            @endif
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-currency-euro"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label mb-1">Commandes</p>
                            <h3 class="stat-value mb-0">{{ $totalOrders }}</h3>
                            <small class="text-muted">{{ $completedOrders }} complétées</small>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-bag-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label mb-1">Taux de conversion</p>
                            <h3 class="stat-value mb-0">{{ $conversionRate }}%</h3>
                            <small class="text-muted">Commandes complétées</small>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-percent"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label mb-1">Panier moyen</p>
                            <h3 class="stat-value mb-0">{{ number_format($averageOrderValue, 2, ',', ' ') }} €</h3>
                            <small class="text-muted">Par commande</small>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-cart3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sales Chart -->
        <div class="col-lg-8">
            <div class="card stat-card mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart me-2"></i>Évolution des ventes</h6>
                        <span class="badge bg-light text-muted">12 derniers mois</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Daily Sales -->
            <div class="card stat-card">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-calendar3 me-2"></i>Ventes quotidiennes ({{ now()->format('F Y') }})</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="dailyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Order Status -->
            <div class="card stat-card mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart me-2"></i>Répartition des commandes</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container-sm">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><span class="badge bg-warning">&nbsp;</span> En attente</span>
                            <strong>{{ $ordersByStatus['pending'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><span class="badge bg-info">&nbsp;</span> En cours</span>
                            <strong>{{ $ordersByStatus['processing'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><span class="badge bg-primary">&nbsp;</span> Expédiées</span>
                            <strong>{{ $ordersByStatus['shipped'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><span class="badge bg-success">&nbsp;</span> Complétées</span>
                            <strong>{{ $ordersByStatus['completed'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span><span class="badge bg-danger">&nbsp;</span> Annulées</span>
                            <strong>{{ $ordersByStatus['cancelled'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Stats -->
            <div class="card stat-card mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2"></i>Produits</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="mb-0 fw-bold">{{ $totalProducts }}</h4>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-4">
                            <h4 class="mb-0 fw-bold text-success">{{ $activeProducts }}</h4>
                            <small class="text-muted">Actifs</small>
                        </div>
                        <div class="col-4">
                            <h4 class="mb-0 fw-bold text-primary">{{ $totalSalesCount }}</h4>
                            <small class="text-muted">Vendus</small>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h4 class="mb-0 fw-bold">{{ number_format($totalViews) }}</h4>
                        <small class="text-muted">Vues totales</small>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="card stat-card">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-trophy me-2 text-warning"></i>Meilleures ventes</h6>
                </div>
                <div class="card-body">
                    @forelse($topProductsBySales->take(5) as $index => $product)
                        <div class="d-flex align-items-center mb-3">
                            <div class="product-rank me-3 {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-grow-1 text-truncate">
                                <div class="text-truncate fw-medium">{{ $product->name }}</div>
                                <small class="text-muted">{{ $product->primaryVariant?->price ?? 0 }} €</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-box-seam fs-3"></i>
                            <p class="mb-0 mt-2">Aucun produit</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart (Bar)
    new Chart(document.getElementById('salesChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($salesByMonth['labels']),
            datasets: [{
                label: 'Ventes (€)',
                data: @json($salesByMonth['data']),
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Daily Chart (Line)
    new Chart(document.getElementById('dailyChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: @json($dailySales['labels']),
            datasets: [{
                label: 'Ventes (€)',
                data: @json($dailySales['data']),
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Status Chart (Doughnut)
    new Chart(document.getElementById('statusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['En attente', 'En cours', 'Expédiées', 'Complétées', 'Annulées'],
            datasets: [{
                data: [
                    {{ $ordersByStatus['pending'] }},
                    {{ $ordersByStatus['processing'] }},
                    {{ $ordersByStatus['shipped'] }},
                    {{ $ordersByStatus['completed'] }},
                    {{ $ordersByStatus['cancelled'] }}
                ],
                backgroundColor: ['#ffc107', '#17a2b8', '#007bff', '#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            cutout: '70%'
        }
    });
});
</script>
@endsection
