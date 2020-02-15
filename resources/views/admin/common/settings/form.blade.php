{{--  Language --}}
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="type"> {{trans('lang.lang')}}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::select('lang', ['en' => trans('lang.english'), 'ar' => trans('lang.arabic')], app()->getLocale(), ['class'=>'form-control ' . ($errors->has('lang') ? 'redborder' : '') , 'id'=>'lang']) }}
                <small class="text-danger">{{ $errors->first('lang') }}</small>
            </div>
        </div>
    </div>
</div>

{{--  Notification switch  --}}
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="type"> {{trans('lang.notifications-switched')}}</label>
            </div>
            <div class="col-md-12 form-input">
                <label class="switch">
                    {{ Form::checkbox('is_notification', 1,(isset($auth->is_notification) && $auth->is_notification == 1) ? true :  false , ['class'=>'no-margin'])  }}
                    <span class="slider round"></span>
                </label>
                <small class="text-danger">{{ $errors->first('is_notification') }}</small>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('admin')}}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>
{{  Form::submit($btn , ['class' => 'btn-loon ' . $classes ]) }}
