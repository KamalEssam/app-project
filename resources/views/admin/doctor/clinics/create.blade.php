@extends('layouts.admin.admin-master')

@section('title', trans('lang.create_clinic'))

@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
@stop

@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{ ($auth->account->type == 0) ? trans('lang.create_clinic') : trans('lang.create_clinic_poly') }}</h1>
                        <hr>
                        {!! Form::open(['route' => 'clinics.store']) !!}
                        @include('admin.doctor.clinics.form', ['btn' => 'Add', 'classes' => 'btn-xs pull-right'])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
