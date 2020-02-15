@extends('layouts.admin.admin-master')

@section('title', trans('lang.manage_holidays'))
@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h2 class="text-center loon bolder">{{ ucfirst(trans('lang.holidays')) }}</h2>
                    <p class="text-center mt-50" style="font-size: 16px">
                        {!! trans('lang.holiday_illustration') !!}
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-md-offset-2 ">
                @if($auth->role_id == $role_assistant)
                    <div class="panel panel-default">
                        <div class="panel-heading bolder">
                            <strong>{{trans('lang.holidays')}}</strong>

                            <a class="trigger-modal pull-right white edit-btn"
                               data-iziModal-open="#modal" style="cursor: pointer;">
                                <i class="fas fa-plus"></i>
                            </a>

                        </div>

                        <div class="panel-body">
                            @if($holidays->count() > 0)
                                <div class="widget-body">
                                    <div class="widget-main no-padding">
                                        @foreach($holidays as $holiday)
                                            <span class=" span-sm  no-hover "
                                                  data-toggle="tooltip"
                                                  data-placement="top"
                                                  style="cursor: pointer;"
                                                  title="{{ $holiday[app()->getLocale() . '_reason'] }}"
                                            ><span
                                                        class="line-height-1 bigger-110 btn-tags black"
                                                        style="background-color: #eee !important;">
                                                    {{ $holiday->day }} <i
                                                            class="loon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                            data-id="{{ $holiday->id }} "
                                                            data-link="{{ route('holiday.destroy', $holiday->id) }}"
                                                            data-type="DELETE"
                                                    ></i> </span></span>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-xs-12 text-center"><img class="no_data_image"
                                                                            src="{{ asset('assets/images/no_data/no_holiday.png') }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 text-center"><p
                                                class="loon no_data">{{trans('lang.no_holiday')}}</p></div>
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif($auth->role_id ==$role_doctor && $auth->account->type == 1)
                    @php
                        $clinics = DB::table('clinics')->where('account_id', $auth->account_id)->get();
                    @endphp
                    <div class="text-center mb-20">
                        <a class="trigger-modal white edit-btn btn btn-primary"
                           data-iziModal-open="#modal" style="cursor: pointer;">
                            add holiday
                        </a>
                    </div>
                    @foreach($clinics as $clinic)
                        <div class="panel panel-default">
                            <div class="panel-heading bolder">
                                <strong>{{ $clinic->{app()->getLocale() . '_name'} }}</strong>
                            </div>

                            <div class="panel-body">
                                @if($holidays->count() > 0)
                                    <div class="widget-body">
                                        <div class="widget-main no-padding">
                                            @foreach($holidays as $holiday)
                                                @if($holiday->clinic_id == $clinic->id)
                                                    <span class=" span-sm  no-hover "
                                                          data-toggle="tooltip"
                                                          data-placement="top"
                                                          style="cursor: pointer;"
                                                          title="{{ $holiday[app()->getLocale() . '_reason'] }}"
                                                    ><span
                                                                class="line-height-1 bigger-110 btn-tags black"
                                                                style="background-color: #eee !important;">
                                                    {{ $holiday->day }} <i
                                                                    class="loon fa fa-trash-alt bigger-120 delete ajax-btn"
                                                                    data-id="{{ $holiday->id }} "
                                                                    data-link="{{ route('holiday.destroy', $holiday->id) }}"
                                                                    data-type="DELETE"
                                                            ></i> </span></span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="row">
                                        <div class="col-xs-12 text-center"><p
                                                    class="loon no_data">{{trans('lang.no_holiday')}}</p></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        @include('admin.assistant.holiday.modal-form')
    </div>
@stop
@section('scripts')

    <script type="text/javascript">
        if (!ace.vars['touch']) {
            $('.chosen-select').chosen({allow_single_deselect: true});
            //resize the chosen on window resize
            $(window)
                .off('resize.chosen')
                .on('resize.chosen', function () {
                    $('.chosen-select').each(function () {
                        var $this = $(this);
                        $this.next().css({'width': $this.parent().width()});
                    })
                }).trigger('resize.chosen');
            //resize chosen on sidebar collapse/expand
            $(document).on('settings.ace.chosen', function (e, event_name, event_val) {
                if (event_name != 'sidebar_collapsed') return;
                $('.chosen-select').each(function () {
                    var $this = $(this);
                    $this.next().css({'width': $this.parent().width()});
                })
            });

            $('#chosen-multiple-style .btn').on('click', function (e) {
                var target = $(this).find('input[type=radio]');
                var which = parseInt(target.val());
                if (which == 2) $('#form-field-select-4').addClass('tag-input-style');
                else $('#form-field-select-4').removeClass('tag-input-style');
            });
        }

        $(document).on('click', '.btn-modal-form-submit', function (e) {
            var form = $('#modal-form');
            if (form.parsley().isValid()) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: form.serialize(),
                    dataType: 'json',
                    success: function (data) {
                        if (data.status === true) {
                            swal({
                                    title: "Success",
                                    text: data.msg,
                                    type: "success",
                                    confirmButtonClass: "btn-success",
                                    confirmButtonText: "Ok",
                                },
                                function () {
                                    //  close modal
                                    $('#modal').iziModal('close');
                                    form[0].reset();
                                    location.reload();
                                });
                        } else {
                            swal("Error!", data.msg, "warning");
                        }
                    }, error: function (data) {
                        swal("Error!", data.msg, "warning");
                    }
                });
            }
        });
    </script>
@stop