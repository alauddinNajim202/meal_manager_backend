<script>
@if(session()->has('t-success'))
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: "{{ session('t-success') }}",
        timer: 2000,
        showConfirmButton: false
    });
@endif

@if(session()->has('t-error'))
    Swal.fire({
        icon: 'error',
        title: 'Oops!',
        text: "{{ session('t-error') }}",
    });
@endif
</script>
