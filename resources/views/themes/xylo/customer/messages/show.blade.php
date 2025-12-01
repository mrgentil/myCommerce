@extends('themes.xylo.layouts.master')

@section('css')
<style>
    .chat-container {
        max-width: 800px;
        margin: 0 auto;
        height: calc(100vh - 250px);
        min-height: 500px;
        display: flex;
        flex-direction: column;
    }
    .chat-header {
        padding: 20px;
        background: white;
        border-radius: 12px 12px 0 0;
        border: 1px solid #eee;
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #f5f5f5;
        border-left: 1px solid #eee;
        border-right: 1px solid #eee;
    }
    .chat-input {
        padding: 20px;
        background: white;
        border-radius: 0 0 12px 12px;
        border: 1px solid #eee;
    }
    .message {
        max-width: 75%;
        margin-bottom: 15px;
        clear: both;
    }
    .message.mine {
        float: right;
    }
    .message.theirs {
        float: left;
    }
    .message-bubble {
        padding: 12px 18px;
        border-radius: 20px;
        position: relative;
    }
    .message.mine .message-bubble {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-bottom-right-radius: 4px;
    }
    .message.theirs .message-bubble {
        background: white;
        border: 1px solid #e0e0e0;
        border-bottom-left-radius: 4px;
    }
    .message-time {
        font-size: 11px;
        color: #999;
        margin-top: 5px;
    }
    .message.mine .message-time {
        text-align: right;
    }
    .shop-avatar-lg {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        object-fit: cover;
    }
    .product-ref {
        background: #f0f0f0;
        padding: 10px 15px;
        border-radius: 8px;
        margin-top: 10px;
    }
    .attachment-img {
        max-width: 250px;
        border-radius: 8px;
        margin-top: 8px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="chat-container">
        <!-- Header -->
        <div class="chat-header">
            <div class="d-flex align-items-center">
                <a href="{{ route('customer.messages.index') }}" class="btn btn-link text-dark me-2">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <img src="{{ $conversation->vendor->shop->logo ?? '/images/default-shop.png' }}" 
                     class="shop-avatar-lg me-3">
                <div class="flex-grow-1">
                    <h5 class="mb-0">{{ $conversation->vendor->shop->name ?? $conversation->vendor->name }}</h5>
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>Répond généralement en quelques heures
                    </small>
                </div>
                <a href="{{ route('shop.view', $conversation->vendor->shop->slug ?? '') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-shop me-1"></i>Voir la boutique
                </a>
            </div>

            @if($conversation->product)
                <div class="product-ref d-flex align-items-center">
                    <i class="bi bi-box text-primary me-2"></i>
                    <span>Concernant: <strong>{{ $conversation->product->name }}</strong></span>
                    <a href="{{ route('product.show', $conversation->product->slug) }}" class="ms-auto btn btn-sm btn-link">
                        Voir le produit
                    </a>
                </div>
            @endif

            @if($conversation->order)
                <div class="product-ref d-flex align-items-center mt-2">
                    <i class="bi bi-bag text-info me-2"></i>
                    <span>Commande: <strong>#{{ $conversation->order->id }}</strong></span>
                </div>
            @endif
        </div>

        <!-- Messages -->
        <div class="chat-messages" id="chatMessages">
            @foreach($conversation->messages as $message)
                <div class="message {{ $message->sender_type === 'customer' ? 'mine' : 'theirs' }}">
                    <div class="message-bubble">
                        {{ $message->content }}
                        @if($message->attachment)
                            <div>
                                @if(Str::endsWith($message->attachment, ['.jpg', '.jpeg', '.png', '.gif']))
                                    <img src="{{ asset('storage/' . $message->attachment) }}" class="attachment-img">
                                @else
                                    <a href="{{ asset('storage/' . $message->attachment) }}" target="_blank" 
                                       class="btn btn-sm btn-light mt-2">
                                        <i class="bi bi-paperclip"></i> Pièce jointe
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="message-time">{{ $message->created_at->format('d/m H:i') }}</div>
                </div>
            @endforeach
            <div style="clear: both;"></div>
        </div>

        <!-- Input -->
        <div class="chat-input">
            <form id="replyForm" action="{{ route('customer.messages.reply', $conversation->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="input-group">
                    <label class="btn btn-outline-secondary rounded-start" for="attachment">
                        <i class="bi bi-paperclip"></i>
                    </label>
                    <input type="file" name="attachment" id="attachment" class="d-none" accept="image/*,.pdf,.doc,.docx">
                    <input type="text" name="content" class="form-control" placeholder="Écrivez votre message..." required>
                    <button type="submit" class="btn btn-primary rounded-end px-4">
                        <i class="bi bi-send"></i>
                    </button>
                </div>
                <div id="attachmentPreview" class="mt-2 d-none">
                    <span class="badge bg-secondary">
                        <i class="bi bi-paperclip me-1"></i>
                        <span id="attachmentName"></span>
                        <button type="button" class="btn-close btn-close-white ms-2" onclick="clearAttachment()" style="font-size: 10px;"></button>
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to bottom
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Attachment preview
    document.getElementById('attachment').addEventListener('change', function() {
        if (this.files.length > 0) {
            document.getElementById('attachmentName').textContent = this.files[0].name;
            document.getElementById('attachmentPreview').classList.remove('d-none');
        }
    });

    // Auto-refresh every 15 seconds
    setInterval(function() {
        fetch('{{ route("customer.messages.get", $conversation->id) }}')
            .then(r => r.json())
            .then(data => {
                // Could refresh messages here
            });
    }, 15000);
});

function clearAttachment() {
    document.getElementById('attachment').value = '';
    document.getElementById('attachmentPreview').classList.add('d-none');
}
</script>
@endsection
