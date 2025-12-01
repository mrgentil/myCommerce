@extends('admin.layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Dispute Details -->
        <div class="card mb-4">
            <div class="card-header card-header-bg text-white d-flex justify-content-between">
                <h6><i class="fas fa-gavel me-2"></i>Litige #{{ $dispute->id }}</h6>
                <span class="badge bg-{{ $dispute->status_color }}">{{ $dispute->status_label }}</span>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Type:</strong> {{ $dispute->type_label }}
                    </div>
                    <div class="col-md-6">
                        <strong>Montant contesté:</strong> {{ number_format($dispute->amount_disputed, 2) }}€
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Description du client:</strong>
                    <p class="bg-light p-3 rounded">{{ $dispute->description }}</p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Client:</strong><br>
                        {{ $dispute->customer->name ?? 'N/A' }}<br>
                        <small class="text-muted">{{ $dispute->customer->email ?? '' }}</small>
                    </div>
                    <div class="col-md-6">
                        <strong>Vendeur:</strong><br>
                        {{ $dispute->vendor->shop->name ?? $dispute->vendor->name ?? 'N/A' }}<br>
                        <small class="text-muted">{{ $dispute->vendor->email ?? '' }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <div class="card mb-4">
            <div class="card-header">
                <h6><i class="fas fa-comments me-2"></i>Messages</h6>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                @foreach($dispute->messages as $message)
                    <div class="mb-3 p-3 rounded {{ $message->sender_type == 'admin' ? 'bg-primary text-white' : 'bg-light' }}">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>
                                @if($message->sender_type == 'admin')
                                    <i class="fas fa-shield-alt me-1"></i>Support
                                @else
                                    {{ $message->sender_name }}
                                @endif
                            </strong>
                            <small>{{ $message->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <p class="mb-0">{{ $message->message }}</p>
                    </div>
                @endforeach
            </div>
            <div class="card-footer">
                <form action="{{ route('admin.disputes.message', $dispute->id) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="message" class="form-control" placeholder="Écrire un message..." required>
                        <button class="btn btn-primary">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Evidence -->
        @if($dispute->evidence->count())
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-image me-2"></i>Preuves</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($dispute->evidence as $evidence)
                            <div class="col-md-4 mb-3">
                                <a href="{{ $evidence->url }}" target="_blank">
                                    <img src="{{ $evidence->url }}" class="img-fluid rounded">
                                </a>
                                <small class="d-block">{{ $evidence->description }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- Order Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h6><i class="fas fa-shopping-cart me-2"></i>Commande #{{ $dispute->order_id }}</h6>
            </div>
            <div class="card-body">
                <p><strong>Total:</strong> {{ number_format($dispute->order->total_amount ?? 0, 2) }}€</p>
                <p><strong>Date:</strong> {{ $dispute->order->created_at->format('d/m/Y') }}</p>
                <p><strong>Statut:</strong> {{ $dispute->order->status }}</p>
            </div>
        </div>

        <!-- Actions -->
        @if($dispute->isOpen())
            <div class="card mb-4">
                <div class="card-header">
                    <h6><i class="fas fa-cog me-2"></i>Actions</h6>
                </div>
                <div class="card-body">
                    <!-- Update Status -->
                    <form action="{{ route('admin.disputes.status', $dispute->id) }}" method="POST" class="mb-3">
                        @csrf
                        <label class="form-label">Changer le statut</label>
                        <select name="status" class="form-select mb-2">
                            <option value="under_review">En cours d'examen</option>
                            <option value="awaiting_vendor">En attente du vendeur</option>
                            <option value="awaiting_customer">En attente du client</option>
                            <option value="escalated">Escalader</option>
                        </select>
                        <button class="btn btn-secondary btn-sm w-100">Mettre à jour</button>
                    </form>

                    <hr>

                    <!-- Resolve -->
                    <form action="{{ route('admin.disputes.resolve', $dispute->id) }}" method="POST">
                        @csrf
                        <label class="form-label">Résoudre le litige</label>
                        <select name="resolution" class="form-select mb-2" required>
                            <option value="">-- Choisir --</option>
                            <option value="resolved_refund">Remboursement total</option>
                            <option value="resolved_partial">Remboursement partiel</option>
                            <option value="resolved_no_refund">Sans remboursement</option>
                        </select>
                        <input type="number" name="refund_amount" class="form-control mb-2" placeholder="Montant remboursé" step="0.01">
                        <textarea name="notes" class="form-control mb-2" placeholder="Notes de résolution"></textarea>
                        <button class="btn btn-success w-100">Résoudre</button>
                    </form>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h6><i class="fas fa-check me-2"></i>Litige résolu</h6>
                </div>
                <div class="card-body">
                    <p><strong>Résolution:</strong> {{ $dispute->status_label }}</p>
                    @if($dispute->refund_amount)
                        <p><strong>Remboursé:</strong> {{ number_format($dispute->refund_amount, 2) }}€</p>
                    @endif
                    @if($dispute->resolution_notes)
                        <p><strong>Notes:</strong> {{ $dispute->resolution_notes }}</p>
                    @endif
                    <p><small>Résolu le {{ $dispute->resolved_at->format('d/m/Y H:i') }}</small></p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
