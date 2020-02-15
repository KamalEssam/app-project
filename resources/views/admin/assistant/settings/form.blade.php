{{--reservation_message--}}
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="en_reservation_message">{{trans('lang.en_reservation_message')}}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::textarea('en_reservation_message', null, ['class'=>'form-control ' . ($errors->has('en_reservation_message') ? 'redborder' : '') , 'id'=>'en_reservation_message', 'rows'=> 2]) }}
                <small class="text-danger">{{ $errors->first('en_reservation_message') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="ar_reservation_message">{{trans('lang.ar_reservation_message')}}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::textarea('ar_reservation_message', null, ['class'=>'form-control ' . ($errors->has('ar_reservation_message') ? 'redborder' : '') , 'id'=>'ar_reservation_message', 'rows'=> 2]) }}
                <small class="text-danger">{{ $errors->first('ar_reservation_message') }}</small>
            </div>
        </div>
    </div>
</div>

{{--socials--}}
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="website">{{trans('lang.website')}}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('website', null, ['class'=>'form-control ' . ($errors->has('website') ? 'redborder' : '') , 'id'=>'website']) }}
                <small class="text-danger">{{ $errors->first('website') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="facebook">{{trans('lang.facebook')}}</label>
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
                <label for="twitter">{{trans('lang.twitter')}}</label>
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
                <label for="linkedin">{{trans('lang.linkedin')}}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('linkedin', null, ['class'=>'form-control ' . ($errors->has('linkedin') ? 'redborder' : '') , 'id'=>'linkedin']) }}
                <small class="text-danger">{{ $errors->first('linkedin') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="youtube">{{trans('lang.youtube')}}</label>
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
                <label for="googlepluse">{{trans('lang.googlepluse')}}</label>
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
                <label for="instagram">{{trans('lang.instagram')}}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('instagram', null, ['class'=>'form-control ' . ($errors->has('instagram') ? 'redborder' : '') , 'id'=>'instagram']) }}
                <small class="text-danger">{{ $errors->first('instagram') }}</small>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('admin')}}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>

{{  Form::submit($btn , ['class' => 'btn-loon ' . $classes ]) }}
