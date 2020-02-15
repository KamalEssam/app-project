@if (isset($offerCategory))
    <div class="row text-center">
        <img src="{{ asset($offerCategory->image) }}" alt="image"
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
                {{ Form::file('image', ['class'=>'form-control ' . ($errors->has('image') ? 'redborder' : '') , 'id'=>'image' ,(!isset($offerCategory)) ? 'required' : '' ]) }}
                <small class="text-danger">{{ $errors->first('image') }}</small>
                <p class="help-block red"><b>Note:</b> the image must be 65px X 65px and height must be equal
                    width </p>
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
                <label for="speciality_id">{{ trans('lang.speciality_name') }}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::select('speciality_id',\App\Models\Speciality::pluck(app()->getLocale(). '_speciality' ,'id') , NULL,[ 'class'=>'form-control' . ($errors->has('speciality_id') ? 'redborder' : '')  , 'id'=>'speciality_id','placeholder'=> 'select speciality','required' => 'required']) }}
                <small class="text-danger">{{ $errors->first('speciality_id') }}</small>
            </div>
        </div>
    </div>
</div>

{!! Form::submit($btn, ['class' => 'btn-loon ' . $classes ]) !!}
