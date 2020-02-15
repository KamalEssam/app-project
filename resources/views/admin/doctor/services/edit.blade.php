@extends('layouts.admin.admin-master')

@section('title', trans('lang.edit_service'))

@section('styles')
    {!! Html::style('assets/css/admin/form.css') !!}
@stop

@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-7 col-md-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{trans('lang.edit_service').' '. $service->name }}</h1>
                        <hr>
                        {!! Form::model($service, ['route' => ['doctor-services.update', $service->id], 'method' => 'PATCH']) !!}
                        @include('admin.doctor.services.form_edit', ['btn' => 'Save', 'classes' => 'btn-xs pull-right'])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
