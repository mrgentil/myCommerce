@extends('admin.layouts.admin')

@section('content')
<div class="card mt-4">
    <div class="card-header card-header-bg text-white">
        <h6><i class="fas fa-bolt me-2"></i>Modifier: {{ $flashSale->title }}</h6>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.flash-sales.update', $flashSale->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Titre *</label>
                    <input type="text" name="title" class="form-control" value="{{ $flashSale->title }}" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Bannière</label>
                    <input type="file" name="banner" class="form-control" accept="image/*">
                    @if($flashSale->banner)
                        <img src="{{ asset('storage/' . $flashSale->banner) }}" class="mt-2" height="50">
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ $flashSale->description }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date de début *</label>
                    <input type="datetime-local" name="starts_at" class="form-control" 
                           value="{{ $flashSale->starts_at->format('Y-m-d\TH:i') }}" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date de fin *</label>
                    <input type="datetime-local" name="ends_at" class="form-control" 
                           value="{{ $flashSale->ends_at->format('Y-m-d\TH:i') }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Statut</label>
                    <div class="form-check form-switch mt-2">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" 
                               {{ $flashSale->is_active ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Enregistrer
            </button>
        </form>
    </div>
</div>

<!-- Products Section -->
<div class="card mt-4">
    <div class="card-header card-header-bg text-white">
        <h6><i class="fas fa-box me-2"></i>Produits de la vente flash</h6>
    </div>
    <div class="card-body">
        <!-- Add Product Form -->
        <form action="{{ route('admin.flash-sales.add-product', $flashSale->id) }}" method="POST" class="row g-3 mb-4">
            @csrf
            <div class="col-md-5">
                <select name="product_id" class="form-select" required>
                    <option value="">Sélectionner un produit</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->primaryVariant?->price ?? 0 }}">
                            {{ $product->name }} ({{ number_format($product->primaryVariant?->price ?? 0, 2) }}€)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" name="sale_price" class="form-control" placeholder="Prix promo" step="0.01" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="quantity_limit" class="form-control" placeholder="Qté limite">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-plus"></i> Ajouter
                </button>
            </div>
        </form>

        <!-- Products List -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Prix normal</th>
                    <th>Prix promo</th>
                    <th>Réduction</th>
                    <th>Vendus</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($flashSale->products as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ number_format($item->product->primaryVariant?->price ?? 0, 2) }}€</td>
                        <td class="text-success fw-bold">{{ number_format($item->sale_price, 2) }}€</td>
                        <td><span class="badge bg-danger">-{{ $item->discount_percentage }}%</span></td>
                        <td>
                            {{ $item->quantity_sold }}
                            @if($item->quantity_limit)
                                / {{ $item->quantity_limit }}
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $item->sold_percentage }}%"></div>
                                </div>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.flash-sales.remove-product', [$flashSale->id, $item->product_id]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Retirer ce produit ?')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-3">Aucun produit ajouté</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
