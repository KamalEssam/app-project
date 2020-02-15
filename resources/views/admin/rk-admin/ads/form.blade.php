@if (isset($ad))
    <div class="row text-center">
        <img src="{{ asset($ad->screen_shot) }}" alt="image"
             style="width:60px; height: 60px ;margin-bottom: 20px">
    </div>
@endif

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="screen_shot"> {{ trans('lang.screen_shot') }}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::file('screen_shot', ['class'=>'form-control ' . ($errors->has('screen_shot') ? 'redborder' : '') , 'id'=>'screen_shot' ,(!isset($ad)) ? 'required' : '' ]) }}
                <small class="text-danger">{{ $errors->first('screen_shot') }}</small>
                <p class="help-block red"><b>Note:</b> the screenShot must have at least 285px X 523px and height must
                    be equal width </p>
            </div>
        </div>
    </div>
</div>


@if (isset($ad))
    <div class="row text-center">
        <img src="{{ asset($ad->background) }}" alt="image"
             style="width:60px; height: 60px ;margin-bottom: 20px">
    </div>
@endif

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="background"> {{ trans('lang.background') }}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::file('background', ['class'=>'form-control ' . ($errors->has('background') ? 'redborder' : '') , 'id'=>'background' ,(!isset($ad)) ? 'required' : '' ]) }}
                <small class="text-danger">{{ $errors->first('background') }}</small>
                <p class="help-block red"><b>Note:</b> the background must have at least 1140px X 455px and height must
                    be equal width </p>
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
                {{ Form::text('en_title', NULL, ['class'=>'form-control ' . ($errors->has('en_title') ? 'redborder' : ''),'pattern' => $english_regex,'title' => trans('lang.only_english') , 'required'=>'required'  , 'id'=>'en_title']) }}
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
            <div class="col-md-12 label-form">
                <label for="en_desc">{{trans('lang.en_desc')}}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::textarea('en_desc', null, ['class'=>'form-control ' . ($errors->has('en_desc') ? 'redborder' : '') ,'pattern' => $english_regex,'title' => trans('lang.only_english'), 'id'=>'en_desc' , 'rows'=> 2]) }}
                <small class="text-danger en_desc_err">{{ $errors->first('en_desc') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="ar_desc">{{trans('lang.ar_desc')}}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::textarea('ar_desc', null, ['class'=>'form-control ' . ($errors->has('ar_desc') ? 'redborder' : '') ,'pattern' => $arabic_regex,'title' => trans('lang.only_arabic'), 'id'=>'ar_desc', 'rows'=> 2]) }}
                <small class="text-danger ar_desc_err">{{ $errors->first('ar_desc') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="type">{{ trans('lang.type') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::select('type',[ 0 => trans('lang.offer'),1 => trans('lang.doctor')] , null,[ 'class'=>'form-control' . ($errors->has('type') ? 'redborder' : '') , 'required'=>'required', 'id'=>'ad_type']) }}
                <small class="text-danger">{{ $errors->first('type') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row" id="offers_div">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="offer_id">{{ trans('lang.offer') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::select('offer_id',(new \App\Http\Repositories\Web\OfferRepository())->getApiOffers(), null,[ 'class'=>'form-control '. ($errors->has('offer_id') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'offer_id']) }}
                <small class="text-danger">{{ $errors->first('offer_id') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row" id="doctors_div">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-9 label-form">
                <label for="doctor_id">{{ trans('lang.doctors') }}<span class="astric">*</span></label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 form-input">
                {{ Form::select('doctor_id' ,(new \App\Http\Repositories\Web\AuthRepository())->getAllDoctors(),null,[ 'class'=>'form-control chosen-select ' . ($errors->has('doctor_id') ? 'redborder' : '') ,'required' => 'required']) }}
                <small class="text-danger">{{ $errors->first('doctor_id') }}</small>
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
                    {{ Form::checkbox('is_active', 1,isset($ad->is_active) ? ($ad->is_active == 1) : true , ['class'=>'no-margin'])  }}
                    <span class="slider round"></span>
                </label>
                <small class="text-danger">{{ $errors->first('is_active') }}</small>
            </div>
        </div>
    </div>
</div>

{{-- day (from - to)--}}
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="date_from">{{ trans('lang.date_from') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::date('date_from', ($ad->date_from) ?? null, ['class'=>'form-control' . ($errors->has('date_from') ? 'redborder' : '')  , 'id'=>'date_from', 'required' => 'required']) }}
                <small class="text-danger">{{ $errors->first('date_from') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="date_to">{{ trans('lang.date_to') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::date('date_to', ($ad->date_to) ?? null, ['class'=>'form-control' . ($errors->has('date_to') ? 'redborder' : '')  , 'id'=>'date_to', 'required' => 'required']) }}
                <small class="text-danger">{{ $errors->first('date_to') }}</small>
            </div>
        </div>
    </div>
</div>
{{-- time (from - to)--}}
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="time_from">{{ trans('lang.time_from') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::time('time_from', ($ad->time_from) ?? null, ['class'=>'form-control' . ($errors->has('time_from') ? 'redborder' : '')  , 'id'=>'time_from', 'required' => 'required']) }}
                <small class="text-danger">{{ $errors->first('time_from') }}</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="time_to">{{ trans('lang.time_to') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::time('time_to', ($ad->time_to) ?? null, ['class'=>'form-control' . ($errors->has('time_to') ? 'redborder' : '')  , 'id'=>'time_to', 'required' => 'required']) }}
                <small class="text-danger">{{ $errors->first('time_to') }}</small>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="slide">{{ trans('lang.slide') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::select('slide',[ 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5] , null,[ 'class'=>'form-control' . ($errors->has('slide') ? 'redborder' : '') , 'required'=>'required', 'id'=>'ad_slide']) }}
                <small class="text-danger">{{ $errors->first('slide') }}</small>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="priority">{{ trans('lang.priority') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::number('priority', NULL, ['class'=>'form-control ' . ($errors->has('priority') ? 'redborder' : '') , 'required'=>'required' ,'min' => 1, 'id'=>'priority' ]) }}
                <small class="text-danger">{{ $errors->first('ar_title') }}</small>
            </div>
        </div>
    </div>
</div>


{!! Form::submit($btn, ['class' => 'btn-loon ' . $classes,'id' => 'add_offer']) !!}

@push('more-scripts')
    <script>
        $('#offers_div').show();
        $('#doctors_div').hide();
        $(document).on('change', '#ad_type', function (e) {
            let type = $(this).val();
            if (type == 0) {
                $('#offers_div').show();
                $('#doctors_div').hide();
            } else if (type == 1) {
                $('#offers_div').hide();
                $('#doctors_div').show();
            }
        });

        $(".chosen-select").chosen();
    </script>
@endpush
