@if (isset($product))
    <div class="row text-center">
        <img src="{{ asset($product->image) }}" alt="image"
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
                {{ Form::file('image', ['class'=>'form-control ' . ($errors->has('image') ? 'redborder' : '') , 'id'=>'image' ,(!isset($product)) ? 'required' : '' ]) }}
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
                <label for="en_title">{{ trans('lang.en_title') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('en_title', NULL, ['class'=>'form-control ' . ($errors->has('ar_title') ? 'redborder' : ''),'pattern' => $english_regex,'title' => trans('lang.only_english') , 'required'=>'required'  , 'id'=>'en_title' ]) }}
                <small class="text-danger">{{ $errors->first('en_title') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="ar_title">{{ trans('lang.ar_title') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('ar_title', NULL, ['class'=>'form-control ' . ($errors->has('ar_title') ? 'redborder' : ''),'pattern' => $arabic_regex,'title' => trans('lang.only_arabic') , 'required'=>'required'  , 'id'=>'ar_title' ]) }}
                <small class="text-danger">{{ $errors->first('ar_title') }}</small>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-9 label-form">
                <label for="market_place_category_id">{{ trans('lang.category') }}<span class="astric">*</span></label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 form-input">
                {{ Form::select('market_place_category_id' ,(new \App\Http\Repositories\Web\MarketPlaceCategoryRepository())->getCategoriesList(),null,[ 'class'=>'form-control chosen-select ' . ($errors->has('market_place_category_id') ? 'redborder' : '') ,'required' => 'required']) }}
                <small class="text-danger">{{ $errors->first('market_place_category_id') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="en_desc">{{ trans('lang.en_desc') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::textarea('en_desc', null, ['class'=>'form-control ' . ($errors->has('en_desc') ? 'redborder' : '')  , 'required'=>'required' ,'rows' => 5, 'id'=>'en_desc']) }}
                <small class="text-danger en_desc_err">{{ $errors->first('en_desc') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="ar_desc">{{ trans('lang.ar_desc') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::textarea('ar_desc', null, ['class'=>'form-control ' . ($errors->has('ar_desc') ? 'redborder' : '')  , 'required'=>'required' ,'rows' => 5, 'id'=>'ar_desc']) }}
                <small class="text-danger ar_desc_err">{{ $errors->first('ar_desc') }}</small>
            </div>
        </div>
    </div>
</div>

{{--<div class="row">--}}
{{--    <div class="form-group col-md-12">--}}
{{--        <div class="row">--}}
{{--            <div class="col-md-12 label-form">--}}
{{--                <label for="points">{{ trans('lang.points') }}<span class="astric">*</span></label>--}}
{{--            </div>--}}
{{--            <div class="col-md-12 form-input">--}}
{{--                {{ Form::number('points', NULL, ['class'=>'form-control ' . ($errors->has('points') ? 'redborder' : '') , 'required'=>'required' ,'min' => 0, 'id'=>'points']) }}--}}
{{--                <small class="text-danger">{{ $errors->first('points') }}</small>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}


<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="price">{{ trans('lang.price') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::number('price', NULL, ['class'=>'form-control ' . ($errors->has('price') ? 'redborder' : '') , 'required'=>'required' ,'min' => 0, 'id'=>'price']) }}
                <small class="text-danger">{{ $errors->first('price') }}</small>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="max_redeems">{{ trans('lang.max_redeemers') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::number('max_redeems', NULL, ['class'=>'form-control ' . ($errors->has('max_redeems') ? 'redborder' : '') , 'required'=>'required' ,'min' => 0, 'id'=>'max_redeems']) }}
                <small class="text-danger">{{ $errors->first('max_redeems') }}</small>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="redeem_expiry_days">{{ trans('lang.redeem_expiry_days') }}<span
                        class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::number('redeem_expiry_days', NULL, ['class'=>'form-control ' . ($errors->has('redeem_expiry_days') ? 'redborder' : '') , 'required'=>'required' ,'min' => 1, 'id'=>'redeem_expiry_days']) }}
                <small class="text-danger">{{ $errors->first('redeem_expiry_days') }}</small>
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
                    {{ Form::checkbox('is_active', 1,isset($product->is_active) ? ($product->is_active == 1 ? true: false) : true , ['class'=>'no-margin'])  }}
                    <span class="slider round"></span>
                </label>
                <small class="text-danger">{{ $errors->first('is_active') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="brand_id">{{ trans('lang.brands') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::select('brand_id', $brands , null,[ 'class'=>'form-control' . ($errors->has('brand_id') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'brand_id']) }}
                <small class="text-danger">{{ $errors->first('brand_id') }}</small>
            </div>
        </div>
    </div>
</div>

<a href="{{ url('market-place/product') }}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>

{!! Form::submit($btn, ['class' => 'btn-loon ' . $classes ]) !!}
