<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.min.css"/>
@if($auth->account->type == 1)
@section('extrascripts')
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDETYrhbBFQrn4yy3vr3PXnTC8r7_TdZSc&libraries=places&sensor=false&language=en"></script>
@stop
@endif
@php
    if(!is_null($profile->lng) && !is_null($profile->lat))
    {
       $lng = $profile->lng;
       $lat = $profile->lat;
    } else {
       $lng =0;
       $lat  =  0;
    }
    $address = "";
@endphp

@if(isset($profile))
    <div
        class="row text-center {{ ( !in_array($auth->role_id,[$role_rk_super_admin,$role_rk_admin,$role_rk_sales]) && $auth->account->is_completed == 1) ? '' : 'hidden' }}">
        <img src="{{ asset($profile->image) }}" alt="image"
             style="width:60px; height: 60px ;margin-bottom: 20px">
    </div>
@endif

<div
    class="row  {{ (!in_array($auth->role_id,[$role_rk_super_admin,$role_rk_admin,$role_rk_sales]) && $auth->account->is_completed == 1) ? '' : 'hidden' }}">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="image"> {{ trans('lang.image') }}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::file('image', ['class'=>'form-control ' . ($errors->has('image') ? 'redborder' : '') , 'id'=>'upload_image', 'accept' => 'image/*']) }}
                <small class="text-danger">{{ $errors->first('image') }}</small>
                <br/>
                <div id="uploaded_image"></div>
            </div>
        </div>
    </div>
</div>

<div
    class="row {{  (!in_array($auth->role_id,[$role_rk_super_admin,$role_rk_admin,$role_rk_sales]) && $auth->account->is_completed == 1) ? '' : 'hidden' }}">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="name">{{trans('lang.name')}}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::text('name', null, ['class'=>'form-control ' . ($errors->has('name') ? 'redborder' : '') , 'id'=>'name']) }}
                <small class="text-danger">{{ $errors->first('name') }}</small>
            </div>
        </div>
    </div>
</div>
@if($auth->role_id == $role_doctor)
    {{--  Doctor Title    --}}
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="en_title">{{trans('lang.en_title')}}</label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::text('en_title', null, ['class'=>'form-control ' . ($errors->has('en_title') ? 'redborder' : '') ,'pattern' => $english_regex,'title' => trans('lang.only_english'),'required', 'id'=>'en_title']) }}
                    <small class="text-danger en_title_err">{{ $errors->first('en_title') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="ar_title">{{trans('lang.ar_title')}}</label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::text('ar_title', null, ['class'=>'form-control ' . ($errors->has('ar_title') ? 'redborder' : '') ,'pattern' => $arabic_regex,'title' => trans('lang.only_arabic'),'required', 'id'=>'ar_title']) }}
                    <small class="text-danger ar_title_err">{{ $errors->first('ar_title') }}</small>
                </div>
            </div>
        </div>
    </div>
@endif

<div
    class="row {{  (!in_array($auth->role_id,[$role_rk_super_admin,$role_rk_admin,$role_rk_sales]) && $auth->account->is_completed == 1) ? '' : 'hidden' }}">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="email">{{trans('lang.email')}}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::email('email', null, ['class'=>'form-control ' . ($errors->has('email') ? 'redborder' : '') , 'id'=>'email', 'disabled'=>'disabled']) }}
                <small class="text-danger">{{ $errors->first('email') }}</small>
            </div>
        </div>
    </div>
</div>

<div
    class="row {{ (!in_array($auth->role_id,[$role_rk_super_admin,$role_rk_admin,$role_rk_sales]) && $auth->account->is_completed == 1) ? '' : 'hidden' }}">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="type"> {{trans('lang.gender')}}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::select('gender', ['0' => 'male', '1' => 'female'], null, ['class'=>'form-control ' . ($errors->has('gender') ? 'redborder' : '') , 'id'=>'gender']) }}
                <small class="text-danger">{{ $errors->first('gender') }}</small>
            </div>
        </div>
    </div>
</div>

<div
    class="row  {{  (!in_array($auth->role_id,[$role_rk_super_admin,$role_rk_admin,$role_rk_sales]) && $auth->account->is_completed == 1) ? '' : 'hidden' }}">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="birthday">{{trans('lang.birthday')}}</label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::date('birthday', (isset($profile->birthday) && $profile->birthday != 'Not Set') ? Carbon\Carbon::parse($profile->birthday)->format('Y-m-d') : now() , ['class'=>'form-control date' . ($errors->has('birthday') ? 'redborder' : '') , 'id'=>'date-profile']) }}
                <small class="text-danger">{{ $errors->first('birthday') }}</small>
            </div>
        </div>
    </div>
</div>
@if($auth->role_id == $role_assistant)
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="address">{{trans('lang.address')}}</label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::textarea('address', null, ['class'=>'form-control ' . ($errors->has('address') ? 'redborder' : '') , 'id'=>'address' ,'rows' => 3]) }}
                    <small class="text-danger">{{ $errors->first('address') }}</small>
                </div>
            </div>
        </div>
    </div>
@endif

@if( (!in_array($auth->role_id,[$role_rk_super_admin,$role_rk_admin,$role_rk_sales]) && $auth->account->is_completed == 1))
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="mobile">{{trans('lang.mobile')}} <span class="astric">*</span></label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::text('mobile', null, ['class'=>'form-control ' . ($errors->has('mobile') ? 'redborder' : '') , 'id'=>'mobile', 'disabled'=>'disabled']) }}
                    <small class="text-danger">{{ $errors->first('mobile') }}</small>
                </div>
            </div>
        </div>
    </div>
@endif

@if($auth->role_id == $role_doctor)
    {{-- clinic name (account name)  --}}
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="en_name">{{trans('lang.en_account_name')}}</label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::text('en_name', null, ['class'=>'form-control ' . ($errors->has('en_name') ? 'redborder' : '') ,'pattern' => $english_regex,'title' => trans('lang.only_english'),'required'=>'required', 'id'=>'en_account_name']) }}
                    <small class="text-danger en_name_err">{{ $errors->first('en_name') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="ar_name">{{trans('lang.ar_account_name')}}</label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::text('ar_name', null, ['class'=>'form-control ' . ($errors->has('ar_name') ? 'redborder' : '') ,'pattern' => $arabic_regex,'title' => trans('lang.only_arabic'),'required'=>'required', 'id'=>'ar_account_name']) }}
                    <small class="text-danger ar_name_err">{{ $errors->first('ar_name') }}</small>
                </div>
            </div>
        </div>
    </div>

    {{--bio--}}
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="en_bio">{{trans('lang.en_bio')}}</label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::textarea('en_bio', null, ['class'=>'form-control ' . ($errors->has('en_bio') ? 'redborder' : '') ,'title' => trans('lang.only_english'), 'id'=>'en_bio' , 'rows'=> 2]) }}
                    <small class="text-danger en_bio_err">{{ $errors->first('en_bio') }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="ar_bio">{{trans('lang.ar_bio')}}</label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::textarea('ar_bio', null, ['class'=>'form-control ' . ($errors->has('ar_bio') ? 'redborder' : '') ,'title' => trans('lang.only_arabic'), 'id'=>'ar_bio', 'rows'=> 2]) }}
                    <small class="text-danger ar_bio_err">{{ $errors->first('ar_bio') }}</small>
                </div>
            </div>
        </div>
    </div>

    @if($auth->account->type == 0)
        {{--specialities--}}
        <div class="row">
            <div class="form-group col-md-12">
                <div class="row">
                    <div class="col-md-12 label-form">
                        <label for="speciality_id">{{ trans('lang.speciality_name') }}</label>
                    </div>
                    <div class="col-md-12 form-input">
                        {{ Form::select('speciality_id',\App\Models\Speciality::pluck(app()->getLocale(). '_speciality' ,'id') , null,[ 'class'=>'form-control' . ($errors->has('speciality_id') ? 'redborder' : '')  , 'id'=>'speciality_id','placeholder'=> 'select speciality','required' => 'required']) }}
                        <small class="text-danger">{{ $errors->first('speciality_id') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row add_sub_speciality">
            @if (isset($profile->speciality_id))
                <div class="form-group col-md-12">
                    <div class="row">
                        <div class="col-md-12 label-form">
                            <label for="sub_specialities">{{ trans('lang.speciality_name') }}</label>
                        </div>
                        <div class="col-md-12 form-input">
                            {{ Form::select('sub_specialities[]',DB::table('sub_specialities')->where('speciality_id',$profile->speciality_id)->pluck(app()->getLocale(). '_name' ,'id') , $profile->sub_specialities ?? null,[ 'class'=>'chosen-select sub_list form-control' . ($errors->has('speciality_id') ? 'redborder' : '')  , 'id'=>'sub_specialities_list form-field-select-2','required' => 'required','multiple']) }}
                            <small
                                class="text-danger sub_speciality_err">{{ $errors->first('sub_specialities') }}</small>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div
            class="row {{(!in_array($auth->role_id,[$role_rk_super_admin,$role_rk_admin,$role_rk_sales]) && $auth->account->is_completed == 1) ? '' : 'hidden' }}">
            <div class="form-group col-md-12">
                <div class="row">
                    <div class="col-md-12 label-form">
                        <label for="insurance_companies">{{ trans('lang.insurance_companies') }}</label>
                    </div>
                    <div class="col-md-12 form-input">
                        {{ Form::select('insurance_companies[]',DB::table('insurance_companies')->pluck(app()->getLocale(). '_name' ,'id') , $profile->insurance_companies ?? null,[ 'class'=>'chosen-select form-control' . ($errors->has('speciality_id') ? 'redborder' : '')  , 'id'=>'form-field-select-2','multiple']) }}
                        <small class="text-danger">{{ $errors->first('insurance_companies') }}</small>
                    </div>
                </div>
            </div>
        </div>

        @if( (!in_array($auth->role_id,[$role_rk_super_admin,$role_rk_admin,$role_rk_sales]) && $auth->account->is_completed == 1))
            {{--  restrict visit switch  --}}
            <div class="row">
                <div class="form-group col-md-12">
                    <div class="row">
                        <div class="col-md-12 label-form">
                            <label for="type">{{ trans('lang.ignore_history') }}</label>
                        </div>
                        <div class="col-md-12 form-input">
                            <label class="switch">
                                {{ Form::checkbox('restrict_visit', 1,(isset($profile->restrict_visit) && $profile->restrict_visit == 1) ? true :  false , ['class'=>'no-margin'])  }}
                                <span class="slider round"></span>
                            </label>
                            <small class="text-danger">{{ $errors->first('restrict_visit') }}</small>
                            <p class="help-block">{{trans('lang.restrict_visit')}}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="row">
            <div class="form-group col-md-12">
                <div class="row">
                    <div class="col-md-12 label-form">
                        <label for="en_address">{{trans('lang.en_address')}} <span class="astric">*</span></label>
                    </div>
                    <div class="col-md-12 form-input">
                        {{ Form::text('en_address', null, ['class'=>'form-control ' . ($errors->has('en_address') ? 'redborder' : ''),'required'=>'required' , 'id'=>'en_address']) }}
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
                        {{ Form::text('ar_address', null, ['class'=>'form-control ' . ($errors->has('ar_address') ? 'redborder' : '') ,'required'=>'required', 'id'=>'ar_address']) }}
                        <small class="text-danger ar_address_err">{{ $errors->first('ar_address') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6 has-float-label">
                {{ Form::hidden('lat', null, ['class'=>'form-control ' . ($errors->has('lat') ? 'redborder' : '') , 'id'=>'lat' ]) }}

            </div>
            <div class="form-group col-md-6 has-float-label">
                {{ Form::hidden('lng', null, ['class'=>'form-control ' . ($errors->has('lng') ? 'redborder' : '') , 'id'=>'lng' ]) }}

            </div>
        </div>

        <div class="form-group col-xs-12 center" style="width: 100%">
            <a href="#" id="get-current-loc" class="btn btn-primary">{{trans('lang.current_location')}} <i
                    class="fa fa-location-arrow"></i></a>
        </div>
        <div class="form-group col-xs-12 has-float-label">
            <div id="us2" data-show="0" style="height: 400px; display: none"></div>
        </div>
    @endif
@endif

<div id="uploadimageModal" class="modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Upload & Crop Image</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8 text-center">
                        <div id="image_demo" style="width:350px; margin-top:30px"></div>
                    </div>
                    <div class="col-md-4" style="padding-top:30px;">
                        <br/>
                        <br/>
                        <br/>
                        <button class="btn btn-success crop_image">Crop & save Image</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@if((!in_array($auth->role_id,[$role_rk_super_admin,$role_rk_admin,$role_rk_sales]) && $auth->account->is_completed == 1))
    <a href="{{ url('/profile')}}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>
@endif
{{  Form::submit($btn , ['class' => 'btn-loon ' . $classes,'id' => 'edit_profile' ]) }}

@if($auth->role_id == $role_doctor)
    @push('more-scripts')
        @if($auth->account->type == 1)
            {!! Html::script('assets/js/admin/locationpicker.jquery.js') !!}
        @endif
        <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.min.js"></script>
        <script>
            URL = "{{ url('/') }}";
            token = "{{ csrf_token() }}";
            $(document).ready(function () {
                $(".chosen-select").chosen();

                $(document).on('click', '#edit_profile', function (event) {

                    @if($auth->role_id == $role_doctor)

                        @if($auth->account->type == 0)
                    if ($(".sub_list").val() == null) {
                        event.preventDefault();
                        $('.sub_speciality_err').text("{{trans('lang.sub_specialities_required') }}");
                    }
                    @endif


                    if ($('#en_account_name').val() == 'No Name' || $('#en_account_name').val() == '') {
                        event.preventDefault();
                        $('.en_name_err').text("english name is required");
                    } else {
                        $('.en_name_err').text("");
                    }

                    if ($('#ar_account_name').val() == 'لا يوجد اسم' || $('#ar_account_name').val() == '') {
                        event.preventDefault();
                        $('.ar_name_err').text("arabic name is required");
                    } else {
                        $('.ar_name_err').text("");
                    }

                    if ($('#en_title').val() == 'en title' || $('#en_title').val() == '') {
                        event.preventDefault();
                        $('.en_title_err').text("english title is required");
                    } else {
                        $('.en_title_err').text("");
                    }

                    if ($('#ar_title').val() == 'اللقب باعربى' || $('#ar_title').val() == '') {
                        event.preventDefault();
                        $('.ar_title_err').text("arabic title is required");
                    } else {
                        $('.ar_title_err').text("");
                    }

                    if ($('#en_bio').val() == 'No Data To Show' || $('#en_bio').val() == '') {
                        event.preventDefault();
                        $('.en_bio_err').text("english bio is required");
                    } else {
                        $('.en_bio_err').text("");
                    }

                    if ($('#ar_bio').val() == 'لا توجد بيانات للعرض' || $('#ar_bio').val() == '') {
                        event.preventDefault();
                        $('.ar_bio_err').text("arabic bio is required");
                    } else {
                        $('.ar_bio_err').text("");
                    }

                    @endif
                });

            });

            // in case of poly clinic
            @if($auth->account->type == 1)
            //         Location Picker
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


                $('#get-current-loc').on('click', function (e) {
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
            $(document).on('change', '#speciality_id', function () {
                let sp_id = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: URL + '/sub-specialities/list',
                    data: {
                        'speciality_id': sp_id,
                        '_token': token
                    },
                    dataType: 'json',
                    success: function (data) {
                        let appednded = '';
                        let i = 0;
                        for (i = 0; i < data.length; i++) {
                            appednded += '<option value=' + data[i].id + '>' + data[i].name + ' </option>'
                        }
                        $('.add_sub_speciality').html(
                            '<div class="form-group col-md-12">' +
                            ' <div class="row">' +
                            '<div class="col-md-12 label-form">' +
                            '<label for="sub_specialities">' +
                            '{{trans('lang.sub_specialities')}}' +
                            '<span class="astric">*</span>' +
                            '</label>' +
                            '</div>' +
                            '<div class="col-md-12 form-input">' +
                            ' <select multiple="" name="sub_specialities[]" class="chosen-select sub_list form-control" id="sub_specialities_list form-field-select-2" required data-placeholder="Choose a speciality...">' +
                            appednded +
                            "</select><small class=\"text-danger\">{{ $errors->first('sub_specialities') }}</small></div></div></div>"
                        );
                        $(".chosen-select").chosen();
                    }, error: function (data) {

                    }
                });
                $(".chosen-select").chosen();
            });
            // crop image package
            $(document).ready(function () {
                $image_crop = $('#image_demo').croppie({
                    enableExif: true,
                    enableOrientation: true,
                    viewport: {
                        width: 200,
                        height: 200,
                        type: 'square'
                    },
                    boundary: {
                        width: 300,
                        height: 300
                    }
                });

                $('#upload_image').on('change', function () {
                    var reader = new FileReader();
                    reader.onload = function (event) {
                        $image_crop.croppie('bind', {
                            url: event.target.result,

                        }).then(function () {
                            console.log('jQuery bind complete');
                        });
                    }
                    reader.readAsDataURL(this.files[0]);
                    $('#uploadimageModal').modal('show');
                });

                $('.crop_image').click(function (event) {
                    $image_crop.croppie('result', {
                        type: 'canvas',
                        size: 'viewport'
                    }).then(function (response) {
                        $.ajax({
                            url: "{{ route('image.upload') }}",
                            type: "POST",
                            data: {
                                '_token': token,
                                "image": response
                            },
                            success: function (data) {
                                if (data.status = 'true') {
                                    $('#uploadimageModal').modal('hide');
                                    $('#uploaded_image').html(data);
                                    window.location.reload()
                                } else {
                                    console.log('failed to upload image')
                                }
                            }
                        });
                    })
                });

            });
        </script>
    @endpush
@endif


