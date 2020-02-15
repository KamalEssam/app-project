@if (isset($speciality))
    <div class="row text-center">
        <img src="{{ asset($speciality->image) }}" alt="image"
             style="width:60px; height: 60px ;margin-bottom: 20px">
    </div>
@endif

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="image"> {{ trans('lang.image') }}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::file('image', ['class'=>'form-control ' . ($errors->has('image') ? 'redborder' : '') , 'id'=>'image' ,(!isset($speciality)) ? 'required' : '' ]) }}
                <small class="text-danger">{{ $errors->first('image') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="en_speciality">{{trans('lang.en_speciality')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('en_speciality', null, ['class'=>'form-control ' . ($errors->has('en_speciality') ? 'redborder' : '') ,'pattern' => $english_regex,'title' => trans('lang.only_english'), 'required'=>'required' , 'id'=>'en_speciality']) }}
                <small class="text-danger">{{ $errors->first('en_speciality') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="ar_speciality">{{trans('lang.ar_speciality')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('ar_speciality', null, ['class'=>'form-control ' . ($errors->has('ar_speciality') ? 'redborder' : '') ,'pattern' => $arabic_regex,'title' => trans('lang.only_arabic'), 'required'=>'required', 'id'=>'ar_speciality']) }}
                <small class="text-danger">{{ $errors->first('ar_speciality') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="is_featured">{{trans('lang.is_featured')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12">
                {{ Form::checkbox('is_featured', 1,(isset($clinic->is_featured) && $clinic->is_featured == 1)) }}
                <small class="text-danger">{{ $errors->first('is_featured') }}</small>
            </div>
        </div>
    </div>
</div>

<a href="{{ url('/specialities') }}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>

{!! Form::submit($btn, ['class' => 'btn-loon ' . $classes ,'id' => 'speciality_form']) !!}
