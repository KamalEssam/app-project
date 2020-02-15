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
                <label for="email">{{ trans('lang.email') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::email('email', NULL, ['class'=>'form-control ' . ($errors->has('add_post') ? 'redborder' : '')  , 'required'=>'required' , 'id'=>'email']) }}
                <small class="text-danger">{{ $errors->first('email') }}</small>
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
                {{ Form::text('mobile', NULL, ['minlength'=>11 , 'class'=>'form-control ' . ($errors->has('mobile') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'mobile','pattern' => '(01)[0-9]{9}']) }}
                <small class="text-danger">{{ $errors->first('mobile') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="type"> {{trans('lang.gender')}}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::select('gender', ['0' => 'male', '1' => 'female'], null, ['class'=>'form-control ' . ($errors->has('gender') ? 'redborder' : '') , 'id'=>'gender']) }}
                <small class="text-danger">{{ $errors->first('gender') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="birthday">{{trans('lang.birthday')}}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::date('birthday', null , ['class'=>'form-control date' . ($errors->has('birthday') ? 'redborder' : '') , 'id'=>'date-profile']) }}
                <small class="text-danger">{{ $errors->first('birthday') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="address">{{trans('lang.address')}}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::textarea('address', null, ['class'=>'form-control ' . ($errors->has('address') ? 'redborder' : '') , 'id'=>'address' ,'rows' => 3]) }}
                <small class="text-danger">{{ $errors->first('address') }}</small>
            </div>
        </div>
    </div>
</div>


<a href="{{ url('/sales') }}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>
{!! Form::submit($btn, ['class' => 'btn-loon ' . $classes ]) !!}