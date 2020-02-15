@extends('layouts.admin.admin-master')

@section('title', trans('lang.create_service'))

@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
@stop

@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{trans('lang.create_service')}}</h1>
                        <hr>
                        {!! Form::open(['route' => 'doctor-services.store', 'files' => true]) !!}
                        @include('admin.doctor.services.form', ['btn' => 'Add', 'classes' => 'btn-xs pull-right'])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
