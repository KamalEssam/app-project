<div id="user-data">
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-9 label-form">
                    <label for="mobile">{{ trans('lang.mobile') }}<span class="astric">*</span></label>
                </div>
                @if(!isset($reservation))
                    <div class="col-md-3">
                        <a class="trigger-modal loon p-7 add-user"
                           data-iziModal-open="#modal" style="cursor: pointer;">{{ trans('lang.add_patient') }}</a>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-md-12 form-input">
                    {{ Form::text('mobile' , isset($reservation) ? $reservation->user->mobile : null ,[ 'class'=>'form-control mobile' . ($errors->has('mobile') ? 'redborder' : '')  , 'id'=>'tags', 'autocomplete' => 'off' , isset($reservation) ? 'disabled' : '' ]) }}
                    <small class="text-danger">{{ $errors->first('mobile') }}</small>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="user_name">{{ trans('lang.name') }} <span class="astric">*</span></label>
                </div>

                <div class="col-md-12 form-input">
                    {{ Form::text('user_name', isset($reservation) ? $reservation->user->name : null ,[ 'class'=>'form-control name' . ($errors->has('user_name') ? 'redborder' : '')  , 'id'=>'name', isset($reservation) ? 'disabled' : ''  ]) }}
                    <small class="text-danger">{{ $errors->first('user_name') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{  Form::hidden('clinic_id' , $clinic->id) }}
{{  Form::hidden('pattern' , $clinic->pattern) }}

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="day">{{ trans('lang.day') }}<span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::date('day', isset($reservation) ? $reservation->day : null , ['class'=>'form-control no-border date' . ($errors->has('day') ? 'redborder' : '')  , 'id'=>'day', 'required' => 'required']) }}
                <small class="text-danger">{{ $errors->first('day') }}</small>
            </div>
        </div>
    </div>
</div>
<div id="time-wrapper">
    @if( (isset($clinic) && $clinic->pattern == 0))
        <div class="row" id="time-box">
            <div class="form-group col-md-12">
                <div class="row">
                    <div class="col-md-12 label-form">
                        <label for="working_hour_id">{{ trans('lang.time') }} <span class="astric">*</span></label>
                    </div>
                    <div class="col-md-12 form-input">
                        {{ Form::select('working_hour_id', isset($reservation) ? $reservation->times : []  , null ,[ 'class'=>'form-control full-width ' . ($errors->has('working_hour_id') ? 'redborder' : '')  , 'id'=>'time' , 'placeholder'=> trans('lang.choose_time')]) }}
                        <small class="text-danger">{{ $errors->first('working_hour_id') }}</small>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="type">{{ trans('lang.type') }} <span class="astric">*</span></label>
            </div>
            <div class="col-md-12 form-input">
                {{ Form::select('type', [0 => trans('lang.check_up') , 1 => trans('lang.follow_up') ] , null,[ 'class'=>'form-control full-width ' . ($errors->has('type') ? 'redborder' : '')  , 'id'=>'type']) }}
                <small class="text-danger">{{ $errors->first('type') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12 label-form">
                <label for="complaint">{{trans('lang.complaint')}} </label>
            </div>
            <div class=" col-md-12 form-input">
                {{ Form::textarea('complaint', null, ['class'=>'form-control ' . ($errors->has('complaint') ? 'redborder' : '') , 'id'=>'complaint' , 'rows'=>'3']) }}
                <small class="text-danger">{{ $errors->first('complaint') }}</small>
            </div>
        </div>
    </div>
</div>

<a href="{{ url('/reservations/all')}}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>

{{  Form::submit($btn , ['class' => 'btn-loon add-reservation ' . $classes ]) }}
@if(isset($reservation))
    {{  Form::hidden('edit_clinic_id' , $reservation->clinic->id , array('id' => 'edit_clinic_id')) }}
@else
    {{  Form::hidden('clinic_id' , $clinic->id) }}
@endif

@push('more-scripts')
    <script>

        // validate mobile as unique and then add user to the list of patients in DB
        $(document).ready(function () {
            $(document).on('click', '.btn-modal-form-submit', function (e) {
                var form = $('#modal-form');
                var mobile_validate = $('#mobile-validate');
                if (form.parsley().isValid()) {
                    e.preventDefault();
                    $.ajax({
                        type: 'GET',
                        url: URL + '/patients/mobile-validation',
                        data: {_token: token, mobile: mobile_validate.val()},
                        success: function (data) {
                            mobile_validate.next().text('');
                            if (data.status === true) {
                                $('#loading').css('display', 'flex');
                                $.ajax({
                                    type: 'POST',
                                    url: form.attr('action'),
                                    data: form.serialize(),
                                    dataType: 'json',
                                    success: function (data) {
                                        if (data.status === true) {
                                            $('#loading').css('display', 'none');
                                            //  close modal
                                            $('#modal').iziModal('close');
                                            form[0].reset();

                                            //   fill  name and email
                                            $('#tags').val(data.user['mobile']);
                                            $('#name').val(data.user['name']);
                                        }
                                    },
                                    error: function (data) {
                                        // raise swl alert
                                        alert('can\'t add user');
                                    }
                                });
                            } else {
                                mobile_validate.next().text(data.msg);
                            }
                        },
                        error: function () {
                            // raise swl alert
                            alert('can\'t add user');
                        }
                    });
                }
            });
        });
    </script>
@endpush
