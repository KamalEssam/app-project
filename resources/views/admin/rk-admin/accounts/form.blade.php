<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="email">{{ trans('lang.email') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::email('email', NULL, ['class'=>'form-control ' . ($errors->has('add_post') ? 'redborder' : '')  , 'required'=>'required' , 'id'=>'email']) }}
                <small class="text-danger">{{ $errors->first('email') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="name">{{ trans('lang.name') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
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
                <label for="en_name">{{trans('lang.en_name')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12">
                {{ Form::text('en_name', null, ['class'=>'form-control ' . ($errors->has('en_name') ? 'redborder' : ''),'pattern' => $english_regex,'title' => trans('lang.only_english') ,'required'=>'required', 'id'=>'en_name','placeholder' => 'clinic name in english' ]) }}
                <small class="text-danger">{{ $errors->first('en_name') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="ar_name">{{trans('lang.ar_name')}} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12">
                {{ Form::text('ar_name', null, ['class'=>'form-control ' . ($errors->has('ar_name') ? 'redborder' : '') ,'pattern' => $arabic_regex,'title' => trans('lang.only_arabic') ,'required'=>'required', 'id'=>'ar_name','placeholder' => 'اسم العياده باللغه العربيه']) }}
                <small class="text-danger">{{ $errors->first('ar_name') }}</small>
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
            <div class="col-md-12 form-input">
                {{ Form::text('mobile', NULL, ['minlength'=>11 , 'class'=>'form-control ' . ($errors->has('add_post') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'mobile','pattern' => '(01)[0-9]{9}']) }}
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
            <div class="col-md-12 form-input">
                {{ Form::select('plan_id', $plans->pluck( App::getLocale() . '_name' ,'id') , null,[ 'class'=>'form-control' . ($errors->has('plan_id') ? 'redborder' : ''), 'required'=>'required'  , 'id'=>'plan_id' ]) }}
                <small class="text-danger">{{ $errors->first('plan_id') }}</small>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="country_id">{{ trans('lang.country') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                <select name="country_id" id="country_id" required
                        class='form-control {{ ($errors->has('country_id') ? 'redborder' : '') }}'>
                    <option value="" disabled selected>{{ trans('lang.choose_country') }} </option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country[ App::getLocale() . '_name'] }}</option>
                    @endforeach
                </select>
                <small class="text-danger">{{ $errors->first('country_id') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row load-cities">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="city_id">{{ trans('lang.city') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                <select name="city_id" id="city_id" required
                        class='form-control {{ ($errors->has('city_id') ? 'redborder' : '') }}'>
                    <option value="" disabled selected>{{ trans('lang.choose_city') }} </option>
                </select>
                <small class="text-danger">{{ $errors->first('city_id') }}</small>
            </div>
        </div>
    </div>
</div>


<a href="{{ url('/accounts') }}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>

{{  Form::submit($btn , ['class' => 'btn-loon ' . $classes ]) }}

@section('scripts')

    <script>
        $(document).ready(function () {
            var URL = "{{ url('/') }}";

            // get the list of cities according to the county_id
            $(document).on('change', '#country_id', function () {
                id = $('#country_id').val();
                $('.load-cities').load(URL + '/cities-select/' + id);
            });
        });
    </script>

@stop