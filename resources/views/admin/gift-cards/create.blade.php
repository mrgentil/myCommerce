@extends('admin.layouts.admin')

@section('content')
<div class="card mt-4">
    <div class="card-header card-header-bg text-white">
        <h6><i class="fas fa-gift me-2"></i>Créer des Cartes Cadeaux</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.gift-cards.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Montant *</label>
                    <div class="input-group">
                        <input type="number" name="amount" class="form-control" value="{{ old('amount', 50) }}" 
                               min="5" max="1000" step="0.01" required>
                        <span class="input-group-text">€</span>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Quantité *</label>
                    <input type="number" name="quantity" class="form-control" value="{{ old('quantity', 1) }}" 
                           min="1" max="100" required>
                    <small class="text-muted">Nombre de cartes à générer</small>
                </div>
            </div>

            <hr>
            <p class="text-muted">Optionnel: informations destinataire (uniquement si quantité = 1)</p>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom du destinataire</label>
                    <input type="text" name="recipient_name" class="form-control" value="{{ old('recipient_name') }}">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email du destinataire</label>
                    <input type="email" name="recipient_email" class="form-control" value="{{ old('recipient_email') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Message personnel</label>
                <textarea name="message" class="form-control" rows="3">{{ old('message') }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Générer les cartes
                </button>
                <a href="{{ route('admin.gift-cards.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
