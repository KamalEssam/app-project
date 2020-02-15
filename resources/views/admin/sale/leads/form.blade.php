<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="name">{{ trans('lang.name') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('name', NULL, ['class'=>'form-control ' . ($errors->has('name') ? 'redborder' : ''),'required'=>'required'  , 'id'=>'name']) }}
                <small class="text-danger">{{ $errors->first('name') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="mobile">{{ trans('lang.mobile') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('mobile', NULL, ['class'=>'form-control ' . ($errors->has('mobile') ? 'redborder' : ''),'required'=>'required'  , 'id'=>'mobile']) }}
                <small class="text-danger">{{ $errors->first('mobile') }}</small>
            </div>
        </div>
    </div>
</div>
<a href="{{ url('sale/leads') }}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>

{!! Form::submit($btn, ['class' => 'btn-loon ' . $classes ]) !!}
