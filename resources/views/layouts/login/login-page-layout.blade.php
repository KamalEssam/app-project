<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | @yield('title')</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
    <meta http-equiv="X-UA-Compatible" content="IE=edge" >
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" >

    {!! Html::style('assets/css/common/font-awesome.min.css') !!}
    {!! Html::style('assets/css/admin/login.css') !!}
    {!! Html::style('assets/css/admin/form.css') !!}
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    {!! Html::style('assets/css/auth/account_completion.css') !!}
</head>
<body>
<div class="dev-page dev-page-login dev-page-login-v2">
    @yield('content')
</div>
{!! Html::script('assets/js/admin/jquery.min.js') !!}
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
{!! Html::script('assets/js/auth/account_completion.js') !!}
@include('flashy::message')

<script>
    $('.error-message').delay(3000).fadeOut(500);
    $('.success-message').delay(3000).fadeOut(500);
</script>
</body>
</html>
