$(document).ready(function () {
    $('#myCarousel').carousel({
        interval: 10000
    });
    $('#loginCarousel').carousel({
        interval: 10000
    });
    $('body').css('overflow','hidden');

    $('#login-page').height($(window).height());
});