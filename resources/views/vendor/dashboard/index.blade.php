@extends('vendor.layouts.master')

@section('css')
<style>
    .stat-card {
        border-radius: 16px;
        border: none;
        transition: all 0.3s;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .stat-card .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .stat-card .stat-value {
        font-size: 28px;
        font-weight: 700;
        line-height: 1.2;
    }
    .stat-card .stat-label {
        font-size: 13px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-card .stat-change {
        font-size: 12px;
        padding: 2px 8px;
        border-radius: 20px;
    }
    .stat-card .stat-change.positive {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    .stat-card .stat-change.negative {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    .welcome-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        color: white;
        padding: 30px;
        margin-bottom: 25px;
    }
    .welcome-banner h2 {
        font-weight: 700;
        margin-bottom: 5px;
    }
    .quick-action {
        background: white;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        transition: all 0.3s;
        text-decoration: none;
        color: #333;
        display: block;
    }
    .quick-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        color: #667eea;
    }
    .quick-action i {
        font-size: 24px;
        margin-bottom: 8px;
        color: #667eea;
    }
    .chart-container {
        position: relative;
        height: 250px;
    }
    .order-item {
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }
    .order-item:hover {
        background: #f8f9fa;
    }
    .order-item:last-child {
        border-bottom: none;
    }
    .status-badge {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 500;
    }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-processing { background: #cce5ff; color: #004085; }
    .status-shipped { background: #d4edda; color: #155724; }
    .status-completed { background: #d1e7dd; color: #0a3622; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    .alert-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 8px;
        background: #fff5f5;
        border-left: 3px solid #dc3545;
    }
    .alert-item.warning {
        background: #fff8e6;
        border-left-color: #ffc107;
    }
    .product-rank {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
    }
    .rank-1 { background: linear-gradient(135deg, #ffd700, #ffb347); color: #333; }
    .rank-2 { background: linear-gradient(135deg, #c0c0c0, #a8a8a8); color: #333; }
    .rank-3 { background: linear-gradient(135deg, #cd7f32, #b8860b); color: #fff; }
    .rank-default { background: #e9ecef; color: #6c757d; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2>Bonjour, {{ $vendor->name }} 👋</h2>
                <p class="mb-0 opacity-75">Bienvenue dans votre espace vendeur. Voici un aperçu de votre activité.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('vendor.products.create') }}" class="btn btn-light px-4">
                    <i class="bi bi-plus-lg me-2"></i>Nouveau produit
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <!-- Ventes du jour -->
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label mb-1">Ventes aujourd'hui</p>
                            <h3 class="stat-value mb-0">{{ number_format($todaySales, 0, ',', ' ') }} €</h3>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-currency-euro"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ventes du mois -->
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label mb-1">Ventes ce mois</p>
                            <h3 class="stat-value mb-0">{{ number_format($monthSales, 0, ',', ' ') }} €</h3>
                            @if($salesGrowth != 0)
                                <span class="stat-change {{ $salesGrowth >= 0 ? 'positive' : 'negative' }}">
                                    <i class="bi bi-arrow-{{ $salesGrowth >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($salesGrowth) }}%
                                </span>
                            @endif
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commandes -->
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label mb-1">Commandes</p>
                            <h3 class="stat-value mb-0">{{ $totalOrders }}</h3>
                            <small class="text-muted">{{ $pendingOrders }} en attente</small>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-bag-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produits -->
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label mb-1">Produits</p>
                            <h3 class="stat-value mb-0">{{ $totalProducts }}</h3>
                            <small class="text-muted">{{ $activeProducts }} actifs</small>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col">
            <a href="{{ route('vendor.products.create') }}" class="quick-action">
                <i class="bi bi-plus-circle d-block"></i>
                <small>Ajouter produit</small>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('vendor.orders.index') }}" class="quick-action">
                <i class="bi bi-clipboard-check d-block"></i>
                <small>Voir commandes</small>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('vendor.shop.edit') }}" class="quick-action">
                <i class="bi bi-shop d-block"></i>
                <small>Ma boutique</small>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('vendor.products.index') }}" class="quick-action">
                <i class="bi bi-grid d-block"></i>
                <small>Mes produits</small>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('vendor.profile.edit') }}" class="quick-action">
                <i class="bi bi-person-gear d-block"></i>
                <small>Mon profil</small>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sales Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart me-2"></i>Évolution des ventes</h6>
                        <span class="badge bg-light text-muted">7 derniers jours</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Commandes récentes</h6>
                        <a href="{{ route('vendor.orders.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @forelse($recentOrders as $order)
                        <div class="order-item px-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>#{{ $order->id }}</strong>
                                    <span class="text-muted ms-2">{{ $order->customer->name ?? 'Client' }}</span>
                                </div>
                                <div class="text-end">
                                    <span class="status-badge status-{{ $order->status }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    <div class="small text-muted mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mb-0 mt-2">Aucune commande récente</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Order Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart me-2"></i>Statut des commandes</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">En attente</span>
                        <span class="badge bg-warning text-dark">{{ $pendingOrders }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">En cours</span>
                        <span class="badge bg-info">{{ $processingOrders }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Complétées</span>
                        <span class="badge bg-success">{{ $completedOrders }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total</strong>
                        <strong>{{ $totalOrders }}</strong>
                    </div>
                </div>
            </div>

            <!-- Stock Alerts -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Alertes stock</h6>
                        @if($lowStockProducts + $outOfStockProducts > 0)
                            <span class="badge bg-danger">{{ $lowStockProducts + $outOfStockProducts }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($outOfStockProducts > 0)
                        <div class="alert-item">
                            <i class="bi bi-x-circle text-danger me-2"></i>
                            <div>
                                <strong>{{ $outOfStockProducts }}</strong> produit(s) en rupture
                            </div>
                        </div>
                    @endif
                    @if($lowStockProducts > 0)
                        <div class="alert-item warning">
                            <i class="bi bi-exclamation-circle text-warning me-2"></i>
                            <div>
                                <strong>{{ $lowStockProducts }}</strong> produit(s) stock faible
                            </div>
                        </div>
                    @endif
                    @if($lowStockProducts + $outOfStockProducts == 0)
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-check-circle fs-3 text-success"></i>
                            <p class="mb-0 mt-2">Tous les stocks sont OK</p>
                        </div>
                    @endif

                    @if($lowStockAlerts->count() > 0)
                        <hr>
                        <small class="text-muted d-block mb-2">Produits à réapprovisionner :</small>
                        @foreach($lowStockAlerts as $product)
                            @php 
                                $minStock = $product->variants->min('stock') ?? 0;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-truncate" style="max-width: 180px;">{{ $product->name }}</span>
                                <span class="badge {{ $minStock == 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                    {{ $minStock }} unité(s)
                                </span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Top Products -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-trophy me-2 text-warning"></i>Mes produits</h6>
                </div>
                <div class="card-body">
                    @forelse($topProducts as $index => $product)
                        <div class="d-flex align-items-center mb-3">
                            <div class="product-rank me-3 {{ $index < 3 ? 'rank-'.($index+1) : 'rank-default' }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-grow-1 text-truncate">
                                <div class="text-truncate fw-medium">{{ $product->name }}</div>
                                <small class="text-muted">{{ $product->primaryVariant?->price ?? 0 }} €</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">
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
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($salesChartData['labels']),
            datasets: [{
                label: 'Ventes (€)',
                data: @json($salesChartData['data']),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
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
