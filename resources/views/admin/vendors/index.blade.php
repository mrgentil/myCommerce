@extends('admin.layouts.admin')

@section('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
    <div class="card mt-4">
        <div class="card-header card-header-bg text-white">
            <h6 class="d-flex align-items-center mb-0 dt-heading">{{ __('cms.vendors.title_list') }}</h6>
        </div>
        <div class="card-body">
            <table id="vendors-table" class="table table-bordered mt-4 dt-style">
                <thead>
                    <tr>
                        <th>{{ __('cms.vendors.id') }}</th>
                        <th>{{ __('cms.vendors.name') }}</th>
                        <th>{{ __('cms.vendors.email') }}</th>
                        <th>{{ __('cms.vendors.phone') }}</th>
                        <th>{{ __('cms.vendors.status') }}</th>
                        <th>{{ __('cms.vendors.actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteVendorModal" tabindex="-1" aria-labelledby="deleteVendorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="deleteVendorModalLabel">{{ __('cms.vendors.modal_confirm_delete_title') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">{{ __('cms.vendors.modal_confirm_delete_body') }}</div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('cms.vendors.cancel') }}</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteVendor">{{ __('cms.vendors.delete') }}</button>
            </div>
        </div>
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
    toastr.success("{{ session('success') }}", "{{ __('cms.vendors.success') }}", {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 5000
    });
</script>
@endif

<script>
$(document).ready(function() {
    $('#vendors-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.vendors.data') }}",
            type: "GET"
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { 
                data: 'status',
                name: 'status',
                render: function(data) {
                    const badges = {
                        'pending': '<span class="badge bg-warning">En attente</span>',
                        'approved': '<span class="badge bg-success">Approuvé</span>',
                        'active': '<span class="badge bg-success">Actif</span>',
                        'rejected': '<span class="badge bg-danger">Rejeté</span>',
                        'inactive': '<span class="badge bg-secondary">Inactif</span>',
                        'banned': '<span class="badge bg-dark">Banni</span>'
                    };
                    return badges[data] || '<span class="badge bg-secondary">' + data + '</span>';
                }
            },
            { 
                data: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    let actions = '<div class="btn-group btn-group-sm">';
                    
                    // View button
                    actions += `<a href="/admin/vendors/${row.id}" class="btn btn-info btn-sm" title="Voir"><i class="bi bi-eye"></i></a>`;
                    
                    // Approve button for pending or inactive vendors
                    if (row.status === 'pending' || row.status === 'inactive' || row.status === 'banned') {
                        actions += `<button class="btn btn-success btn-sm" onclick="approveVendor(${row.id})" title="Approuver"><i class="bi bi-check"></i></button>`;
                    }
                    
                    // Suspend button for active/approved vendors
                    if (row.status === 'approved' || row.status === 'active') {
                        actions += `<button class="btn btn-warning btn-sm" onclick="suspendVendor(${row.id})" title="Suspendre"><i class="bi bi-pause-circle"></i></button>`;
                    }

                    // Ban button
                    if (row.status !== 'banned') {
                        actions += `<button class="btn btn-dark btn-sm" onclick="banVendor(${row.id})" title="Bannir"><i class="bi bi-slash-circle"></i></button>`;
                    }
                    
                    // Delete button
                    actions += `<button class="btn btn-danger btn-sm" onclick="deleteVendor(${row.id})" title="Supprimer"><i class="bi bi-trash"></i></button>`;
                    
                    actions += '</div>';
                    return actions;
                }
            }
        ],
        pageLength: 10,
        language: @json($datatableLang)
    });
});

let vendorToDeleteId = null;

function approveVendor(id) {
    if (confirm('Voulez-vous approuver ce vendeur ?')) {
        $.ajax({
            url: '/admin/vendors/' + id + '/approve',
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
                if (response.success) {
                    $('#vendors-table').DataTable().ajax.reload();
                    toastr.success(response.message, "Succès");
                }
            },
            error: function() { toastr.error("Erreur lors de l'approbation", "Erreur"); }
        });
    }
}

function suspendVendor(id) {
    if (confirm('Voulez-vous suspendre ce vendeur ? Il ne pourra plus se connecter.')) {
        $.ajax({
            url: '/admin/vendors/' + id + '/suspend',
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
                if (response.success) {
                    $('#vendors-table').DataTable().ajax.reload();
                    toastr.warning(response.message, "Vendeur suspendu");
                }
            },
            error: function() { toastr.error("Erreur lors de la suspension", "Erreur"); }
        });
    }
}

function banVendor(id) {
    if (confirm('Voulez-vous BANNIR ce vendeur ? Cette action est plus sévère que la suspension.')) {
        $.ajax({
            url: '/admin/vendors/' + id + '/ban',
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
                if (response.success) {
                    $('#vendors-table').DataTable().ajax.reload();
                    toastr.error(response.message, "Vendeur banni");
                }
            },
            error: function() { toastr.error("Erreur lors du bannissement", "Erreur"); }
        });
    }
}

function deleteVendor(id) {
    vendorToDeleteId = id;        
    $('#deleteVendorModal').modal('show');

    $('#confirmDeleteVendor').off('click').on('click', function() {
        if (vendorToDeleteId !== null) {
            $.ajax({
                url: '{{ route('admin.vendors.destroy', ':id') }}'.replace(':id', vendorToDeleteId),
                method: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.success) {
                        $('#vendors-table').DataTable().ajax.reload();
                        toastr.error(response.message, "{{ __('cms.vendors.success') }}", {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-top-right",
                            timeOut: 5000
                        });
                        $('#deleteVendorModal').modal('hide');
                    }
                },
                error: function() {
                    toastr.error("{{ __('cms.vendors.error_delete') }}", "Error");
                    $('#deleteVendorModal').modal('hide');
                }
            });
        }
    });
}
</script>
@endsection
