<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.news.update', $news->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <h5 class="mb-4">Edit News</h5>

            <!-- News Title & Type -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="news_title" class="form-label fw-semibold d-block">News Title</label>
                    <input type="text" id="news_title" class="form-control" name="news_title"
                        value="{{ old('news_title', $news->title) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="type" class="form-label fw-semibold d-block">Type</label>
                    <input type="text" id="type" class="form-control" name="type"
                        value="{{ old('type', $news->type) }}">
                </div>
            </div>

            <!-- Short Description -->
            <div class="mb-3">
                <label for="short_description" class="form-label fw-semibold d-block">Short Description</label>
                <textarea id="short_description" class="form-control " name="short_description">{{ old('short_description', $news->short_description) }}</textarea>
            </div>

            <!-- Thumbnail -->
            <div class="mb-3">
                <label for="thumbnail" class="form-label fw-semibold d-block">Thumbnail</label>
                <input type="file" id="thumbnail" class="form-control" name="thumbnail">
                @if ($news->thumbnail)
                    <label class="form-label fw-semibold d-block mt-3">Existing Thumbnail</label>
                    <img src="{{ asset($news->thumbnail) }}" width="120" class="mt-2">
                @endif
            </div>

            <!-- News Details -->
            <h5 class="mt-4 mb-3 text-start">News Details</h5>
            <div id="news-details-wrapper">

                <div class="text-end mb-3">
                    <button type="button" class="btn" id="add-detail"
                        style="background: linear-gradient(90deg, #4e73df, #1cc88a); color:#fff; font-weight:600; border-radius:6px; padding:0.5rem 1.2rem; box-shadow:0 2px 6px rgba(0,0,0,0.15); transition:all 0.2s ease;">
                        <i class="fa-solid fa-plus me-2"></i> Add News Detail
                    </button>
                </div>

                @foreach ($news->details as $index => $detail)
                    <div class="news-detail-row border p-3 mb-3 position-relative">

                        <input type="hidden" name="details[{{ $index }}][id]" value="{{ $detail->id }}">

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Detail</h6>
                            <button type="button" class="btn btn-danger btn-sm remove-detail">Remove</button>
                        </div>

                        <!-- Detail Title -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-block">Detail Title</label>
                            <input type="text" name="details[{{ $index }}][title]" class="form-control"
                                value="{{ $detail->title }}">
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-block">Description</label>
                            <textarea name="details[{{ $index }}][description]" class="form-control summernote">{{ $detail->description }}</textarea>
                        </div>

                        <!-- Add New Images -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-block">Add Images</label>
                            <div class="images-wrapper">
                                <div class="input-group mb-2">
                                    <input type="file" name="details[{{ $index }}][images][]"
                                        class="form-control">
                                    <button type="button" class="btn btn-success add-image">+</button>
                                </div>
                            </div>
                        </div>

                        <!-- Existing Images -->
                        @if ($detail->images->count() > 0)
                            <div class="mb-3">
                                <label class="form-label fw-semibold d-block">Existing Images</label>
                                <div class="d-flex flex-wrap">
                                    @foreach ($detail->images as $img)
                                        <div class="position-relative me-2 mb-2">
                                            <img src="{{ asset($img->image) }}" width="100">
                                            <button type="button"
                                                class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-existing-image"
                                                data-id="{{ $img->id }}" style="padding:0 5px;">&times;</button>
                                            <input type="hidden" name="existing_images[{{ $detail->id }}][]"
                                                value="{{ $img->id }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>


            <div>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<style>
    .note-editable {
        text-align: left !important;
    }

    .note-editable p {
        text-align: left !important;
    }
</style>

<script>
    let detailIndex = {{ $news->details->count() }};

    // Add new detail row
    $(document).on('click', '#add-detail', function() {
        let html = `
        <div class="news-detail-row border p-3 mb-3 position-relative">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Detail</h6>
                <button type="button" class="btn btn-danger btn-sm remove-detail">Remove</button>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold d-block">Detail Title</label>
                <input type="text" name="details[${detailIndex}][title]" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold d-block">Description</label>
                <textarea name="details[${detailIndex}][description]" class="form-control summernote"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold d-block">Add Images</label>
                <div class="images-wrapper">
                    <div class="input-group mb-2">
                        <input type="file" name="details[${detailIndex}][images][]" class="form-control">
                        <button type="button" class="btn btn-success add-image">+</button>
                    </div>
                </div>
            </div>
        </div>`;
        $('#news-details-wrapper').append(html);

        // Initialize Summernote for the new textarea
        $('#news-details-wrapper').find('textarea.summernote').last().summernote({
            height: 120
        });
        detailIndex++;
    });

    // Remove detail row
    $(document).on('click', '.remove-detail', function() {
        $(this).closest('.news-detail-row').remove();
    });

    // Add new image input
    $(document).on('click', '.add-image', function() {
        let name = $(this).prev('input').attr('name');
        let html = `<div class="input-group mb-2">
                        <input type="file" name="${name}" class="form-control">
                        <button type="button" class="btn btn-danger remove-image">-</button>
                    </div>`;
        $(this).closest('.images-wrapper').append(html);
    });

    // Remove new image input
    $(document).on('click', '.remove-image', function() {
        $(this).closest('.input-group').remove();
    });

    // Remove existing image (mark for deletion)
    $(document).on('click', '.remove-existing-image', function() {
        let container = $(this).closest('div.position-relative');

        let imageId = $(this).data('id');


        $('<input>').attr({
            type: 'hidden',
            name: 'image_id[]',
            value: imageId
        }).appendTo('form');
        container.remove();


        // remove from DOM, backend will handle deletion based on removed hidden input
    });

    // Initialize Summernote for existing textareas
    $(document).ready(function() {
        $('textarea.summernote').summernote({
            height: 120
        });
    });
</script>
