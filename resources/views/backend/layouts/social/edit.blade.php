@extends('backend.app', ['title' => 'Update Social Link'])

@section('content')

<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

        <!-- CONTAINER -->
        <div class="main-container container-fluid">

            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ $crud ? ucwords(str_replace('_', ' ', $crud)) : 'N/A' }}</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url("admin/dashboard") }}"><i class="fe fe-home me-2 fs-14"></i>Home</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Social-Link</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Update</li>
                    </ol>
                </div>
            </div>

            <div class="row" id="user-profile">
                <div class="col-lg-12">

                    <div class="tab-content">
                        <div class="tab-pane active show" id="editProfile">
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h3 class="card-title mb-0">Update</h3>
                                    <div class="card-options">
                                        <a href="javascript:window.history.back()" class="btn btn-sm btn-primary">Back</a>
                                    </div>
                                </div>
                                <div class="card-body border-0">
                                    <form class="form form-horizontal" method="post" action="{{ route('admin.social.update', $social->id) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('POST')
                                        <div class="row mb-4">

                                            <div class="form-group">
                                                <label for="sn" class="form-label">SN:</label>
                                                <input type="number" min="1" class="form-control @error('sn') is-invalid @enderror" name="sn" placeholder="sn" id="" value="{{ $social->sn }}">
                                                @error('sn')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="username" class="form-label">Name:</label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Name" id="" value="{{ $social->name }}">
                                                @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="url" class="form-label">URL:</label>
                                                <input type="text" class="form-control @error('url') is-invalid @enderror" name="url" placeholder="URL" id="" value="{{ $social->url }}">
                                                @error('url')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="icon" class="form-label">Icon:</label>
                                                <input type="file" data-default-file="{{ $social->icon && file_exists(public_path($social->icon)) ? url($social->icon) : url('default/logo.svg') }}" class="dropify form-control @error('icon') is-invalid @enderror" name="icon" id="icon">
                                                <p class="textTransform">Image Size Less than 5MB and Image Type must be jpeg,jpg,png.</p>
                                                @error('icon')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <button class="submit btn btn-primary" type="submit">Submit</button>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- CONTAINER CLOSED -->
@endsection
@push('scripts')
    
@endpush