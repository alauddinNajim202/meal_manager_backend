@extends('backend.app', ['title' => 'Project'])

@push('styles')

@endpush


@section('content')
<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

        <!-- CONTAINER -->
        <div class="main-container container-fluid">


            <!-- PAGE-HEADER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Project</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Project</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Show</li>
                    </ol>
                </div>
            </div>
            <!-- PAGE-HEADER END -->

            <div class="row">
                <div class="col-md-12">
                    <div class="card product-sales-main">
                        <div class="card-header border-bottom">
                            <h3 class="card-title mb-0">Description</h3>
                        </div>
                        <div class="card-body" style="overflow-x: auto;">
                            {!! $project->description ?? '' !!}
                        </div>
                    </div>
                </div><!-- COL END -->
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card product-sales-main">
                        <div class="card-header border-bottom">
                            <h3 class="card-title mb-0">Credintials</h3>
                        </div>
                        <div class="card-body" style="overflow-x: auto;">
                            {!! $project->credintials ?? '' !!}
                        </div>
                    </div>
                </div><!-- COL END -->
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card product-sales-main">
                        <div class="card-header border-bottom">
                            <h3 class="card-title mb-0">Technologies</h3>
                        </div>
                        <div class="card-body" style="overflow-x: auto;">
                            {!! $project->technologies ?? '' !!}
                        </div>
                    </div>
                </div><!-- COL END -->
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card product-sales-main">
                        <div class="card-header border-bottom">
                            <h3 class="card-title mb-0">Features</h3>
                        </div>
                        <div class="card-body" style="overflow-x: auto;">
                            {!! $project->features ?? '' !!}
                        </div>
                    </div>
                </div><!-- COL END -->
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card product-sales-main">
                        <div class="card-header border-bottom">
                            <h3 class="card-title mb-0">Note</h3>
                        </div>
                        <div class="card-body" style="overflow-x: auto;">
                            {!! $project->note ?? '' !!}
                        </div>
                    </div>
                </div><!-- COL END -->
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card product-sales-main">
                                <div class="card-header border-bottom">
                                    <h3 class="card-title mb-0">Project</h3>
                                </div>
                                <div class="card-body" style="overflow-x: auto;">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <td colspan="2" class="text-center"><img src="{{ asset($project->image && file_exists(public_path($project->image)) ? $project->image : 'default/logo.png') }}" alt="" width="100"></td>
                                        </tr>
                                        <tr>
                                            <th>Author</th>
                                            <td><a href="{{ route('admin.users.show', $project->user->id) }}">{{ $project->user->name ?? '' }}</a></td><td>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <td>{{ $project->name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Title</th>
                                            <td>{{ $project->title ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Type</th>
                                            <td><a href="{{ route('admin.type.show', $project->type->id) }}">{{ $project->type->name ?? '' }}</a></td>
                                        </tr>
                                        <tr>
                                            <th>Backend Url</th>
                                            <td><a href="{{ $project->backend ?? '' }}" target="_blank">Backend</a></td>
                                        </tr>
                                        <tr>
                                            <th>Frontend Url</th>
                                            <td><a href="{{ $project->frontend ?? '' }}" target="_blank">Fontend</a></td>
                                        </tr>
                                        <tr>
                                            <th>Github</th>
                                            <td><a href="{{ $project->github ?? '' }}" target="_blank">Github</a></td>
                                        </tr>
                                        <tr>
                                            <th>Start Date</th>
                                            <td>{{ $project->start_date ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>End Date</th>
                                            <td>{{ $project->end_date ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Action</th>
                                            <td>
                                                <button class="btn btn-sm btn-danger" onclick="showDeleteConfirm(`{{ $project->id }}`)">Delete</button>
                                                <button class="btn btn-sm btn-primary" onclick="goToEdit(`{{ $project->id }}`)">Edit</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Project</th>
                                            <td><a class="btn btn-sm btn-primary" href="{{ asset($project->file && file_exists(public_path($project->file)) ? $project->file : 'default/logo.png') }}" target="_blank"><i class="fa fa-download"></i></a> Project File Download. </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card product-sales-main">
                                <div class="card-header border-bottom">
                                    <h3 class="card-title mb-0">Metadata</h3>
                                </div>
                                <div class="card-body" style="overflow-x: auto;">
                                    @if($project->thumbnail && file_exists(public_path($project->thumbnail)))
                                    <img class="img-fluid" src="{{ asset($project->thumbnail) }}" alt="" width="100%">
                                    @endif
                                    <table class="table table-bordered table-striped metadata-table">
                                        @foreach (json_decode($project->metadata, true) as $key => $value)
                                        <tr>
                                            <th>{{ $key }}</th>
                                            <td>{{ $value }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- COL END -->
            </div>

        </div>
    </div>
</div>
<!-- CONTAINER CLOSED -->
@endsection

@push('scripts')
<script>
    // delete Confirm
    function showDeleteConfirm(id) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure you want to delete this record?',
            text: 'If you delete this, it will be gone forever.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                deleteItem(id);
            }
        });
    }

    // Delete Button
    function deleteItem(id) {
        NProgress.start();
        let url = "{{ route('admin.project.destroy', ':id') }}";
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
                window.location.href = "{{ route('admin.project.index') }}";
            },
            error: function(error) {
                NProgress.done();
                toastr.error(error.message);
            }
        });
    }

    //edit
    function goToEdit(id) {
        let url = "{{ route('admin.project.edit', ':id') }}";
        window.location.href = url.replace(':id', id);
    }
</script>
@endpush