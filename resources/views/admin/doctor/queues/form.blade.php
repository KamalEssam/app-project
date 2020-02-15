<div class="jumbotron">
    <ul class="list-unstyled">
        <li>
            <span class="bolder loon"> {{ trans('lang.user_name') .' '.':'.' ' }}</span>
            <span id="username">{{ $patient->name }}</span>
        </li>
        <li>
            <span class="bolder loon">{{ trans('lang.email') .' '.':'.' ' }}</span>
            <span id="clinicname">{{ $patient->email }}</span>
        </li>
        <li>
            <span class="bolder loon">{{ trans('lang.reservation_type') .' '.':'.' ' }}</span>
            <span id="type">{{ $reservation->type == 0 ? trans('lang.check_up') :  trans('lang.follow_up') }}</span>
        </li>
        <li>
            <span class="bolder loon">{{ trans('lang.complaint') .' '.':'.' ' }}</span>
            <span id="type">{{  Super::getProperty($reservation->complaint) }}</span>
        </li>
    </ul>
</div>

<div class="row">
    <div class="form-group col-md-10">
        <div class="row">
            <div class="col-md-2 label-form">
                <label for="diagnosis">{{trans('lang.diagnosis')}} </label>
            </div>
            <div class="col-md-10 form-input">
                {{ Form::textarea('diagnosis', null, ['class'=>'form-control ' . ($errors->has('diagnosis') ? 'redborder' : '') , 'id'=>'diagnosis' ,'rows'=>'3']) }}
                <small class="text-danger">{{ $errors->first('diagnosis') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-10">
        <div class="row">
            <div class="col-md-2 label-form">
                <label for="medications">{{ trans('lang.medications') }}  </label>
            </div>
            <div class="col-md-10 form-input">
                {{ Form::select('medications[]', App\Models\Medication::pluck('name', 'id'), null,  ['multiple' => 'multiple'/*, 'required' => 'required'*/, 'class'=>'form-control multiple-select ' . ($errors->has('skills') ? 'redborder' : '')  , 'id'=>'medications' ]) }}
                <small class="text-danger">{{ $errors->first('medications') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-10">
        <div class="row">
            <div class="col-md-2 label-form">
                <label for="next_visit">{{ trans('lang.next_visit') }}</label>
            </div>
            <div class="col-md-10 form-input">
                {{ Form::date('next_visit', null, ['class'=>'form-control date ' . ($errors->has('next_visit') ? 'redborder' : '') , 'id'=>'day']) }}
                <small class="text-danger">{{ $errors->first('next_visit') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="manage-multiple-rows">
    <div class="row">
        <div class="row-input ">
            <div class="form-group col-md-10">
                <div class="add-row row">
                    <div class="col-md-2 label-form">
                        <label for="comment">{{trans('lang.comment')}} </label>
                    </div>
                    <div class="col-md-10 form-input">
                        {{ Form::textarea('commnts[]', null , ['class'=>'form-control' . ($errors->has('comment') ? 'redborder' : '') , 'id'=>'comment', 'rows'=> '3']) }}
                        <small class="text-danger">{{ $errors->first('comment') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <a class="btn btn-primary btn-large add-other padding-7 no-border"><i
                        class="fa fa-plus"></i></a>
        </div>
    </div>
</div>

{{  Form::submit($btn , ['class' => 'btn-loon ' .$classes ]) }}

@section('scripts')
    {!! Html::script('assets/js/admin/steps.js') !!}
    {!! Html::script('assets/js/admin/select2.min.js') !!}
    {!! Html::script('assets/js/admin/logic.js') !!}
    <script>
        $('.multiple-select').select2({
            'placeholder': 'Select Medications'
        });
    </script>
@stop