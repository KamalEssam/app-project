<div class="row">
      <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="en_name">{{ trans('lang.en_name') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('en_name', NULL, ['class'=>'form-control ' . ($errors->has('en_name') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'en_name' ]) }}
                <small class="text-danger">{{ $errors->first('en_name') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
      <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="ar_name">{{ trans('lang.ar_name') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('ar_name', NULL, ['class'=>'form-control ' . ($errors->has('ar_name') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'ar_name']) }}
                <small class="text-danger">{{ $errors->first('ar_name') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
      <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="price_of_day">{{ trans('lang.price_of_day') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::number('price_of_day', NULL, ['pattern' => '\d{11}','class'=>'form-control ' . ($errors->has('price_of_day') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'price_of_day' ]) }}
                <small class="text-danger">{{ $errors->first('price_of_day') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
      <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="en_desc">{{ trans('lang.en_desc') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::textarea('en_desc', NULL, ['class'=>'form-control ' . ($errors->has('en_desc') ? 'redborder' : '')  , 'required'=>'required' , 'id'=>'en_desc' , 'rows'=>'2']) }}
                <small class="text-danger">{{ $errors->first('en_desc') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
      <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="ar_desc">{{ trans('lang.ar_desc') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::textarea('ar_desc', NULL, ['class'=>'form-control ' . ($errors->has('ar_desc') ? 'redborder' : ''), 'required'=>'required' , 'id'=>'ar_desc' , 'rows'=>'2']) }}
                <small class="text-danger">{{ $errors->first('ar_desc') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="no_of_clinics">{{ trans('lang.no_of_clinics') }}</label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::select('no_of_clinics', [ 3 => "Doctor plan" , 0 => "Center plan"] , null,[ 'class'=>'form-control' . ($errors->has('no_of_clinics') ? 'redborder' : '') , 'required'=>'required', 'id'=>'no_of_clinics']) }}
                    <small class="text-danger">{{ $errors->first('no_of_clinics') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="{{ url('/plans') }}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>
{{  Form::submit($btn , ['class' => 'btn-loon ' . $classes ]) }}