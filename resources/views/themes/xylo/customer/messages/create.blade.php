@extends('themes.xylo.layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-dots me-2"></i>Contacter le vendeur
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Vendor Info -->
                    <div class="d-flex align-items-center mb-4 p-3 bg-light rounded">
                        <img src="{{ $vendor->shop->logo ?? '/images/default-shop.png' }}" 
                             alt="Shop" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                        <div class="ms-3">
                            <h6 class="mb-0">{{ $vendor->shop->name ?? $vendor->name }}</h6>
                            <small class="text-muted">
                                <i class="bi bi-geo-alt me-1"></i>{{ $vendor->shop->address ?? 'Non spécifié' }}
                            </small>
                        </div>
                    </div>

                    @if($product)
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="bi bi-box me-2"></i>
                            <span>Concernant: <strong>{{ $product->name }}</strong></span>
                        </div>
                    @endif

                    @if($order)
                        <div class="alert alert-warning d-flex align-items-center">
                            <i class="bi bi-bag me-2"></i>
                            <span>Commande: <strong>#{{ $order->id }}</strong></span>
                        </div>
                    @endif

                    <form action="{{ route('customer.messages.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                        @if($product)
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                        @endif
                        @if($order)
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Votre message</label>
                            <textarea name="content" class="form-control" rows="5" required
                                      placeholder="Bonjour, j'aimerais avoir plus d'informations sur..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Pièce jointe (optionnel)</label>
                            <input type="file" name="attachment" class="form-control" accept="image/*,.pdf,.doc,.docx">
                            <small class="text-muted">Images, PDF ou documents (max 5MB)</small>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-2"></i>Envoyer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
