<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{--<meta name="csrf-token" content="{{ csrf_token() }}">--}}

    <title>Create password</title>

    {!! Html::style('assets/css/common/bootstrap.min.css') !!}
    {!! Html::style('assets/css/common/font-awesome.min.css') !!}
    <link rel="shortcut icon" type="image/ico" href="../assets/images/frontend/slider/fav.png" style="width: 20px"/>
    <link rel="icon" type="image/ico" href="../assets/images/frontend/slider/fav.png"/>
</head>
<body>
<div class="container" style="margin-top: 20%">
    <div class="row ">
        <div class="alert alert-{{ ($alert) ?? 'info'  }}" style="margin-left: 10%;margin-right: 10%">
            <h4 class="text-center">
                {{ ($msg) ?? 'welcome to rKlinic' }}

            </h4>
        </div>
    </div>
    <div class="row mt-10">
        @if(isset($alert) && $alert == 'success')
            <div class="col-md-6">
                <!-- apple store button -->
                <a href="https://itunes.apple.com/us/app/rklinic/id1437051698"
                   style="box-shadow: 0px 24px 11px #868686;}">
                    <img src="{{ asset('assets/images/app-store.png') }}">
                </a>
            </div>
            <div class="col-md-6">
                <!-- android button -->
                <a href="https://play.google.com/store/apps/details?id=com.rklinic&hl=en"
                   style="box-shadow: 0px 24px 11px #868686;}">
                    <img src="{{ asset('assets/images/play-store.png') }}">
                </a>
            </div>
        @endif
    </div>
</div>
</body>
</html>