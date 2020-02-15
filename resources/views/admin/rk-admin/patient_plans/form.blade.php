<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="en_name">{{ trans('lang.en_name') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('en_name', NULL, ['class'=>'form-control ' . ($errors->has('en_name') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'en_name' ]) }}
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
                {{ Form::text('ar_name', NULL, ['class'=>'form-control ' . ($errors->has('ar_name') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'ar_name']) }}
                <small class="text-danger">{{ $errors->first('ar_name') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="price">{{ trans('lang.price') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::number('price', NULL, ['class'=>'form-control ' . ($errors->has('price') ? 'redborder' : '') , 'required'=>'required' ,'min' => 0, 'id'=>'price','step' =>'0.01']) }}
                <small class="text-danger">{{ $errors->first('price') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="points">{{ trans('lang.points') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::number('points', NULL, ['class'=>'form-control ' . ($errors->has('points') ? 'redborder' : '') , 'required'=>'required' ,'min' => 0, 'id'=>'points']) }}
                <small class="text-danger">{{ $errors->first('points') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="months">{{ trans('lang.months') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::select('months',['1' => trans('lang.one_month'),'6' => trans('lang.six_months'),'12' => trans('lang.one_year')] , null, ['class'=>'form-control ' . ($errors->has('months') ? 'redborder' : '') , 'required'=>'required', 'id'=>'months']) }}
                <small class="text-danger">{{ $errors->first('months') }}</small>
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
                {{ Form::textarea('en_desc', NULL, ['class'=>'form-control ' . ($errors->has('en_desc') ? 'redborder' : '')  , 'required'=>'required' , 'id'=>'en_desc' , 'rows'=>'2']) }}
                <small class="text-danger">{{ $errors->first('en_desc') }}</small>
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
                {{ Form::textarea('ar_desc', NULL, ['class'=>'form-control ' . ($errors->has('ar_desc') ? 'redborder' : ''), 'required'=>'required' , 'id'=>'ar_desc' , 'rows'=>'2']) }}
                <small class="text-danger">{{ $errors->first('ar_desc') }}</small>
            </div>
        </div>
    </div>
</div>

<a href="{{ url('/patient-plans') }}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>
{{  Form::submit($btn , ['class' => 'btn-loon ' . $classes ]) }}
