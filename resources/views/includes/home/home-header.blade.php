<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>Home | @yield('title')</title>


{!! Html::style('assets/css/common/bootstrap.min.css') !!}
{!! Html::style('assets/css/common/font-awesome.min.css') !!}

{!! Html::style('assets/css/common/chosen.min.css') !!}

{!! Html::style('assets/css/common/basics.css') !!}

{!! Html::style('assets/css/common/sweetalert.css') !!}

{!! Html::style('assets/css/common/jquery-ui.min.css') !!}

{!! Html::style('assets/css/admin/theme.css') !!}

{!! Html::style('assets/css/home/main.css') !!}

<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
<link rel="shortcut icon" type="image/ico" href="{{ asset('assets/images/favicon.png') }}" style="width: 20px"/>
<link rel="icon" type="image/ico" href="{{ asset('assets/images/favicon.png') }}"/>

@yield('styles')

@yield('extrascripts')



    