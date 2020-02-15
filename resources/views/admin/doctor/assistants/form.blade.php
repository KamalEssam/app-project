<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="name">{{trans('lang.name')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('name', null, ['class'=>'form-control ' . ($errors->has('name') ? 'redborder' : '') ,'required'=>'required', 'id'=>'name','placeholder' =>trans('lang.enter_name') ]) }}
                <small class="text-danger">{{ $errors->first('name') }}</small>
            </div>
        </div>
    </div>
</div>

@if(!isset($assistant))
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="email">{{trans('lang.email')}} <span class="astric">*</span></label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::email('email', null, ['class'=>'form-control email-validate ' . ($errors->has('email') ? 'redborder' : '') ,'required'=>'required', 'id'=>'email','placeholder' =>trans('lang.enter_email')]) }}
                    <small class="text-danger">{{ $errors->first('email') }}</small>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="mobile">{{ trans('lang.mobile') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('mobile', NULL, ['minlength'=>11 , 'class'=>'form-control ' . ($errors->has('add_post') ? 'redborder' : '') , 'required'=>'required' ,isset($assistant) ? 'disabled' : '', 'id'=>'mobile','pattern' => '(01)[0-9]{9}','placeholder' => trans('lang.enter_mobile')]) }}
                <small class="text-danger">{{ $errors->first('mobile') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label
                    for="clinic_id">{{ ($auth->account->type == 0) ?  trans('lang.clinic_address') : trans('lang.clinic_name') }}
                    <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::select('clinic_id', \App\Models\Clinic::where('account_id', auth()->user()->account_id)->pluck( App::getLocale() . '_address' ,'id') , null, ['class'=>'form-control ' . ($errors->has('clinic_id') ? 'redborder' : '') , 'required'=>'required', 'id'=>'clinic_id','placeholder' => ($auth->account->type == 0) ?  trans('lang.clinic_address') : trans('lang.clinic_name')]) }}
                <small class="text-danger">{{ $errors->first('clinic_id') }}</small>
            </div>
        </div>
    </div>
</div>

<input id="create-assistant" type="submit" value="{{ trans('lang.save') }}"
       class="btn-loon btn-xs pull-right">
