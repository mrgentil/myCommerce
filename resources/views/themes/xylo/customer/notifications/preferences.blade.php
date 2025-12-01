@extends('themes.xylo.layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <a href="{{ route('customer.notifications.index') }}" class="btn btn-link text-muted mb-3">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Préférences de notification</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('customer.notifications.update-preferences') }}" method="POST">
                        @csrf

                        <h6 class="text-muted mb-3">Notifications par email</h6>

                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" name="email_orders" class="form-check-input" id="emailOrders"
                                   {{ $preferences->email_orders ? 'checked' : '' }}>
                            <label class="form-check-label" for="emailOrders">
                                <strong>Commandes</strong>
                                <small class="d-block text-muted">Confirmations, expéditions, livraisons</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" name="email_messages" class="form-check-input" id="emailMessages"
                                   {{ $preferences->email_messages ? 'checked' : '' }}>
                            <label class="form-check-label" for="emailMessages">
                                <strong>Messages</strong>
                                <small class="d-block text-muted">Nouveaux messages des vendeurs</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" name="email_reviews" class="form-check-input" id="emailReviews"
                                   {{ $preferences->email_reviews ? 'checked' : '' }}>
                            <label class="form-check-label" for="emailReviews">
                                <strong>Avis</strong>
                                <small class="d-block text-muted">Rappels pour laisser un avis</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" name="email_promotions" class="form-check-input" id="emailPromotions"
                                   {{ $preferences->email_promotions ? 'checked' : '' }}>
                            <label class="form-check-label" for="emailPromotions">
                                <strong>Promotions</strong>
                                <small class="d-block text-muted">Offres spéciales et réductions</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input type="checkbox" name="email_newsletter" class="form-check-input" id="emailNewsletter"
                                   {{ $preferences->email_newsletter ? 'checked' : '' }}>
                            <label class="form-check-label" for="emailNewsletter">
                                <strong>Newsletter</strong>
                                <small class="d-block text-muted">Actualités et nouveautés</small>
                            </label>
                        </div>

                        <hr>

                        <h6 class="text-muted mb-3">Notifications push</h6>

                        <div class="form-check form-switch mb-4">
                            <input type="checkbox" name="push_enabled" class="form-check-input" id="pushEnabled"
                                   {{ $preferences->push_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="pushEnabled">
                                <strong>Activer les notifications push</strong>
                                <small class="d-block text-muted">Recevez des alertes en temps réel</small>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check me-2"></i>Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
