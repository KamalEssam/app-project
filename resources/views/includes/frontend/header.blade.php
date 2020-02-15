<!--====== USEFULL META ======-->
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description"
      content="It is a community where both patients and doctors can meet, communicate and trust one another."/>
<meta name="keywords" content="Clinics, clinic, clinic system, rklinic, doctors, doctor, rkanjel"/>

<!--====== TITLE TAG ======-->
<title>Seena | Homepage</title>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-125116959-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'UA-125116959-1');
</script>

<link href="https://fonts.googleapis.com/css?family=Sunflower:300" rel="stylesheet">
<!--====== FAVICON ICON =======-->
<link rel="shortcut icon" type="image/ico" href="{{ asset('assets/images/favicon.png') }}"/>

<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js"
        integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+"
        crossorigin="anonymous"></script>


<!--====== STYLESHEETS ======-->
{!! Html::style('/assets/css/frontend/normalize.css') !!}
{!! Html::style('/assets/css/frontend/bootstrap.min.css') !!}
{!! Html::style('/assets/css/frontend/font-awesome.min.css') !!}
{!! Html::style('/assets/css/frontend/basics.css') !!}
{!! Html::style('/assets/css/frontend/iziToast.min.css') !!}


<!--====== MAIN STYLESHEETS ======-->
{!! Html::style('/assets/css/frontend/style.css') !!}
{!! Html::style('/assets/css/frontend/responsive.css') !!}
    <!--[if lt IE 9]>
<script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
@yield('styles')
