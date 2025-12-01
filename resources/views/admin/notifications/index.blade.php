@extends('admin.layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['customers'] }}</h3>
                <small>Clients</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['vendors'] }}</h3>
                <small>Vendeurs</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['notifications_today'] }}</h3>
                <small>Notifications aujourd'hui</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header card-header-bg text-white d-flex justify-content-between align-items-center">
        <h6><i class="fas fa-bell me-2"></i>Notifications</h6>
        <a href="{{ route('admin.notifications.create') }}" class="btn btn-light btn-sm">
            <i class="fas fa-paper-plane me-1"></i>Envoyer une notification
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <h6 class="mb-3">Notifications récentes</h6>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Titre</th>
                    <th>Message</th>
                    <th>Destinataire</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentNotifications as $notification)
                    <tr>
                        <td><span class="badge bg-{{ $notification->color }}">{{ $notification->type }}</span></td>
                        <td>{{ $notification->title }}</td>
                        <td>{{ Str::limit($notification->message, 50) }}</td>
                        <td>
                            {{ class_basename($notification->notifiable_type) }} #{{ $notification->notifiable_id }}
                        </td>
                        <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">Aucune notification récente</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
