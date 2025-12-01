@extends('vendor.layouts.master')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('vendor.coupons.index') }}" class="text-muted text-decoration-none mb-2 d-inline-block">
                <i class="bi bi-arrow-left me-1"></i>Retour aux coupons
            </a>
            <h4 class="mb-0"><i class="bi bi-pencil me-2"></i>Modifier le coupon</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('vendor.coupons.update', $coupon->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Informations du coupon</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom du coupon <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $coupon->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Code promo <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                       value="{{ old('code', $coupon->code) }}" required
                                       style="text-transform: uppercase;">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2">{{ old('description', $coupon->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-percent me-2"></i>Réduction</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Type de réduction <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" id="discountType">
                                    <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>Pourcentage (%)</option>
                                    <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Montant fixe (€)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Valeur <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="value" class="form-control"
                                           value="{{ old('value', $coupon->value) }}" step="0.01" min="0" required>
                                    <span class="input-group-text" id="valueSuffix">{{ $coupon->type == 'percentage' ? '%' : '€' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Montant minimum de commande</label>
                                <div class="input-group">
                                    <input type="number" name="min_order_amount" class="form-control"
                                           value="{{ old('min_order_amount', $coupon->min_order_amount) }}" step="0.01" min="0">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Réduction maximale</label>
                                <div class="input-group">
                                    <input type="number" name="max_discount" class="form-control"
                                           value="{{ old('max_discount', $coupon->max_discount) }}" step="0.01" min="0">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-range me-2"></i>Validité</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Date de début</label>
                                <input type="date" name="start_date" class="form-control"
                                       value="{{ old('start_date', $coupon->start_date?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de fin</label>
                                <input type="date" name="end_date" class="form-control"
                                       value="{{ old('end_date', $coupon->end_date?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Limite d'utilisation totale</label>
                                <input type="number" name="usage_limit" class="form-control"
                                       value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Limite par client</label>
                                <input type="number" name="usage_per_customer" class="form-control"
                                       value="{{ old('usage_per_customer', $coupon->usage_per_customer) }}" min="1">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" 
                                   {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">
                                <strong>Coupon actif</strong>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('vendor.coupons.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Statistiques</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="fw-bold text-primary mb-0">{{ $coupon->usage_count }}</h2>
                    <p class="text-muted">Utilisations</p>
                    
                    @if($coupon->usage_limit)
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ min(100, ($coupon->usage_count / $coupon->usage_limit) * 100) }}%"></div>
                        </div>
                        <small class="text-muted">{{ $coupon->usage_count }} / {{ $coupon->usage_limit }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.getElementById('discountType').addEventListener('change', function() {
    document.getElementById('valueSuffix').textContent = this.value === 'percentage' ? '%' : '€';
});
</script>
@endsection
