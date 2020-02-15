{{--//show clinic data--}}
<div class="jumbotron">
    <ul class="list-unstyled">
        <li>
            <span class="bolder loon"> {{ trans('lang.address') .' '.':'.' ' }}</span>
            <span id="address">{{ Super::getProperty( $clinic[ App::getLocale() . '_address' ])  }}</span>

        </li>
        <li>
            <span class="bolder loon"> {{ trans('lang.mobile') .' '.':'.' ' }}</span>
            <span id="mobile">{{ Super::getProperty( $clinic->mobile )  }}</span>

        </li>
        <li>
            <span class="bolder loon"> {{ trans('lang.pattern') .' '.':'.' ' }}</span>
            <span id="pattern">{{ $clinic->pattern == 0 ? trans('lang.intervals')  : trans('lang.queue')  }}</span>
        </li>

    </ul>
</div>
{{--
//update account data
--}}
<div class="row">
    <div class="form-group col-md-10">
        <div class="row">
            <div class="col-md-3 label-form">
                <label for="avg_reservation_time">{{ trans('lang.avg_reservation_time_in_minutes') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-9">
                {{ Form::number('avg_reservation_time', NULL, ['min' => '1', 'class'=>'form-control ' . ($errors->has('avg_reservation_time') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'avg_reservation_time']) }}
                <small class="text-danger">{{ $errors->first('avg_reservation_time') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-10">
        <div class="row">
            <div class="col-md-3 label-form">
                <label for="mobile">{{ trans('lang.reservation_deadline') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-9">
                {{ Form::date('reservation_deadline', isset($clinic) ? \Carbon\Carbon::now()->addDays($clinic->reservation_deadline) : null, ['class'=>'form-control date' . ($errors->has('reservation_deadline') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'day']) }}
                <small class="text-danger">{{ $errors->first('reservation_deadline') }}</small>
            </div>
        </div>
    </div>
</div>

{{  Form::submit($btn , ['class' => 'btn-loon ' . $classes ]) }}