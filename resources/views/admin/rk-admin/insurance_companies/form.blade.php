<div class="row">
      <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="en_name">{{ trans('lang.en_name') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('en_name', NULL, ['class'=>'form-control ' . ($errors->has('add_post') ? 'redborder' : ''),'pattern' => $english_regex,'title' => trans('lang.only_english') , 'required'=>'required'  , 'id'=>'en_name']) }}
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
                {{ Form::text('ar_name', NULL, ['class'=>'form-control ' . ($errors->has('add_post') ? 'redborder' : ''),'pattern' => $arabic_regex,'title' => trans('lang.only_arabic') , 'required'=>'required'  , 'id'=>'ar_name' ]) }}
                <small class="text-danger">{{ $errors->first('ar_name') }}</small>
            </div>
        </div>
    </div>
</div>

<a href="{{ url('/marketing/insurance_company') }}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>

{!! Form::submit($btn, ['class' => 'btn-loon ' . $classes ]) !!}
