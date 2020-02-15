<div class="row">
    <div class="form-group col-md-10">
        <div class="row">
            <div class="col-md-2 label-form">
                <label for="clinic_name">{{trans('lang.clinic_name')}}<span class="astric">*</span></label>
            </div>
            <div class="col-md-10 form-input">
                {{ Form::text('clinic_id' ,$clinic->en_name , ['class'=>'form-control ' . ($errors->has('clinic_id') ? 'redborder' : ''), 'disabled'=>'disabled' ]) }}
                <small class="text-danger">{{ $errors->first('clinic_name') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-10">
        <div class="row">
            <div class="col-md-2 label-form">
                <label for="from">{{trans('lang.from')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-10 form-input">
                {{ Form::text('from', isset($working_hours_from) ? $working_hours_from : null, ['class'=>'time form-control ' . ($errors->has('from') ? 'redborder' : ''),'required'=>'required' , 'id'=>'time-from']) }}
                <small class="text-danger">{{ $errors->first('from') }}</small>

               {{-- <div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
                    <input type="text" class="form-control" name="from" value="13:14">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                </div>--}}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-10">
        <div class="row">
            <div class="col-md-2 label-form">
                <label for="to">{{trans('lang.to')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-10 form-input">
                {{ Form::text('to', isset($working_hours_to) ? $working_hours_to : null, ['class'=>'time form-control ' . ($errors->has('to') ? 'redborder' : ''),'required'=>'required' , 'id'=>'time-to']) }}
                <small class="text-danger">{{ $errors->first('to') }}</small>
            </div>
        </div>
    </div>
</div>
@if($clinic->pattern == 0)
    <div class="row">
        <div class="form-group col-md-10">
            <div class="row">
                <div class="col-md-2 label-form">
                    <label for="interval">{{trans('lang.interval_per_minutes')}} <span class="astric">*</span></label>
                </div>
                <div class="col-md-10 form-input">
                    {{ Form::number('interval', $clinic->avg_reservation_time , [ 'class'=>'form-control ' . ($errors->has('interval') ? 'redborder' : ''),'required'=>'required', 'id'=>'interval', 'disabled'=>'disabled' ]) }}
                    <small class="text-danger">{{ $errors->first('interval_per_minutes') }}</small>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row">
    <div class="form-group col-md-10">
        <div class="row">
            <div class="col-md-2 label-form">
                <label for="day">{{trans('lang.day')}}<span class="astric">*</span></label>
            </div>
            <div class="col-md-10 form-input">
                {{ Form::text('day_name', $clinic ? $clinic->day_name : null , ['class'=>' form-control ' . ($errors->has('day_name') ? 'redborder' : ''), 'disabled'=>'disabled']) }}
                <small class="text-danger">{{ $errors->first('day') }}</small>
            </div>
        </div>
    </div>
</div>

{{  Form::hidden('clinic_id' , $clinic->id) }}
{{  Form::hidden('day' , $clinic->day) }}

<a href="{{ url('/working-hours?clinic='. $clinic->id)}}"
   class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>

{{  Form::submit($btn , ['class' => 'btn-loon ' . $classes]) }}


