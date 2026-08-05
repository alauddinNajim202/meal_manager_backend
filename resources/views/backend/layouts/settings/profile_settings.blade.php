@extends('backend.app')

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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Settings</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </div>
            </div>

            <div class="row" id="user-profile">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title mb-0">Show</h3>
                            <div class="card-options">
                                <a href="javascript:window.history.back()" class="btn btn-sm btn-primary">Back</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-lg-12 col-md-12 col-xl-6">
                                    <div class="d-flex flex-wrap align-items-center">
                                        <div class="profile-img-main rounded"
                                            style="width: 125px; height: 125px; overflow: hidden;">
                                            <img src="{{ Auth::user()->avatar ? asset(Auth::user()->avatar) : asset('default/profile.jpg') }}"
                                                alt="Profile Picture" class="m-0 p-1"
                                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                        </div>
                                        <div class="ms-4">
                                            <h4>{{ Auth::user()->name ?? 'N/A' }}</h4>
                                            <h4>{{ Auth::user()->email ?? 'N/A' }}</h4>
                                            <a href="#" class="btn btn-primary btn-sm" id="uploadImageBtn">
                                                <i class="fa fa-rss"></i> Update Profile
                                            </a>
                                            <input type="file" name="profile_picture" id="profile_picture_input"
                                                style="display: none;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-top">
                            <div class="wideget-user-tab">
                                <div class="tab-menu-heading">
                                    <div class="tabs-menu1">
                                        <ul class="nav">
                                            <li><a href="#editProfile" class="active show" data-bs-toggle="tab">Edit Profile</a>
                                            </li>
                                            <li><a href="#updatePassword" data-bs-toggle="tab">Update Password</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane active show" id="editProfile">
                            <div class="card">
                                <div class="card-body border-0">
                                    <form class="form form-horizontal" method="post" action="{{ route('admin.setting.profile.update') }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="row mb-4">
                                            <div class="form-group">
                                                <label for="username" class="form-label">Name:</label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                    name="name" placeholder="Name" id="username"
                                                    value="{{ Auth::user()->name ?? 'N/A' }}">
                                                @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="firstname" class="form-label">Email:</label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                    name="email" id="firstname" placeholder="Email"
                                                    value="{{ Auth::user()->email ?? 'N/A' }}">
                                                @error('email')
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

                        <div class="tab-pane" id="updatePassword">
                            <div class="card">
                                <div class="card-body border-0">
                                    <form class="form form-horizontal" method="post" action="{{ route('admin.setting.profile.update.Password') }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="row mb-4">
                                            <div class="form-group">
                                                <label for="old_password" class="form-label">Current Password:</label>
                                                <input type="password"
                                                    class="form-control @error('old_password') is-invalid @enderror"
                                                    name="old_password" placeholder="Current Password" id="old_password">
                                                @error('old_password')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="password" class="form-label">New Password:</label>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                    name="password" id="password" placeholder="New Password">
                                                @error('password')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="password_confirmation" class="form-label">Confirm Password:</label>
                                                <input type="password"
                                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                                    name="password_confirmation" id="password_confirmation"
                                                    placeholder="Confirm Password">
                                                @error('password_confirmation')
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
<script>
$(document).ready(function () {

    // SweetAlert2 Toast
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    // Trigger file input
    $('#uploadImageBtn').on('click', function (e) {
        e.preventDefault();
        $('#profile_picture_input').click();
    });

    // Handle image change (preview + upload)
    $('#profile_picture_input').on('change', function () {

        const file = this.files[0];
        if (!file) return; // user cancelled

        // 🔹 Preview image
        const reader = new FileReader();
        reader.onload = function (e) {
            $('.profile-img-main img').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);

        // 🔹 Upload image
        const formData = new FormData();
        formData.append('profile_picture', file);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: "{{ route('admin.update.profile.picture') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,

            success: function (response) {
                if (response.success) {
                    $('.profile-img-main img').attr('src', response.image_url);
                    $('.profile-img-change').attr('src', response.image_url);

                    Toast.fire({
                        icon: 'success',
                        title: 'Profile picture updated'
                    });
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: response.message || 'Update failed'
                    });
                }
            },

            error: function (xhr) {
    if (xhr.status === 422) {
        Toast.fire({
            icon: 'error',
            title: xhr.responseJSON.message || 'Validation error'
        });
    } else {
        Toast.fire({
            icon: 'error',
            title: 'Upload failed'
        });
    }
}

        });
    });
});
</script>

@endpush
