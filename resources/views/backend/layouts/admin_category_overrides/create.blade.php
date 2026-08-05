@extends('backend.app', ['title' => 'Create Category Override'])

@section('content')

<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

        <!-- CONTAINER -->
        <div class="main-container container-fluid">

            <div class="page-header">
                <div>
                    <h1 class="page-title">Create Category Override</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url("admin/dashboard") }}"><i class="fe fe-home me-2 fs-14"></i>Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.admin-category-overrides.index') }}">Category Overrides</a></li>
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
                                    <h3 class="card-title mb-0">Override Details</h3>
                                    <div class="card-options">
                                        <a href="javascript:window.history.back()" class="btn btn-sm btn-primary">Back</a>
                                    </div>
                                </div>
                                <div class="card-body border-0">
                                    <form class="form form-horizontal" method="post" action="{{ route('admin.admin-category-overrides.store') }}">
                                        @csrf
                                        <div class="row mb-4">

                                            <div class="form-group col-md-12">
                                                <label for="question_pattern" class="form-label">Question Pattern:</label>
                                                <input type="text" class="form-control @error('question_pattern') is-invalid @enderror" name="question_pattern" placeholder="Enter Question Pattern" id="question_pattern" value="{{ old('question_pattern') }}">
                                                @error('question_pattern')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-12">
                                                <label for="force_category" class="form-label">Force Category:</label>
                                                <input type="text" class="form-control @error('force_category') is-invalid @enderror" name="force_category" placeholder="Enter Category to Force" id="force_category" value="{{ old('force_category') }}">
                                                @error('force_category')
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
