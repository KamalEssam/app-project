<!doctype html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-123744117-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-123744117-1');
</script>

@include('includes.frontend.header')

<body class="home-one" data-spy="scroll" data-target=".mainmenu-area" data-offset="90">

<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
   your browser</a> to improve your experience.</p>
<![endif]-->

<!--- PRELOADER -->
{{--<div class="preeloader">
      --}}{{--<img style="width: 300px; height: 300px" src="https://cdn.dribbble.com/users/22/screenshots/3376812/kapture_2017-03-20_at_15.27.01.gif">--}}{{--
      @include('includes.frontend.pre-loader')
</div>--}}
<div id="app">
   <router-view></router-view>
</div>

<script src="/assets/js/frontend/app.js"></script>
@include('includes.frontend.scripts')


</body>
</html>
