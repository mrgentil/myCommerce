@extends('admin.layouts.admin')

@section('content')
<div class="card mt-4">
    <div class="card-header card-header-bg text-white">
        <h6><i class="fas fa-award me-2"></i>Modifier: {{ $badge->name }}</h6>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.badges.update', $badge->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="name" class="form-control" value="{{ $badge->name }}" required>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Icône *</label>
                    <input type="text" name="icon" class="form-control" value="{{ $badge->icon }}" required>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Couleur *</label>
                    <input type="color" name="color" class="form-control form-control-color w-100" 
                           value="{{ $badge->color }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2">{{ $badge->description }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Priorité</label>
                    <input type="number" name="priority" class="form-control" value="{{ $badge->priority }}" min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Statut</label>
                    <div class="form-check form-switch mt-2">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" 
                               {{ $badge->is_active ? 'checked' : '' }}>
                        <label class="form-check-label">Actif</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Enregistrer
                </button>
                <a href="{{ route('admin.badges.index') }}" class="btn btn-secondary">Retour</a>
            </div>
        </form>

        <!-- Preview -->
        <div class="mt-4 p-4 bg-light rounded text-center">
            <h6>Aperçu</h6>
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" 
                  style="width: 60px; height: 60px; background: {{ $badge->color }}20;">
                <i class="bi {{ $badge->icon }}" style="font-size: 24px; color: {{ $badge->color }}"></i>
            </span>
            <p class="mb-0 fw-bold">{{ $badge->name }}</p>
        </div>
    </div>
</div>
@endsection
