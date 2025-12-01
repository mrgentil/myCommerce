<div class="filter-sidebar">
    <form id="filterForm" method="GET" action="{{ route('shop.index') }}">
        @if($filters['search'] ?? false)
            <input type="hidden" name="q" value="{{ $filters['search'] }}">
        @endif

        <!-- Sort -->
        <div class="filter-section mb-4">
            <h6 class="filter-title">Trier par</h6>
            <select name="sort" class="form-select" onchange="this.form.submit()">
                <option value="newest" {{ ($filters['sort'] ?? '') == 'newest' ? 'selected' : '' }}>Plus récents</option>
                <option value="price_low" {{ ($filters['sort'] ?? '') == 'price_low' ? 'selected' : '' }}>Prix croissant</option>
                <option value="price_high" {{ ($filters['sort'] ?? '') == 'price_high' ? 'selected' : '' }}>Prix décroissant</option>
                <option value="rating" {{ ($filters['sort'] ?? '') == 'rating' ? 'selected' : '' }}>Meilleures notes</option>
                <option value="popular" {{ ($filters['sort'] ?? '') == 'popular' ? 'selected' : '' }}>Plus populaires</option>
                <option value="bestselling" {{ ($filters['sort'] ?? '') == 'bestselling' ? 'selected' : '' }}>Meilleures ventes</option>
            </select>
        </div>

        <!-- Quick Filters -->
        <div class="filter-section mb-4">
            <h6 class="filter-title">Filtres rapides</h6>
            <div class="form-check mb-2">
                <input type="checkbox" name="in_stock" value="1" class="form-check-input" id="inStock"
                       {{ ($filters['in_stock'] ?? false) ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="form-check-label" for="inStock">
                    <i class="bi bi-box-seam text-success me-1"></i> En stock
                </label>
            </div>
            <div class="form-check mb-2">
                <input type="checkbox" name="on_sale" value="1" class="form-check-input" id="onSale"
                       {{ ($filters['on_sale'] ?? false) ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="form-check-label" for="onSale">
                    <i class="bi bi-tag text-danger me-1"></i> En promotion
                </label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="free_shipping" value="1" class="form-check-input" id="freeShipping"
                       {{ ($filters['free_shipping'] ?? false) ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="form-check-label" for="freeShipping">
                    <i class="bi bi-truck text-primary me-1"></i> Livraison gratuite
                </label>
            </div>
        </div>

        <!-- Price Range -->
        <div class="filter-section mb-4">
            <h6 class="filter-title">Prix</h6>
            <div class="price-range-inputs">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">€</span>
                            <input type="number" name="price_min" class="form-control" 
                                   value="{{ $filters['price_min'] ?? 0 }}" min="0" placeholder="Min">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">€</span>
                            <input type="number" name="price_max" class="form-control" 
                                   value="{{ $filters['price_max'] ?? '' }}" placeholder="Max">
                        </div>
                    </div>
                </div>
                <input type="range" class="form-range" id="priceRange" 
                       min="{{ $priceRange['min'] ?? 0 }}" 
                       max="{{ $priceRange['max'] ?? 1000 }}" 
                       value="{{ $filters['price_max'] ?? ($priceRange['max'] ?? 1000) }}">
            </div>
        </div>

        <!-- Rating -->
        <div class="filter-section mb-4">
            <h6 class="filter-title">Note minimum</h6>
            @foreach([4, 3, 2, 1] as $rating)
                <div class="form-check mb-2">
                    <input type="radio" name="rating" value="{{ $rating }}" class="form-check-input" 
                           id="rating{{ $rating }}" {{ ($filters['rating'] ?? '') == $rating ? 'checked' : '' }}
                           onchange="this.form.submit()">
                    <label class="form-check-label" for="rating{{ $rating }}">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $rating ? '-fill text-warning' : ' text-muted' }}"></i>
                        @endfor
                        <span class="ms-1 text-muted small">et plus</span>
                    </label>
                </div>
            @endforeach
            @if($filters['rating'] ?? false)
                <a href="{{ request()->fullUrlWithQuery(['rating' => null]) }}" class="text-danger small">
                    <i class="bi bi-x"></i> Effacer
                </a>
            @endif
        </div>

        <!-- Categories -->
        @if($categories->count() > 0)
            <div class="filter-section mb-4">
                <h6 class="filter-title d-flex justify-content-between align-items-center" 
                    data-bs-toggle="collapse" data-bs-target="#categoryCollapse" style="cursor: pointer;">
                    Catégories
                    <i class="bi bi-chevron-down"></i>
                </h6>
                <div class="collapse show" id="categoryCollapse">
                    @foreach($categories->take(8) as $category)
                        <div class="form-check mb-2">
                            <input type="checkbox" name="category[]" value="{{ $category->id }}" 
                                   class="form-check-input" id="cat{{ $category->id }}"
                                   {{ in_array($category->id, (array)($filters['category'] ?? [])) ? 'checked' : '' }}
                                   onchange="this.form.submit()">
                            <label class="form-check-label d-flex justify-content-between" for="cat{{ $category->id }}">
                                <span>{{ $category->name }}</span>
                                <span class="badge bg-light text-dark">{{ $category->products_count }}</span>
                            </label>
                        </div>
                    @endforeach
                    @if($categories->count() > 8)
                        <a href="#" class="text-primary small" data-bs-toggle="modal" data-bs-target="#allCategoriesModal">
                            Voir tout ({{ $categories->count() }})
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Brands -->
        @if($brands->count() > 0)
            <div class="filter-section mb-4">
                <h6 class="filter-title d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse" data-bs-target="#brandCollapse" style="cursor: pointer;">
                    Marques
                    <i class="bi bi-chevron-down"></i>
                </h6>
                <div class="collapse show" id="brandCollapse">
                    @foreach($brands->take(8) as $brand)
                        <div class="form-check mb-2">
                            <input type="checkbox" name="brand[]" value="{{ $brand->id }}" 
                                   class="form-check-input" id="brand{{ $brand->id }}"
                                   {{ in_array($brand->id, (array)($filters['brand'] ?? [])) ? 'checked' : '' }}
                                   onchange="this.form.submit()">
                            <label class="form-check-label d-flex justify-content-between" for="brand{{ $brand->id }}">
                                <span>{{ $brand->name }}</span>
                                <span class="badge bg-light text-dark">{{ $brand->products_count }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Shops -->
        @if($shops->count() > 0)
            <div class="filter-section mb-4">
                <h6 class="filter-title d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse" data-bs-target="#shopCollapse" style="cursor: pointer;">
                    Boutiques
                    <i class="bi bi-chevron-down"></i>
                </h6>
                <div class="collapse show" id="shopCollapse">
                    @foreach($shops->take(6) as $shop)
                        <div class="form-check mb-2">
                            <input type="checkbox" name="shop[]" value="{{ $shop->id }}" 
                                   class="form-check-input" id="shop{{ $shop->id }}"
                                   {{ in_array($shop->id, (array)($filters['shop'] ?? [])) ? 'checked' : '' }}
                                   onchange="this.form.submit()">
                            <label class="form-check-label d-flex justify-content-between" for="shop{{ $shop->id }}">
                                <span>{{ $shop->name }}</span>
                                <span class="badge bg-light text-dark">{{ $shop->products_count }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Apply/Reset -->
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-funnel me-2"></i>Appliquer les filtres
            </button>
            <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-2"></i>Réinitialiser
            </a>
        </div>
    </form>
</div>

<style>
.filter-sidebar {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
}
.filter-title {
    font-weight: 600;
    margin-bottom: 12px;
    color: #333;
}
.filter-section {
    padding-bottom: 16px;
    border-bottom: 1px solid #eee;
}
.filter-section:last-child {
    border-bottom: none;
}
</style>

<script>
document.getElementById('priceRange')?.addEventListener('input', function() {
    document.querySelector('input[name="price_max"]').value = this.value;
});
</script>
