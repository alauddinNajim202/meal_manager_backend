<form action="/file-upload" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file">
    <button>Upload</button>
</form>

<ul>
@foreach($files as $file)
    <li>{{ basename($file) }}</li>
@endforeach
</ul>
