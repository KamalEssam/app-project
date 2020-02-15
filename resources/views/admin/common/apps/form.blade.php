<div class="row">
      <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="unique_id">{{ trans('lang.unique_id') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('unique_id' , isset($apps) ? $apps->account->unique_id : null ,[ 'class'=>'form-control unique_id' . ($errors->has('unique_id') ? 'redborder' : '')  , 'id'=>'tags', 'autocomplete' => 'off' , isset($apps) ? 'disabled' : '' ]) }}
                <small class="text-danger">{{ $errors->first('unique_id') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
      <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="ios">{{ trans('lang.ios') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('ios', NULL, ['class'=>'form-control ' . ($errors->has('add_post') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'ios' ]) }}
                <small class="text-danger">{{ $errors->first('ios') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
      <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="android">{{ trans('lang.android') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('android', NULL, ['class'=>'form-control ' . ($errors->has('add_post') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'android' ]) }}
                <small class="text-danger">{{ $errors->first('android') }}</small>
            </div>
        </div>
    </div>
</div>
<a href="{{ url('/apps') }}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>

{!! Form::submit($btn, ['class' => 'btn-loon ' . $classes ]) !!}