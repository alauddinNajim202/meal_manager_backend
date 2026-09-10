@extends('backend.app', ['title' => 'Send Push Notification'])

@section('content')

<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

        <!-- CONTAINER -->
        <div class="main-container container-fluid">

            <div class="page-header">
                <div>
                    <h1 class="page-title">Push Notification</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}"><i class="fe fe-home me-2 fs-14"></i>Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Push Notification</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">

                    <div class="tab-content">
                        <div class="tab-pane active show" id="pushNotification">
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h3 class="card-title mb-0">Send Notification</h3>
                                </div>
                                <div class="card-body border-0">
                                    <form class="form form-horizontal" method="post" action="{{ route('admin.push_notification.send') }}">
                                        @csrf
                                        <div class="row mb-4">

                                            <div class="form-group col-md-12">
                                                <label for="title" class="form-label">Notification Title <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" placeholder="Enter title" id="title" value="{{ old('title') }}" required>
                                                @error('title')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-12">
                                                <label for="message" class="form-label">Message Content <span class="text-danger">*</span></label>
                                                <textarea class="form-control @error('message') is-invalid @enderror" name="message" id="message" rows="4" placeholder="Enter message" required>{{ old('message') }}</textarea>
                                                @error('message')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-12">
                                                <label for="user_ids" class="form-label">Select Users <span class="text-danger">*</span></label>
                                                <select class="form-control select2 @error('user_ids') is-invalid @enderror" name="user_ids[]" id="user_ids" multiple required>
                                                    <option value="all">Send to All Users</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Select "Send to All Users" to broadcast to everyone, or select specific users.</small>
                                                @error('user_ids')
                                                <span class="text-danger d-block">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group mt-3">
                                                <button class="submit btn btn-primary" type="submit"><i class="fe fe-send me-1"></i> Send Notification</button>
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#user_ids').select2({
            placeholder: "Select users",
            allowClear: true
        });

        // If "Send to All Users" is selected, clear other options
        $('#user_ids').on('select2:select', function (e) {
            var data = e.params.data;
            if (data.id === 'all') {
                $('#user_ids').val(['all']).trigger('change');
            } else {
                var selected = $('#user_ids').val();
                if (selected.includes('all')) {
                    selected = selected.filter(item => item !== 'all');
                    $('#user_ids').val(selected).trigger('change');
                }
            }
        });
    });
</script>  
@endpush
