@extends('vendor.layouts.master')

@section('css')
<style>
    .chat-container {
        display: flex;
        height: calc(100vh - 200px);
        min-height: 500px;
    }
    .conversations-sidebar {
        width: 300px;
        border-right: 1px solid #eee;
        overflow-y: auto;
    }
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .chat-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        background: #f8f9fa;
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #f5f5f5;
    }
    .chat-input {
        padding: 15px;
        border-top: 1px solid #eee;
        background: white;
    }
    .message {
        max-width: 70%;
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
        padding: 12px 16px;
        border-radius: 18px;
        position: relative;
    }
    .message.mine .message-bubble {
        background: #667eea;
        color: white;
        border-bottom-right-radius: 4px;
    }
    .message.theirs .message-bubble {
        background: white;
        border: 1px solid #eee;
        border-bottom-left-radius: 4px;
    }
    .message-time {
        font-size: 11px;
        color: #999;
        margin-top: 4px;
    }
    .message.mine .message-time {
        text-align: right;
    }
    .conv-item {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: all 0.2s;
    }
    .conv-item:hover, .conv-item.active {
        background: #e3f2fd;
    }
    .conv-item.unread {
        border-left: 3px solid #2196f3;
    }
    .avatar-xs {
        width: 35px;
        height: 35px;
        border-radius: 50%;
    }
    .attachment-preview {
        max-width: 200px;
        border-radius: 8px;
        margin-top: 8px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm">
        <div class="chat-container">
            <!-- Sidebar -->
            <div class="conversations-sidebar d-none d-lg-block">
                <div class="p-3 border-bottom">
                    <h6 class="mb-0"><i class="bi bi-chat-dots me-2"></i>Conversations</h6>
                </div>
                @foreach($conversations as $conv)
                    <a href="{{ route('vendor.messages.show', $conv->id) }}" class="text-decoration-none text-dark">
                        <div class="conv-item d-flex align-items-center {{ $conv->id == $conversation->id ? 'active' : '' }} {{ $conv->unread_count > 0 ? 'unread' : '' }}">
                            <img src="{{ $conv->customer->avatar ?? '/images/default-avatar.png' }}" class="avatar-xs me-2">
                            <div class="flex-grow-1 min-width-0">
                                <div class="fw-medium text-truncate">{{ $conv->customer->name ?? 'Client' }}</div>
                                <small class="text-muted text-truncate d-block">{{ Str::limit($conv->lastMessage?->content, 25) }}</small>
                            </div>
                            @if($conv->unread_count > 0)
                                <span class="badge bg-danger rounded-pill">{{ $conv->unread_count }}</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Main Chat -->
            <div class="chat-main">
                <!-- Header -->
                <div class="chat-header d-flex align-items-center">
                    <a href="{{ route('vendor.messages.index') }}" class="btn btn-link d-lg-none me-2">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <img src="{{ $conversation->customer->avatar ?? '/images/default-avatar.png' }}" class="avatar-xs me-3">
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $conversation->customer->name ?? 'Client' }}</h6>
                        @if($conversation->product)
                            <small class="text-muted">
                                <i class="bi bi-box me-1"></i>{{ $conversation->product->name }}
                            </small>
                        @endif
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if($conversation->order)
                                <li><a class="dropdown-item" href="{{ route('vendor.orders.show', $conversation->order->id) }}">
                                    <i class="bi bi-bag me-2"></i>Voir la commande
                                </a></li>
                            @endif
                            <li><a class="dropdown-item text-danger" href="#" onclick="closeConversation()">
                                <i class="bi bi-x-circle me-2"></i>Fermer la conversation
                            </a></li>
                        </ul>
                    </div>
                </div>

                <!-- Messages -->
                <div class="chat-messages" id="chatMessages">
                    @foreach($conversation->messages as $message)
                        <div class="message {{ $message->sender_type === 'vendor' ? 'mine' : 'theirs' }}">
                            <div class="message-bubble">
                                {{ $message->content }}
                                @if($message->attachment)
                                    <div>
                                        @if(Str::endsWith($message->attachment, ['.jpg', '.jpeg', '.png', '.gif']))
                                            <img src="{{ asset('storage/' . $message->attachment) }}" class="attachment-preview">
                                        @else
                                            <a href="{{ asset('storage/' . $message->attachment) }}" target="_blank" class="btn btn-sm btn-light mt-2">
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
                    <form id="replyForm" action="{{ route('vendor.messages.reply', $conversation->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <label class="btn btn-outline-secondary" for="attachment">
                                <i class="bi bi-paperclip"></i>
                            </label>
                            <input type="file" name="attachment" id="attachment" class="d-none" accept="image/*,.pdf,.doc,.docx">
                            <input type="text" name="content" class="form-control" placeholder="Votre message..." required>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i>
                            </button>
                        </div>
                        <div id="attachmentPreview" class="mt-2 d-none">
                            <span class="badge bg-secondary">
                                <i class="bi bi-paperclip me-1"></i>
                                <span id="attachmentName"></span>
                                <button type="button" class="btn-close btn-close-white ms-2" onclick="clearAttachment()"></button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
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

    // Auto-refresh messages every 10 seconds
    setInterval(refreshMessages, 10000);
});

function clearAttachment() {
    document.getElementById('attachment').value = '';
    document.getElementById('attachmentPreview').classList.add('d-none');
}

function refreshMessages() {
    fetch('{{ route("vendor.messages.get", $conversation->id) }}')
        .then(r => r.json())
        .then(data => {
            // Could update messages here if needed
        });
}

function closeConversation() {
    if (confirm('Fermer cette conversation ?')) {
        fetch('{{ route("vendor.messages.close", $conversation->id) }}', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        }).then(() => {
            window.location.href = '{{ route("vendor.messages.index") }}';
        });
    }
}
</script>
@endsection
