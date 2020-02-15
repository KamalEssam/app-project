<div class="row">
    <div class="form-group col-xs-12 has-float-label">
        {{ Form::text('email' , null,[ 'class'=>'form-control email' . ($errors->has('email') ? 'redborder' : '')  , 'id'=>'tags', 'autocomplete' => 'off' ]) }}
        <label for="email">{{ trans('lang.email') }}<span class="astric">*</span></label>
        <small class="text-danger">{{ $errors->first('email') }}</small>
    </div>
</div>


<div class="row">
    <div class="form-group col-xs-12 has-float-label">
        {{ Form::text('user_name',  null,[ 'class'=>'form-control name' . ($errors->has('user_name') ? 'redborder' : '')  , 'id'=>'name' ]) }}
        <label for="user_name">{{ trans('lang.name') }} <span class="astric">*</span></label>
        <small class="text-danger">{{ $errors->first('user_name') }}</small>
    </div>
</div>