
<!--====== SCRIPTS JS ======-->
{!! Html::script('assets/js/frontend/vendor/jquery-1.12.4.min.js') !!}
{!! Html::script('assets/js/frontend/vendor/bootstrap.min.js') !!}


<!--====== PLUGINS JS ======-->
{!! Html::script('assets/js/frontend/vendor/jquery.easing.1.3.js') !!}
{!! Html::script('assets/js/frontend/vendor/jquery-migrate-1.2.1.min.js') !!}
{!! Html::script('assets/js/frontend/vendor/jquery.appear.js') !!}
{!! Html::script('assets/js/frontend/owl.carousel.min.js') !!}
{!! Html::script('assets/js/frontend/slick.min.js') !!}
{!! Html::script('assets/js/frontend/stellar.js') !!}
{!! Html::script('assets/js/frontend/wow.min.js') !!}
{!! Html::script('assets/js/frontend/jquery-modal-video.min.js') !!}
{!! Html::script('assets/js/frontend/stellarnav.min.js') !!}
{!! Html::script('assets/js/frontend/contact-form.js') !!}
{!! Html::script('assets/js/frontend/jquery.ajaxchimp.js') !!}
{!! Html::script('assets/js/frontend/jquery.sticky.js') !!}
{!! Html::script('assets/js/frontend/classie.js') !!}
{!! Html::script('assets/js/frontend/iziToast.min.js')!!}
<!--===== ACTIVE JS=====-->
{!! Html::script('assets/js/frontend/main.js') !!}


<script>
    window.FontAwesomeConfig = {
        searchPseudoElements: true
    }
</script>
<script>
    $(document).ready(function () {
        // slider
        $('#customers-testimonials').owlCarousel({
            loop: true,
            center: true,
            items: 3,
            margin: 0,
            dots:true,
            autoplay:true,
            autoplayTimeout: 8500,
            smartSpeed: 450,
/*
            nav: true,
            navText : ["<i class='fa fa-chevron-left green'></i>","<i class='fa fa-chevron-right green'></i>"],
*/
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                1170: {
                    items: 3
                }
            }
        });

        $('.nav-toggle').click(function (e) {
            e.preventDefault();
            var collapse_content_selector = $(this).attr('href');
            var toggle_switch = $(this);
            $(collapse_content_selector).toggle(function () {
                if ($(this).css('display') == 'none') {
                    toggle_switch.html('Read More');
                } else {
                    toggle_switch.html('Read Less');
                }
            });
        });
        $(window).scroll(function() {
            if ($(this).scrollTop() > 50 ) {
                $('.scrolltop:hidden').stop(true, true).fadeIn();
            } else {
                $('.scrolltop').stop(true, true).fadeOut();
            }
        });
        $(function(){
            $(".scroll").click(function(){
                $("html,body").animate({scrollTop: 0}, 1000)
                ;return false})
        })
        $('#home').height($(window).height());

    });
</script>
@yield('scripts')
