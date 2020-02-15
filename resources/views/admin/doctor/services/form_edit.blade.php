<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="en_name">{{trans('lang.en_name')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::number('price', null, ['class'=>'form-control ' . ($errors->has('en_name') ? 'redborder' : '') ,'required'=>'required','min' => '0', 'id'=>'price', 'step'=>'0.01']) }}
                <small class="text-danger">{{ $errors->first('en_name') }}</small>
            </div>
        </div>
    </div>
</div>

@if ($auth->is_premium == 1)
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="ar_name">{{trans('lang.ar_name')}} <span class="astric">*</span></label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::number('premium_price', null, ['class'=>'form-control ' . ($errors->has('ar_name') ? 'redborder' : '') ,'min' => '0','required'=>'required','id'=>'ar_name', 'step'=>'0.01']) }}
                    <small class="text-danger">{{ $errors->first('ar_name') }}</small>
                </div>
            </div>
        </div>
    </div>
@endif

{{  Form::submit($btn , ['class' => 'btn-loon ' . $classes ]) }}
