@extends('admin.layouts.admin')

@section('content')
<div class="card mt-4">
    <div class="card-header card-header-bg text-white">
        <h6><i class="fas fa-paper-plane me-2"></i>Envoyer une notification</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.notifications.send') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Destinataires *</label>
                    <select name="target" class="form-select" id="targetSelect" required>
                        <option value="">Sélectionner...</option>
                        <option value="all_customers">Tous les clients</option>
                        <option value="all_vendors">Tous les vendeurs</option>
                        <option value="all">Tout le monde</option>
                        <option value="specific_customer">Client spécifique</option>
                        <option value="specific_vendor">Vendeur spécifique</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3" id="specificTargetDiv" style="display: none;">
                    <label class="form-label">ID du destinataire</label>
                    <input type="number" name="target_id" class="form-control" id="targetId">
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Titre *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Type *</label>
                    <select name="type" class="form-select" required>
                        <option value="system">Système</option>
                        <option value="promotion">Promotion</option>
                        <option value="order">Commande</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Message *</label>
                <textarea name="message" class="form-control" rows="4" required>{{ old('message') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">URL d'action (optionnel)</label>
                <input type="url" name="action_url" class="form-control" placeholder="https://...">
                <small class="text-muted">Lien vers lequel l'utilisateur sera redirigé en cliquant</small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i>Envoyer
                </button>
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
document.getElementById('targetSelect').addEventListener('change', function() {
    const div = document.getElementById('specificTargetDiv');
    const input = document.getElementById('targetId');
    
    if (this.value === 'specific_customer' || this.value === 'specific_vendor') {
        div.style.display = 'block';
        input.required = true;
    } else {
        div.style.display = 'none';
        input.required = false;
    }
});
</script>
@endsection
