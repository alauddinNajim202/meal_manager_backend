@extends('backend.app', ['title' => 'Property List'])

@push('styles')
<link href="{{ asset('default/datatable.css') }}" rel="stylesheet" />  
@endpush

@section('content')
<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">
        <!-- CONTAINER -->
        <div class="main-container container-fluid">
            <!-- PAGE-HEADER -->
            <!-- PAGE-HEADER -->
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="page-title fs-3 fw-bold mb-0 text-dark">Property</h1>
                <a href="#" class="btn btn-warning text-white fw-semibold shadow-sm px-4 rounded-pill" style="background-color: #f97316; border-color: #f97316;">
                    <i class="fe fe-plus me-2"></i>Add Property
                </a>
            </div>
            <!-- PAGE-HEADER END -->

            <!-- SUMMARY CARDS -->
            <div class="row mb-4">
                <div class="col-sm-6 col-md-3">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <p class="mb-0 text-muted fw-medium">Total Income</p>
                                <div class="bg-light p-2 rounded"><i class="fe fe-credit-card text-dark"></i></div>
                            </div>
                            <h3 class="mb-2 fw-bold text-dark">${{ number_format($totalIncome ?? 0, 2) }}</h3>
                            <div class="d-flex justify-content-between align-items-center" style="font-size: 13px;">
                                <span class="text-success"><i class="fe fe-trending-up"></i> 12% Last week</span>
                                <a href="#" class="text-muted text-decoration-none">Show more &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <p class="mb-0 text-muted fw-medium">Total Properties</p>
                                <div class="bg-light p-2 rounded"><i class="fe fe-home text-dark"></i></div>
                            </div>
                            <h3 class="mb-2 fw-bold text-dark">{{ number_format($totalProperties ?? 0) }} Unit</h3>
                            <div class="d-flex justify-content-between align-items-center" style="font-size: 13px;">
                                <span class="text-danger"><i class="fe fe-trending-down"></i> 8% Last week</span>
                                <a href="#" class="text-muted text-decoration-none">Show more &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <p class="mb-0 text-muted fw-medium">Active Unit (Sold)</p>
                                <div class="bg-light p-2 rounded"><i class="fe fe-check-circle text-dark"></i></div>
                            </div>
                            <h3 class="mb-2 fw-bold text-dark">{{ number_format($unitSold ?? 0) }} Unit</h3>
                            <div class="d-flex justify-content-between align-items-center" style="font-size: 13px;">
                                <span class="text-danger"><i class="fe fe-trending-down"></i> 16% Last week</span>
                                <a href="#" class="text-muted text-decoration-none">Show more &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <p class="mb-0 text-muted fw-medium">Pending Unit (Rent)</p>
                                <div class="bg-light p-2 rounded"><i class="fe fe-clock text-dark"></i></div>
                            </div>
                            <h3 class="mb-2 fw-bold text-dark">{{ number_format($unitRent ?? 0) }} Unit</h3>
                            <div class="d-flex justify-content-between align-items-center" style="font-size: 13px;">
                                <span class="text-success"><i class="fe fe-trending-up"></i> 12% Last week</span>
                                <a href="#" class="text-muted text-decoration-none">Show more &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROW-4 -->
            <div class="row">
                <div class="col-12 col-sm-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header border-bottom-0 pt-4 pb-2 d-flex justify-content-between">
                            <h4 class="card-title fw-bold text-dark mb-0">All Properties List</h4>
                            <div class="card-options ms-auto">
                                <button class="btn btn-light btn-sm border text-muted">Last Month <i class="fe fe-chevron-down ms-1"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table text-nowrap border-0" id="datatable" style="border-collapse: separate; border-spacing: 0 10px;">
                                    <thead>
                                        <tr class="text-muted" style="border-bottom: 1px solid #f3f4f6;">
                                            <th class="bg-transparent border-bottom-0 fw-medium text-muted">ID</th>
                                            <th class="bg-transparent border-bottom-0 fw-medium text-muted">Properties Name</th>
                                            <th class="bg-transparent border-bottom-0 fw-medium text-muted">Properties Type</th>
                                            <th class="bg-transparent border-bottom-0 fw-medium text-muted">Rent/Sale</th>
                                            <th class="bg-transparent border-bottom-0 fw-medium text-muted">Bedrooms</th>
                                            <th class="bg-transparent border-bottom-0 fw-medium text-muted">Location</th>
                                            <th class="bg-transparent border-bottom-0 fw-medium text-muted">Price</th>
                                            <th class="bg-transparent border-bottom-0 fw-medium text-muted">Status</th>
                                            <th class="bg-transparent border-bottom-0 fw-medium text-muted">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div><!-- COL END -->
            </div>
            <!-- ROW-4 END -->
        </div>
    </div>
</div>
<!-- CONTAINER CLOSED -->
@endsection

@push('scripts')
<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            }
        });
        if (!$.fn.DataTable.isDataTable('#datatable')) {
            let dTable = $('#datatable').DataTable({
                order: [],
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                processing: true,
                responsive: true,
                serverSide: true,

                language: {
                    processing: `<div class="text-center">
                        <img src="{{ asset('default/loader.gif') }}" alt="Loader" style="width: 50px;">
                        </div>`
                },

                scroller: {
                    loadingIndicator: false
                },
                pagingType: "full_numbers",
                dom: "<'row justify-content-between table-topbar'<'col-md-4 col-sm-3'l><'col-md-5 col-sm-5 px-0'f>>tipr",
                ajax: {
                    url: "{{ route('admin.property.index') }}",
                    type: "GET",
                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'category',
                        name: 'category.name',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'for_whom',
                        name: 'for_whom',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'beds_baths',
                        name: 'beds_baths',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'location',
                        name: 'location',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'rent_amount',
                        name: 'rent_amount',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'dt-center text-center'
                    },
                ],
            });
        }
    });

    // Update Status Confirm
    function updateStatus(id, status) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to update the status to ' + status + '?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
        }).then((result) => {
            if (result.isConfirmed) {
                NProgress.start();
                let url = "{{ route('admin.property.status', ':id') }}";
                $.ajax({
                    type: "POST",
                    url: url.replace(':id', id),
                    data: {
                        status: status
                    },
                    success: function(resp) {
                        NProgress.done();
                        toastr.success(resp.message);
                        $('#datatable').DataTable().ajax.reload();
                    },
                    error: function(error) {
                        NProgress.done();
                        toastr.error('Error updating status');
                    }
                });
            }
        });
    }

    // delete Confirm
    function showDeleteConfirm(id) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure you want to delete this property?',
            text: 'If you delete this, it will be gone forever.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                NProgress.start();
                let url = "{{ route('admin.property.destroy', ':id') }}";
                let csrfToken = '{{ csrf_token() }}';
                $.ajax({
                    type: "DELETE",
                    url: url.replace(':id', id),
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(resp) {
                        NProgress.done();
                        toastr.success(resp.message);
                        $('#datatable').DataTable().ajax.reload();
                    },
                    error: function(error) {
                        NProgress.done();
                        toastr.error('Error deleting property');
                    }
                });
            }
        });
    }
</script>
@endpush
