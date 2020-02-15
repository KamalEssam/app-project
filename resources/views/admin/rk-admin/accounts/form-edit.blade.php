@if(isset($user->image))
    <div class=" row text-center">
        <img src="{{ $user->image }}" alt="image"
             style="width:50px; height: 50px ;margin-bottom: 20px">
    </div>
@endif
<div class="jumbotron">
    <ul class="list-unstyled">
        <li>
            <span class="bolder loon"> {{ trans('lang.email') .' '.':'.' ' }}</span>
            <span id="username">{{ Super::getProperty( $user->email)  }}</span>

        </li>
        <li>
            <span class="bolder loon"> {{ trans('lang.name') .' '.':'.' ' }}</span>
            <span id="username">{{ Super::getProperty( $user->name)  }}</span>

        </li>
        <li>
            <span class="bolder loon"> {{ trans('lang.unique_id') .' '.':'.' ' }}</span>
            <span id="username">{{ Super::getProperty( $account->unique_id )  }}</span>

        </li>
        <li>
            <span class="bolder loon"> {{ trans('lang.due_amount') .' '.':'.' ' }}</span>
            <span id="username">{{ Super::getProperty( $account->due_amount )  }}</span>

        </li>
        <li>
            <span class="bolder loon"> {{ trans('lang.due_date') .' '.':'.' ' }}</span>
            <span id="username">{{ Super::getProperty( $account->due_date )  }}</span>

        </li>
        <li>
            <span class="bolder loon"> {{ trans('lang.mobile') .' '.':'.' ' }}</span>
            <span id="username">{{ Super::getProperty( $user->mobile )  }}</span>

        </li>
        <li>
            <span class="bolder loon"> {{ trans('lang.country_name') .' '.':'.' ' }}</span>
            <span id="username">{{ Super::getProperty($account->country[App::getLocale() . '_name']) }}</span>

        </li>
        <li>
            <span class="bolder loon"> {{ trans('lang.city_name') .' '.':'.' ' }}</span>
            <span id="username">{{ Super::getProperty($account->city[App::getLocale() . '_name']) }}</span>

        </li>
        <li>
            <span class="bolder"> {{ trans('lang.plan_name') .' '.':'.' ' }}</span>
            <span id="username">{{ Super::getProperty($account->plan[App::getLocale() . '_name']) }}</span>

        </li>
        <li>
            <span class="bolder"> {{ trans('lang.created_by') .' '.':'.' ' }}</span>
            <span id="username">{{ Super::getProperty($created_by) }}</span>
        </li>
        <li>
            <span class="bolder loon"> {{ trans('lang.updated_by') .' '.':'.' ' }}</span>
            <span id="username">{{Super::getProperty( $updated_by) }}</span>
        </li>
    </ul>
</div>
{{--
//update account data
--}}
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="name">{{ trans('lang.name') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12">
                {{ Form::text('name', NULL, ['class'=>'form-control ' . ($errors->has('add_post') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'name']) }}
                <small class="text-danger">{{ $errors->first('name') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="mobile">{{ trans('lang.mobile') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12">
                {{ Form::text('mobile', NULL, ['minlength'=>11 , 'class'=>'form-control ' . ($errors->has('mobile') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'mobile']) }}
                <small class="text-danger">{{ $errors->first('mobile') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="plan_id">{{ trans('lang.plan_name') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12">
                {{ Form::select('plan_id', $plans->pluck( App::getLocale() . '_name' ,'id') , null,[ 'class'=>'form-control' . ($errors->has('plan_id') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'plan_id' ]) }}
                <small class="text-danger">{{ $errors->first('plan_id') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="type"> {{ trans('lang.days') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12">
                {{ Form::select('days', [ 30 => 'Month', 180 => '6 Months' , 365 => '12 Months'], null, ['class'=>'form-control ' . ($errors->has('days') ? 'redborder' : '') , 'required'=>'required' , 'id'=>'days' ]) }}
                <small class="text-danger">{{ $errors->first('days') }}</small>
            </div>
        </div>
    </div>
</div>


<a href="{{ url('/accounts') }}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>

{{  Form::submit($btn , ['class' => 'btn-loon ' . $classes ]) }}