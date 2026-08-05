@extends('backend.app', ['title' => 'News'])

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
                    <h1 class="page-title">News</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ url('admin/dashboard') }}">
                                <i class="fe fe-home me-2 fs-14"></i>Home
                            </a>
                        </li>
                        <li class="breadcrumb-item">News</li>
                        <li class="breadcrumb-item active">Index</li>
                    </ol>
                </div>
            </div>
            <!-- PAGE-HEADER END -->

            <!-- ROW -->
            <div class="row">
                <div class="col-12">
                    <div class="card product-sales-main">

                        <div class="card-header border-bottom">
                            <div class="card-options ms-auto">
                                <a href="{{ route('admin.news.create') }}" class="btn btn-primary btn-sm">Add News</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered text-nowrap border-bottom" id="datatable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Thumbnail</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <!-- ROW END -->

        </div>
    </div>
</div>

<!-- ================== Edit Modal ================== -->
<div class="modal fade" id="editNewsModal" tabindex="-1" aria-labelledby="editNewsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit News</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="editNewsFormContainer" class="text-center">
                    <img src="{{ asset('default/loader.gif') }}" style="width:50px">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================== View Modal ================== -->
<div class="modal fade" id="viewNewsModal" tabindex="-1" aria-labelledby="viewNewsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View News</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewNewsContainer">
                <div class="text-center">
                    <img src="{{ asset('default/loader.gif') }}" style="width:50px">
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        }
    });

    if (!$.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable({
            order: [],
            lengthMenu: [[10, 25, 50, 100, -1],[10, 25, 50, 100, "All"]],
            processing: true,
            serverSide: true,
            responsive: true,

            language: {
                processing: `<div class="text-center">
                    <img src="{{ asset('default/loader.gif') }}" style="width:50px">
                </div>`
            },

            ajax: {
                url: "{{ route('admin.news.index') }}",
                type: "GET",
            },

            columns: [
                { data: 'DT_RowIndex', orderable:false, searchable:false },
                { data: 'thumbnail', orderable:false, searchable:false },
                { data: 'title', name:'title' },
                { data: 'status', orderable:false, searchable:false },
                { data: 'action', orderable:false, searchable:false, className:'text-center' },
            ],
        });
    }
});

/* ================= STATUS ================= */
    function showStatusChangeAlert(id) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: 'You want to update the status?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
        }).then((result) => {
            if (result.isConfirmed) {
                statusChange(id);
            }
        });
    }

function statusChange(id) {
    NProgress.start();
    let url = "{{ route('admin.news.status', ':id') }}";
    $.get(url.replace(':id', id), function (resp) {
        NProgress.done();
        toastr.success(resp.message);
        $('#datatable').DataTable().ajax.reload(null, false);
    });
}

/* ================= DELETE ================= */
function showDeleteConfirm(id) {
    event.preventDefault();
    Swal.fire({
        title: 'Are you sure?',
        text: 'This data will be deleted permanently!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            deleteItem(id);
        }
    });
}

function deleteItem(id) {
    NProgress.start();
    let url = "{{ route('admin.news.destroy', ':id') }}";
    $.ajax({
        type: "DELETE",
        url: url.replace(':id', id),
        success: function (resp) {
            NProgress.done();
            toastr.success(resp.message);
        $('#datatable').DataTable().ajax.reload(null, false);
        }
    });
}

/* ================= EDIT ================= */
function goToEdit(id) {
    let url = "{{ route('admin.news.edit', ':id') }}".replace(':id', id);
    $('#editNewsModal').modal('show');
    $('#editNewsFormContainer').html('<div class="text-center"><img src="{{ asset('default/loader.gif') }}" style="width:50px"></div>');

    $.ajax({
        url: url,
        type: 'GET',
        success: function(response) {
            $('#editNewsFormContainer').html(response);
            // Re-initialize Summernote editors if present
            if (typeof $('.summernote').summernote !== 'undefined') {
                $('.summernote').summernote({
                    height: 200,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['fontname', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });
            }
            // Re-initialize any other dynamic scripts (e.g., detail add/remove if needed)
            initializeEditScripts();
        },
        error: function(xhr, status, error) {
            console.error('Error loading edit form:', error);
            $('#editNewsFormContainer').html('<div class="alert alert-danger text-center">Error loading form. Please try again.</div>');
            // Optionally close modal on error
            // $('#editNewsModal').modal('hide');
        }
    });
}

// Helper function to re-initialize scripts after AJAX load (call this in success if needed)
function initializeEditScripts() {
    let detailIndex = $('.news-detail-row').length;

    // Add new detail row (if add-detail button exists)
    $(document).off('click', '#add-detail').on('click', '#add-detail', function() {
        let html = `
        <div class="news-detail-row border p-3 mb-3">
            <div class="d-flex justify-content-between mb-2">
                <h6>Detail</h6>
                <button type="button" class="btn btn-danger btn-sm remove-detail">Remove</button>
            </div>

            <div class="mb-3">
                <label>Detail Title</label>
                <input type="text" name="details[${detailIndex}][title]" class="form-control">
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="details[${detailIndex}][description]" class="form-control summernote"></textarea>
            </div>

            <div class="images-wrapper">
                <div class="mb-3 image-row">
                    <label>Image</label>
                    <div class="input-group mb-2">
                        <input type="file" name="details[${detailIndex}][images][]" class="form-control" accept="image/*">
                        <button type="button" class="btn btn-success add-image">+</button>
                    </div>
                </div>
            </div>
        </div>
        `;
        $('#news-details-wrapper').append(html);
        detailIndex++;
        // Re-init summernote on new textarea
        $('.summernote').summernote({ height: 200 });
    });

    // Remove detail row
    $(document).off('click', '.remove-detail').on('click', '.remove-detail', function() {
           $(this).closest('.news-detail-row').remove();
    });

    // Add new image input inside a detail
    $(document).off('click', '.add-image').on('click', '.add-image', function() {
        let currentDetailIndex = $(this).closest('.news-detail-row').find('input[name*="details["]:first').attr('name').match(/details\[(\d+)\]/)[1];
        let inputGroup = `
            <div class="input-group mb-2">
                <input type="file" name="details[${currentDetailIndex}][images][]" class="form-control" accept="image/*">
                <button type="button" class="btn btn-danger remove-image">-</button>
            </div>
        `;
        $(this).closest('.images-wrapper').append(inputGroup);
    });

    // Remove image input (for new ones)
    $(document).off('click', '.remove-image').on('click', '.remove-image', function() {
            $(this).closest('.input-group').remove();

    });
}
// Bind dynamic buttons inside modal
function bindDynamicEditEvents(detailIndexStart = 0) {
    let detailIndex = detailIndexStart;

    // Add new detail
    $('#add-detail').off('click').on('click', function() {
        let html = `
            <div class="news-detail-row border p-3 mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <h6>Detail</h6>
                    <button type="button" class="btn btn-danger btn-sm remove-detail">Remove</button>
                </div>
                <div class="mb-3">
                    <label>Detail Title</label>
                    <input type="text" name="details[${detailIndex}][title]" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="details[${detailIndex}][description]" class="form-control summernote"></textarea>
                </div>
                <div class="images-wrapper mb-3">
                    <div class="image-row mb-2">
                        <div class="input-group mb-2">
                            <input type="file" name="details[${detailIndex}][images][]" class="form-control">
                            <button type="button" class="btn btn-success add-image">+</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#news-details-wrapper').append(html);
        $('.summernote').summernote({ height: 100 });
        detailIndex++;
    });

    // Remove detail row
    $(document).off('click', '.remove-detail').on('click', '.remove-detail', function() {
        $(this).closest('.news-detail-row').remove();
    });

    // Add new image input
    $(document).off('click', '.add-image').on('click', '.add-image', function() {
        let inputGroup = `
            <div class="input-group mb-2">
                <input type="file" name="${$(this).prev('input').attr('name')}" class="form-control">
                <button type="button" class="btn btn-danger remove-image">-</button>
            </div>
        `;
        $(this).closest('.images-wrapper').append(inputGroup);
    });

    // Remove image input
    $(document).off('click', '.remove-image').on('click', '.remove-image', function() {
        $(this).closest('.input-group').remove();
    });
}


/* ================= VIEW ================= */
function goToView(id) {
    let url = "{{ route('admin.news.show', ':id') }}";
    $('#viewNewsModal').modal('show');
    $('#viewNewsContainer').html('<div class="text-center"><img src="{{ asset('default/loader.gif') }}" style="width:50px"></div>');

    $.get(url.replace(':id', id), function(resp) {
        if(resp.status) {
            let news = resp.data;
            let html = `
                <h5>${news.title}</h5>
                <p><strong>Type:</strong> ${news.type}</p>
                <p><strong>Status:</strong> ${news.status}</p>
                <p><strong>Short Description:</strong> ${news.short_description}</p>
                <img src="/${news.thumbnail}" width="150">
                <hr>
            `;
            news.details.forEach(detail => {
                html += `<div class="border p-2 mb-2">
                            <p><strong>Detail Title:</strong> ${detail.title}</p>
                            <p><strong>Description:</strong> ${detail.description}</p>`;
                if(detail.images.length > 0) {
                    detail.images.forEach(img => {
                        html += `<img src="/${img.image}" width="100" class="me-1 mb-1">`;
                    });
                }
                html += `</div>`;
            });

            $('#viewNewsContainer').html(html);
        } else {
            $('#viewNewsContainer').html('<p class="text-danger">Failed to load news.</p>');
        }
    });
}

</script>
@endpush
