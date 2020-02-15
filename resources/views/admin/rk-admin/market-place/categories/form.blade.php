@if (isset($category))
    <div class="row text-center">
        <img src="{{ asset($category->image) }}" alt="image"
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
                {{ Form::file('image', ['class'=>'form-control ' . ($errors->has('image') ? 'redborder' : '') , 'id'=>'image' ,(!isset($category)) ? 'required' : '' ]) }}
                <small class="text-danger">{{ $errors->first('image') }}</small>
                <p class="help-block red"><b>Note:</b> the image must have at least 500px X 500px and height must be
                    equal width </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="en_name">{{ trans('lang.en_name') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('en_name', NULL, ['class'=>'form-control ' . ($errors->has('add_post') ? 'redborder' : ''),'pattern' => $english_regex,'title' => trans('lang.only_english') , 'required'=>'required'  , 'id'=>'en_name']) }}
                <small class="text-danger">{{ $errors->first('en_name') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="ar_name">{{ trans('lang.ar_name') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('ar_name', NULL, ['class'=>'form-control ' . ($errors->has('add_post') ? 'redborder' : ''),'pattern' => $arabic_regex,'title' => trans('lang.only_arabic') , 'required'=>'required'  , 'id'=>'ar_name' ]) }}
                <small class="text-danger">{{ $errors->first('ar_name') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="is_active">{{ trans('lang.is_active') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                <label class="switch">
                    {{ Form::checkbox('is_active', 1,isset($category->is_active) ? ($category->is_active == 1) : true , ['class'=>'no-margin'])  }}
                    <span class="slider round"></span>
                </label>
                <small class="text-danger">{{ $errors->first('is_active') }}</small>
            </div>
        </div>
    </div>
</div>

<a href="{{ url('/marketing/category') }}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>
{!! Form::submit($btn, ['class' => 'btn-loon ' . $classes ]) !!}
