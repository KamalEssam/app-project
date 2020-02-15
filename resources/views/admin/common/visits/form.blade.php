<div class="jumbotron">
    <ul class="list-unstyled">
        <li>
            <span class="bolder loon"> {{ trans('lang.user_name') .' '.':'.' ' }}</span>
            <span id="username">{{ $visit->user_name }}</span>
        </li>
        <li>
            <span class="bolder loon">{{ trans('lang.clinic_name') .' '.':'.' ' }}</span>
            <span id="clinicname">{{ $visit->clinic_name }}</span>

        </li>
        <li>
            <span class="bolder loon">{{ trans('lang.reservation') .' '.':'.' ' }}</span>
            <span id="day">{{ $visit->reservation_day .' '.','.' '}}</span>
            <span id="time">{{ Super::getNiceTime($visit->time)}} </span>
        </li>
        <li>
            @if(($visit->attachments)->count() > 0)
                <span class="bolder loon">{{ trans('lang.attachments') .' '.':'.' ' }}</span>
                @foreach($visit->attachments as $attachment)
                    @if($attachment->type == 1 || $attachment->type == 2)
                        <span id="attachment"><a
                                    href="{{ $attachment->attachment }}"
                                    download>{{ $attachment->attachment }}</a></span>
                    @elseif($attachment->type == 0)
                        <span id="attachment-img"><a
                                    href="{{ $attachment->attachment }}"
                                    target="_blank"><img
                                        src="{{ $attachment->attachment }}"
                                        style="width: 50px;height:50px;"></a></span>
                    @endif
                @endforeach
            @else
                <span id="no_attachment">{{ trans('lang.no_attachments') }} </span>
            @endif
        </li>
    </ul>
</div>

@if( Auth::user()->role_id == $role_doctor )
    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="diagnosis">{{trans('lang.diagnosis')}} </label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::textarea('diagnosis', null, ['class'=>'form-control ' . ($errors->has('diagnosis') ? 'redborder' : '') , 'id'=>'diagnosis' ,'rows'=>'3']) }}
                    <small class="text-danger">{{ $errors->first('diagnosis') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="medications">{{ trans('lang.medications') }}  </label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::select('medications[]', App\Models\Medication::pluck('name', 'id'), null,  ['multiple' => 'multiple'/*, 'required' => 'required'*/, 'class'=>'form-control multiple-select ' . ($errors->has('skills') ? 'redborder' : '')  , 'id'=>'medications' ]) }}
                    <small class="text-danger">{{ $errors->first('medications') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-12">
            <div class="row">
                <div class="col-md-12 label-form">
                    <label for="next_visit">{{ trans('lang.next_visit') }}</label>
                </div>
                <div class="col-md-12 form-input">
                    {{ Form::date('next_visit',  isset($visit->next_visit) ? Carbon\Carbon::parse($visit->next_visit)->format('Y-m-d') : null, ['class'=>'form-control date ' . ($errors->has('next_visit') ? 'redborder' : '') , 'id'=>'next_visit']) }}
                    <small class="text-danger">{{ $errors->first('next_visit') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="manage-multiple-rows">

        @if(isset($comments))
            @foreach($comments as $comment)
                <div class="row add-row">
                    <div class="form-group col-md-10">
                        <div class="row">
                            <div class="col-md-12 label-form">
                                <label for="comment">{{trans('lang.comment')}} </label>
                            </div>
                            <div class="col-md-12 form-input">
                                {{ Form::textarea('visit_comments[]', isset($comment) ? $comment->comment: null , ['class'=>'form-control' . ($errors->has('comment') ? 'redborder' : '') , 'id'=>'comment', 'rows'=> '3']) }}
                                <small class="text-danger">{{ $errors->first('comment') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <a class="btn btn-danger btn-large del-other padding-7 no-border"><i
                                    class="fa fa-trash"></i></a>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="row">
        <div class="row-input ">
            <div class="form-group col-md-10">
                <div class="row">
                    <div class="col-md-12 label-form">
                        <label for="comment">{{trans('lang.comment')}} </label>
                    </div>
                    <div class="col-md-12 form-input">
                        {{ Form::textarea('visit_comments[]', null , ['class'=>'form-control' . ($errors->has('comment') ? 'redborder' : '') , 'id'=>'comment', 'rows'=> '3']) }}
                        <small class="text-danger">{{ $errors->first('comment') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <a class="btn btn-primary btn-large add-other padding-7 no-border"><i class="fa fa-plus"></i></a>
        </div>
    </div>

@endif

<a href="{{ url('/visits')}}" class="pull-right loon p-7">{{ trans('lang.cancel') }}</a>


{{  Form::submit($btn , ['class' => 'btn-loon ' . $classes ]) }}

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
