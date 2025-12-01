@extends('themes.xylo.layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 80px;"></i>
                    </div>
                    
                    <h2 class="mb-3">Commande confirmée !</h2>
                    <p class="text-muted mb-4">
                        Merci pour votre commande. Votre numéro de commande est :
                    </p>
                    
                    <div class="bg-light rounded p-3 mb-4">
                        <h3 class="mb-0 text-primary">#{{ $order->id }}</h3>
                    </div>

                    <p class="text-muted">
                        Un email de confirmation a été envoyé à 
                        <strong>{{ $order->guest_email ?? $order->customer?->email }}</strong>
                    </p>

                    <hr class="my-4">

                    <h5 class="mb-3">Récapitulatif de la commande</h5>
                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start">Produit</th>
                                    <th class="text-center">Qté</th>
                                    <th class="text-end">Prix</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->details as $detail)
                                    <tr>
                                        <td class="text-start">{{ $detail->product?->name ?? 'Produit' }}</td>
                                        <td class="text-center">{{ $detail->quantity }}</td>
                                        <td class="text-end">{{ number_format($detail->price * $detail->quantity, 2, ',', ' ') }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2" class="text-end">Total</th>
                                    <th class="text-end">{{ number_format($order->total_amount, 2, ',', ' ') }} €</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="bg-light rounded p-3 text-start">
                                <h6 class="mb-2"><i class="bi bi-truck me-2"></i>Adresse de livraison</h6>
                                @php $address = json_decode($order->shipping_address); @endphp
                                @if($address)
                                    <p class="mb-0 small text-muted">
                                        {{ $address->first_name ?? '' }} {{ $address->last_name ?? '' }}<br>
                                        {{ $address->address ?? '' }}<br>
                                        {{ $address->zipcode ?? '' }} {{ $address->city ?? '' }}<br>
                                        {{ $address->phone ?? '' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="bg-light rounded p-3 text-start">
                                <h6 class="mb-2"><i class="bi bi-credit-card me-2"></i>Paiement</h6>
                                <p class="mb-0 small text-muted">
                                    Méthode: <strong>{{ ucfirst($order->payment_method) }}</strong><br>
                                    Statut: 
                                    <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                        {{ $order->payment_status == 'paid' ? 'Payé' : 'En attente' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary px-4">
                            <i class="bi bi-house me-2"></i>Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
