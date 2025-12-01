@extends('admin.layouts.admin')

@section('content')
<div class="card mt-4">
    <div class="card-header card-header-bg text-white d-flex justify-content-between align-items-center">
        <h6><i class="fas fa-user-tag me-2"></i>Attribution des Badges</h6>
        <a href="{{ route('admin.badges.index') }}" class="btn btn-outline-light btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour aux badges
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Vendeur</th>
                    <th>Boutique</th>
                    <th>Badges actuels</th>
                    <th>Vérifié</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vendors as $vendor)
                    <tr>
                        <td>{{ $vendor->name }}</td>
                        <td>{{ $vendor->shop->name ?? 'N/A' }}</td>
                        <td>
                            @foreach($vendor->badges as $badge)
                                <span class="badge me-1" style="background: {{ $badge->color }}">
                                    <i class="bi {{ $badge->icon }}"></i> {{ $badge->name }}
                                </span>
                            @endforeach
                            @if($vendor->badges->isEmpty())
                                <span class="text-muted">Aucun</span>
                            @endif
                        </td>
                        <td>
                            @if($vendor->is_verified)
                                <span class="badge bg-success"><i class="fas fa-check"></i> Vérifié</span>
                            @else
                                <form action="{{ route('admin.vendors.verify', $vendor->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success">Vérifier</button>
                                </form>
                            @endif
                        </td>
                        <td>
                            <!-- Assign Badge -->
                            <div class="dropdown d-inline">
                                <button class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <div class="dropdown-menu">
                                    @foreach($badges as $badge)
                                        @if(!$vendor->badges->contains($badge->id))
                                            <form action="{{ route('admin.badges.assign') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                                                <input type="hidden" name="badge_id" value="{{ $badge->id }}">
                                                <button class="dropdown-item">
                                                    <i class="bi {{ $badge->icon }}" style="color: {{ $badge->color }}"></i>
                                                    {{ $badge->name }}
                                                </button>
                                            </form>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <!-- Remove Badge -->
                            @foreach($vendor->badges as $badge)
                                <form action="{{ route('admin.badges.remove') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                                    <input type="hidden" name="badge_id" value="{{ $badge->id }}">
                                    <button class="btn btn-sm btn-outline-danger" title="Retirer {{ $badge->name }}">
                                        <i class="bi {{ $badge->icon }}"></i> ×
                                    </button>
                                </form>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $vendors->links() }}
    </div>
</div>
@endsection
