@extends('vendor.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-question-circle me-2"></i>Questions clients</h4>
        <div class="badge bg-warning">{{ $stats['unanswered'] }} sans réponse</div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Statut</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Toutes</option>
                        <option value="unanswered" {{ request('status') == 'unanswered' ? 'selected' : '' }}>Sans réponse</option>
                        <option value="answered" {{ request('status') == 'answered' ? 'selected' : '' }}>Répondues</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Produit</label>
                    <select name="product_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les produits</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Questions list -->
    <div class="card border-0 shadow-sm">
        @forelse($questions as $question)
            <div class="card-body border-bottom">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge bg-light text-dark mb-2">{{ $question->product->name }}</span>
                        <h6 class="mb-1">{{ $question->question }}</h6>
                        <small class="text-muted">
                            <i class="bi bi-person me-1"></i>{{ $question->customer?->name ?? 'Anonyme' }}
                            · {{ $question->created_at->diffForHumans() }}
                        </small>
                    </div>
                    <div>
                        @if($question->answers->isEmpty())
                            <span class="badge bg-warning">En attente</span>
                        @else
                            <span class="badge bg-success">Répondu</span>
                        @endif
                    </div>
                </div>

                <!-- Existing answers -->
                @foreach($question->answers as $answer)
                    <div class="bg-light rounded p-3 mb-2 {{ $answer->is_official ? 'border-start border-primary border-3' : '' }}">
                        <div class="d-flex justify-content-between">
                            <div>
                                @if($answer->is_official)
                                    <span class="badge bg-primary me-2">Réponse officielle</span>
                                @endif
                                <strong>{{ $answer->answerer_name }}</strong>
                            </div>
                            <small class="text-muted">{{ $answer->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-0 mt-2">{{ $answer->answer }}</p>
                    </div>
                @endforeach

                <!-- Answer form -->
                <div class="mt-3">
                    <form onsubmit="submitAnswer(event, {{ $question->id }})" class="d-flex gap-2">
                        <input type="text" id="answer-{{ $question->id }}" class="form-control" 
                               placeholder="Votre réponse..." required>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="card-body text-center py-5">
                <i class="bi bi-chat-square-text fs-1 text-muted"></i>
                <p class="mt-3 text-muted">Aucune question pour le moment</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $questions->links() }}
    </div>
</div>
@endsection

@section('js')
<script>
function submitAnswer(event, questionId) {
    event.preventDefault();
    const input = document.getElementById('answer-' + questionId);
    const answer = input.value;

    fetch(`/vendor/questions/${questionId}/answer`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ answer })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Erreur');
        }
    });
}
</script>
@endsection
