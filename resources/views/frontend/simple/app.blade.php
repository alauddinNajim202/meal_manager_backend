@php
$systemSetting = App\Models\Setting::first();
@endphp
<!doctype html>
<html lang="en" dir="ltr">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>{{ $title ?? '' }} | {{$systemSetting->system_name ?? env('APP_NAME')}}</title>
  <meta name="description" content="{!! strip_tags($description ?? $systemSetting->description ?? '') !!}">
  <meta name="keywords" content="{!! strip_tags($keywords ?? $systemSetting->keywords ?? '') !!}">
  <meta name="author" content="{{$author ?? $systemSetting->system_name ?? env('APP_NAME')}}">
  <link rel="icon" type="image/png" href="{{ !empty($icon) ? asset($icon) : asset($systemSetting->favicon ?? env('APP_LOGO')) }}" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @include('frontend.simple.partials.style')

</head>

<body class="ltr app horizontal landing-page">
  @include('frontend.simple.partials.switcher')

  <!-- <a href="javascript:void(0);" class="buy-now">Buy Now</a> -->

  @include('frontend.simple.partials.loader')

  <div class="page">
    <div class="page-main">
      @yield('content')
    </div>
  </div>

  @include('frontend.simple.partials.script')
</body>

</html>