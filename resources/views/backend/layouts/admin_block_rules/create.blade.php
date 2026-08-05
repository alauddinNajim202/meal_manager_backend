@extends('backend.app', ['title' => 'Create Admin Block Rule'])

@section('content')

<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

        <!-- CONTAINER -->
        <div class="main-container container-fluid">

            <div class="page-header">
                <div>
                    <h1 class="page-title">Create Admin Block Rule</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url("admin/dashboard") }}"><i class="fe fe-home me-2 fs-14"></i>Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.admin-block-rules.index') }}">Block Rules</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </div>
            </div>

            <div class="row" id="user-profile">
                <div class="col-lg-12">

                    <div class="tab-content">
                        <div class="tab-pane active show" id="editProfile">
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h3 class="card-title mb-0">Block Rule Details</h3>
                                    <div class="card-options">
                                        <a href="javascript:window.history.back()" class="btn btn-sm btn-primary">Back</a>
                                    </div>
                                </div>
                                <div class="card-body border-0">
                                    <form class="form form-horizontal" method="post" action="{{ route('admin.admin-block-rules.store') }}">
                                        @csrf
                                        <div class="row mb-4">

                                            <div class="form-group col-md-12">
                                                <label for="pattern" class="form-label">Pattern:</label>
                                                <input type="text" class="form-control @error('pattern') is-invalid @enderror" name="pattern" placeholder="Enter Pattern" id="pattern" value="{{ old('pattern') }}">
                                                @error('pattern')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-12">
                                                <label for="reason" class="form-label">Reason:</label>
                                                <input type="text" class="form-control @error('reason') is-invalid @enderror" name="reason" placeholder="Enter reason for blocking" id="reason" value="{{ old('reason') }}">
                                                @error('reason')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-12 mt-3">
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
