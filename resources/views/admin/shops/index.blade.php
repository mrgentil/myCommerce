@extends('admin.layouts.admin')

@section('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Boutiques</h6>
                            <h2 class="mb-0">{{ $totalShops }}</h2>
                        </div>
                        <i class="bi bi-shop fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">En attente</h6>
                            <h2 class="mb-0">{{ $pendingShops }}</h2>
                        </div>
                        <i class="bi bi-hourglass-split fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Approuvées</h6>
                            <h2 class="mb-0">{{ $approvedShops }}</h2>
                        </div>
                        <i class="bi bi-check-circle fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header card-header-bg text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0 dt-heading">{{ __('Liste des Boutiques') }}</h6>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-light filter-btn active" data-status="all">Toutes</button>
                <button type="button" class="btn btn-sm btn-warning filter-btn" data-status="pending">En attente</button>
                <button type="button" class="btn btn-sm btn-success filter-btn" data-status="approved">Approuvées</button>
                <button type="button" class="btn btn-sm btn-secondary filter-btn" data-status="inactive">Inactives</button>
            </div>
        </div>
        <div class="card-body">
            <table id="shops-table" class="table table-bordered mt-4 dt-style">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom de la boutique</th>
                        <th>Vendeur</th>
                        <th>Email vendeur</th>
                        <th>Statut</th>
                        <th>Date création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

@php
    $datatableLang = __('cms.datatables'); 
@endphp

@if (session('success'))
<script>
    toastr.success("{{ session('success') }}", "Succès", {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 5000
    });
</script>
@endif

<script>
$(document).ready(function() {
    let currentStatus = 'all';
    
    let table = $('#shops-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.shops.data') }}",
            type: "GET",
            data: function(d) {
                d.status = currentStatus;
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'vendor_name', name: 'vendor_name', orderable: false },
            { data: 'vendor_email', name: 'vendor_email', orderable: false },
            { data: 'status_badge', name: 'status', orderable: true, searchable: false },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: function(data) {
                    return new Date(data).toLocaleDateString('fr-FR');
                }
            },
            { data: 'action', orderable: false, searchable: false }
        ],
        pageLength: 10,
        language: @json($datatableLang)
    });

    // Filter buttons
    $('.filter-btn').on('click', function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        currentStatus = $(this).data('status');
        table.ajax.reload();
    });
});

function approveShop(id) {
    if (confirm('Voulez-vous approuver cette boutique ?')) {
        $.ajax({
            url: '/admin/shops/' + id + '/approve',
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
                if (response.success) {
                    $('#shops-table').DataTable().ajax.reload();
                    toastr.success(response.message, "Succès");
                }
            },
            error: function() { toastr.error("Erreur lors de l'approbation", "Erreur"); }
        });
    }
}

function rejectShop(id) {
    if (confirm('Voulez-vous rejeter cette boutique ?')) {
        $.ajax({
            url: '/admin/shops/' + id + '/reject',
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
                if (response.success) {
                    $('#shops-table').DataTable().ajax.reload();
                    toastr.warning(response.message, "Boutique rejetée");
                }
            },
            error: function() { toastr.error("Erreur lors du rejet", "Erreur"); }
        });
    }
}

function suspendShop(id) {
    if (confirm('Voulez-vous suspendre cette boutique ?')) {
        $.ajax({
            url: '/admin/shops/' + id + '/suspend',
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
                if (response.success) {
                    $('#shops-table').DataTable().ajax.reload();
                    toastr.warning(response.message, "Boutique suspendue");
                }
            },
            error: function() { toastr.error("Erreur lors de la suspension", "Erreur"); }
        });
    }
}
</script>
@endsection
