@extends('admin.layouts.admin')

@section('content')
<div class="card mt-4">
    <div class="card-header card-header-bg text-white">
        <h6><i class="fas fa-award me-2"></i>Nouveau Badge</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.badges.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Icône (Bootstrap Icons) *</label>
                    <input type="text" name="icon" class="form-control" value="{{ old('icon', 'bi-award') }}" 
                           placeholder="bi-award-fill" required>
                    <small class="text-muted">Ex: bi-award-fill, bi-patch-check-fill</small>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Couleur *</label>
                    <input type="color" name="color" class="form-control form-control-color w-100" 
                           value="{{ old('color', '#ffc107') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Priorité d'affichage</label>
                <input type="number" name="priority" class="form-control" value="{{ old('priority', 0) }}" min="0">
                <small class="text-muted">Plus le nombre est élevé, plus le badge est prioritaire</small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Créer le badge
                </button>
                <a href="{{ route('admin.badges.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
