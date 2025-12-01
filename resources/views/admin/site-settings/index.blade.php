@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-gear me-2"></i>Paramètres du site</h4>
            <p class="text-muted mb-0">Configurez les informations générales de votre boutique</p>
        </div>
        <a href="{{ route('admin.site-settings.edit') }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Logo & Favicon Preview -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-image me-2"></i>Logo & Favicon</h6>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <p class="text-muted small mb-2">Logo principal</p>
                    @if($settings->logo ?? null)
                        <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo" class="img-thumbnail" style="max-height: 60px;">
                    @else
                        <div class="bg-light rounded d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 60px;">
                            <i class="bi bi-image text-muted"></i>
                        </div>
                        <p class="text-muted small mt-1">Non défini</p>
                    @endif
                </div>
                <div class="col-md-4">
                    <p class="text-muted small mb-2">Logo sombre</p>
                    @if($settings->logo_dark ?? null)
                        <img src="{{ asset('storage/' . $settings->logo_dark) }}" alt="Logo Dark" class="img-thumbnail bg-dark" style="max-height: 60px;">
                    @else
                        <div class="bg-dark rounded d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 60px;">
                            <i class="bi bi-image text-white-50"></i>
                        </div>
                        <p class="text-muted small mt-1">Non défini</p>
                    @endif
                </div>
                <div class="col-md-4">
                    <p class="text-muted small mb-2">Favicon</p>
                    @if($settings->favicon ?? null)
                        <img src="{{ asset('storage/' . $settings->favicon) }}" alt="Favicon" class="img-thumbnail" style="width: 48px; height: 48px;">
                    @else
                        <div class="bg-light rounded d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-app text-muted"></i>
                        </div>
                        <p class="text-muted small mt-1">Non défini</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Identité du site -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-building me-2"></i>Identité du site</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%;">Nom du site</td>
                            <td><strong>{{ $settings->site_name ?? '—' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Slogan</td>
                            <td>{{ $settings->tagline ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- SEO -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-search me-2"></i>Référencement (SEO)</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%;">Titre méta</td>
                            <td>{{ $settings->meta_title ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Description méta</td>
                            <td>{{ Str::limit($settings->meta_description ?? '—', 50) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Mots-clés</td>
                            <td>{{ Str::limit($settings->meta_keywords ?? '—', 50) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Contact -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-envelope me-2"></i>Informations de contact</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%;">Email</td>
                            <td>
                                @if($settings->contact_email ?? null)
                                    <a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Téléphone</td>
                            <td>{{ $settings->contact_phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Adresse</td>
                            <td>{{ $settings->address ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-layout-text-window-reverse me-2"></i>Pied de page</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $settings->footer_text ?? 'Aucun texte défini' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="alert alert-info d-flex align-items-start">
        <i class="bi bi-info-circle fs-4 me-3"></i>
        <div>
            <strong>Conseil :</strong> Ces paramètres affectent l'ensemble de votre boutique. 
            Le titre et la description méta sont importants pour le référencement sur Google. 
            Assurez-vous de remplir tous les champs pour une meilleure visibilité.
        </div>
    </div>
</div>
@endsection
