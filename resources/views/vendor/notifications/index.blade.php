@extends('vendor.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-bell me-2"></i>Notifications</h4>
        <div>
            <a href="{{ route('vendor.notifications.preferences') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-gear"></i>
            </a>
            <button class="btn btn-outline-primary" onclick="markAllRead()">
                <i class="bi bi-check-all me-1"></i>Tout marquer comme lu
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="list-group list-group-flush">
            @forelse($notifications as $notification)
                <a href="{{ $notification->action_url ?? '#' }}" 
                   class="list-group-item list-group-item-action d-flex align-items-start py-3 {{ !$notification->isRead() ? 'bg-light' : '' }}"
                   onclick="markRead({{ $notification->id }})">
                    <div class="me-3">
                        <div class="rounded-circle bg-{{ $notification->color }} bg-opacity-10 d-flex align-items-center justify-content-center"
                             style="width: 45px; height: 45px;">
                            <i class="bi {{ $notification->icon }} text-{{ $notification->color }}"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-1 {{ !$notification->isRead() ? 'fw-bold' : '' }}">
                                {{ $notification->title }}
                            </h6>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-0 text-muted">{{ $notification->message }}</p>
                    </div>
                    @if(!$notification->isRead())
                        <span class="badge bg-primary rounded-pill ms-2">Nouveau</span>
                    @endif
                </a>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Aucune notification</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
@endsection

@section('js')
<script>
function markRead(id) {
    fetch(`/vendor/notifications/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
}

function markAllRead() {
    fetch('{{ route('vendor.notifications.mark-all-read') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => location.reload());
}
</script>
@endsection
