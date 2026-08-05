<?php
$path = public_path('uploads/plugins/');
$files = scandir($path);
$extensions = ['zip'];
foreach ($files as $file) {
    $file_name = pathinfo($file, PATHINFO_FILENAME);
    $file_extension = pathinfo($file, PATHINFO_EXTENSION);
    if (in_array($file_extension, $extensions)) {
        $plugins[$file]['name'] = $file;
        $plugins[$file]['url'] = $path . $file;
        $plugins[$file]['extension'] = $file_extension;
        $plugins[$file]['size_md'] = round(filesize($path . $file) / (1024 * 1024), 2) . ' MB';
        $plugins[$file]['modified'] = date("F d Y H:i:s.", filemtime($path . $file));

        $zip = new ZipArchive;
        if ($zip->open($path . $file) === true) {
            $filesInZip = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                $filesInZip[] = $stat['name'];
            }

            foreach ($filesInZip as $fileInZip) {
                if (strpos($fileInZip, 'logo.png') !== false) {
                    $plugins[$file]['icon'] = 'data:image/png;base64,' . base64_encode($zip->getFromName($fileInZip));
                    break;
                }
            }

            foreach ($filesInZip as $fileInZip) {
                if (strpos($fileInZip, 'info.json') !== false) {
                    $pluginInfoJson = $zip->getFromName($fileInZip);
                    $pluginInfo = json_decode($pluginInfoJson, true, 512, JSON_THROW_ON_ERROR);
                    if ($pluginInfo !== null) {
                        $plugins[$file]['app'] = $pluginInfo['app'] ?? null;
                        $plugins[$file]['version'] = $pluginInfo['version'] ?? null;
                    }

                    break;
                }
            }

            $zip->close();
        }

        if (file_exists(base_path('Plugins/' . $plugins[$file]['app'] . 'v' . $plugins[$file]['version'] . '/'))) {
            $plugins[$file]['installed'] = true;
        }
    }
}
?>

@extends('backend.app', ['title' => 'Plugin'])

@push('styles')

@endpush


@section('content')
<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

        <!-- CONTAINER -->
        <div class="main-container container-fluid">


            <!-- PAGE-HEADER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Plugin</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url("admin/dashboard") }}"><i class="fe fe-home me-2 fs-14"></i>Home</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Plugin</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Index</li>
                    </ol>
                </div>
            </div>
            <!-- PAGE-HEADER END -->

            <div class="row" id="user-profile">
                <div class="col-lg-12">
                    <div class="card post-sales-main">
                        <div class="card-body border-0">
                            <div class="form-group">
                                <input type="file" class="dropify form-control" name="plugins[]" id="plugins" multiple />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROW-4 -->
            <div class="row">
                <div class="col-12 col-sm-12">
                    <div class="card product-sales-main">
                        <div class="card-header border-bottom">
                            <h3 class="card-title mb-0">Plugins</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom" id="example1">
                                    <thead>
                                        <tr>
                                            <th class="border-bottom-0">ID</th>
                                            <th class="border-bottom-0">Icon</th>
                                            <th class="border-bottom-0">Name</th>
                                            <th class="border-bottom-0">Size</th>
                                            <th class="border-bottom-0">Extension</th>
                                            <th class="border-bottom-0">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1; @endphp
                                        @foreach ($plugins as $key => $plugin)
                                        <tr class="@if(isset($plugin['installed']) && $plugin['installed'] === true) table-success @endif">
                                            <td>{{ $i++ }}</td>
                                            <td><img src="{{ $plugin['icon'] ?? '' }}" alt="icon" width="50" height="50"></td>
                                            <td>{{ $plugin['app'] ?? '' }}v{{ $plugin['version'] ?? '' }}</td>
                                            <td>{{ $plugin['size_md'] ?? '' }}</td>
                                            <td>{{ $plugin['extension'] ?? '' }}</td>
                                            <td>
                                                <a href="/plugins/{{ $plugin['app'].'v'.$plugin['version'] }}/index" class="btn btn-info"><i class="fa-solid fa-eye"></i></a>
                                                @if(isset($plugin['installed']) && $plugin['installed'] === true)
                                                <a href="{{ Route('plugins.uninstall', base64_encode($plugin['app'].'v'.$plugin['version'])) }}" class="btn btn-primary"><i class="fa-solid fa-minus"></i></a>
                                                @else
                                                <a href="{{ Route('plugins.install', base64_encode($plugin['name'])) }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i></a>
                                                @endif
                                                <a href="{{ Route('plugins.delete', base64_encode($plugin['name'])) }}" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a>
                                                <a href="{{ Route('plugins.download', base64_encode($plugin['name'])) }}" class="btn btn-success"><i class="fa-solid fa-download"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div><!-- COL END -->
            </div>
            <!-- ROW-4 END -->


        </div>
    </div>
</div>
<!-- CONTAINER CLOSED -->
@endsection

@push('scripts')
<script>
    $('#plugins').change(function() {
        let data = new FormData();
        let files = $(this)[0].files;

        for (let i = 0; i < files.length; i++) {
            data.append('plugins[]', files[i]);
        }

        NProgress.start();

        $.ajax({
            url: `{{ route('plugins.upload') }}`,
            type: 'POST',
            data: data,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(resp) {
                NProgress.done();
                $('.dropify-clear').click();
                toastr.success(resp.message);
                setTimeout(function() {
                    location.reload();
                }, 2000);
            },
            error: function(xhr) {
                NProgress.done();
                let errorMsg = xhr.responseJSON?.message || 'Upload failed';
                toastr.error(errorMsg);
            }
        });
    });
</script>
@endpush
