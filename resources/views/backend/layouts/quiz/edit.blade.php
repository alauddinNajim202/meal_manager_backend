@extends('backend.app', ['title' => 'Update Quiz'])

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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Quiz</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Update</li>
                    </ol>
                </div>
            </div>

            <div class="row" id="user-profile">
                <div class="col-lg-12">
                    <div class="card post-sales-main">
                        <div class="card-header border-bottom">
                            <h3 class="card-title mb-0">Update</h3>
                            <div class="card-options">
                                <a href="javascript:window.history.back()" class="btn btn-sm btn-primary">Back</a>
                            </div>
                        </div>
                        <div class="card-body border-0">
                            <form class="form form-horizontal" method="POST" action="{{ route($route . '.update', $quiz->id) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row mb-4">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="question" class="form-label">Question:</label>
                                                <textarea class="form-control @error('question') is-invalid @enderror" name="question" placeholder="Enter here question" id="question">{{ $quiz->question ?? '' }}</textarea>
                                                @error('question')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="subcategory_id" class="form-label">Sub Category:</label>
                                                <select class="form-control @error('subcategory_id') is-invalid @enderror" name="subcategory_id" id="subcategory_id">
                                                    <option value="">Select a Sub Category</option>
                                                    @if(!empty($subcategories) && $subcategories->count() > 0)
                                                    @foreach($subcategories as $subcategory)
                                                    <option value="{{ $subcategory->id }}" {{ $quiz->subcategory_id == $subcategory->id ? 'selected' : '' }}>{{ $subcategory->name }}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                                @error('subcategory_id')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="answer" class="form-label">Answer:</label>
                                                <input type="text" class="form-control @error('answer') is-invalid @enderror" name="answer" placeholder="Enter here answer" id="answer" value="{{ $quiz->answer ?? '' }}" readonly>
                                                @error('answer')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <div id="optionContainer"></div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div id="image_load"></div>
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
<!-- CONTAINER CLOSED -->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('optionContainer');
    const answerField = document.getElementById('answer');

    // 🔹 Function to create a new option row
    function createOptionRow(index) {
        const row = document.createElement('div');
        row.classList.add('form-group');

        row.innerHTML = `
            <div class="input-group mb-2">
                <label class="input-group-text" style="cursor: pointer;">Option ${index}</label>
                <input type="text" class="form-control" name="option[]" placeholder="Enter option">
                <button type="button" class="btn btn-warning answerBtn" style="cursor: pointer;">Answer</button>
                <button type="button" class="btn btn-primary addOption"><i class="fa-solid fa-plus"></i></button>
                <button type="button" class="btn btn-danger removeOption"><i class="fa-solid fa-minus"></i></button>
            </div>
        `;
        return row;
    }

    // 🔹 Update label names (Option 1, 2, 3, ...)
    function updateLabels() {
        const labels = container.querySelectorAll('.input-group-text');
        labels.forEach((label, i) => {
            label.textContent = `Option ${i + 1}`;
        });
    }

    // 🔹 Add first option row
    const existingOptions = JSON.parse(@json($quiz->options ?? []));
    if (existingOptions.length > 0) {
        existingOptions.forEach((option, index) => {
            const row = createOptionRow(index + 1);
            row.querySelector('input[name="option[]"]').value = option;
            container.appendChild(row);
        });
        updateLabels();
    }
    
    // 🔹 Handle clicks inside the container
    container.addEventListener('click', (e) => {
        const addBtn = e.target.closest('.addOption');
        const removeBtn = e.target.closest('.removeOption');
        const answerBtn = e.target.closest('.answerBtn');

        // ➕ Add new option
        if (addBtn) {
            const rows = container.querySelectorAll('.form-group');
            container.appendChild(createOptionRow(rows.length + 1));
            updateLabels();
        }

        // ➖ Remove option
        if (removeBtn) {
            const allRows = container.querySelectorAll('.form-group');
            if (allRows.length > 1) {
                removeBtn.closest('.form-group').remove();
                updateLabels();
            }
        }

        // ✅ Set Answer
        if (answerBtn) {
            const input = answerBtn.closest('.input-group').querySelector('input[name="option[]"]');
            if (input.value.trim() !== '') {
                answerField.value = input.value.trim();
            } else {
                alert('Please enter a value for this option before selecting it as the answer.');
            }
        }
    });
});
</script>
@endpush