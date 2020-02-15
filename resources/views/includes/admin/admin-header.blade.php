<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>Seena Admin area | @yield('title')</title>

{!! Html::style('assets/css/common/bootstrap.min.css') !!}
{!! Html::style('assets/css/admin/bootstrap-tour.css') !!}


<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js"
        integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+"
        crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.5.6/tinymce.min.js"></script>

<script src="{{ asset('assets/js/admin/jquery_1.11.1.min.js') }}"></script>
<script src="{{ asset('assets/js/admin/jquery.tinymce.min.js') }}"></script>
<script>
    tinymce.init({
        selector: '#mytextarea',
    });

    tinymce.init({
        selector: '#ar_condition',
    });

    tinymce.init({
        selector: '#en_condition',
    });
</script>

{!! Html::style('assets/css/admin/parsley.css') !!}
{!! Html::style('https://cdnjs.cloudflare.com/ajax/libs/izimodal/1.5.1/css/iziModal.min.css') !!}
{!! Html::style('assets/css/admin/bootstrap-material-datetimepicker.css') !!}
{!! Html::style('assets/css/common/jquery-ui.custom.min.css') !!}
{!! Html::style('assets/css/common/chosen.min.css') !!}


{!! Html::style('assets/css/common/sweetalert.css') !!}

<!-- ace styles -->

{!! Html::style('assets/css/admin/ace.min.css') !!}
{!! Html::style('assets/css/admin/ace-rtl.min.css') !!}
{!! Html::style('assets/css/admin/dashboard.css') !!}
{!! Html::style('assets/css/admin/dashboard-widgets.css') !!}
{!! Html::style('assets/css/admin/ace-skins.min.css') !!}

<!--[if lte IE 9]>
<link rel="stylesheet" href="assets/css/ace-ie.min.css"/>
<![endif]-->

<link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.2.0/css/iziToast.min.css">

{!! Html::style('assets/css/common/basics.css') !!}
{!! Html::style('assets/css/admin/theme.css') !!}
{!! Html::style('assets/css/admin/media.css') !!}

@if(app()->getLocale() == 'ar')
    {!! Html::style('assets/css/admin/main-rtl.css') !!}
    <link href=“https://fonts.googleapis.com/css?family=Tajawal:400,500” rel=“stylesheet”>
@else
    {!! Html::style('assets/css/admin/main.css') !!}
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
@endif

<link href="https://fonts.googleapis.com/css?family=Boogaloo|Bungee" rel="stylesheet">
<link rel="shortcut icon" type="image/ico" href="{{ asset('assets/images/favicon.png') }}"/>
@yield('styles')
