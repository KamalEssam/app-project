<!doctype html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--====== USEFULL META ======-->
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!--====== TITLE TAG ======-->
<title>RKlinic</title>


<!--====== FAVICON ICON =======-->
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">

<link rel="shortcut icon" type="image/ico" href="{{ asset('/assets/images/frontend/favicon.png') }}"/>
<link href="https://fonts.googleapis.com/css?family=Sunflower:300" rel="stylesheet">
<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js"
        integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+"
        crossorigin="anonymous"></script>

<style>
    body {
        font-family: 'Sunflower', sans-serif;
    }

    .green{
        color: #43bda7;
    }
    .grey{
        color: #777777;
    }
    .white{
        color: white;
    }
    #header-bk {
        background-image: linear-gradient(to top, #43bda7, #3180c0);
    }
    #header-bk .container{
        padding-bottom: 2%;
    }

    #title {
        text-align: center;
        padding-top: 3%;
    }

    #title a {
        position: relative;
        padding-bottom: 3%;
    }

    #title a:after {
        content: ' ';
        position: absolute;
        display: block;
        top: 100%;
        left: -253%;
        width: 615%;
        margin: 0 2%;
        border: 0.5px solid white;
        border-radius: 4px;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
    }
    #main {
        margin-bottom: 3%;
        margin-top: 5%;
    }

    #welcome-section{
        margin-top: 5%;
    }
    .envelope-img{
        float: right;
        width: 43%;
    }
    /********************************footer***************************/
    footer{
        background: #F3F3F3;
    }
    .footer-social-bookmark ul {
        list-style-type: none;
    }

    .footer-social-bookmark .social-bookmark li a {
        border-radius: 5px;
        font-size: 20px;
        height: 40px;
        margin: 0 5px;
        padding-top: 8px;
        text-align: center;
        width: 40px;
    }

    .footer-copyright {
        padding: 30px 0 15px;
    }

    #social-icons {
        margin-left: 34%;
        padding-top: 3.5%;
    }

    #social-icons .icon {
        font-size: 30px;
        width: 15%;
    }
.store{
    padding-right: 5%;
}
    #store-row{
        margin-left: 35%;
    }
    #mobile-section h1{
        margin-top: 5%;
    }
    #mobile-section p{
        margin-top: -10px;
    }
    #version-section{
        margin-top: 6%;
        width: 60%;
        margin-left: 20%;
    }
</style>
<body>
<header>
    <div class="container-fluid">
        <div id="header-bk">
            <div class="container">
                <div id="title">
                    <a href="http://rklinic.com"><img src="{{ asset('/assets/images/frontend/logo.png') }}"></a>
                </div>
                <div class="row white" id="welcome-section">
                    <div class="col-md-8">
                        <h1>WELCOME !</h1>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s</p>
                    </div>
                    <div class="col-md-4">
                        <img src="{{ asset('/assets/images/envelope.svg') }}" class="envelope-img">
                    </div>
                </div>
                <div class="row" id="store-row">
                    <div class="store">
                        <img src="{{ asset('/assets/images/apple.png') }}">
                    </div>
                    <div>
                        <img src="{{ asset('/assets/images/google.png') }}">
                    </div>

                </div>
            </div>

        </div>
    </div>
</header>
<section id="main">
    <div class="container">
        <div class="row" id="mobile-section">

            <div class="col-md-4 text-center">
                <img src="{{ asset('/assets/images/manage.png') }}">
                <h1 class="green">MANAGE</h1>
                <p class="grey">Check and manage your reservations.</p>
            </div>

            <div class="col-md-4 text-center">
                <img src="{{ asset('/assets/images/check.png') }}">
                <h1 class="green">CHECK</h1>
                <p class="grey">Create alive chat with your patients.</p>
            </div>

            <div class="col-md-4 text-center">
                <img src="{{ asset('/assets/images/chat.png') }}">
                <h1 class="green">CHAT</h1>
                <p class="grey">Check your patients' medical history.</p>
            </div>

        </div>

        <div class="row green" id="version-section">
            <div class="text-center">
                <h4>We will contact you very soon to setup you a version of our mobile app, but until then you can try this version</h4>
            </div>
        </div>

    </div>
</section>

<footer>
    <div class="footer-bottom-area blue-bg">
        <div class="container">
            <div class="row" id="social-icons">
                <div class="icon">
                    <a target="_blank" class="facebook green"
                       href="https://www.facebook.com/rklinic/?modal=admin_todo_tour"><i
                                class="fab fa-facebook-square"></i></a>
                </div>
                <div class="icon">
                    <a target="_blank" class="instagram green" href="https://www.instagram.com/rklinic_system/">
                        <img src="{{ asset('/assets/images/insta.png') }}">
                    </a>
                </div>
                <div class="icon">
                    <a target="_blank" class="youtube green"
                       href="https://www.youtube.com/channel/UCYaO_gtdnzqn0KEKTfQLQ4w?view_as=subscriber"><i
                                class="fab fa-youtube"></i></a>
                </div>
                <div class="icon">
                    <a target="_blank" class="twitter green" href="https://twitter.com/RklinicS"><i
                                class="fab fab fa-twitter"></i></a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="footer-copyright text-center">
                        <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                            Copyright &copy;All rights reserved | RK Anjel <a href="http://rkanjel.com"
                                                                              target="_blank"><img
                                        src="{{ asset('/assets/images/logo.png') }}" alt="Rk Anjel"
                                        style="width: 24px;height: auto;"></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</footer>

<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</body>

</html>
