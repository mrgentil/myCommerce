@extends('vendor.layouts.master')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .image-preview {
        position: relative;
        display: inline-block;
    }
    .image-preview .remove-btn {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        cursor: pointer;
        font-size: 12px;
    }
    .banner-preview {
        width: 100%;
        max-height: 150px;
        object-fit: cover;
        border-radius: 8px;
    }
    .logo-preview {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #667eea;
    }
    .upload-zone {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: #fafafa;
    }
    .upload-zone:hover {
        border-color: #667eea;
        background-color: #f8f9ff;
    }
    .nav-pills .nav-link {
        color: #6c757d;
        border-radius: 10px;
        padding: 12px 20px;
        font-weight: 500;
    }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
    }
    .nav-pills .nav-link:hover:not(.active) {
        background: #f0f0f0;
    }
    .tab-content {
        padding-top: 20px;
    }
    .hero-preset {
        padding: 8px 12px;
        font-size: 0.75rem;
        border-radius: 20px;
    }
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
    }
    .stat-card.success {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    .stat-card.warning {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    .quick-stats {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }
    .quick-stat {
        flex: 1;
        background: #fff;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .quick-stat h3 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .quick-stat small {
        color: #6c757d;
        font-size: 0.8rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-shop me-2"></i>Configuration de ma boutique</h4>
            <p class="text-muted mb-0">Personnalisez l'apparence et les informations de votre boutique</p>
        </div>
        @if($shop)
            <a href="{{ route('shop.view', $shop->slug) }}" target="_blank" class="btn btn-outline-primary">
                <i class="bi bi-eye me-1"></i> Voir ma boutique
            </a>
        @endif
    </div>

    <!-- Status Alert -->
    @if($shop)
        @if($shop->status === 'pending')
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="bi bi-hourglass-split me-2 fs-5"></i>
                <div>
                    <strong>En attente d'approbation</strong> - Votre boutique est en cours de vérification par notre équipe.
                </div>
            </div>
        @elseif($shop->status === 'rejected')
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="bi bi-x-circle me-2 fs-5"></i>
                <div>
                    <strong>Boutique rejetée</strong> - Veuillez contacter le support pour plus d'informations.
                </div>
            </div>
        @elseif($shop->status === 'approved')
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle me-2 fs-5"></i>
                <div>
                    <strong>Boutique active</strong> - Votre boutique est visible par les clients.
                </div>
            </div>
        @endif
    @else
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="bi bi-info-circle me-2 fs-5"></i>
            <div>
                <strong>Créez votre boutique</strong> - Remplissez les informations ci-dessous pour créer votre boutique.
            </div>
        </div>
    @endif

    @if($shop)
    @php
        $totalProducts = \App\Models\Product::where(function($q) use ($shop, $vendor) {
            $q->where('shop_id', $shop->id)->orWhere('vendor_id', $vendor->id);
        })->count();
        $activeProducts = \App\Models\Product::where(function($q) use ($shop, $vendor) {
            $q->where('shop_id', $shop->id)->orWhere('vendor_id', $vendor->id);
        })->where('status', 1)->count();
    @endphp
    <!-- Quick Stats -->
    <div class="quick-stats">
        <div class="quick-stat">
            <h3>{{ $totalProducts }}</h3>
            <small>Produits</small>
        </div>
        <div class="quick-stat">
            <h3>{{ $activeProducts }}</h3>
            <small>Actifs</small>
        </div>
        <div class="quick-stat">
            @php
                $statusLabels = [
                    'pending' => ['label' => 'En attente', 'icon' => 'hourglass-split', 'color' => '#ffc107'],
                    'approved' => ['label' => 'Active', 'icon' => 'check-circle', 'color' => '#28a745'],
                    'rejected' => ['label' => 'Rejetée', 'icon' => 'x-circle', 'color' => '#dc3545'],
                ];
                $status = $statusLabels[$shop->status] ?? ['label' => 'Inconnu', 'icon' => 'question-circle', 'color' => '#6c757d'];
            @endphp
            <h3 style="-webkit-text-fill-color: {{ $status['color'] }};"><i class="bi bi-{{ $status['icon'] }}"></i></h3>
            <small>{{ $status['label'] }}</small>
        </div>
    </div>
    @endif

    <form action="{{ route('vendor.shop.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Tabs Navigation -->
        <ul class="nav nav-pills mb-3" id="shopTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="info-tab" data-bs-toggle="pill" data-bs-target="#info" type="button">
                    <i class="bi bi-info-circle me-1"></i> Informations
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="media-tab" data-bs-toggle="pill" data-bs-target="#media" type="button">
                    <i class="bi bi-images me-1"></i> Médias
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hero-tab" data-bs-toggle="pill" data-bs-target="#hero" type="button">
                    <i class="bi bi-palette me-1"></i> Personnalisation
                </button>
            </li>
        </ul>

        <div class="tab-content" id="shopTabsContent">
            <!-- Tab 1: Informations -->
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom de la boutique <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $shop->name ?? '') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($shop)
                                        <small class="text-muted">URL: {{ url('/shop/' . $shop->slug) }}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $shop->phone ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $shop->description ?? '') }}</textarea>
                            <small class="text-muted">Maximum 2000 caractères</small>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Adresse</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                       id="address" name="address" value="{{ old('address', $shop->address ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Médias -->
            <div class="tab-pane fade" id="media" role="tabpanel">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-image me-2"></i>Logo</h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="image-preview mb-3">
                                    @if($shop && $shop->logo)
                                        <img src="{{ asset('storage/' . $shop->logo) }}" alt="Logo" class="logo-preview" id="logoPreview">
                                        <button type="button" class="remove-btn" onclick="removeLogo()"><i class="bi bi-x"></i></button>
                                    @else
                                        <div class="logo-preview d-flex align-items-center justify-content-center bg-light" id="logoPreview">
                                            <i class="bi bi-shop fs-1 text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="upload-zone" onclick="document.getElementById('logo').click()">
                                    <i class="bi bi-upload"></i>
                                    <p class="mb-0 small text-muted">Choisir un logo (max 2 Mo)</p>
                                </div>
                                <input type="file" id="logo" name="logo" accept="image/*" class="d-none" onchange="previewLogo(this)">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-card-image me-2"></i>Bannière</h6>
                            </div>
                            <div class="card-body">
                                <div class="image-preview mb-3 w-100">
                                    @if($shop && $shop->banner)
                                        <img src="{{ asset('storage/' . $shop->banner) }}" alt="Banner" class="banner-preview w-100" id="bannerPreview">
                                        <button type="button" class="remove-btn" onclick="removeBanner()"><i class="bi bi-x"></i></button>
                                    @else
                                        <div class="banner-preview d-flex align-items-center justify-content-center bg-light w-100" id="bannerPreview" style="height: 150px;">
                                            <i class="bi bi-image fs-1 text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="upload-zone" onclick="document.getElementById('banner').click()">
                                    <i class="bi bi-upload"></i>
                                    <p class="mb-0 small text-muted">Choisir une bannière (max 4 Mo) - Recommandé: 1200x300px</p>
                                </div>
                                <input type="file" id="banner" name="banner" accept="image/*" class="d-none" onchange="previewBanner(this)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Personnalisation Hero -->
            <div class="tab-pane fade" id="hero" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            <i class="bi bi-info-circle me-1"></i>
                            Personnalisez le header de votre page boutique pour attirer plus de clients.
                        </p>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hero_title" class="form-label">Titre du Hero</label>
                                    <input type="text" class="form-control" id="hero_title" name="hero_title" 
                                           value="{{ old('hero_title', $shop->hero_title ?? '') }}" 
                                           placeholder="Ex: Bienvenue dans ma boutique">
                                </div>
                                <div class="mb-3">
                                    <label for="hero_subtitle" class="form-label">Sous-titre</label>
                                    <input type="text" class="form-control" id="hero_subtitle" name="hero_subtitle" 
                                           value="{{ old('hero_subtitle', $shop->hero_subtitle ?? '') }}" 
                                           placeholder="Ex: Les meilleurs produits">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hero_button_text" class="form-label">Texte du bouton</label>
                                    <input type="text" class="form-control" id="hero_button_text" name="hero_button_text" 
                                           value="{{ old('hero_button_text', $shop->hero_button_text ?? 'Voir les produits') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="hero_button_link" class="form-label">Lien du bouton</label>
                                    <input type="text" class="form-control" id="hero_button_link" name="hero_button_link" 
                                           value="{{ old('hero_button_link', $shop->hero_button_link ?? '') }}" 
                                           placeholder="Laissez vide pour défaut">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Couleur de fond</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="hero_bg_picker" value="#667eea"
                                               onchange="document.getElementById('hero_background').value = this.value; updatePreview();">
                                        <input type="text" class="form-control" id="hero_background" name="hero_background" 
                                               value="{{ old('hero_background', $shop->hero_background ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Couleur du texte</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="hero_text_picker" 
                                               value="{{ $shop->hero_text_color ?? '#ffffff' }}"
                                               onchange="document.getElementById('hero_text_color').value = this.value; updatePreview();">
                                        <input type="text" class="form-control" id="hero_text_color" name="hero_text_color" 
                                               value="{{ old('hero_text_color', $shop->hero_text_color ?? '#ffffff') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Thèmes rapides</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm hero-preset" style="background: linear-gradient(135deg, #667eea, #764ba2); color: #fff;" data-bg="linear-gradient(135deg, #667eea 0%, #764ba2 100%)">Violet</button>
                                <button type="button" class="btn btn-sm hero-preset" style="background: linear-gradient(135deg, #1a1a2e, #16213e); color: #fff;" data-bg="linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%)">Bleu nuit</button>
                                <button type="button" class="btn btn-sm hero-preset" style="background: linear-gradient(135deg, #f093fb, #f5576c); color: #fff;" data-bg="linear-gradient(135deg, #f093fb 0%, #f5576c 100%)">Rose</button>
                                <button type="button" class="btn btn-sm hero-preset" style="background: linear-gradient(135deg, #4facfe, #00f2fe); color: #fff;" data-bg="linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)">Cyan</button>
                                <button type="button" class="btn btn-sm hero-preset" style="background: linear-gradient(135deg, #43e97b, #38f9d7); color: #fff;" data-bg="linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)">Vert</button>
                                <button type="button" class="btn btn-sm hero-preset" style="background: linear-gradient(135deg, #fa709a, #fee140); color: #fff;" data-bg="linear-gradient(135deg, #fa709a 0%, #fee140 100%)">Sunset</button>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Aperçu</label>
                            <div id="heroPreview" class="rounded p-4 text-center" 
                                 style="background: {{ $shop->hero_background ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' }};">
                                <h4 id="previewTitle" style="color: {{ $shop->hero_text_color ?? '#ffffff' }}; margin-bottom: 8px;">
                                    {{ $shop->hero_title ?? $shop->name ?? 'Votre titre' }}
                                </h4>
                                <p id="previewSubtitle" style="color: {{ $shop->hero_text_color ?? '#ffffff' }}; opacity: 0.8; margin-bottom: 12px;">
                                    {{ $shop->hero_subtitle ?? 'Votre sous-titre' }}
                                </p>
                                <button type="button" class="btn btn-light btn-sm" id="previewButton">
                                    {{ $shop->hero_button_text ?? 'Voir les produits' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="d-flex justify-content-end gap-2 mt-4 mb-4">
            <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Retour
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreview').src = e.target.result;
            document.getElementById('logoPreview').classList.remove('d-flex', 'align-items-center', 'justify-content-center', 'bg-light');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewBanner(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('bannerPreview');
            if (preview.tagName === 'DIV') {
                preview.outerHTML = '<img src="' + e.target.result + '" alt="Banner" class="banner-preview w-100" id="bannerPreview">';
            } else {
                preview.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeLogo() {
    if (confirm('Voulez-vous supprimer le logo ?')) {
        fetch('{{ route("vendor.shop.remove-logo") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        })
        .then(response => response.json())
        .then(data => { if (data.success) location.reload(); });
    }
}

function removeBanner() {
    if (confirm('Voulez-vous supprimer la bannière ?')) {
        fetch('{{ route("vendor.shop.remove-banner") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        })
        .then(response => response.json())
        .then(data => { if (data.success) location.reload(); });
    }
}

function updatePreview() {
    const bg = document.getElementById('hero_background').value;
    const textColor = document.getElementById('hero_text_color').value;
    document.getElementById('heroPreview').style.background = bg;
    document.getElementById('previewTitle').style.color = textColor;
    document.getElementById('previewSubtitle').style.color = textColor;
}

document.addEventListener('DOMContentLoaded', function() {
    // Live preview updates
    document.getElementById('hero_title')?.addEventListener('input', function() {
        document.getElementById('previewTitle').textContent = this.value || 'Votre titre';
    });
    document.getElementById('hero_subtitle')?.addEventListener('input', function() {
        document.getElementById('previewSubtitle').textContent = this.value || 'Votre sous-titre';
    });
    document.getElementById('hero_button_text')?.addEventListener('input', function() {
        document.getElementById('previewButton').textContent = this.value || 'Voir les produits';
    });
    document.getElementById('hero_background')?.addEventListener('input', updatePreview);
    document.getElementById('hero_text_color')?.addEventListener('input', updatePreview);

    // Color presets
    document.querySelectorAll('.hero-preset').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('hero_background').value = this.dataset.bg;
            updatePreview();
        });
    });
});
</script>
@endsection
