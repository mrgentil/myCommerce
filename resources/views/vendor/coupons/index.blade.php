@extends('vendor.layouts.master')

@section('css')
<style>
    .coupon-card {
        border: none;
        border-radius: 16px;
        transition: all 0.3s;
        overflow: hidden;
    }
    .coupon-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .coupon-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 11px;
        padding: 5px 12px;
        border-radius: 20px;
    }
    .coupon-value {
        font-size: 32px;
        font-weight: 700;
        color: #667eea;
    }
    .coupon-code {
        background: #f8f9fa;
        padding: 8px 15px;
        border-radius: 8px;
        font-family: monospace;
        font-size: 16px;
        font-weight: 600;
        letter-spacing: 2px;
    }
    .stat-mini {
        background: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .stat-mini h3 {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
    }
    .stat-mini p {
        margin: 5px 0 0;
        color: #6c757d;
        font-size: 13px;
    }
    .toggle-switch {
        position: relative;
        width: 50px;
        height: 26px;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.3s;
        border-radius: 26px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }
    input:checked + .toggle-slider {
        background-color: #28a745;
    }
    input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-tag me-2"></i>Mes coupons</h4>
            <p class="text-muted mb-0">Créez et gérez vos codes promo</p>
        </div>
        <a href="{{ route('vendor.coupons.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nouveau coupon
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-mini">
                <h3 class="text-primary">{{ $coupons->count() }}</h3>
                <p>Total coupons</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-mini">
                <h3 class="text-success">{{ $activeCoupons }}</h3>
                <p>Actifs</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-mini">
                <h3 class="text-info">{{ $totalUsage }}</h3>
                <p>Utilisations totales</p>
            </div>
        </div>
    </div>

    <!-- Coupons Grid -->
    <div class="row g-4">
        @forelse($coupons as $coupon)
            <div class="col-md-6 col-lg-4">
                <div class="card coupon-card position-relative h-100">
                    <span class="coupon-badge {{ $coupon->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $coupon->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">{{ $coupon->name }}</h6>
                        
                        <div class="coupon-value mb-2">
                            @if($coupon->type === 'percentage')
                                {{ $coupon->value }}%
                            @else
                                {{ number_format($coupon->value, 2, ',', ' ') }} €
                            @endif
                        </div>

                        <div class="coupon-code mb-3">{{ $coupon->code }}</div>

                        @if($coupon->description)
                            <p class="text-muted small mb-3">{{ Str::limit($coupon->description, 80) }}</p>
                        @endif

                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Utilisations</small>
                                <strong>{{ $coupon->usage_count }}{{ $coupon->usage_limit ? '/'.$coupon->usage_limit : '' }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Expire le</small>
                                <strong>{{ $coupon->end_date ? $coupon->end_date->format('d/m/Y') : '—' }}</strong>
                            </div>
                        </div>

                        @if($coupon->min_order_amount)
                            <small class="text-muted d-block mb-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Min. {{ number_format($coupon->min_order_amount, 2, ',', ' ') }} €
                            </small>
                        @endif

                        <div class="d-flex justify-content-between align-items-center">
                            <label class="toggle-switch mb-0">
                                <input type="checkbox" {{ $coupon->is_active ? 'checked' : '' }} 
                                       onchange="toggleCoupon({{ $coupon->id }})">
                                <span class="toggle-slider"></span>
                            </label>
                            <div>
                                <a href="{{ route('vendor.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCoupon({{ $coupon->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-tag fs-1 text-muted"></i>
                    <h5 class="mt-3">Aucun coupon</h5>
                    <p class="text-muted">Créez votre premier coupon pour attirer plus de clients</p>
                    <a href="{{ route('vendor.coupons.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Créer un coupon
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@section('js')
<script>
function toggleCoupon(id) {
    fetch(`{{ url('vendor/coupons') }}/${id}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
        }
    });
}

function deleteCoupon(id) {
    if (confirm('Supprimer ce coupon ?')) {
        fetch(`{{ url('vendor/coupons') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>
@endsection
