@extends('layouts.admin.admin-master')

@section('title', trans('lang.create_reservation'))

@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
    {!! Html::style('assets/css/admin/jquery-ui.min.css') !!}
@stop


@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{ trans('lang.create_reservation') }}</h1>
                        <hr>
                        {!! Form::open(['route' => 'reservations.store', 'files' => true , 'id' => 'add-reservation-form']) !!}
                        @include('admin.common.reservations.form', ['btn' => 'Add', 'classes' => 'btn-xs pull-right'])
                        {!! Form::close() !!}
                        @include('admin.common.reservations.modal-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('more-scripts')
    <script type="text/javascript">
        var times_avaliable = 'false';
        jQuery(function ($) {
            var clinic_patten = "{{ $clinic->pattern }}";
            // on key up on mobile field auto complete the mobiles number
            $(document).on('keyup', '#tags', function (e) {
                $.ajax({
                    url: URL + '/reservation/refresh-user-results',
                    type: 'POST',
                    data: {_token: token}
                }).done(function (data) {
                    // auto complete mobiles
                    $('#tags').autocomplete({
                        source: data.split("#")
                    });
                });
            });
            //get username by mobile
            $(document).on('click', '.ui-menu-item-wrapper', function (e) {
                $.ajax({
                    url: URL + '/reservation/user_filter',
                    type: 'POST',
                    data: {_token: token, mobile: $('#tags').val()}
                }).done(function (data) {
                    if (data === false) {
                        $('#mobile').val('');
                    } else {
                        $('#name').val(data);
                    }
                });
            });
            //filter day times
            $('#day').on('change', function () {
                $.ajax({
                    url: URL + '/reservation/time_reserved',
                    type: 'POST',
                    data: {_token: token, day: $('#day').val(), clinic_id: "{{ $clinic->id }}"}
                }).done(function (data) {

                    if (clinic_patten == 1) {
                        if (data != 'true' && data != 'false') {
                            $('#time-wrapper').html(data);
                        }
                    }

                    times_avaliable = data;
                    var string = '';
                    var timeSelector = $('#time');
                    if (data.length === 0) {
                        string = '<option value="">' + 'No times available on this day' + '</option>';
                        timeSelector.prop('required', true);
                    } else {
                        for (var i = 0; i < data.length; i++) {
                            if (data[i]) {
                                string += '<option value="' + data[i].id + '">' + data[i].time + '</option>';
                            }
                        }
                    }
                    timeSelector.html(string);
                });
            });
            // add new reservation
            $(document).on('click', '.add-reservation', function (e) {
                e.preventDefault();
                var clinic = "{{ $_GET['clinic'] ?? '' }}";
                $.ajax({
                    // check date in case of the pattern is queue and there is not time available today
                    url: URL + '/reservation/check-date',
                    type: 'POST',
                    data: {_token: token, clinic: clinic, day: $('#day').val(), pattern: clinic_patten}
                }).done(function (data) {
                    if (data.status == false) {
                        swal({
                            title: "Warning",
                            text: data.msg,
                            type: "warning",
                        });
                    } else {
                        if (times_avaliable === 'false') {
                            swal({
                                title: "Warning",
                                text: "there is no times available in that day",
                                type: "warning",
                            });
                        } else {
                            $('#add-reservation-form').submit();
                        }

                    }
                });
            });
        });

    </script>
@endpush
