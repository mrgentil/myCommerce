@extends('admin.layouts.admin')

@section('content')
<div class="card mt-4">
    <div class="card-header card-header-bg text-white d-flex justify-content-between align-items-center">
        <h6><i class="fas fa-award me-2"></i>Badges Vendeur</h6>
        <div>
            <a href="{{ route('admin.badges.assignments') }}" class="btn btn-outline-light btn-sm me-2">
                <i class="fas fa-user-tag me-1"></i>Attributions
            </a>
            <a href="{{ route('admin.badges.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus me-1"></i>Nouveau Badge
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            @forelse($badges as $badge)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle" 
                                      style="width: 60px; height: 60px; background: {{ $badge->color }}20;">
                                    <i class="bi {{ $badge->icon }}" style="font-size: 24px; color: {{ $badge->color }}"></i>
                                </span>
                            </div>
                            <h5>{{ $badge->name }}</h5>
                            <p class="text-muted small">{{ $badge->description }}</p>
                            <p>
                                <span class="badge bg-secondary">{{ $badge->vendors_count }} vendeurs</span>
                                @if(!$badge->is_active)
                                    <span class="badge bg-danger">Inactif</span>
                                @endif
                            </p>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="{{ route('admin.badges.edit', $badge->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <form action="{{ route('admin.badges.destroy', $badge->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce badge ?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-award fa-3x text-muted"></i>
                    <p class="mt-3">Aucun badge créé</p>
                    <a href="{{ route('admin.badges.create') }}" class="btn btn-primary">Créer le premier badge</a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
