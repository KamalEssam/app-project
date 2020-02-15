<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.frontend.header')
</head>
<body>
<div class="limiter">
    <div class="container-login100">
        <div class="login100-more" style="background-image: url('/assets/images/auth/background.png');"></div>
        @yield('content')
    </div>
</div>
{!! Html::script('assets/js/admin/jquery.min.js') !!}
<script>
    // Show Password Eye
    $(".glyphicon-eye-open").on("click", function () {
        $(this).toggleClass("glyphicon-eye-close");
        var type = $("#password").attr("type");
        if (type == "text") {
            $("#password").prop('type', 'password');
        } else {
            $("#password").prop('type', 'text');
        }
    });

</script>
</body>
</html>


