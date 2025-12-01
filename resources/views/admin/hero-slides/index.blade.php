@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-images me-2"></i>Gestion du Hero Slider</h4>
            <p class="text-muted mb-0">Configurez les slides de la page d'accueil</p>
        </div>
        <a href="{{ route('admin.hero-slides.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nouveau Slide
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Slides actifs ({{ $slides->where('status', true)->count() }}/{{ $slides->count() }})</h6>
        </div>
        <div class="card-body">
            @if($slides->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="slidesTable">
                        <thead class="table-light">
                            <tr>
                                <th width="50"><i class="bi bi-grip-vertical"></i></th>
                                <th width="80">Image</th>
                                <th>Titre</th>
                                <th>Sous-titre</th>
                                <th width="100">Bouton</th>
                                <th width="100">Statut</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-slides">
                            @foreach($slides as $slide)
                            <tr data-id="{{ $slide->id }}">
                                <td class="handle" style="cursor: grab;">
                                    <i class="bi bi-grip-vertical text-muted"></i>
                                </td>
                                <td>
                                    @if($slide->image)
                                        <img src="{{ asset('storage/' . $slide->image) }}" 
                                             alt="{{ $slide->title }}" 
                                             class="rounded" 
                                             style="width: 60px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 40px; background: {{ $slide->background_color }};">
                                            <i class="bi bi-image text-white"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $slide->title }}</strong>
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($slide->subtitle, 40) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $slide->button_text }}</span>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" 
                                               type="checkbox" 
                                               data-id="{{ $slide->id }}"
                                               {{ $slide->status ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.hero-slides.edit', $slide) }}" 
                                           class="btn btn-outline-primary" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger delete-slide" 
                                                data-id="{{ $slide->id }}"
                                                title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-muted small mt-3">
                    <i class="bi bi-info-circle me-1"></i> 
                    Glissez-déposez les lignes pour réorganiser l'ordre des slides.
                </p>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-images fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Aucun slide configuré</p>
                    <a href="{{ route('admin.hero-slides.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Créer votre premier slide
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Preview Section -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-eye me-2"></i>Aperçu</h6>
        </div>
        <div class="card-body p-0">
            <div class="hero-preview" style="height: 300px; overflow: hidden; position: relative;">
                @if($slides->where('status', true)->count() > 0)
                    @php $firstSlide = $slides->where('status', true)->first(); @endphp
                    <div style="background: {{ $firstSlide->background_color }}; height: 100%; display: flex; align-items: center; padding: 2rem;">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h2 style="color: {{ $firstSlide->text_color }}; font-size: 2rem;">{{ $firstSlide->title }}</h2>
                                    <p style="color: {{ $firstSlide->text_color }}; opacity: 0.8;">{{ $firstSlide->subtitle }}</p>
                                    <span class="btn btn-light">{{ $firstSlide->button_text }}</span>
                                </div>
                                <div class="col-md-6 text-center">
                                    @if($firstSlide->image)
                                        <img src="{{ asset('storage/' . $firstSlide->image) }}" 
                                             alt="{{ $firstSlide->title }}" 
                                             style="max-height: 200px; filter: drop-shadow(0 10px 30px rgba(0,0,0,0.3));">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                        <p class="text-muted">Aucun slide actif à prévisualiser</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce slide ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Supprimer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    // Sortable for drag & drop
    var el = document.getElementById('sortable-slides');
    if (el) {
        var sortable = Sortable.create(el, {
            handle: '.handle',
            animation: 150,
            onEnd: function() {
                var order = [];
                $('#sortable-slides tr').each(function() {
                    order.push($(this).data('id'));
                });
                
                $.ajax({
                    url: '{{ route("admin.hero-slides.update-order") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order: order
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Ordre mis à jour');
                        }
                    }
                });
            }
        });
    }

    // Status toggle
    $('.status-toggle').on('change', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '/admin/hero-slides/' + id + '/toggle-status',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                }
            }
        });
    });

    // Delete slide
    var deleteId = null;
    $('.delete-slide').on('click', function() {
        deleteId = $(this).data('id');
        $('#deleteModal').modal('show');
    });

    $('#confirmDelete').on('click', function() {
        if (deleteId) {
            $.ajax({
                url: '/admin/hero-slides/' + deleteId,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        $('tr[data-id="' + deleteId + '"]').fadeOut(function() {
                            $(this).remove();
                        });
                        $('#deleteModal').modal('hide');
                        toastr.success(response.message);
                    }
                }
            });
        }
    });
});
</script>
@endsection
