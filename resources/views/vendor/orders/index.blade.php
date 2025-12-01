@extends('vendor.layouts.master')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .order-stats {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .order-stat {
            flex: 1;
            background: white;
            border-radius: 12px;
            padding: 15px 20px;
            border-left: 4px solid;
        }
        .order-stat.pending { border-left-color: #ffc107; }
        .order-stat.processing { border-left-color: #17a2b8; }
        .order-stat.shipped { border-left-color: #007bff; }
        .order-stat.completed { border-left-color: #28a745; }
        .order-stat h4 {
            margin: 0;
            font-weight: 700;
        }
        .order-stat small {
            color: #6c757d;
        }
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .filter-tabs .btn {
            border-radius: 20px;
            padding: 8px 20px;
        }
        .filter-tabs .btn.active {
            background: #667eea;
            border-color: #667eea;
            color: white;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-bag-check me-2"></i>Mes commandes</h4>
            <p class="text-muted mb-0">Gérez et suivez vos commandes</p>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <button class="btn btn-outline-secondary active" data-filter="all">Toutes</button>
        <button class="btn btn-outline-warning" data-filter="pending">En attente</button>
        <button class="btn btn-outline-info" data-filter="processing">En cours</button>
        <button class="btn btn-outline-primary" data-filter="shipped">Expédiées</button>
        <button class="btn btn-outline-success" data-filter="completed">Complétées</button>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table id="orders-table" class="table table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>N° Commande</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Montant</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-labelledby="deleteOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteOrderModalLabel">{{ __('cms.orders.delete_confirm_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">{{ __('cms.orders.delete_confirm_message') }}</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('cms.orders.delete_cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteOrder">{{ __('cms.orders.delete_button') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@php $datatableLang = __('cms.datatables'); @endphp

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

@if (session('success'))
<script>
    toastr.success("{{ session('success') }}", "{{ __('cms.orders.success') }}", {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 5000
    });
</script>
@endif

<script>
$(function () {
    const table = $('#orders-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('vendor.orders.data') }}",
            type: 'POST',
            data: function (d) {
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'customer', name: 'customer', orderable: false, searchable: false },
            { data: 'order_date', name: 'order_date', orderable: false, searchable: false },
            { data: 'status', name: 'status' },
            { data: 'total_price', name: 'total_price', orderable: false, searchable: false },
            {
                data: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `<span class="border border-danger dt-trash rounded-3 d-inline-block" 
                                onclick="deleteOrder(${row.id})">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </span>`;
                }
            }
        ],
        pageLength: 10,
        language: @json($datatableLang)
    });
});

let orderToDeleteId = null;

function deleteOrder(id) {
    orderToDeleteId = id;
    $('#deleteOrderModal').modal('show');

    $('#confirmDeleteOrder').off('click').on('click', function() {
        if (orderToDeleteId !== null) {
            $.ajax({
                url: '{{ route('vendor.orders.destroy', ':id') }}'.replace(':id', orderToDeleteId),
                method: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    if (response.success) {
                        $('#orders-table').DataTable().ajax.reload();
                        toastr.error(response.message, "{{ __('cms.orders.success') }}", {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-top-right",
                            timeOut: 5000
                        });
                        $('#deleteOrderModal').modal('hide');
                    } else {
                        toastr.error(response.message || 'Failed to delete order.', "Error", {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-top-right"
                        });
                    }
                },
                error: function() {
                    toastr.error('An error occurred while deleting the order.', "Error", {
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-top-right"
                    });
                    $('#deleteOrderModal').modal('hide');
                }
            });
        }
    });
}
</script>
@endsection
