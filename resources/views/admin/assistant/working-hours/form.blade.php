@if($auth->account->is_completed == 1)
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="clinic_name">{{trans('lang.clinic_name')}}<span class="astric">*</span></label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::text('clinic_name' , $clinic_account->type == 0 ? $clinic[app()->getLocale(). '_address'] : $clinic[app()->getLocale(). '_name'] , ['class'=>'form-control ' . ($errors->has('clinic_id') ? 'redborder' : ''), 'disabled'=>'disabled' ]) }}
                    <small class="text-danger">{{ $errors->first('clinic_name') }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="interval">{{trans('lang.interval_per_minutes')}} <span class="astric">*</span></label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::number('interval', $clinic->avg_reservation_time , [ 'class'=>'form-control ' . ($errors->has('interval') ? 'redborder' : ''),'required'=>'required', 'id'=>'interval', 'disabled'=>'disabled' ]) }}
                    <small class="text-danger">{{ $errors->first('interval_per_minutes') }}</small>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row">
    <div class="form-group col-md-6">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="from">{{trans('lang.from')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::time('from', $working_hours_from ?? null, ['class'=>'time form-control ' . ($errors->has('from') ? 'redborder' : ''),'required'=>'required' , 'id'=>'from']) }}
                <small class="text-danger">{{ $errors->first('interval_per_minutes') }}</small>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="to">{{trans('lang.to')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::time('to', $working_hours_to ?? null, ['class'=>'time form-control ' . ($errors->has('to') ? 'redborder' : ''),'required'=>'required' , 'id'=>'to']) }}
                <small class="text-danger">{{ $errors->first('interval_per_minutes') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="day">{{trans('lang.day')}}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                <select name='day[]'
                        class="form-control choose-days chosen-select {{ ($errors->has('day') ? 'redborder' : '') }}"
                        required multiple
                        id="dayIndex">
                    @foreach(Config::get('lists.days') as $day)
                        <option value="{{$day['day']}}">{{ $day[app()->getLocale() . '_name'] }}</option>
                    @endforeach
                </select>
                <small class="text-danger">{{ $errors->first('day') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="interval">{{trans('lang.start_immediately')}}</label>
                {{ Form::checkbox('start_immediately',1,true,['class' => 'inline_checkBox']) }}
            </div>
        </div>
        <p class='help-block bold'>if you check this that means all updates will be applied immediately starting from
            today.</p>
    </div>
</div>

<div class="row add_expiry">
    {{-- Expiry date input will be here --}}
</div>

<div class="row">
    <div class="col-md-12">
        <div>
            @if(count($days) > 0)
                <h5 class="center margin-20 bolder grey">{{ trans('lang.current_data') }}</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="center">{{trans('lang.day')}}</th>
                            <th class="center">{{trans('lang.from')}}</th>
                            <th class="center">{{ trans('lang.to') }}</th>
                            <th class="center">{{ trans('lang.controls') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($days as $day)
                            <tr>
                                <td class="center">{{ \App\Http\Traits\DateTrait::getDayNameByIndex($day->day) }}</td>
                                <td class="center">{{ \App\Http\Traits\DateTrait::getTimeByFormat($day->min_time, 'h:i a') }}</td>
                                <td class="center">{{\App\Http\Traits\DateTrait::getTimeByFormat($day->max_time, 'h:i a')}}</td>
                                <td class="center">
                                    <div class="btn-group control-icon">
                                        <a href="#"><i
                                                class="ace-icon fa fa-trash-alt bigger-120 delete delete_workingHours"
                                                data-index="{{ $day->day }} "
                                                data-start="{{ $day->start_date }} "
                                                data-end="{{ $day->expiry_date }} "
                                                data-clinic="{{ $clinic->id }} "
                                                data-name="{{ \App\Http\Traits\DateTrait::getDayNameByIndex($day->day) }} "></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else

                <div class="row">
                    <div class="col-xs-12 text-center"><p
                            class="loon no_data">{{trans('lang.no_working_hours')}}</p></div>
                </div>
            @endif
        </div>
    </div>

    @if(count($upcoming_workingHours) > 0)
        <div class="col-md-12">
            <h5 class="center margin-20 bolder grey">{{ trans('lang.future_data') }}</h5>
            <div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="center">{{trans('lang.day')}}</th>
                            <th class="center">{{trans('lang.from')}}</th>
                            <th class="center">{{ trans('lang.to') }}</th>
                            <th class="center">{{ trans('lang.apply_from') }}</th>
                            <th class="center">{{ trans('lang.controls') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($upcoming_workingHours as $day)
                            <tr>
                                <td class="center">{{ \App\Http\Traits\DateTrait::getDayNameByIndex($day->day) }}</td>
                                <td class="center">{{ \App\Http\Traits\DateTrait::getTimeByFormat($day->min_time, 'h:i a') }}</td>
                                <td class="center">{{\App\Http\Traits\DateTrait::getTimeByFormat($day->max_time, 'h:i a')}}</td>
                                <td class="center">{{ $day->start_date }}</td>
                                <td class="center">
                                    <div class="btn-group control-icon">
                                        <a title="{{ trans('lang.delete_working_hours') }}"
                                           href="#"><i
                                                class="ace-icon fa fa-trash-alt bigger-120 delete delete_workingHours"
                                                data-index="{{ $day->day }} "
                                                data-start="{{ $day->start_date }} "
                                                data-end="{{ $day->expiry_date }} "
                                                data-clinic="{{ $clinic->id }} "
                                                data-name="{{ \App\Http\Traits\DateTrait::getDayNameByIndex($day->day) }}"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
{{  Form::hidden('clinic_id' , $clinic->id == null ? $_GET['clinic'] : $clinic->id ,['id' => 'clinicID']) }}

@if($auth->account->is_completed == 1)
    <a href="{{ url('/working-hours?clinic='. $clinic->id)}}"
       class="pull-right loon p-7">{{ trans('lang.back') }}</a>
@endif
{{  Form::submit($btn , ['class' => 'btn-loon ' . $classes]) }}
<br><br>


<hr>
<h1 class="font-18 loon">{{trans('lang.manage_working_hours_breaks')}}</h1>
{!! Form::open(['route' => 'working-hours.store','class' => 'breaks-form']) !!}
<div class="row">
    <div class="form-group col-md-6">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="br_from">{{trans('lang.from')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::time('br_from', $working_hours_from ?? null, ['class'=>'time form-control ' . ($errors->has('from') ? 'redborder' : ''),'required'=>'required' , 'id'=>'br_from']) }}
                <small class="text-danger">{{ $errors->first('interval_per_minutes') }}</small>
            </div>
        </div>
    </div>

    <div class="form-group col-md-6">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="br_to">{{trans('lang.to')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::time('br_to', $working_hours_to ?? null, ['class'=>'time form-control ' . ($errors->has('to') ? 'redborder' : ''),'required'=>'required' , 'id'=>'br_to']) }}
                <small class="text-danger">{{ $errors->first('interval_per_minutes') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="day">{{trans('lang.day')}}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                <select name='day[]'
                        class="form-control choose-break-days chosen-select {{ ($errors->has('day') ? 'redborder' : '') }}"
                        required multiple
                        id="breakDayIndex">
                    @foreach(Config::get('lists.days') as $day)
                        <option value="{{$day['day']}}">{{ $day[app()->getLocale() . '_name'] }}</option>
                    @endforeach
                </select>
                <small class="text-danger">{{ $errors->first('day') }}</small>
            </div>
        </div>
    </div>
</div>
{{  Form::submit('save' , ['class' => 'btn-loon btn-xs pull-right add-btn add-working-hours-breaks']) }}
<div class="row">
    @php
        $breaks = (new \App\Http\Repositories\Web\WorkingHourRepository())->getAllBreaksWorkingHoursByClinicId($clinic->id)
    @endphp
    @if(count($breaks) > 0)
        <div class="col-md-12 mt-60">
            <h5 class="center margin-20 bolder grey">{{ trans('lang.breaks') }}</h5>
            <div>
                <div class="table-responsive" style="width:100%">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="center">{{trans('lang.day')}}</th>
                            <th class="center">{{trans('lang.from')}}</th>
                            <th class="center">{{ trans('lang.to') }}</th>
                            <th class="center">{{ trans('lang.controls') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach(Config::get('lists.days') as $day)
                            @php
                                $breakTimes = (new \App\Http\Repositories\Web\WorkingHourRepository())->getBreakWorkingHoursByClinicId($clinic->id,$day['day']);
                            @endphp
                            @foreach($breakTimes as $break)
                                <tr>
                                    <td class="center">{{ \App\Http\Traits\DateTrait::getDayNameByIndex($day['day']) }}</td>
                                    <td class="center">{{ \App\Http\Traits\DateTrait::getTimeByFormat($break->min_time, 'h:i a') }}</td>
                                    <td class="center">{{\App\Http\Traits\DateTrait::getTimeByFormat($break->max_time, 'h:i a')}}</td>
                                    <td class="center">
                                        <div class="btn-group control-icon">
                                            <a title="{{ trans('lang.delete_working_hours') }}"
                                               href="#"><i
                                                    class="ace-icon fa fa-trash-alt bigger-120 delete delete_workingHours_breaks"
                                                    data-day="{{ $day['day'] }} "
                                                    data-date="{{ $break->updated_at }} "
                                                    data-clinic="{{ $clinic->id }} "
                                                ></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<br><br><br><br>

@push('more-scripts')
    <script>
        $(".chosen-select").chosen();

        $(document).on('click', '.add-working-hours', function (e) {
            e.preventDefault();
            let dayIndex = $('#dayIndex');
            let daysName = [];

            // get the names of the selected indexes
            dayIndex.find(":selected").each(function () {
                let $this = $(this);
                if ($this.length) {
                    daysName.push($this.text());
                }
            });

            $.ajax({
                url: URL + '/working-hours/check-all',
                type: 'POST',
                data: {
                    _token: token,
                    from: $('#from').val(),
                    to: $('#to').val(),
                    clinic_id: $('#clinicID').val(),
                    dayName: daysName,
                    dayIndex: dayIndex.val(),
                    start_date: $('#start_date').val()
                }
            }).done(function (data) {

                if (data.case == -1) {
                    swal({
                        title: "Failure",
                        text: "Please choose Days",
                        type: "warning",
                    });
                }

                if (data.case == 1 || data.case == 2 && data.status == false) {
                    // check if working hours valid or not
                    swal({
                        title: "Failure",
                        text: data.msg,
                        type: "warning",
                    });

                } else if (data.case == 3 && data.status == true) {
                    // check for reservations in that day
                    let message = '';
                    if (data.reservations === 0) {
                        message = 'Are you Sure you want to proceed';
                    } else {
                        message = data.reservations + ' reservations will be canceled with SMS to each patient, are you sure you want to proceed ? !!!';
                    }
                    swal({
                            title: "Are you sure?",
                            text: message,
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes, I'am sure!",
                            closeOnConfirm: false
                        },
                        function () {
                            $('.wh-form').submit();
                        });
                }
            });

        });

        $('input:checkbox').change(function () {
            if ($(this).is(":checked")) {
                $('.add_expiry').html('');
            } else {
                $('.add_expiry').append(
                    "<div class='form-group col-md-12'>" +
                    "    <div class='row'>" +
                    "        <div class='col-md-12 label-form'>" +
                    "            <label for='mobile'>{{ trans('lang.start_date') }}<span class='astric'>*</span></label>" +
                    "        </div>" +
                    "        <div class='col-md-12 form-input'>" +
                    "            <input class='form-control date' required='required' id='start_date' name='start_date' type='date'>" +
                    "        </div>" +
                    "    </div>" +
                    "</div>"
                );
                $('#start_date').bootstrapMaterialDatePicker({weekStart: 0, time: false, minDate: new Date(),});
            }
        });

        $(document).on('click', '.delete_workingHours', function (e) {
            e.preventDefault();
            let dayIndex = $(this).data('index');
            let dayName = $(this).data('name');
            let startDate = $(this).data('start');
            let endDate = $(this).data('end');
            let clinic_id = $(this).data('clinic');
            $.ajax({
                url: URL + '/working-hours/get-deleted-reservations',
                type: 'POST',
                data: {
                    _token: token,
                    clinic_id: clinic_id,
                    dayName: dayName,
                    start_date: startDate,
                    end_date: endDate,
                }
            }).done(function (data) {
                let message = '';
                if (data.reservations === 0) {
                    message = 'Are you Sure you want to Delete this working hours';
                } else {
                    message = data.reservations + ' reservations will be canceled with SMS to each patient, are you sure you want to proceed ? !!!';
                }
                swal({
                        title: "Are you sure?",
                        text: message,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes, I'am sure!",
                        closeOnConfirm: false
                    },
                    function () {

                        // delete method
                        $.ajax({
                            url: URL + '/working-hours/reset',
                            type: 'DELETE',
                            data: {
                                _token: token,
                                clinic_id: clinic_id,
                                dayName: dayName,
                                day: dayIndex,
                                start_date: startDate,
                                end_date: endDate
                            }
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
        });

        // Add Breaks Times
        $(document).on('click', '.add-working-hours-breaks', function (e) {
            e.preventDefault();

            // get the names of the selected indexes
            let daysIndex = $('#breakDayIndex').val();
            if (!from || !to || !Array.isArray(daysIndex) || (daysIndex.length == 0)) {
                swal({
                    title: "Failure",
                    text: "Please fill all information to add Break",
                    type: "warning",
                });
            } else {
                $.ajax({
                    url: URL + '/working-hours/add-break',
                    type: 'POST',
                    data: {
                        _token: token,
                        from: $('#br_from').val(),
                        to: $('#br_to').val(),
                        deyIndexes: daysIndex,
                        clinic_id: $('#clinicID').val(),
                    }
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
                                text: data.err,
                                type: "error",
                            },
                            function () {
                                window.location.reload();
                            });
                    }
                });
            }
        });


        $(document).on('click', '.delete_workingHours_breaks', function (e) {
            e.preventDefault();
            let day = $(this).data('day');
            let updated_at = $(this).data('date');
            let clinic_id = $(this).data('clinic');
            $.ajax({
                url: URL + '/working-hours/delete-breaks',
                type: 'POST',
                data: {
                    _token: token,
                    clinic_id: clinic_id,
                    day: day,
                    updated_at: updated_at,
                }
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

    </script>
@endpush
