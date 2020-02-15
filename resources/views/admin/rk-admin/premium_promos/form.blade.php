<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="code">{{ trans('lang.code') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('code', NULL, ['class'=>'form-control ' . ($errors->has('code') ? 'redborder' : ''),'title' => trans('lang.only_english') , 'required'=>'required'  , 'id'=>'code']) }}
                <small class="text-danger">{{ $errors->first('code') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="discount_type">{{ trans('lang.discount_type') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::select('discount_type',[ 0 => 'money',1 => 'percentage'] , null,[ 'class'=>'form-control' . ($errors->has('discount_type') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'discount_type']) }}
                <small class="text-danger">{{ $errors->first('discount_type') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="discount">{{ trans('lang.discount') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::number('discount', NULL, ['class'=>'form-control ' . ($errors->has('discount') ? 'redborder' : ''),'title' => trans('lang.only_english') ,'step' => 0.01, 'required'=>'required'  , 'id'=>'discount']) }}
                <small class="text-danger">{{ $errors->first('discount') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="influencer_id">{{ trans('lang.influencers') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::select('influencer_id',(new \App\Http\Repositories\Web\InfluencersRepository())->getArrayOfInfluencers(), null,[ 'class'=>'form-control' . ($errors->has('influencer_id') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'influencer_id']) }}
                <small class="text-danger">{{ $errors->first('influencer_id') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="expiry_date">{{ trans('lang.expiry_date') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::date('expiry_date', ($promo_code->expiry_date) ?? null, ['class'=>'form-control' . ($errors->has('expiry_date') ? 'redborder' : '')  , 'id'=>'expiry_date', 'required' => 'required']) }}
                <small class="text-danger">{{ $errors->first('expiry_date') }}</small>
            </div>
        </div>
    </div>
</div>

{!! Form::submit($btn, ['class' => 'btn-loon ' . $classes ]) !!}
