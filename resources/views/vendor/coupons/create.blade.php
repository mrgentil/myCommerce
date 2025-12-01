@extends('vendor.layouts.master')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('vendor.coupons.index') }}" class="text-muted text-decoration-none mb-2 d-inline-block">
                <i class="bi bi-arrow-left me-1"></i>Retour aux coupons
            </a>
            <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Nouveau coupon</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('vendor.coupons.store') }}" method="POST">
                @csrf
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Informations du coupon</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom du coupon <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" placeholder="Ex: Soldes d'été" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Code promo</label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                       value="{{ old('code') }}" placeholder="Auto-généré si vide" 
                                       style="text-transform: uppercase;">
                                <small class="text-muted">Laissez vide pour génération automatique</small>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2"
                                          placeholder="Description affichée aux clients...">{{ old('description') }}</textarea>
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
                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Pourcentage (%)</option>
                                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Montant fixe (€)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Valeur <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="value" class="form-control @error('value') is-invalid @enderror"
                                           value="{{ old('value') }}" step="0.01" min="0" required>
                                    <span class="input-group-text" id="valueSuffix">%</span>
                                </div>
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Montant minimum de commande</label>
                                <div class="input-group">
                                    <input type="number" name="min_order_amount" class="form-control"
                                           value="{{ old('min_order_amount') }}" step="0.01" min="0">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Réduction maximale</label>
                                <div class="input-group">
                                    <input type="number" name="max_discount" class="form-control"
                                           value="{{ old('max_discount') }}" step="0.01" min="0">
                                    <span class="input-group-text">€</span>
                                </div>
                                <small class="text-muted">Pour les pourcentages uniquement</small>
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
                                       value="{{ old('start_date') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de fin</label>
                                <input type="date" name="end_date" class="form-control"
                                       value="{{ old('end_date') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Limite d'utilisation totale</label>
                                <input type="number" name="usage_limit" class="form-control"
                                       value="{{ old('usage_limit') }}" min="1">
                                <small class="text-muted">Laissez vide pour illimité</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Limite par client</label>
                                <input type="number" name="usage_per_customer" class="form-control"
                                       value="{{ old('usage_per_customer') }}" min="1">
                                <small class="text-muted">Laissez vide pour illimité</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                            <label class="form-check-label" for="isActive">
                                <strong>Coupon actif</strong>
                                <br><small class="text-muted">Désactivez pour mettre en pause</small>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('vendor.coupons.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Créer le coupon
                    </button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Conseils</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Code mémorable</strong><br>
                            <small class="text-muted">Utilisez des codes faciles à retenir comme SOLDES20</small>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Montant minimum</strong><br>
                            <small class="text-muted">Définissez un minimum pour encourager les achats plus importants</small>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Limite d'utilisation</strong><br>
                            <small class="text-muted">Créez un sentiment d'urgence avec des limites</small>
                        </li>
                        <li>
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Date d'expiration</strong><br>
                            <small class="text-muted">Les offres limitées dans le temps convertissent mieux</small>
                        </li>
                    </ul>
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
