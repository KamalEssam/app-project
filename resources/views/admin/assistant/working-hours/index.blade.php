@extends('layouts.admin.admin-master')

@section('title', trans('lang.manage_working_hours'))

@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-4 col-md-offset-5">
                    <a href='{{ route('working-hours.create',['clinic'=>  Request::get('clinic')  ?? '-1']) }}'
                       class="btn btn-loon btn-xs center"
                       id="working-hours-add"> {{ trans('lang.manage_working_hours') }}</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-10 col-md-offset-1 ">
                <div>
                    @php  $all_working_hours = 0  @endphp
                    @foreach(Config::get('lists.days') as $day_index)
                        @php $colors = ['#f7f3c5', '#C5EFF7', '#b8c1ff ', '#d3f7f0','#dcc9fd','#ffe0d7'] @endphp
                        @php
                            $old_working_hours = (new \App\Http\Repositories\Web\WorkingHourRepository())->getWorkingHoursByClinicId($clinic->id,true,$day_index['day']);
                            $new_working_hours = (new \App\Http\Repositories\Web\WorkingHourRepository())->getWorkingHoursByClinicId($clinic->id,false,$day_index['day']);

                            $breakTimes = (new \App\Http\Repositories\Web\WorkingHourRepository())->getBreakWorkingHoursByClinicId($clinic->id,$day_index['day']);
                            if($new_working_hours) {
                                     $all_working_hours +=count($old_working_hours);
                            }

                            if($new_working_hours) {
                                     $all_working_hours +=  + count($new_working_hours);
                            }

                        @endphp
                        @if(($old_working_hours && count($old_working_hours) != 0) || ($new_working_hours && count($new_working_hours) != 0))
                            <div class="col-md-10 col-md-offset-1 mr-25 panel panel-default">
                                <div class="panel-heading bolder">
                                    <strong>{{ $day_index[app()->getLocale() . '_name']  }}</strong>
                                </div>
                                <div class="panel-body">
                                    <div class="widget-body">
                                        <div class="widget-main no-padding">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    @foreach($old_working_hours as $wh)
                                                        <p class="bolder">current working hours</p>
                                                        <span class=" span-sm  no-hover "><span
                                                                class="line-height-1 bigger-110 btn-tags black"
                                                                style="background-color: #dcc9fd !important;">
                                                    {{ \App\Http\Traits\DateTrait::getTimeByFormat($wh->min_time, 'h:i a') }} </span></span>
                                                        <span class=" span-sm  no-hover "><span
                                                                class="line-height-1 bigger-110 btn-tags black"
                                                                style="background-color: {{ $colors[rand(0,5)] }} !important;">
                                                    {{ \App\Http\Traits\DateTrait::getTimeByFormat($wh->max_time, 'h:i a') }} </span></span>
                                                    @endforeach
                                                </div>
                                                <div class="col-md-6">
                                                    @foreach($new_working_hours as $wh)
                                                        <p class="bolder">valid from {{ $wh->start_date }}</p>
                                                        <span class=" span-sm  no-hover "><span
                                                                class="line-height-1 bigger-110 btn-tags black"
                                                                style="background-color: #dcc9fd !important;">
                                                    {{ \App\Http\Traits\DateTrait::getTimeByFormat($wh->min_time, 'h:i a') }} </span></span>
                                                        <span class=" span-sm no-hover "><span
                                                                class="line-height-1 bigger-110 btn-tags black"
                                                                style="background-color: {{ $colors[rand(0,5)] }} !important;">
                                                    {{ \App\Http\Traits\DateTrait::getTimeByFormat($wh->max_time, 'h:i a') }} </span></span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            @if($breakTimes && count($breakTimes) >  0)
                                                <div class="col-md-6">
                                                    <p class="bolder">Breaks </p>
                                                    @foreach($breakTimes as $wh)
                                                        <span class=" span-sm  no-hover "><span
                                                                class="line-height-1 bigger-110 btn-tags black"
                                                                style="background-color: #dcc9fd !important;">
                                                    {{ \App\Http\Traits\DateTrait::getTimeByFormat($wh->min_time, 'h:i a') }} </span></span>
                                                        <span class=" span-sm no-hover "><span
                                                                class="line-height-1 bigger-110 btn-tags black"
                                                                style="background-color: {{ $colors[rand(0,5)] }} !important;">
                                                    {{ \App\Http\Traits\DateTrait::getTimeByFormat($wh->max_time, 'h:i a') }} </span></span>
                                                        <br>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @if($all_working_hours <= 0)
                        <div class="row">
                            <div class="col-xs-12 text-center"><img class="no_data_image"
                                                                    src="{{ asset('assets/images/no_data/no_times.png') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 text-center"><p
                                    class="loon no_data">{{trans('lang.no_times')}}</p></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            var URL = "{{ url('/') }}";

            // for tour
            $.ajax({
                url: URL + '/check-first-time',
                type: 'POST',
                data: {_token: token, column_name: 'working_hours_tour'}
            }).done(function (data) {
                if (data == 'true') {
                    var tour = new Tour({
                        debug: true,
                        steps: [
                            {
                                element: "#working-hours-add",
                                title: "Manage Working Hours",
                                content: "Add your business working hours here.",
                                placement: "bottom",
                                backdrop: true,
                                template: "<div class='popover tour'>" +
                                    "<div class='arrow'></div>" +
                                    "<h3 class='popover-title'></h3>" +
                                    "<div class='popover-content'></div>" +
                                    "<div class='popover-navigation'>" +
                                    "<button class='btn btn-default' data-role='end'>Got it!</button>" +
                                    "</div>" +
                                    "</div>",
                            }
                        ]
                    });
                    tour.init();
                    tour.restart();
                }
            });

        });
    </script>
@stop
