@extends('vendor.layouts.master')

@section('css')
<style>
    .conversation-item {
        padding: 15px;
        border-bottom: 1px solid #eee;
        transition: all 0.2s;
        cursor: pointer;
    }
    .conversation-item:hover {
        background: #f8f9fa;
    }
    .conversation-item.unread {
        background: #e3f2fd;
        border-left: 3px solid #2196f3;
    }
    .avatar-sm {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
    }
    .message-preview {
        color: #666;
        font-size: 13px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 300px;
    }
    .unread-badge {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #dc3545;
        color: white;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .time-ago {
        font-size: 12px;
        color: #999;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-chat-dots me-2"></i>Messages</h4>
            <p class="text-muted mb-0">{{ $totalUnread }} message(s) non lu(s)</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @forelse($conversations as $conversation)
                <a href="{{ route('vendor.messages.show', $conversation->id) }}" class="text-decoration-none text-dark">
                    <div class="conversation-item d-flex align-items-center {{ $conversation->unread_count > 0 ? 'unread' : '' }}">
                        <img src="{{ $conversation->customer->avatar ?? '/images/default-avatar.png' }}" 
                             alt="Avatar" class="avatar-sm me-3">
                        
                        <div class="flex-grow-1 min-width-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">{{ $conversation->customer->name ?? 'Client' }}</h6>
                                <span class="time-ago">{{ $conversation->lastMessage?->created_at?->diffForHumans() }}</span>
                            </div>
                            
                            @if($conversation->product)
                                <small class="text-primary">
                                    <i class="bi bi-box me-1"></i>{{ $conversation->product->name }}
                                </small>
                            @endif
                            
                            @if($conversation->order)
                                <small class="text-info">
                                    <i class="bi bi-bag me-1"></i>Commande #{{ $conversation->order->id }}
                                </small>
                            @endif
                            
                            <p class="message-preview mb-0">
                                @if($conversation->lastMessage)
                                    @if($conversation->lastMessage->sender_type === 'vendor')
                                        <span class="text-muted">Vous: </span>
                                    @endif
                                    {{ Str::limit($conversation->lastMessage->content, 50) }}
                                @else
                                    <span class="text-muted fst-italic">Pas de message</span>
                                @endif
                            </p>
                        </div>

                        @if($conversation->unread_count > 0)
                            <div class="unread-badge ms-2">{{ $conversation->unread_count }}</div>
                        @endif
                    </div>
                </a>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-chat-dots fs-1 text-muted"></i>
                    <h5 class="mt-3">Aucune conversation</h5>
                    <p class="text-muted">Les messages de vos clients apparaîtront ici</p>
                </div>
            @endforelse
        </div>

        @if($conversations->hasPages())
            <div class="card-footer bg-white">
                {{ $conversations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
