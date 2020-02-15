@extends('layouts.admin.admin-master')

@section('title',  trans('lang.create_working_hour'))

@section('styles')
    {!! Html::style('assets/css/admin/select2.min.css') !!}
    {!! Html::style('assets/css/admin/form.css') !!}
    <style>
        .search-field > input {
            margin-bottom: 8px !important;
        }
    </style>
@stop

@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{trans('lang.manage_working_hours')}}</h1>
                        <hr>
                        {!! Form::open(['route' => 'working-hours.store', 'files' => true,  'class' => 'wh-form']) !!}
                        @include('admin.assistant.working-hours.form', ['btn' => trans('lang.save'), 'classes' => 'btn-xs pull-right add-btn add-working-hours'])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

