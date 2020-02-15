{!! Html::script('assets/js/admin/jquery.min.js') !!}
{!! Html::script('assets/js/admin/moment.latest.min.js') !!}

@include('flashy::message')

{!! Html::script('assets/js/common/bootstrap.min.js') !!}
{!! Html::script('assets/js/admin/bootstrap-tour.js') !!}

{!! Html::script('assets/js/admin/ace-extra.min.js') !!}


{!! Html::script('https://cdnjs.cloudflare.com/ajax/libs/izimodal/1.5.1/js/iziModal.min.js') !!}
<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.2.0/js/iziToast.min.js"></script>

<script>
    window.FontAwesomeConfig = {
        searchPseudoElements: true
    }
</script>

{!! Html::script('assets/js/admin/bootstrap-material-datetimepicker.js') !!}

<script>

    $("#modal").iziModal();
    $("#modal_premium").iziModal();
    $("#modal_import").iziModal();
    $("#edit-modal").iziModal();
    $("#reschedule-modal").iziModal();

    $('#date-profile').bootstrapMaterialDatePicker({
        weekStart: 0,
        time: false,
        format: 'YYYY-MM-DD',
        maxDate: '2000/12/31',
    });
    $('#next_visit').bootstrapMaterialDatePicker({
        weekStart: 0,
        time: false,
        format: 'YYYY/MM/DD',
        minDate: new Date(),
    });
    $('#day').bootstrapMaterialDatePicker({weekStart: 0, time: false, minDate: new Date(),});
    $('#expiry_date').bootstrapMaterialDatePicker({weekStart: 0, time: false, minDate: "{{ now()->addDay(1) }}",});

    $('#search').bootstrapMaterialDatePicker({weekStart: 0, time: false});

    $('#time-from').bootstrapMaterialDatePicker({
        date: false,
        format: 'HH:mm',
    });
    $('#time-to').bootstrapMaterialDatePicker({
        date: false,
        format: 'HH:mm',
    });

</script>

<script type="text/javascript">
    if ('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
</script>


<!-- page specific plugin scripts -->

{{--{!! Html::script('assets/js/admin/bootstrap-datetimepicker.js') !!}--}}

{!! Html::script('assets/js/admin/jquery.hotkeys.index.min.js') !!}
<!-- ace scripts  -->

{!! Html::script('assets/js/admin/ace-elements.min.js') !!}
{!! Html::script('assets/js/admin/ace.min.js') !!}

{!! Html::script('assets/js/common/sweetalert.min.js') !!}


{!! Html::script('//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js') !!}


{{--{!! Html::script('assets/js/admin/moment.latest.min.js') !!}--}}

{!! Html::script('assets/js/admin/parsley.min.js') !!}


{{--
{!! Html::script('assets/js/common/sweetalert.min.js') !!}
--}}


{!! Html::script('assets/js/admin/jquery-ui.min.js') !!}
{!! Html::script('assets/js/admin/jquery.ui.touch-punch.min.js') !!}

<script src="https://unpkg.com/flatpickr"></script>
<script src="{{ asset('assets/js/admin/chosen.jquery.min.js') }}"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>

<script>
    $(document).ready(function () {
        URL = "{{ url('/') }}";
        token = "{{ csrf_token() }}";

        @if($auth)
        // load notification indicator
        $('.notification-counter-click').load(URL + '/notifications/counter');
        @endif

        @if(app()->getLocale() == 'en')
        $(document).on('click', '#sidebar-toggle-icon', function (event) {
            event.preventDefault();
            console.log($(this));
            $(this).removeClass('fa-angle-left').addClass('fa-angle-right');
        });

        $(document).on('click', '.toggle-custom', function () {
            if ($('#sidebar').hasClass('menu-min')) {
                $('#sidebar').removeClass('menu-min');
                $('.toggle-custom svg').attr("class", "svg-inline--fa fa-angle-left fa-w-8 ace-icon ace-save-state");
            } else {
                $('#sidebar').addClass('menu-min');
                $('.toggle-custom svg').attr("class", "svg-inline--fa fa-angle-right fa-w-8 ace-icon ace-save-state");
            }

        });

        @else
        $(document).on('click', '#sidebar-toggle-icon', function (event) {
            event.preventDefault();
            console.log($(this));
            $(this).removeClass('fa-angle-right').addClass('fa-angle-left');
        });

        $(document).on('click', '.toggle-custom', function () {
            if ($('#sidebar').hasClass('menu-min')) {
                $('#sidebar').removeClass('menu-min');
                $('.toggle-custom svg').attr("class", "svg-inline--fa fa-angle-right fa-w-8 ace-icon ace-save-state");
            } else {
                $('#sidebar').addClass('menu-min');
                $('.toggle-custom svg').attr("class", "svg-inline--fa fa-angle-left fa-w-8 ace-icon ace-save-state");
            }

        });
        @endif

        $(document).on('click', '.ajax-btn', function (event) {
            event.preventDefault();
            id = $(this).data('id');
            link = $(this).data('link');
            type = $(event.target).data('type');
            swal({
                    title: "Are you sure?",
                    text: "Your will not be able to recover this imaginary file!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    showLoaderOnConfirm: true,
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                },
                function () {
                    $.ajax({
                        url: link,
                        type: 'DELETE',
                        data: {_token: token, id: id}
                    }).done(function (data) {
                        if (data.msg == true) {
                            swal({
                                    title: "Done",
                                    text: "",
                                    type: "success",
                                },
                                function () {
                                    window.location.reload();

                                });
                        } else {
                            swal({
                                    title: "Error",
                                    text: "Whoops something went wrong",
                                    type: "error",
                                },
                                function () {
                                    window.location.reload();
                                });
                        }
                    });
                });
        });

        // $('.main-content-inner').css('display', 'block').css('height', ($(window).height() - 110));

        if ($('.navbar-fixed-bottom')[0]) {
            $('.main-content-inner').css('display', 'block').css('height', ($(window).height() - 128));
        } else {
            $('.main-content-inner').css('display', 'block').css('height', ($(window).height() - 92));
        }
        $('.add-other').click(function () {
            var oth = $('.row-input').html();
            var final = "<div class='row'>" +
                oth +
                "<div class='col-md-2'>" +
                "<a class='btn btn-danger btn-large del-other padding-7 no-border' >" +
                "<i class='fa fa-trash'></i>" +
                "</a>" +
                "</div>" +
                "</div>";
            $('.manage-multiple-rows').append(final);
        });


        $(document).on('click', '.del-other', function () {
            $(this).parent().parent().html('');
        });
    });


    {{-- start  sidebar angle script  --}}
    $(document).on('mouseover', '.toggle-custom, .sidebar, .no-skin .sidebar', function () {
        $('.toggle-custom svg').css('display', 'block');
    });

    $(document).on('mouseout', '.toggle-custom, .sidebar, .no-skin .sidebar', function () {
        $('.toggle-custom svg').css('display', 'none');
    });


    {{-- end sidebar angle script  --}}

</script>

{!! Html::script('assets/js/admin/jquery.dataTables.min.js') !!}
{!! Html::script('assets/js/admin/jquery.dataTables.bootstrap.min.js') !!}
{!! Html::script('assets/js/admin/smart-tables.js') !!}

@yield('scripts')


@yield('extrascripts')


@stack('more-scripts')


{!! Html::script('assets/js/admin/main.js') !!}
