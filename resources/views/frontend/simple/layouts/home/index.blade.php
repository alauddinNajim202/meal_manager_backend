@extends('frontend.simple.app', ['title' => 'landing page'])
@section('content')
<!-- CONTAINER OPEN -->
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="text-center">
        <div class="container-login100 mt-4">
            <div class="wrap-login100 p-5 bg-white rounded-lg shadow" style="max-width: 400px;">
                <a href="{{ url('/') }}" class="text-center">
                    <img src="{{ asset($settings->logo ?? 'default/logo.svg') }}" class="" alt="" style="width: 200px; height: 70px">
                </a>
                <div class="card-body text-center">
                    <h4 class="mb-4 font-weight-bold">Welcome Back!</h4>
                    <p class="text-muted mb-4">Log in to access your dashboard</p>
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg btn-block">{{ Auth::check() ? 'Dashboard' : 'Login Now' }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- CONTAINER CLOSED -->
@endsection