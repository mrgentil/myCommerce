@extends('admin.layouts.admin')

@section('css')
<style>
    .settings-nav .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
        border-radius: 0;
        padding: 15px 25px;
        font-weight: 500;
        transition: all 0.3s;
    }
    .settings-nav .nav-link:hover {
        color: #667eea;
        background: rgba(102, 126, 234, 0.05);
    }
    .settings-nav .nav-link.active {
        color: #667eea;
        background: transparent;
        border-bottom-color: #667eea;
    }
    .upload-box {
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
        background: #fafbfc;
    }
    .upload-box:hover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.05);
    }
    .upload-box.has-image {
        border-style: solid;
        border-color: #28a745;
    }
    .upload-box img {
        max-height: 80px;
        max-width: 100%;
        object-fit: contain;
    }
    .upload-box .remove-btn {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        padding: 0;
        font-size: 12px;
    }
    .form-floating-icon {
        position: relative;
    }
    .form-floating-icon .icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 5;
    }
    .form-floating-icon input {
        padding-left: 40px;
    }
    .char-counter {
        font-size: 11px;
        color: #6c757d;
    }
    .char-counter.warning { color: #ffc107; }
    .char-counter.danger { color: #dc3545; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-gear-fill me-2" style="color: #667eea;"></i>Paramètres du site</h4>
            <p class="text-muted mb-0">Configurez l'identité et les informations de votre boutique</p>
        </div>
        <button type="submit" form="settingsForm" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-1"></i> Enregistrer
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
        @csrf

        <div class="card shadow-sm">
            <!-- Navigation Tabs -->
            <div class="card-header bg-white border-bottom">
                <ul class="nav settings-nav" id="settingsTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#branding" type="button">
                            <i class="bi bi-palette me-2"></i>Identité visuelle
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#general" type="button">
                            <i class="bi bi-building me-2"></i>Informations générales
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#seo" type="button">
                            <i class="bi bi-search me-2"></i>SEO
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#contact" type="button">
                            <i class="bi bi-telephone me-2"></i>Contact
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content">
                    <!-- Tab: Branding -->
                    <div class="tab-pane fade show active" id="branding">
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-1">Logo & Favicon</h5>
                                <p class="text-muted small">Ces images représentent votre marque sur le site</p>
                            </div>
                        </div>
                        <div class="row g-4">
                            <!-- Logo principal -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Logo principal</label>
                                <div class="upload-box position-relative {{ ($settings->logo ?? null) ? 'has-image' : '' }}" 
                                     onclick="document.getElementById('logo').click()">
                                    @if($settings->logo ?? null)
                                        <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo">
                                        <button type="button" class="btn btn-danger btn-sm remove-btn" 
                                                onclick="event.stopPropagation(); removeLogo('logo')">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    @else
                                        <i class="bi bi-image fs-1 text-muted d-block mb-2"></i>
                                        <span class="text-muted small">Cliquez pour télécharger</span>
                                    @endif
                                </div>
                                <input type="file" name="logo" id="logo" class="d-none" accept="image/*" onchange="previewImage(this, 'logo')">
                                <small class="text-muted d-block mt-2">PNG ou SVG transparent recommandé</small>
                            </div>

                            <!-- Logo sombre -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Logo sombre <span class="badge bg-light text-muted">optionnel</span></label>
                                <div class="upload-box position-relative {{ ($settings->logo_dark ?? null) ? 'has-image' : '' }}" 
                                     style="background: #1a1a2e;"
                                     onclick="document.getElementById('logo_dark').click()">
                                    @if($settings->logo_dark ?? null)
                                        <img src="{{ asset('storage/' . $settings->logo_dark) }}" alt="Logo Dark">
                                        <button type="button" class="btn btn-danger btn-sm remove-btn" 
                                                onclick="event.stopPropagation(); removeLogo('logo_dark')">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    @else
                                        <i class="bi bi-image fs-1 text-white-50 d-block mb-2"></i>
                                        <span class="text-white-50 small">Pour le mode sombre</span>
                                    @endif
                                </div>
                                <input type="file" name="logo_dark" id="logo_dark" class="d-none" accept="image/*">
                                <small class="text-muted d-block mt-2">Version claire pour fonds sombres</small>
                            </div>

                            <!-- Favicon -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Favicon</label>
                                <div class="upload-box position-relative {{ ($settings->favicon ?? null) ? 'has-image' : '' }}" 
                                     onclick="document.getElementById('favicon').click()">
                                    @if($settings->favicon ?? null)
                                        <img src="{{ asset('storage/' . $settings->favicon) }}" alt="Favicon" style="width: 48px; height: 48px;">
                                        <button type="button" class="btn btn-danger btn-sm remove-btn" 
                                                onclick="event.stopPropagation(); removeLogo('favicon')">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    @else
                                        <i class="bi bi-app fs-1 text-muted d-block mb-2"></i>
                                        <span class="text-muted small">Icône de l'onglet</span>
                                    @endif
                                </div>
                                <input type="file" name="favicon" id="favicon" class="d-none" accept="image/*,.ico">
                                <small class="text-muted d-block mt-2">ICO ou PNG 32×32 / 64×64</small>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: General -->
                    <div class="tab-pane fade" id="general">
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-1">Informations de la boutique</h5>
                                <p class="text-muted small">Nom et description affichés sur votre site</p>
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="site_name" class="form-label fw-semibold">Nom du site <span class="text-danger">*</span></label>
                                <input type="text" name="site_name" id="site_name" class="form-control form-control-lg" 
                                       value="{{ old('site_name', $settings->site_name ?? '') }}" 
                                       placeholder="Ma Boutique" required>
                                <small class="text-muted">Affiché dans l'en-tête et le titre des pages</small>
                            </div>
                            <div class="col-md-6">
                                <label for="tagline" class="form-label fw-semibold">Slogan</label>
                                <input type="text" name="tagline" id="tagline" class="form-control form-control-lg" 
                                       value="{{ old('tagline', $settings->tagline ?? '') }}"
                                       placeholder="Qualité et service depuis 2020">
                                <small class="text-muted">Phrase d'accroche sous le logo</small>
                            </div>
                            <div class="col-12">
                                <label for="footer_text" class="form-label fw-semibold">Texte du pied de page</label>
                                <textarea name="footer_text" id="footer_text" class="form-control" rows="2"
                                          placeholder="© 2025 Ma Boutique. Tous droits réservés.">{{ old('footer_text', $settings->footer_text ?? '') }}</textarea>
                                <small class="text-muted">Texte affiché en bas de chaque page</small>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: SEO -->
                    <div class="tab-pane fade" id="seo">
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-1">Référencement (SEO)</h5>
                                <p class="text-muted small">Optimisez votre visibilité sur Google et autres moteurs de recherche</p>
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col-12">
                                <label for="meta_title" class="form-label fw-semibold">Titre méta</label>
                                <input type="text" name="meta_title" id="meta_title" class="form-control" 
                                       value="{{ old('meta_title', $settings->meta_title ?? '') }}"
                                       placeholder="Ma Boutique - Vêtements de qualité"
                                       maxlength="70"
                                       oninput="updateCharCount(this, 60)">
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Apparaît dans les résultats de recherche</small>
                                    <span class="char-counter" id="meta_title_counter">0/60</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="meta_description" class="form-label fw-semibold">Description méta</label>
                                <textarea name="meta_description" id="meta_description" class="form-control" rows="3"
                                          placeholder="Découvrez notre sélection de vêtements tendance et accessoires de mode..."
                                          maxlength="180"
                                          oninput="updateCharCount(this, 160)">{{ old('meta_description', $settings->meta_description ?? '') }}</textarea>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Description sous le titre dans Google</small>
                                    <span class="char-counter" id="meta_description_counter">0/160</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="meta_keywords" class="form-label fw-semibold">Mots-clés</label>
                                <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" 
                                       value="{{ old('meta_keywords', $settings->meta_keywords ?? '') }}"
                                       placeholder="boutique, mode, vêtements, accessoires">
                                <small class="text-muted">Séparez les mots-clés par des virgules</small>
                            </div>

                            <!-- SEO Preview -->
                            <div class="col-12 mt-4">
                                <label class="form-label fw-semibold">Aperçu Google</label>
                                <div class="border rounded p-3 bg-white">
                                    <div class="text-primary" style="font-size: 18px;" id="seoPreviewTitle">
                                        {{ $settings->meta_title ?? $settings->site_name ?? 'Titre de votre site' }}
                                    </div>
                                    <div class="text-success small">{{ url('/') }}</div>
                                    <div class="text-muted small" id="seoPreviewDesc">
                                        {{ Str::limit($settings->meta_description ?? 'Description de votre site...', 160) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Contact -->
                    <div class="tab-pane fade" id="contact">
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-1">Coordonnées</h5>
                                <p class="text-muted small">Informations affichées aux clients pour vous contacter</p>
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="contact_email" class="form-label fw-semibold">Email de contact</label>
                                <div class="form-floating-icon">
                                    <i class="bi bi-envelope icon"></i>
                                    <input type="email" name="contact_email" id="contact_email" class="form-control" 
                                           value="{{ old('contact_email', $settings->contact_email ?? '') }}"
                                           placeholder="contact@maboutique.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="contact_phone" class="form-label fw-semibold">Téléphone</label>
                                <div class="form-floating-icon">
                                    <i class="bi bi-telephone icon"></i>
                                    <input type="text" name="contact_phone" id="contact_phone" class="form-control" 
                                           value="{{ old('contact_phone', $settings->contact_phone ?? '') }}"
                                           placeholder="+33 1 23 45 67 89">
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">Adresse</label>
                                <div class="form-floating-icon">
                                    <i class="bi bi-geo-alt icon"></i>
                                    <input type="text" name="address" id="address" class="form-control" 
                                           value="{{ old('address', $settings->address ?? '') }}"
                                           placeholder="123 Rue du Commerce, 75001 Paris">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer with save button -->
            <div class="card-footer bg-light border-top d-flex justify-content-between align-items-center py-3">
                <span class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i> Les modifications sont enregistrées immédiatement
                </span>
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-check-lg me-1"></i> Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
function removeLogo(type) {
    if (confirm('Voulez-vous supprimer cette image ?')) {
        fetch('{{ route("admin.site-settings.remove-logo") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ type: type })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function updateCharCount(input, limit) {
    const counter = document.getElementById(input.id + '_counter');
    const len = input.value.length;
    counter.textContent = len + '/' + limit;
    counter.classList.remove('warning', 'danger');
    if (len > limit) {
        counter.classList.add('danger');
    } else if (len > limit * 0.9) {
        counter.classList.add('warning');
    }

    // Update SEO preview
    if (input.id === 'meta_title') {
        document.getElementById('seoPreviewTitle').textContent = input.value || 'Titre de votre site';
    }
    if (input.id === 'meta_description') {
        document.getElementById('seoPreviewDesc').textContent = input.value || 'Description de votre site...';
    }
}

// Initialize counters
document.addEventListener('DOMContentLoaded', function() {
    const metaTitle = document.getElementById('meta_title');
    const metaDesc = document.getElementById('meta_description');
    if (metaTitle) updateCharCount(metaTitle, 60);
    if (metaDesc) updateCharCount(metaDesc, 160);
});
</script>
@endsection
