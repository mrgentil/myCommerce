@extends('themes.xylo.layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-link text-muted mb-3">
                <i class="bi bi-arrow-left me-2"></i>Retour à la commande
            </a>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-arrow-return-left me-2"></i>Demande de retour</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('customer.returns.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">

                        <!-- Select item -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Sélectionnez l'article à retourner</label>
                            @foreach($order->details as $detail)
                                <div class="form-check p-3 border rounded mb-2">
                                    <input type="radio" name="order_detail_id" value="{{ $detail->id }}" 
                                           class="form-check-input" id="item{{ $detail->id }}" required>
                                    <label class="form-check-label d-flex align-items-center" for="item{{ $detail->id }}">
                                        <div class="ms-2">
                                            <strong>{{ $detail->product->name ?? 'Produit' }}</strong>
                                            @if($detail->productVariant)
                                                <small class="text-muted d-block">{{ $detail->productVariant->variant_slug }}</small>
                                            @endif
                                            <small class="text-muted">Qté: {{ $detail->quantity }} · {{ number_format($detail->price, 2, ',', ' ') }} €</small>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                            @error('order_detail_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Type de demande</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check border rounded p-3 h-100">
                                        <input type="radio" name="type" value="return" class="form-check-input" id="typeReturn" checked>
                                        <label class="form-check-label" for="typeReturn">
                                            <i class="bi bi-box-arrow-left text-primary me-2"></i>
                                            <strong>Retour</strong>
                                            <small class="d-block text-muted">Renvoyer l'article</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check border rounded p-3 h-100">
                                        <input type="radio" name="type" value="refund" class="form-check-input" id="typeRefund">
                                        <label class="form-check-label" for="typeRefund">
                                            <i class="bi bi-cash text-success me-2"></i>
                                            <strong>Remboursement</strong>
                                            <small class="d-block text-muted">Sans retour</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check border rounded p-3 h-100">
                                        <input type="radio" name="type" value="exchange" class="form-check-input" id="typeExchange">
                                        <label class="form-check-label" for="typeExchange">
                                            <i class="bi bi-arrow-left-right text-info me-2"></i>
                                            <strong>Échange</strong>
                                            <small class="d-block text-muted">Autre article</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Raison</label>
                            <select name="reason" class="form-select" required>
                                <option value="">Sélectionnez une raison</option>
                                @foreach($reasons as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('reason')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Quantity -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Quantité à retourner</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Décrivez le problème</label>
                            <textarea name="description" class="form-control" rows="4" required
                                      placeholder="Expliquez en détail pourquoi vous souhaitez retourner cet article..."></textarea>
                            @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Images -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Photos (optionnel)</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                            <small class="text-muted">Ajoutez des photos pour appuyer votre demande (max 5 images)</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Politique de retour:</strong> Vous avez 14 jours après réception pour retourner un article. 
                            Le remboursement sera effectué sous 5-7 jours ouvrés après réception du retour.
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-2"></i>Envoyer la demande
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
