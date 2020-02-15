<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="en_name">{{trans('lang.en_name')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('en_name', null, ['class'=>'form-control ' . ($errors->has('en_name') ? 'redborder' : '') ,'required'=>'required', 'id'=>'en_name']) }}
                <small class="text-danger">{{ $errors->first('en_name') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="ar_name">{{trans('lang.ar_name')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('ar_name', null, ['class'=>'form-control ' . ($errors->has('ar_name') ? 'redborder' : '') ,'required'=>'required', 'id'=>'ar_name']) }}
                <small class="text-danger">{{ $errors->first('ar_name') }}</small>
            </div>
        </div>
    </div>
</div>
{{  Form::submit($btn , ['class' => 'btn-loon ' . $classes ]) }}
