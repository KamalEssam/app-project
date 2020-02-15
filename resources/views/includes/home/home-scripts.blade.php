{!! Html::script('assets/js/admin/jquery-2.1.4.min.js') !!}
@include('flashy::message')
{{--
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script> --}}

{{-- {!! Html::script('assets/js/common/app.js') !!} --}}

{!! Html::script('assets/js/common/bootstrap.min.js') !!}

<!-- page specific plugin scripts -->


<!-- ace scripts  -->

{!! Html::script('assets/js/common/sweetalert.min.js') !!}

{!! Html::script('assets/js/common/owl.carousel.min.js') !!}

{!! Html::script('assets/js/common/owl.js') !!}

@yield('scripts')


{!! Html::script('assets/js/common/main.js') !!}


