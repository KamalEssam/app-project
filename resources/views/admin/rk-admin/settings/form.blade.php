{{-- Generals --}}
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="email">{{trans('lang.email')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::email('email', null, ['class'=>'form-control ' . ($errors->has('email') ? 'redborder' : '') , 'id'=>'email']) }}
                <small class="text-danger">{{ $errors->first('email') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="mobile">{{trans('lang.mobile')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('mobile', null, ['class'=>'form-control ' . ($errors->has('mobile') ? 'redborder' : '') , 'id'=>'mobile','pattern' => '(01)[0-9]{9}']) }}
                <small class="text-danger">{{ $errors->first('mobile') }}</small>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="min_featured_stars">{{trans('lang.min_featured_stars')}} <span
                        class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::number('min_featured_stars', null, ['class'=>'form-control ' . ($errors->has('min_featured_stars') ? 'redborder' : '') , 'id'=>'min_featured_stars','min' => 1]) }}
                <small class="text-danger">{{ $errors->first('min_featured_stars') }}</small>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="website">{{trans('lang.website')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('website', null, ['class'=>'form-control ' . ($errors->has('website') ? 'redborder' : '') , 'id'=>'website']) }}
                <small class="text-danger">{{ $errors->first('website') }}</small>
            </div>
        </div>
    </div>
</div>

{{--socials--}}

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="facebook">{{trans('lang.facebook')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('facebook', null, ['class'=>'form-control ' . ($errors->has('facebook') ? 'redborder' : '') , 'id'=>'facebook']) }}
                <small class="text-danger">{{ $errors->first('facebook') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="twitter">{{trans('lang.twitter')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('twitter', null, ['class'=>'form-control ' . ($errors->has('twitter') ? 'redborder' : '') , 'id'=>'twitter']) }}
                <small class="text-danger">{{ $errors->first('twitter') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="youtube">{{trans('lang.youtube')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('youtube', null, ['class'=>'form-control ' . ($errors->has('youtube') ? 'redborder' : '') , 'id'=>'youtube']) }}
                <small class="text-danger">{{ $errors->first('youtube') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="googlepluse">{{trans('lang.googlepluse')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('googlepluse', null, ['class'=>'form-control ' . ($errors->has('googlepluse') ? 'redborder' : '') , 'id'=>'googlepluse']) }}
                <small class="text-danger">{{ $errors->first('googlepluse') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="instagram">{{trans('lang.instagram')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('instagram', null, ['class'=>'form-control ' . ($errors->has('instagram') ? 'redborder' : '') , 'id'=>'instagram']) }}
                <small class="text-danger">{{ $errors->first('instagram') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="en_about_us">{{ trans('lang.en_about_us') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12">
                {{ Form::textarea('en_about_us', NULL, ['class'=>'form-control ' . ($errors->has('en_about_us') ? 'redborder' : '')  , 'required'=>'required' , 'id'=>'en_about_us']) }}
                <small class="text-danger">{{ $errors->first('en_about_us') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="ar_about_us">{{ trans('lang.ar_about_us') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12">
                {{ Form::textarea('ar_about_us', NULL, ['class'=>'form-control ' . ($errors->has('ar_about_us') ? 'redborder' : '')  , 'required'=>'required' , 'id'=>'ar_about_us']) }}
                <small class="text-danger">{{ $errors->first('ar_about_us') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-9 label-form">
                <label for="doctors">{{ trans('lang.doctors') }}<span class="astric">*</span></label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 form-input">
                {{ Form::select('doctors[]' ,(new \App\Http\Repositories\Web\AuthRepository())->getAllDoctors(),get_test_users('doctor'),[ 'class'=>'form-control chosen-select ' . ($errors->has('doctor_id') ? 'redborder' : '') ,'id' => 'form-field-select-2','multiple']) }}
                <small class="text-danger">{{ $errors->first('doctors') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-9 label-form">
                <label for="patients">{{ trans('lang.patients') }}<span class="astric">*</span></label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 form-input">
                {{ Form::select('patients[]' ,(new \App\Http\Repositories\Web\AuthRepository())->getAllPatientsByEmails(),get_test_users('patient'),[ 'class'=>'form-control chosen-select ' . ($errors->has('doctor_id') ? 'redborder' : '') ,'id' => 'form-field-select-2','multiple']) }}
                <small class="text-danger">{{ $errors->first('patients') }}</small>
            </div>
        </div>
    </div>
</div>


{{--  Notification switch  --}}
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="type"> {{trans('lang.debug_mode')}}</label>
            </div>
            <div class="col-md-12 form-input">
                <label class="switch">
                    {{ Form::checkbox('debug_mode', 1 ,NULL, ['class'=>'no-margin'])  }}
                    <span class="slider round"></span>
                </label>
                <small class="text-danger">{{ $errors->first('debug_mode') }}</small>
            </div>
        </div>
    </div>
</div>


<a href="{{ route('admin')}}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>

{{  Form::submit($btn , ['class' => 'btn-loon ' . $classes ]) }}


@push('more-scripts')
    <script>
        $(".chosen-select").chosen();
    </script>
@endpush
