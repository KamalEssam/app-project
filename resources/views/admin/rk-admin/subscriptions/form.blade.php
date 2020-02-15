<div class="row">
    <div class="form-group col-md-112">
        <div class="col-md-12 label-form">
            <label for="news">{{ trans('lang.news') }}<span class="astric">*</span></label>
        </div>
        <div class="col-md-12 form-input">
            {{ Form::textarea('news', NULL, ['class'=>'form-control ' . ($errors->has('news') ? 'redborder' : '') , 'id'=>'mytextarea']) }}
            <small class="text-danger">{{ $errors->first('news') }}</small>
        </div>
    </div>
</div>

{!! Form::submit($btn, ['class' => 'btn-loon ' . $classes ]) !!}