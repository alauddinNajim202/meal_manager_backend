@extends('backend.app', ['title' => 'Platform Feedback'])

@push('styles')
<link href="{{ asset('default/datatable.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">

            <!-- PAGE-HEADER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Platform Feedback</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ url('admin/dashboard') }}"><i class="fe fe-home me-2 fs-14"></i>Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Platform Feedback</li>
                    </ol>
                </div>
            </div>
            <!-- PAGE-HEADER END -->

            <!-- ROW -->
            <div class="row">
                <div class="col-12">
                    <div class="card product-sales-main">
                        <div class="card-header border-bottom">
                            <h3 class="card-title"><i class="fa-solid fa-comments text-primary me-2"></i> User Feedback</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom" id="feedback-table">
                                    <thead>
                                        <tr>
                                            <th class="bg-transparent border-bottom-0">#</th>
                                            <th class="bg-transparent border-bottom-0">Date</th>
                                            <th class="bg-transparent border-bottom-0">User Info</th>
                                            <th class="bg-transparent border-bottom-0">Feedback</th>
                                            <th class="bg-transparent border-bottom-0">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ROW END -->

        </div>
    </div>
</div>

<!-- View Feedback Modal -->
<div class="modal fade" id="viewFeedbackModal" tabindex="-1" aria-labelledby="viewFeedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="viewFeedbackModalLabel">Feedback Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="mb-2"><strong>From:</strong> <span id="modal-email" class="text-primary fw-bold"></span></p>
                <div class="p-3 bg-light text-dark border text-break" style="border-radius: 5px; white-space: pre-wrap; min-height: 100px;" id="modal-feedback-content"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('default/datatable.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTable
        var table = $('#feedback-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.feedback.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'user_info', name: 'email' },
                { data: 'feedback_content', name: 'feedback' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'desc']],
            language: {
                searchPlaceholder: "Search records..."
            }
        });

        // View Modal Logic
        $('body').on('click', '.view-feedback', function () {
            var feedback = $(this).data('feedback');
            var email = $(this).data('email');

            $('#modal-email').text(email);
            $('#modal-feedback-content').text(feedback);
            $('#viewFeedbackModal').modal('show');
        });

        // Delete Logic
        $('body').on('click', '.delete-feedback', function () {
            var feedback_id = $(this).data("id");

            Swal.fire({
                title: 'Are you sure?',
                text: "This feedback will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#secondary',
                confirmButtonText: '<i class="fa fa-trash"></i> Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/feedback') }}" + '/' + feedback_id,
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (data) {
                            if (data.success) {
                                Swal.fire(
                                    'Deleted!',
                                    data.message,
                                    'success'
                                );
                                table.ajax.reload(null, false);
                            }
                        },
                        error: function (data) {
                            Swal.fire(
                                'Error!',
                                'Something went wrong.',
                                'error'
                            );
                            console.log('Error:', data);
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
