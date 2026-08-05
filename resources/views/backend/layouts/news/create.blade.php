@extends('backend.app', ['title' => 'Create News'])

@section('content')
    <div class="app-content main-content mt-0">
        <div class="side-app">
            <div class="main-container container-fluid">

                <div class="page-header">
                    <div>
                        <h1 class="page-title">Create News</h1>
                    </div>
                    <div class="ms-auto pageheader-btn">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.news.index') }}">News</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create News</h3>
                        <div class="card-options">
                            <a href="javascript:window.history.back()" class="btn btn-sm btn-primary">Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data">
                            @csrf

                            {{-- News Section --}}
                            <div class="d-flex justify-content-between">
                                <div class="mb-3 col-md-6">
                                    <label for="news_title">News Title</label>
                                    <input type="text" class="form-control" name="news_title" id="news_title" required>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="type">Type</label>
                                    <input type="text" class="form-control" name="type" id="type" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="short_description">Short Description</label>
                                <textarea class="form-control " name="short_description"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="thumbnail">Thumbnail</label>
                                <input type="file" class="form-control" name="thumbnail" required>
                            </div>

                            {{-- News Details --}}
                            <div id="news-details-wrapper">

                                <div class="text-end mb-3">
                                    <button type="button" class="btn" id="add-detail"
                                        style="background: linear-gradient(90deg, #4e73df, #1cc88a); color: #fff; font-weight: 600; border-radius: 6px; padding: 0.5rem 1.2rem; box-shadow: 0 2px 6px rgba(0,0,0,0.15); transition: all 0.2s ease;">
                                        <i class="fa-solid fa-plus me-2"></i> Add News Detail
                                    </button>
                                </div>

                                <!-- Initial detail row -->
                                <div class="news-detail-row border p-3 mb-3" data-detail-index="0">
                                    <div class="d-flex justify-content-between mb-2">
                                        <h6>Detail</h6>
                                        <button type="button" class="btn btn-danger btn-sm remove-detail">Remove</button>
                                    </div>

                                    <div class="mb-3">
                                        <label>Detail Title</label>
                                        <input type="text" name="details[0][title]" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Description</label>
                                        <textarea name="details[0][description]" class="form-control summernote" required></textarea>
                                    </div>

                                    <div class="images-wrapper">
                                        <label class="form-label mb-2">Images</label>
                                        <div class="row g-3 images-container">
                                            <div class="col-md-4 col-sm-6 image-row">
                                                <div class="input-group">
                                                    <input type="file" name="details[0][images][]" class="form-control" accept="image/*" required>
                                                    <button type="button" class="btn btn-success add-image">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let detailIndex = 1; // next index to use

        // Add new detail row
        $('#add-detail').on('click', function () {
            const currentIndex = detailIndex;

            let html = `
            <div class="news-detail-row border p-3 mb-3" data-detail-index="${currentIndex}">
                <div class="d-flex justify-content-between mb-2">
                    <h6>Detail</h6>
                    <button type="button" class="btn btn-danger btn-sm remove-detail">Remove</button>
                </div>

                <div class="mb-3">
                    <label>Detail Title</label>
                    <input type="text" name="details[${currentIndex}][title]" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="details[${currentIndex}][description]" class="form-control summernote" required></textarea>
                </div>

                <div class="images-wrapper">
                    <label class="form-label mb-2">Images</label>
                    <div class="row g-3 images-container">
                        <div class="col-md-4 col-sm-6 image-row">
                            <div class="input-group">
                                <input type="file" name="details[${currentIndex}][images][]" class="form-control" accept="image/*" required>
                                <button type="button" class="btn btn-success add-image">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;

            $('#news-details-wrapper').append(html);

            // Optional: initialize summernote on new textarea if needed
            // $('.summernote').summernote(); // uncomment if you initialize manually

            detailIndex++;
        });

        // Remove detail row
        $(document).on('click', '.remove-detail', function () {
            $(this).closest('.news-detail-row').remove();
        });

        // Add new image field
        $(document).on('click', '.add-image', function () {
            const $row = $(this).closest('.news-detail-row');
            const index = $row.data('detail-index');

            const newImageField = `
            <div class="col-md-4 col-sm-6 image-row">
                <div class="input-group">
                    <input type="file" name="details[${index}][images][]" class="form-control" accept="image/*">
                    <button type="button" class="btn btn-danger remove-image">−</button>
                </div>
            </div>`;

            $row.find('.images-container').append(newImageField);
        });

        // Remove image field
        $(document).on('click', '.remove-image', function () {
            $(this).closest('.image-row').remove();
        });
    </script>
@endpush
