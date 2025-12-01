@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-plus-circle me-2"></i>Nouveau Slide</h4>
            <p class="text-muted mb-0">Créez un nouveau slide pour le Hero</p>
        </div>
        <a href="{{ route('admin.hero-slides.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>

    <form action="{{ route('admin.hero-slides.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Contenu du slide</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre principal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" 
                                   placeholder="Ex: Nouveautés 2025" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subtitle" class="form-label">Sous-titre</label>
                            <textarea class="form-control @error('subtitle') is-invalid @enderror" 
                                      id="subtitle" name="subtitle" rows="2"
                                      placeholder="Ex: Découvrez notre nouvelle collection...">{{ old('subtitle') }}</textarea>
                            @error('subtitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="button_text" class="form-label">Texte du bouton <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('button_text') is-invalid @enderror" 
                                           id="button_text" name="button_text" 
                                           value="{{ old('button_text', 'Acheter maintenant') }}" required>
                                    @error('button_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="button_link" class="form-label">Lien du bouton <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('button_link') is-invalid @enderror" 
                                           id="button_link" name="button_link" 
                                           value="{{ old('button_link', '/shop') }}" required>
                                    @error('button_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Image du slide</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="image" class="form-label">Image produit</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*" onchange="previewImage(this)">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Recommandé: PNG avec fond transparent, 600x600px minimum</small>
                        </div>
                        <div id="imagePreview" class="mt-3 text-center" style="display: none;">
                            <img src="" alt="Preview" class="img-fluid" style="max-height: 200px;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Apparence</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="background_color" class="form-label">Couleur de fond</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" 
                                       id="bg_color_picker" value="#1a1a2e" 
                                       onchange="document.getElementById('background_color').value = this.value">
                                <input type="text" class="form-control @error('background_color') is-invalid @enderror" 
                                       id="background_color" name="background_color" 
                                       value="{{ old('background_color', '#1a1a2e') }}">
                            </div>
                            <small class="text-muted">Couleur hexadécimale ou gradient CSS</small>
                        </div>

                        <div class="mb-3">
                            <label for="text_color" class="form-label">Couleur du texte</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" 
                                       id="text_color_picker" value="#ffffff"
                                       onchange="document.getElementById('text_color').value = this.value">
                                <input type="text" class="form-control @error('text_color') is-invalid @enderror" 
                                       id="text_color" name="text_color" 
                                       value="{{ old('text_color', '#ffffff') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="order" class="form-label">Ordre d'affichage</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                   id="order" name="order" value="{{ old('order', 0) }}" min="0">
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="status" name="status" 
                                       {{ old('status', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">Slide actif</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Présets de couleurs</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm color-preset" 
                                    style="background: linear-gradient(135deg, #1a1a2e, #16213e); color: #fff;"
                                    data-bg="linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%)">
                                Bleu nuit
                            </button>
                            <button type="button" class="btn btn-sm color-preset" 
                                    style="background: linear-gradient(135deg, #667eea, #764ba2); color: #fff;"
                                    data-bg="linear-gradient(135deg, #667eea 0%, #764ba2 100%)">
                                Violet
                            </button>
                            <button type="button" class="btn btn-sm color-preset" 
                                    style="background: linear-gradient(135deg, #f093fb, #f5576c); color: #fff;"
                                    data-bg="linear-gradient(135deg, #f093fb 0%, #f5576c 100%)">
                                Rose
                            </button>
                            <button type="button" class="btn btn-sm color-preset" 
                                    style="background: linear-gradient(135deg, #4facfe, #00f2fe); color: #fff;"
                                    data-bg="linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)">
                                Cyan
                            </button>
                            <button type="button" class="btn btn-sm color-preset" 
                                    style="background: linear-gradient(135deg, #43e97b, #38f9d7); color: #fff;"
                                    data-bg="linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)">
                                Vert
                            </button>
                            <button type="button" class="btn btn-sm color-preset" 
                                    style="background: linear-gradient(135deg, #fa709a, #fee140); color: #fff;"
                                    data-bg="linear-gradient(135deg, #fa709a 0%, #fee140 100%)">
                                Sunset
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-1"></i> Créer le slide
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('#imagePreview img').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.querySelectorAll('.color-preset').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('background_color').value = this.dataset.bg;
    });
});
</script>
@endsection
