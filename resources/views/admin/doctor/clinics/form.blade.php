@section('extrascripts')
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDETYrhbBFQrn4yy3vr3PXnTC8r7_TdZSc&libraries=places&sensor=false&language=ar"></script>
@stop
@php
    if(isset($clinic))
    {
       $lng = $clinic->lng;
       $lat = $clinic->lat;
       $address = $clinic->en_address;
    } else {
       if ($auth->role_id == $role_doctor && $auth->account->type == 1) {
         $lng = $auth->account->lng;
         $lat  =  $auth->account->lat;
       } else {
        $lng = 0.0;
        $lat  =  0.0;
       }
       $address = '';
    }
@endphp
{{-- in case of poly clinic  --}}
@if($auth->account->type == 1)
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="en_name">{{trans('lang.en_name')}} <span class="astric">*</span></label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::text('en_name', null, ['class'=>'form-control ' . ($errors->has('en_name') ? 'redborder' : ''),'required'=>'required','pattern' => $english_regex ,'placeholder' => trans('lang.en_name'), 'id'=>'en_name']) }}
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
                <div class="col-md-12 form-input">
                    {{ Form::text('ar_name', null, ['class'=>'form-control ' . ($errors->has('ar_name') ? 'redborder' : '') ,'required'=>'required','pattern' => $arabic_regex,'placeholder' => trans('lang.ar_name'), 'id'=>'ar_name']) }}
                    <small class="text-danger">{{ $errors->first('ar_name') }}</small>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="pattern">{{ trans('lang.pattern') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::select('pattern', [ 0 => trans('lang.intervals') , 1 => trans('lang.queuing') ] , null,[ 'class'=>'form-control' . ($errors->has('pattern') ? 'redborder' : '')  , 'id'=>'pattern']) }}
                <small class="text-danger">{{ $errors->first('pattern') }}</small>
            </div>
        </div>
    </div>
</div>
{{--   Dont Show Mobile Field In Case Of Poly In Steps--}}
@if (!($auth->account->is_completed == 0 &&  $auth->account->type == 1))
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="mobile">{{ trans('lang.mobile') }}<span class="astric">*</span></label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::text('mobile', NULL, ['minlength'=>11 , 'class'=>'form-control ' . ($errors->has('add_post') ? 'redborder' : '') , 'required'=>'required' , 'id'=>'mobile','pattern' => '(01)[0-9]{9}','placeholder' => trans('lang.enter_mobile')]) }}
                    <small class="text-danger">{{ $errors->first('mobile') }}</small>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="fees">{{ trans('lang.fees') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::number('fees', null, ['min' => '1', 'class'=>'form-control ' . ($errors->has('fees') ? 'redborder' : '') , 'required'=>'required'  , 'placeholder'=>'enter fees']) }}
                <small class="text-danger">{{ $errors->first('fees') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="follow_up_fees">{{ trans('lang.follow_up_fees') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::number('follow_up_fees', null, ['min' => '0', 'class'=>'form-control ' . ($errors->has('follow_up_fees') ? 'redborder' : '') , 'required'=>'required'  , 'placeholder'=>'enter follow up fees']) }}
                <small class="text-danger">{{ $errors->first('follow_up_fees') }}</small>
            </div>
        </div>
    </div>
</div>
@if($auth->is_premium == 1)
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="premium_fees">{{ trans('lang.premium_fees') }} <span class="astric">*</span></label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::number('premium_fees', null, ['min' => '1', 'class'=>'form-control ' . ($errors->has('premium_fees') ? 'redborder' : '') , 'required'=>'required','step' => '0.01', 'placeholder'=>'enter premium_fees']) }}
                    <small class="text-danger">{{ $errors->first('premium_fees') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="premium_follow_up_fees">{{ trans('lang.premium_follow_up_fees') }} <span class="astric">*</span></label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::number('premium_follow_up_fees', null, ['min' => '0', 'class'=>'form-control ' . ($errors->has('premium_follow_up_fees') ? 'redborder' : '') , 'required'=>'required'  , 'placeholder'=>'enter follow up fees']) }}
                    <small class="text-danger">{{ $errors->first('premium_follow_up_fees') }}</small>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row {{ ($auth->account->is_completed == 1) ? '' : 'hidden' }}">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="res_limit">{{ trans('lang.res_limit') }} <span class="astric">*</span></label>
            </div>

            <div class="col-md-12 form-input">
                {{ Form::number('res_limit', 50, ['min' => '1', 'class'=>'form-control ' . ($errors->has('res_limit') ? 'redborder' : '') , 'required'=>'required'  , 'placeholder'=>trans('lang.select_reservation_number_limit')]) }}
                <small class="text-danger">{{ $errors->first('res_limit') }}</small>
            </div>
        </div>
    </div>
</div>

{{--  incase of single clinic  --}}
@if($auth->account->type == 0)
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="en_address">{{trans('lang.en_address')}} <span class="astric">*</span></label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::text('en_address', null, ['class'=>'form-control ' . ($errors->has('en_address') ? 'redborder' : ''),'required'=>'required' , 'id'=>'en_address','placeholder' => trans('lang.en_address')]) }}
                    <small class="text-danger">{{ $errors->first('en_address') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="ar_address">{{trans('lang.ar_address')}} <span class="astric">*</span></label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::text('ar_address', null, ['class'=>'form-control ' . ($errors->has('ar_address') ? 'redborder' : '') ,'required'=>'required', 'id'=>'ar_address','placeholder' => trans('lang.ar_address')]) }}
                    <small class="text-danger">{{ $errors->first('ar_address') }}</small>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row hidden">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="vat_included">{{trans('lang.vat_included')}} <span class="astric">*</span></label>
                {{ Form::checkbox('vat_included', 1,(isset($clinic->vat_included) && $clinic->vat_included == 1) ? true :  false , ['class'=>'no-margin inline_checkBox'])  }}
            </div>
        </div>
    </div>
</div>

<div class="row {{ ($auth->account->is_completed == 1) ? '' : 'hidden' }}">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="avg_reservation_time">{{ trans('lang.avg_reservation_time_in_minutes') }}<span
                        class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::number('avg_reservation_time', 15, ['min' => '1', 'class'=>'form-control ' . ($errors->has('avg_reservation_time') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'avg_reservation_time']) }}
                <small class="text-danger">{{ $errors->first('avg_reservation_time') }}</small>
            </div>
        </div>
    </div>
</div>

{{--specialities--}}
@if($auth->account->type == 1)
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="speciality_id">{{ trans('lang.speciality_name') }}</label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::select('speciality_id', \App\Models\Speciality::pluck( app()->getLocale() . '_speciality' ,'id') ,null,[ 'class'=>'form-control' . ($errors->has('speciality_id') ? 'redborder' : '')  , 'id'=>'speciality_id' ]) }}
                    <small class="text-danger">{{ $errors->first('speciality_id') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="reservation_deadline">{{ trans('lang.reservation_deadline') }}<span
                            class="astric">*</span></label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::date('reservation_deadline', isset($clinic) ? now()->addDays($clinic->reservation_deadline) : null, ['class'=>'form-control date' . ($errors->has('reservation_deadline') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'day']) }}
                    <small class="text-danger">{{ $errors->first('reservation_deadline') }}</small>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- choose province --}}

<div class="row">
    <div class="form-group col-md-6">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="city_id">{{ trans('lang.city_name') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::select('city_id', \App\Models\City::pluck(app()->getLocale() . '_name' ,'id') , null,[ 'class'=>'form-control' . ($errors->has('city_id') ? 'redborder' : '') ,'placeholder' => 'select city', 'id'=>'city_id']) }}
                <small class="text-danger">{{ $errors->first('city_id') }}</small>
            </div>
        </div>
    </div>
    <div class="form-group col-md-6">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="province_id">{{ trans('lang.province') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input province_area">
                {{ Form::select('province_id', \App\Models\Province::pluck(app()->getLocale() . '_name' ,'id') , null,[ 'class'=>'form-control' . ($errors->has('city_id') ? 'redborder' : '') , 'required'=>'required'  , 'id'=>'province_id','placeholder' => trans('lang.province')]) }}
                <small class="text-danger">{{ $errors->first('province_id') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-6 has-float-label">
        {{ Form::hidden('lat', $lat, ['class'=>'form-control ' . ($errors->has('lat') ? 'redborder' : '') , 'id'=>'lat' ]) }}

    </div>
    <div class="form-group col-md-6 has-float-label">
        {{ Form::hidden('lng', $lng, ['class'=>'form-control ' . ($errors->has('lng') ? 'redborder' : '') , 'id'=>'lng' ]) }}
    </div>
</div>

@if($auth->account->type == 0)
    <div class="form-group col-xs-12 center" style="width: 100%">
        <a href="#" id="get-loc" class="btn btn-primary">{{trans('lang.current_location')}} <i
                class="fa fa-location-arrow"></i></a>
    </div>
    <div class="form-group col-xs-12 has-float-label">
        <div id="us2" data-show="0" style="height: 400px; display: none"></div>
    </div>
@endif
@if($auth->account->is_completed == 1)
    <a href="{{ url('/clinics')}}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>
@endif
{{  Form::submit($btn , ['class' => 'btn-loon ' . $classes ]) }}

@section('scripts')
    {!! Html::script('assets/js/admin/locationpicker.jquery.js') !!}
    <script>
        @if($auth->account->type == 0)
        $(document).ready(function () {

            $('#us2').locationpicker({
                location: {
                    latitude: "{{ $lat }}",
                    longitude: "{{ $lng }}"
                },
                locationName: "",
                radius: 500,
                zoom: 20,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: [],
                mapOptions: {},
                scrollwheel: true,
                inputBinding: {
                    latitudeInput: $('#lat'),
                    longitudeInput: $('#lng'),
                    radiusInput: $(300),
                },
                enableAutocomplete: true,
                enableAutocompleteBlur: true,
                autocompleteOptions: null,
                addressFormat: 'postal_code',
                enableReverseGeocode: true,
                draggable: true,
                onchanged: function (currentLocation, radius, isMarkerDropped) {
                    var latlng = new google.maps.LatLng(currentLocation.latitude, currentLocation.longitude);
                    var geocoder = geocoder = new google.maps.Geocoder();
                    geocoder.geocode({'latLng': latlng}, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[1]) {
                                // var address = results[1].formatted_address;
                                //$('#en_address').val(address);
                            }
                        }
                    });
                },
                onlocationnotfound: function (locationName) {
                    // console.log(locationName)
                },
                oninitialized: function (component) {

                },
                // must be undefined to use the default gMaps marker
                markerIcon: undefined,
                markerDraggable: true,
                markerVisible: true
            });

            function getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition);
                } else {
                    alert("Geolocation is not supported by this browser.");
                }
            }

            function showPosition(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                $('#lat').val(lat);
                $('#lng').val(lng);

                var latlng = new google.maps.LatLng(lat, lng);
                var geocoder = geocoder = new google.maps.Geocoder();

                geocoder.geocode({'latLng': latlng}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[1]) {
                            var address = results[1].formatted_address;
                            // console.log(address);
                            // $('#en_address').val(address);
                        }
                    }
                });

                $('#us2').locationpicker({
                    location: {

                        latitude: lat,
                        longitude: lng
                    },
                    locationName: "",
                    radius: 500,
                    zoom: 15,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    styles: [],
                    mapOptions: {},
                    scrollwheel: true,
                    inputBinding: {
                        latitudeInput: $('#lat'),
                        longitudeInput: $('#lng'),
                        radiusInput: $(300),
                        @if(!isset($user))
                        locationNameInput: $('#en_address')
                        @endif
                    },
                    enableAutocomplete: true,
                    enableAutocompleteBlur: true,
                    autocompleteOptions: null,
                    addressFormat: 'postal_code',
                    enableReverseGeocode: true,
                    draggable: true,
                    onchanged: function (currentLocation, radius, isMarkerDropped) {
                        var latlng = new google.maps.LatLng(currentLocation.latitude, currentLocation.longitude);
                        var geocoder = geocoder = new google.maps.Geocoder();
                        geocoder.geocode({'latLng': latlng}, function (results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                if (results[1]) {
                                    var address = results[1].formatted_address;
                                    $('#en_address').val(address);
                                }
                            }
                        });
                    },
                    onlocationnotfound: function (locationName) {
                        // console.log(locationName)
                    },
                    oninitialized: function (component) {

                    },
                    // must be undefined to use the default gMaps marker
                    markerIcon: undefined,
                    markerDraggable: true,
                    markerVisible: true
                });
            }

            $('#get-loc').on('click', function (e) {
                e.preventDefault();
                let map = $('#us2');
                let currentDate = map.data('show');

                if (currentDate == 0) {

                    map.css('display', 'block');
                    map.data('show', 1);
                    getLocation();
                } else {
                    map.css('display', 'none');
                    map.data('show', 0);
                    $('#lat').val('0');
                    $('#lng').val('0');
                }
            });

        });
        @endif

        $(document).on('change', '#city_id', function (e) {
            e.preventDefault();
            let city_id = $(this).val();
            if (!isNaN(city_id)) {
                $.ajax({
                    url: URL + '/location/provinces/list',
                    type: 'POST',
                    data: {
                        _token: token,
                        city_id: city_id
                    }
                }).done(function (data) {
                    if (data.status === true) {
                        let provinces = data.provinces;
                        let provinces_temp = " <select name='province_id' class='form-control' id='province_id' required>";
                        for (let key in provinces) {
                            if (provinces.hasOwnProperty(key)) {
                                provinces_temp += "<option value=" + key + ">" + provinces[key] + "</option>";
                            }
                        }
                        provinces_temp += "</select>";
                        $('.province_area').html(provinces_temp);
                    }
                });
            }
        });
    </script>
@stop
