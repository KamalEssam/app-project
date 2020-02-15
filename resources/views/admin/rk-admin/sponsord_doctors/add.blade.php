@extends('layouts.admin.admin-master')
@section('title',  trans('lang.manage_specialities') )
@section('content')
    <div class="page-content">
        <div class="page-header">
            <div class="row">
                <div class="col-md-11">
                    <h1>{{trans('lang.manage-sponsored-doctor')}}</h1>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <br><br>
                <div class="clearfix">
                    <div class="pull-right tableTools-container"></div>
                </div>
                <div class="fields" id="other">

                    {!! Form::open(['route' => 'sponsored.store']) !!}
                    {{ csrf_field() }}
                    {{ Form::hidden('speciality_id',$speciality_id) }}
                    @for ($i = 1; $i <= $not_sponsor; $i++)
                        <br><br>
                        <div class="form-group col-md-4" style="padding-left: 0px;">
                            <label for="doctor_id">{{ trans('lang.sponsored-doctor') }}</label>
                        </div>
                        <div class="form-group col-md-8" style="padding-left: 0px;">
                            {{ Form::select('doctor_id[]',$doctors, null,[ 'class'=>'chosen-select form-control' . ($errors->has('doctor_id') ? 'redborder' : '')  , 'id'=>'doctor_id form-field-select-2','title' => trans('lang.only_english'),'placeholder' => 'select sponsor doctor']) }}
                            <small class="text-danger">{{ $errors->first('doctor_id[]') }}</small>
                        </div>
                    @endfor

                    <div class="text-center">
                        {!! Form::submit('save doctors', ['class' => 'btn-loon btn btn-primary mt-15','id' => 'add_doctors']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop
@push('more-scripts')
    <script>
        $(".chosen-select").chosen('');
    </script>
@endpush
