<div class="row" id="user-profile">
    <div class="col-lg-12">
        <div class="card post-sales-main">
            <div class="card-header border-bottom">
                <h3 class="card-title mb-0">Curriculum</h3>
                <div class="card-options">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Add</button>
                </div>
            </div>
            <div class="card-body border-0">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(App\Models\Curriculum::where('course_id', $course->id)->get() as $curricula)
                        <tr>
                            <th>{{ $loop->iteration }}</th>
                            <td>{{ $curricula->title }}</td>
                            <td>{{ $curricula->created_at ? \Carbon\Carbon::parse($curricula->created_at)->format('d M Y') : 'N/A' }}</td>
                            <td>
                                <button 
                                  type="button" 
                                  class="btn btn-sm btn-primary"
                                  data-bs-toggle="modal"
                                  data-bs-target="#exampleModal"
                                  data-id="{{ $curricula->id }}"
                                  data-title="{{ $curricula->title }}"
                                >Edit</button>

                                <a href="{{ route('admin.curriculum.destroy', $curricula->id) }}" class="btn btn-sm btn-danger">Delete</a>
                                
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add Curriculum</h1>
      </div>
      <form id="curriculum-form" method="POST">
        @csrf
        <input type="hidden" name="course_id" value="{{ $course->id }}">

        <div class="modal-body">
          <div class="mb-3">
            <label class="col-form-label">Title:</label>
            <input type="text" class="form-control" id="curriculum-title" name="title" required>
            @error('title')
              <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="curriculum-submit">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const exampleModal = document.getElementById('exampleModal');
const curriculumForm = document.getElementById('curriculum-form');
const modalTitle = document.getElementById('exampleModalLabel');
const inputTitle = document.getElementById('curriculum-title');
const submitBtn = document.getElementById('curriculum-submit');

exampleModal.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget;
  const id = button.getAttribute('data-id');
  const title = button.getAttribute('data-title');

  if (id) {
    // Edit Mode
    modalTitle.textContent = 'Edit Curriculum';
    inputTitle.value = title;
    curriculumForm.action = `/admin/curriculum/update/${id}`;
    submitBtn.textContent = 'Update';
  } else {
    // Add Mode
    modalTitle.textContent = 'Add Curriculum';
    inputTitle.value = '';
    curriculumForm.action = `{{ route('admin.curriculum.store') }}`;
    submitBtn.textContent = 'Save';
  }
});
</script>
