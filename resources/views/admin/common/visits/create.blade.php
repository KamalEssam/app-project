@extends('layouts.admin.admin-master')

@section('title', trans('lang.create_visit'))

@section('styles')
    {!! Html::style('assets/css/admin/select2.min.css') !!}
    {!! Html::style('assets/css/admin/form.css') !!}
@stop
@section('content')
    <div class="page-content mt-70">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h1 class="font-18 loon">{{trans('lang.create_visit')}}</h1>
                        <hr>
                        {!! Form::model($visit , ['route' => ['visits.store', $visit->reservation_id],'method' => 'POST', 'files' => true]) !!}
                        @include('admin.common.visits.form', ['btn' => 'Add', 'classes' => 'btn-xs pull-right'])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
