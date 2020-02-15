<div id="modal" data-iziModal-title="{{ trans('lang.add_holiday') }}"
     data-iziModal-subtitle="{{ trans('lang.add_holiday_text') }}" data-iziModal-icon="icon-home">

    <form id="modal-form" action="{{ route('holiday.store') }}" method="POST" class="p-25">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="row">
            <div class="form-group col-md-12">
                <div class="row">
                    <div class="col-md-12 label-form">
                        <label for="en_reason">{{trans('lang.en_reason')}}<span class="astric">*</span></label>
                    </div>
                    <div class="col-md-12 form-input">
                        {{ Form::text('en_reason', null, ['class'=>' form-control ' . ($errors->has('en_reason') ? 'redborder' : '') ,'pattern' => $english_regex,'title' => trans('lang.only_english') ,'required' => 'required']) }}
                        <small class="text-danger">{{ $errors->first('en_reason') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-12">
                <div class="row">
                    <div class="col-md-12 label-form">
                        <label for="ar_reason">{{trans('lang.ar_reason')}}<span class="astric">*</span></label>
                    </div>
                    <div class="col-md-12 form-input">
                        {{ Form::text('ar_reason', null, ['class'=>' form-control ' . ($errors->has('ar_reason') ? 'redborder' : '') ,'pattern' => $arabic_regex,'title' => trans('lang.only_arabic') ,'required' => 'required']) }}
                        <small class="text-danger">{{ $errors->first('ar_reason') }}</small>
                    </div>
                </div>
            </div>
        </div>
        {{-- choose clinic in case of poly clinic --}}
        @if($auth->role_id == $role_doctor && $auth->account->type == 1)
            <div class="row">
                <div class="form-group col-md-12">
                    <div class="row">
                        <div class="col-md-12 label-form">
                            <label for="clinics">{{trans('lang.all_clinics')}}</label>
                            {{ Form::checkbox('clinics[]',-1,true,['class' => 'inline_checkBox']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row add_holiday_to_clinics">
            </div>
        @endif

        <div class="row">
            <div class="form-group col-md-12">
                <div class="row">
                    <div class="col-md-12 label-form">
                        <label for="day">{{ trans('lang.day') }}<span class="astric">*</span></label>
                    </div>
                    <div class="col-md-12 form-input">
                        {{ Form::date('day', null , ['class'=>'form-control no-border date' . ($errors->has('day') ? 'redborder' : '')  , 'id'=>'day', 'required' => 'required']) }}
                        <small class="text-danger">{{ $errors->first('day') }}</small>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-3 pull-right">
                <input type="submit" value="{{ trans('lang.add') }}"
                       class="btn-modal-form-submit btn btn-block btn-xs " id="submit-patient">
            </div>
        </div>
    </form>
    <div class="container" id="loading" style="display: none; !important;">
        <div id="overlay" class="open">
            <div class="display-loading open"></div>
        </div>
    </div>
</div>
@push('more-scripts')
    <script>
        // document.getElementById("select-clinics").style.display = "none";
        $(document).on('change', 'input:checkbox', function () {
            if ($(this).is(":checked")) {
                $('.add_holiday_to_clinics').html('');
            } else {
                $('.add_holiday_to_clinics').append(
                    '<div class="form-group col-md-12">' +
                    ' <div class="row">' +
                    '<div class="col-md-12 label-form">' +
                    '<label for="clinics">' +
                    '{{trans('lang.clinic_poly')}}' +
                    '<span class="astric">*</span>' +
                    '</label>' +
                    '</div>' +
                    '<div class="col-md-12 form-input">' +
                    ' <select multiple="" name="clinics[]" class="chosen-select form-control" id="form-field-select-4" required data-placeholder="Choose a clinic...">' +
                        @php
                            $clinics = DB::table('clinics')->where('account_id', $auth->account_id)->get();
                        @endphp
                                @foreach($clinics as $clinic)
                            '<option value="{{ $clinic->id }}">{{$clinic->{app()->getLocale() . '_name'} }} </option>' +
                        @endforeach
                            '</select>' +
                    '<small class="text-danger">{{ $errors->first('clinics') }}</small>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                );

                $(".chosen-select").chosen();

            }
        });
    </script>
@endpush