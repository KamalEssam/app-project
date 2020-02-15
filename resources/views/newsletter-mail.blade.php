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

    #header-bk {
        background-image: linear-gradient(to top, #43bda7, #3180c0);
    }

    #title {
        text-align: center;
        padding-top: 1%;
    }

    #title a {
        position: relative;
        padding-bottom: 2%;
    }

    #title a:after {
        content: ' ';
        position: absolute;
        display: block;
        top: 100%;
        left: -100%;
        width: 300%;
        margin: 0 2%;
        border: 0.5px solid white;
        border-radius: 4px;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
    }

    #title h1 {
        color: white;
        margin-top: 3%;
        padding-bottom: 3%;
    }

    #main {
        margin-bottom: 5%;
        margin-top: 5%;
    }

    /*Profile Card 5*/
    .profile-card-5 {
        margin-top: 20px;
    }

    .profile-card-5 .btn {
        border-radius: 2px;
        text-transform: uppercase;
        font-size: 12px;
        padding: 7px 20px;
    }

    .profile-card-5 .card-img-block {
        width: 91%;
        margin: 0 auto;
        position: relative;
        top: -20px;

    }

    .profile-card-5 .card-img-block img {
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.63);
    }

    .profile-card-5 h5 {
        color: #43bda7;
        font-weight: 600;
    }

    .profile-card-5 p {
        font-size: 14px;
        font-weight: 300;
    }

    .profile-card-5 .btn-primary {
        background-color: #43bda7;
        border-color: #43bda7;
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

    #social-icons a {
        color: #43bda7;
    }

    #social-icons .icon {
        font-size: 30px;
        width: 15%;
    }

</style>
<body>
<header>
    <div class="container-fluid">
        <div id="header-bk">
            <div id="title">
                <a href="http://rklinic.com"><img src="{{ asset('/assets/images/frontend/logo.png') }}"></a>
                <h1>RKLINIC NEWSLETTER !</h1>
            </div>
        </div>
    </div>
</header>
<section id="main">
    <div class="container">
        <div class="row">

            <div class="col-md-4 mt-4">
                <div class="card profile-card-5">
                    <div class="card-img-block">
                        <img class="card-img-top" src="{{ asset('/assets/images/coco.jpg') }}"
                             alt="Card image cap">
                    </div>
                    <div class="card-body pt-0">
                        <h5 class="card-title">Florence Garza</h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk
                            of the card's content.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-4">
                <div class="card profile-card-5">
                    <div class="card-img-block">
                        <img class="card-img-top" src="https://images.unsplash.com/photo-1517832207067-4db24a2ae47c"
                             alt="Card image cap">
                    </div>
                    <div class="card-body pt-0">
                        <h5 class="card-title">Florence Garza</h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk
                            of the card's content.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-4">
                <div class="card profile-card-5">
                    <div class="card-img-block">
                        <img class="card-img-top" src="{{ asset('/assets/images/coco.jpg') }}"
                             alt="Card image cap">
                    </div>
                    <div class="card-body pt-0">
                        <h5 class="card-title">Florence Garza</h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk
                            of the card's content.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<footer>
    <div class="footer-bottom-area blue-bg">
        <div class="container">
            <div class="row" id="social-icons">
                <div class="icon">
                    <a target="_blank" class="facebook"
                       href="https://www.facebook.com/rklinic/?modal=admin_todo_tour"><i
                                class="fab fa-facebook-square"></i></a>
                </div>
                <div class="icon">
                    <a target="_blank" class="instagram" href="https://www.instagram.com/rklinic_system/">
                        <img src="{{ asset('/assets/images/insta.png') }}">
                    </a>
                </div>
                <div class="icon">
                    <a target="_blank" class="youtube"
                       href="https://www.youtube.com/channel/UCYaO_gtdnzqn0KEKTfQLQ4w?view_as=subscriber"><i
                                class="fab fa-youtube"></i></a>
                </div>
                <div class="icon">
                    <a target="_blank" class="twitter" href="https://twitter.com/RklinicS"><i
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
