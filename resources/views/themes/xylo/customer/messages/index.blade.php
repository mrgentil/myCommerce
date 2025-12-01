@extends('themes.xylo.layouts.master')

@section('css')
<style>
    .messages-container {
        max-width: 900px;
        margin: 0 auto;
    }
    .conversation-card {
        border: none;
        border-radius: 12px;
        transition: all 0.2s;
        margin-bottom: 15px;
    }
    .conversation-card:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .conversation-card.unread {
        border-left: 4px solid #667eea;
        background: #f8f9ff;
    }
    .shop-avatar {
        width: 55px;
        height: 55px;
        border-radius: 12px;
        object-fit: cover;
    }
    .message-preview {
        color: #666;
        font-size: 14px;
    }
    .unread-badge {
        background: #667eea;
        color: white;
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 20px;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="messages-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="bi bi-chat-dots me-2"></i>Mes messages</h3>
        </div>

        @forelse($conversations as $conversation)
            <a href="{{ route('customer.messages.show', $conversation->id) }}" class="text-decoration-none">
                <div class="card conversation-card {{ $conversation->unread_count > 0 ? 'unread' : '' }}">
                    <div class="card-body d-flex align-items-center">
                        <img src="{{ $conversation->vendor->shop->logo ?? '/images/default-shop.png' }}" 
                             alt="Shop" class="shop-avatar me-3">
                        
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 text-dark fw-bold">
                                        {{ $conversation->vendor->shop->name ?? $conversation->vendor->name }}
                                    </h6>
                                    @if($conversation->product)
                                        <small class="text-primary">
                                            <i class="bi bi-box me-1"></i>{{ $conversation->product->name }}
                                        </small>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $conversation->updated_at->diffForHumans() }}</small>
                            </div>
                            
                            <p class="message-preview mb-0 mt-2">
                                @if($conversation->lastMessage)
                                    @if($conversation->lastMessage->sender_type === 'customer')
                                        <span class="text-muted">Vous: </span>
                                    @endif
                                    {{ Str::limit($conversation->lastMessage->content, 80) }}
                                @endif
                            </p>
                        </div>

                        @if($conversation->unread_count > 0)
                            <span class="unread-badge ms-3">{{ $conversation->unread_count }}</span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-chat-dots fs-1 text-muted"></i>
                <h5 class="mt-3">Aucun message</h5>
                <p class="text-muted">Vos conversations avec les vendeurs apparaîtront ici</p>
                <a href="{{ route('shops.index') }}" class="btn btn-primary">
                    <i class="bi bi-shop me-2"></i>Découvrir les boutiques
                </a>
            </div>
        @endforelse

        @if($conversations->hasPages())
            <div class="mt-4">
                {{ $conversations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
