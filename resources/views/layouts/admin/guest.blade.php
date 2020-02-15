<!DOCTYPE html>
<html lang="en">
<head>
    <title>Seena Admin | @yield('title')</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
          integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    {!! Html::style('assets/css/auth/account_completion.css') !!}
    {!! Html::style('assets/css/common/basics.css') !!}
    {!! Html::style('assets/css/admin/theme.css') !!}
    {!! Html::style('assets/css/admin/media.css') !!}
    {!! Html::style('assets/css/common/sweetalert.css') !!}
    {!! Html::style('assets/css/common/chosen.min.css') !!}


    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link rel="shortcut icon" type="image/ico" href="{{ asset('assets/images/favicon.png') }}"/>
</head>
<body>

@yield('content')

{!! Html::script('assets/js/admin/jquery.min.js') !!}
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
{!! Html::script('assets/js/auth/account_completion.js') !!}
@include('flashy::message')

<script>
    URL = "{{ url('/') }}";
    token = "{{ csrf_token() }}";

    $('.error-message').delay(3000).fadeOut(500);
    $('.success-message').delay(3000).fadeOut(500);

</script>
<script src="{{ asset('assets/js/admin/chosen.jquery.min.js') }}"></script>

@yield('scripts')
@yield('extrascripts')
@stack('more-scripts')
{!! Html::script('assets/js/common/sweetalert.min.js') !!}
</body>
</html>
