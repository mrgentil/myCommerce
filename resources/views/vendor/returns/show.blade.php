@extends('vendor.layouts.master')

@section('content')
<div class="container-fluid">
    <a href="{{ route('vendor.returns.index') }}" class="btn btn-link text-muted mb-3">
        <i class="bi bi-arrow-left me-2"></i>Retour à la liste
    </a>

    <div class="row">
        <div class="col-lg-8">
            <!-- Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Demande de retour #{{ $return->id }}</h5>
                            <p class="text-muted mb-0">Commande #{{ $return->order_id }} · {{ $return->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="badge bg-{{ $return->status_color }} px-3 py-2 fs-6">
                            {{ $return->status_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions based on status -->
            @if($return->status === 'pending')
                <div class="card border-0 shadow-sm mb-4 border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h6 class="mb-0"><i class="bi bi-clock me-2"></i>Action requise</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">Le client demande un {{ strtolower($return->type_label) }}. Veuillez examiner la demande et prendre une décision.</p>
                        
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success" onclick="approveReturn()">
                                <i class="bi bi-check-circle me-2"></i>Approuver
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle me-2"></i>Refuser
                            </button>
                        </div>
                    </div>
                </div>
            @elseif($return->status === 'shipped')
                <div class="card border-0 shadow-sm mb-4 border-info">
                    <div class="card-header bg-info bg-opacity-10">
                        <h6 class="mb-0"><i class="bi bi-truck me-2"></i>Article en transit</h6>
                    </div>
                    <div class="card-body">
                        <p>Le client a renvoyé l'article. Numéro de suivi: <strong>{{ $return->return_tracking }}</strong></p>
                        <button type="button" class="btn btn-primary" onclick="markReceived()">
                            <i class="bi bi-box-seam me-2"></i>Marquer comme reçu
                        </button>
                    </div>
                </div>
            @elseif($return->status === 'received')
                <div class="card border-0 shadow-sm mb-4 border-success">
                    <div class="card-header bg-success bg-opacity-10">
                        <h6 class="mb-0"><i class="bi bi-check2-circle me-2"></i>Article reçu</h6>
                    </div>
                    <div class="card-body">
                        <p>L'article a été reçu. Vous pouvez maintenant procéder au remboursement.</p>
                        <div class="input-group" style="max-width: 300px;">
                            <span class="input-group-text">€</span>
                            <input type="number" id="refundAmount" class="form-control" 
                                   value="{{ $return->refund_amount }}" step="0.01">
                            <button type="button" class="btn btn-success" onclick="processRefund()">
                                <i class="bi bi-cash me-1"></i>Rembourser
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Product info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-box me-2"></i>Article concerné</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $return->orderDetail->product->name ?? 'Produit' }}</h6>
                            @if($return->orderDetail->productVariant)
                                <small class="text-muted">{{ $return->orderDetail->productVariant->variant_slug }}</small>
                            @endif
                        </div>
                        <div class="text-end">
                            <span class="text-muted">Qté: {{ $return->quantity }}</span>
                            <strong class="d-block">{{ number_format($return->refund_amount, 2, ',', ' ') }} €</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reason and description -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-chat-text me-2"></i>Raison du retour</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Motif:</strong> {{ $return->reason_label }}</p>
                    <p class="mb-0"><strong>Description:</strong></p>
                    <p class="text-muted">{{ $return->description }}</p>
                </div>
            </div>

            <!-- Photos -->
            @if($return->images->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-images me-2"></i>Photos fournies</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach($return->images as $image)
                                <div class="col-md-3">
                                    <a href="{{ $image->url }}" target="_blank">
                                        <img src="{{ $image->url }}" class="img-fluid rounded" alt="Photo">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Customer -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>Client</h6>
                </div>
                <div class="card-body">
                    <strong>{{ $return->customer->name ?? 'Client' }}</strong>
                    <small class="d-block text-muted">{{ $return->customer->email }}</small>
                    <a href="{{ route('vendor.messages.show', ['id' => $return->order_id]) }}" 
                       class="btn btn-sm btn-outline-primary mt-3 w-100">
                        <i class="bi bi-chat me-1"></i>Contacter
                    </a>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historique</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <small class="text-muted">{{ $return->created_at->format('d/m/Y H:i') }}</small>
                            <p class="mb-0">Demande créée</p>
                        </li>
                        @if($return->approved_at)
                            <li class="mb-2">
                                <small class="text-muted">{{ $return->approved_at->format('d/m/Y H:i') }}</small>
                                <p class="mb-0">Approuvé</p>
                            </li>
                        @endif
                        @if($return->shipped_at)
                            <li class="mb-2">
                                <small class="text-muted">{{ $return->shipped_at->format('d/m/Y H:i') }}</small>
                                <p class="mb-0">Renvoyé par le client</p>
                            </li>
                        @endif
                        @if($return->received_at)
                            <li class="mb-2">
                                <small class="text-muted">{{ $return->received_at->format('d/m/Y H:i') }}</small>
                                <p class="mb-0">Article reçu</p>
                            </li>
                        @endif
                        @if($return->refunded_at)
                            <li class="mb-2">
                                <small class="text-muted">{{ $return->refunded_at->format('d/m/Y H:i') }}</small>
                                <p class="mb-0">Remboursé</p>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Return tracking -->
            @if($return->return_tracking)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-truck me-2"></i>Suivi retour</h6>
                    </div>
                    <div class="card-body">
                        <code>{{ $return->return_tracking }}</code>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Refuser le retour</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Raison du refus</label>
                <textarea id="rejectReason" class="form-control" rows="3" 
                          placeholder="Expliquez pourquoi vous refusez ce retour..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" onclick="rejectReturn()">Refuser</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function approveReturn() {
    if (!confirm('Approuver ce retour ?')) return;
    
    fetch('{{ route('vendor.returns.approve', $return->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function rejectReturn() {
    const reason = document.getElementById('rejectReason').value;
    if (!reason) {
        alert('Veuillez entrer une raison');
        return;
    }
    
    fetch('{{ route('vendor.returns.reject', $return->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ reason })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function markReceived() {
    if (!confirm('Confirmer la réception de l\'article ?')) return;
    
    fetch('{{ route('vendor.returns.received', $return->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function processRefund() {
    const amount = document.getElementById('refundAmount').value;
    if (!confirm(`Procéder au remboursement de ${amount}€ ?`)) return;
    
    fetch('{{ route('vendor.returns.refund', $return->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ amount })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endsection
