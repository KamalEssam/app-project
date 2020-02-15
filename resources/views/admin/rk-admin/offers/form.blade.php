@if (isset($offer))
    <div class="row text-center">
        <img src="{{ asset($offer->image) }}" alt="image"
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
                {{ Form::file('image', ['class'=>'form-control ' . ($errors->has('image') ? 'redborder' : '') , 'id'=>'image' ,(!isset($offer)) ? 'required' : '' ]) }}
                <small class="text-danger">{{ $errors->first('image') }}</small>
                <p class="help-block red"><b>Note:</b> the image must have at least 500px X 250px and height must be
                    equal
                    width </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="category_id">{{ trans('lang.offer_category') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::select('category_id',(new \App\Http\Repositories\Web\OfferCategoryRepository())->getArrayOfOfferCategories(), null,[ 'class'=>'form-control '. ($errors->has('category_id') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'category_id']) }}
                <small class="text-danger">{{ $errors->first('category_id') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
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

{{--     list of services   --}}
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-9 label-form">
                <label for="services">{{ trans('lang.clinic-services') }}<span class="astric">*</span></label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 form-input">
                {{ Form::select('services[]' ,(new \App\Http\Repositories\Web\ServiceRepository())->getListOfServices(),isset($offer) ? $offer->services : null,[ 'class'=>'form-control chosen-select ' . ($errors->has('services') ? 'redborder' : '') ,'required' => 'required','multiple' => 'multiple','id' => 'services']) }}
                <small class="text-danger services_err">{{ $errors->first('services') }}</small>
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
                {{ Form::text('en_name', NULL, ['class'=>'form-control ' . ($errors->has('en_name') ? 'redborder' : ''),'pattern' => $english_regex,'title' => trans('lang.only_english') , 'required'=>'required'  , 'id'=>'en_name']) }}
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
                {{ Form::text('ar_name', NULL, ['class'=>'form-control ' . ($errors->has('ar_name') ? 'redborder' : ''),'pattern' => $arabic_regex,'title' => trans('lang.only_arabic') , 'required'=>'required'  , 'id'=>'ar_name' ]) }}
                <small class="text-danger">{{ $errors->first('ar_name') }}</small>
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
                <label for="type">{{ trans('lang.reservation_fees_included') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                <label class="switch">
                    {{ Form::checkbox('reservation_fees_included', 1,(isset($offer->reservation_fees_included) && $offer->reservation_fees_included == 1) ? true :  false , ['class'=>'no-margin'])  }}
                    <span class="slider round"></span>
                </label>
                <small class="text-danger">{{ $errors->first('reservation_fees_included') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="old_price">{{ trans('lang.old_price') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::number('old_price', NULL, ['class'=>'form-control ' . ($errors->has('old_price') ? 'redborder' : ''), 'required'=>'required','steps' => 0.01,'min' => 0, 'id'=>'old_price']) }}
                <small class="text-danger">{{ $errors->first('old_price') }}</small>
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
                {{ Form::number('price', NULL, ['class'=>'form-control ' . ($errors->has('price') ? 'redborder' : ''), 'required'=>'required','steps' => 0.01,'min' => 0, 'id'=>'price']) }}
                <small class="text-danger">{{ $errors->first('price') }}</small>
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
                {{ Form::date('expiry_date', ($offer->expiry_date) ?? null, ['class'=>'form-control' . ($errors->has('expiry_date') ? 'redborder' : '')  , 'id'=>'expiry_date', 'required' => 'required']) }}
                <small class="text-danger">{{ $errors->first('expiry_date') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="type">{{ trans('lang.is_featured') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                <label class="switch">
                    {{ Form::checkbox('is_featured', 1,(isset($offer->is_featured) && $offer->is_featured == 1) ? true :  false , ['class'=>'no-margin'])  }}
                    <span class="slider round"></span>
                </label>
                <small class="text-danger">{{ $errors->first('is_featured') }}</small>
            </div>
        </div>
    </div>
</div>

{!! Form::submit($btn, ['class' => 'btn-loon ' . $classes,'id' => 'add_offer']) !!}

@push('more-scripts')
    <script>

        $(document).on('keyup', '#doctors', function (e) {
            $.ajax({
                url: URL + '/offers/get-doctors',
                type: 'POST',
                data: {_token: token}
            }).done(function (data) {
                // auto complete mobiles
                $('#doctors').autocomplete({
                    source: data.split("#")
                });
            });
        });

        $(".chosen-select").chosen();

        $(document).on('click', '.add_offer', function (event) {
            if ($("#services").val() == null) {
                event.preventDefault();
                $('.services_err').text("{{trans('lang.service_required') }}");
            } else {
                $('.services_err').text("");
            }
        });
    </script>
@endpush
