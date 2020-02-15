@extends('layouts.admin.admin-master')

@section('title', trans('lang.edit_visit'))

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
                        <h1 class="font-18 loon">{{trans('lang.edit_visit')." ". $visit->user_name  }}</h1>
                        <hr>
                        {!! Form::model($visit, ['route' => ['visits.update', $visit->id], 'method' => 'PATCH', 'files' => true]) !!}
                            @include('admin.common.visits.form', ['btn' => 'Save', 'classes' => 'btn-xs pull-right'])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    {!! Html::script('assets/js/admin/jquery-1.11.3.min.js') !!}
    {!! Html::script('assets/js/admin/jquery-ui.min.js') !!}
    {!! Html::script('assets/js/admin/jquery.ui.touch-punch.min.js') !!}
@stop