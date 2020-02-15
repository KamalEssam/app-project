<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="en_name">{{ trans('lang.en_name') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('en_name', NULL, ['class'=>'form-control ' . ($errors->has('en_name') ? 'redborder' : '') ,'pattern' => $english_regex,'title' => trans('lang.only_english') , 'required'=>'required' , 'id'=>'en_name' ]) }}
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
                {{ Form::text('ar_name', NULL, ['class'=>'form-control ' . ($errors->has('ar_name') ? 'redborder' : '') ,'pattern' => $arabic_regex,'title' => trans('lang.only_arabic')  , 'required'=>'required' , 'id'=>'ar_name']) }}
                <small class="text-danger">{{ $errors->first('ar_name') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="en_condition">{{ trans('lang.en_condition') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::textarea('en_condition', NULL, ['class'=>'form-control ' . ($errors->has('en_condition') ? 'redborder' : '')  , 'id'=>'en_condition','novalidate']) }}
                <small class="text-danger en_condition_err">{{ $errors->first('en_condition') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="ar_condition">{{ trans('lang.ar_condition') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::textarea('ar_condition', NULL, ['class'=>'form-control ' . ($errors->has('ar_condition') ? 'redborder' : '')  , 'id'=>'ar_condition','novalidate']) }}
                <small class="text-danger ar_condition_err">{{ $errors->first('ar_condition') }}</small>
            </div>
        </div>
    </div>
</div>



<a href="{{ url('/policies') }}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>

{{  Form::submit($btn , ['class' => 'btn-loon ' . $classes ,'id' => 'policy_form']) }}
